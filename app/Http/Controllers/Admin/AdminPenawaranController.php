<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminPenawaranController extends Controller
{
    public function index(Request $request)
    {
        $counts = [
            'total'    => Penawaran::count(),
            'pending'  => Penawaran::where('status', 'pending')->count(),
            'approved' => Penawaran::where('status', 'approved')->count(),
            'rejected' => Penawaran::where('status', 'rejected')->count(),
        ];

        $query = Penawaran::with('project.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('budget', 'like', "%{$search}%");
            });
        }

        $penawarans = $query->paginate(10)->withQueryString();
        $penawarans->through(function (Penawaran $quote) {
            $contact = $quote->project_id || $quote->status === 'approved'
                ? $quote->project?->user?->phone
                : $quote->telepon;
            $quote->wa_phone = $this->normalizePhone($contact);

            return $quote;
        });

        return view('admin.penawaran.index', compact('penawarans', 'counts'));
    }

    public function approve(Request $request, Penawaran $penawaran)
    {
        $request->session()->forget(['approved_success', '_old_input', 'errors']);

        return DB::transaction(function () use ($request, $penawaran) {
            $penawaran = Penawaran::whereKey($penawaran->getKey())->lockForUpdate()->firstOrFail();

            if ($penawaran->status === 'approved' && $penawaran->project_id && $penawaran->project()->exists()) {
                return back()->with('info', 'Penawaran ini sudah disetujui sebelumnya. Akun dan proyek tidak diubah.');
            }

            if ($penawaran->project_id || $penawaran->status !== 'pending') {
                return back()->with('error', 'Hanya penawaran berstatus menunggu tanpa proyek tertaut yang dapat disetujui. Periksa data penawaran ini.');
            }

            $phone = $this->normalizePhone($penawaran->telepon);
            $email = filled($penawaran->email) ? mb_strtolower(trim($penawaran->email)) : null;

            if (! $phone) {
                return back()->with('error', 'Nomor WhatsApp penawaran tidak valid. Gunakan nomor Indonesia lengkap berawalan 0, 62, atau +62.');
            }

            if (Validator::make(['email' => $email], ['email' => 'nullable|email|max:255'])->fails()) {
                return back()->with('error', 'Email penawaran tidak valid. Perbaiki data kontak sebelum menyetujui.');
            }

            // Legacy contacts may contain spaces, parentheses, or country-code variants.
            $matches = User::where(function ($query) use ($email) {
                $query->whereNotNull('phone');
                if ($email !== null) {
                    $query->orWhereRaw('LOWER(email) = ?', [$email]);
                }
            })->lockForUpdate()->get(['id', 'name', 'email', 'phone', 'role'])->filter(function (User $user) use ($email, $phone) {
                return ($email !== null && mb_strtolower($user->email) === $email)
                    || $this->normalizePhone($user->phone) === $phone;
            });

            if ($matches->contains(fn (User $user) => ! in_array($user->role, ['owner', 'user', 'client'], true))) {
                return back()->with('error', 'Kontak penawaran cocok dengan akun staf atau akun non-klien. Akun tersebut tidak boleh digunakan untuk proyek klien.');
            }

            if ($matches->count() > 1) {
                return back()->with('error', 'Email dan nomor WhatsApp cocok dengan beberapa akun atau akun yang berbeda. Verifikasi dan perbaiki kontak sebelum menyetujui.');
            }

            $user = $matches->first();
            $accountCreated = $user === null;
            $plainPassword = null;

            if ($user) {
                $existingPhone = $this->normalizePhone($user->phone);
                if (($email !== null && mb_strtolower($user->email) !== $email)
                    || ($existingPhone !== null && $existingPhone !== $phone)) {
                    return back()->with('error', 'Email dan nomor WhatsApp penawaran tidak sesuai dengan kontak akun klien yang sama. Verifikasi kontak terlebih dahulu.');
                }

                if (! $request->boolean('confirm_existing_account')) {
                    return back()->withErrors([
                        'confirm_existing_account' => "Akun klien {$user->email} sudah ada. Verifikasi identitas pemohon, lalu centang konfirmasi penggunaan akun lama. Password dan kontak akun tidak akan diubah.",
                    ])->withInput(['approval_quote_id' => $penawaran->getKey()]);
                }
            } else {
                if ($email === null) {
                    $baseEmail = $phone.'@klien.indoberkah.com';
                    $email = $baseEmail;
                    $counter = 1;
                    while (User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
                        $email = str_replace('@', '_'.$counter++.'@', $baseEmail);
                    }
                }

                $plainPassword = Str::password(24);
                $user = User::create([
                    'name' => $penawaran->nama,
                    'email' => $email,
                    'phone' => $phone,
                    'role' => 'owner',
                    'password' => Hash::make($plainPassword),
                ]);
            }

            $projectName = 'Proyek '.$penawaran->nama;
            if (is_string($penawaran->deskripsi) && preg_match('/(?:Pengajuan Proyek Baru:|Rencana Proyek:)\s*([^\.]+)/i', $penawaran->deskripsi, $projectMatches)) {
                $extracted = trim($projectMatches[1]);
                if ($extracted !== '') {
                    $projectName = Str::limit($extracted, 255, '');
                }
            }

            $project = Project::create([
                'user_id' => $user->id,
                'nama_proyek' => $projectName,
                'alamat_proyek' => $penawaran->alamat,
                'deskripsi' => $penawaran->deskripsi,
                'kategori' => 'Pembangunan Rumah',
                'progress_percentage' => 0,
                'status' => 'aktif',
                'main_image' => $penawaran->foto,
                'portfolio_approved' => false,
            ]);

            $penawaran->update([
                'status' => 'approved',
                'project_id' => $project->id,
            ]);

            $phoneForWa = $this->normalizePhone($user->phone);
            $loginUrl = url('/login');
            $waMessage = "Halo Bapak/Ibu {$user->name},\n\n"
                ."Penawaran Anda telah disetujui dan {$project->nama_proyek} telah aktif di sistem PT Indo Berkah Konstruksi.\n\n"
                ."Portal klien: {$loginUrl}\n"
                ."Gunakan akun klien Anda untuk memantau progres. Informasi akses akun baru disampaikan secara terpisah melalui kontak yang telah diverifikasi.\n\n"
                ."Terima kasih,\nPT Indo Berkah Konstruksi";

            return back()->with('approved_success', [
                'account_created' => $accountCreated,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,
                'plain_password' => $plainPassword,
                'project_name' => $project->nama_proyek,
                'project_id' => $project->id,
                'wa_link' => $phoneForWa ? 'https://wa.me/'.$phoneForWa.'?text='.urlencode($waMessage) : null,
            ]);
        });
    }

    public function reject(Penawaran $penawaran)
    {
        return DB::transaction(function () use ($penawaran) {
            $penawaran = Penawaran::whereKey($penawaran->getKey())->lockForUpdate()->firstOrFail();

            if ($penawaran->status !== 'pending' || $penawaran->project_id) {
                return back()->with('error', 'Hanya penawaran berstatus menunggu tanpa proyek tertaut yang dapat ditolak.');
            }

            $penawaran->update(['status' => 'rejected']);

            return back()->with('success', 'Status penawaran berhasil diubah menjadi Ditolak.');
        });
    }

    public function destroy(Penawaran $penawaran)
    {
        $penawaran->delete();

        return back()->with('success', 'Data penawaran berhasil dihapus.');
    }

    private function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/[\s().-]+/', '', trim($phone ?? ''));

        return preg_match('/\A(?:0|\+?62)([1-9][0-9]{7,11})\z/', $phone, $parts) ? '62'.$parts[1] : null;
    }
}
