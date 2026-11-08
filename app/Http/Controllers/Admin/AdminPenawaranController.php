<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        $query = Penawaran::with('project')->latest();

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
        return view('admin.penawaran.index', compact('penawarans', 'counts'));
    }

    public function approve(Penawaran $penawaran)
    {
        if ($penawaran->status === 'approved' && $penawaran->project_id) {
            return back()->with('info', 'Penawaran ini sudah disetujui sebelumnya.');
        }

        return DB::transaction(function () use ($penawaran) {
            // Format password: IBK + dmy tanggal pembuatan penawaran (contoh: IBK120826)
            $dateSource = $penawaran->created_at ?? now();
            $plainPassword = 'IBK' . $dateSource->format('dmy');

            // 1. Temukan atau terbitkan akun user klien
            $email = $penawaran->email;
            if (empty($email)) {
                $cleanedPhone = preg_replace('/[^0-9]/', '', $penawaran->telepon);
                $email = ($cleanedPhone ?: 'client' . $penawaran->id) . '@klien.indoberkah.com';
            }

            $user = null;
            if ($penawaran->email) {
                $user = User::where('email', $penawaran->email)->first();
            }
            if (!$user && $penawaran->telepon) {
                $user = User::where('phone', $penawaran->telepon)->first();
            }

            if (!$user) {
                // Pastikan email unik jika auto-generated
                $baseEmail = $email;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = str_replace('@', "_{$counter}@", $baseEmail);
                    $counter++;
                }

                $user = User::create([
                    'name'     => $penawaran->nama,
                    'email'    => $email,
                    'phone'    => $penawaran->telepon,
                    'role'     => 'owner',
                    'password' => Hash::make($plainPassword),
                ]);
            } else {
                // Perbarui password ke format IBK<dmy> agar klien dapat langsung login
                $user->update([
                    'password' => Hash::make($plainPassword),
                    'phone'    => $user->phone ?: $penawaran->telepon,
                ]);
            }

            // 2. Terbitkan Proyek Baru Otomatis
            $project = null;
            if ($penawaran->project_id) {
                $project = Project::find($penawaran->project_id);
            }

            if (!$project) {
                $projectName = 'Proyek ' . $penawaran->nama;
                if (preg_match('/(?:Pengajuan Proyek Baru:|Rencana Proyek:)\s*([^\.]+)/i', $penawaran->deskripsi, $matches)) {
                    $extracted = trim($matches[1]);
                    if (!empty($extracted)) {
                        $projectName = $extracted;
                    }
                }

                $project = Project::create([
                    'user_id'             => $user->id,
                    'nama_proyek'         => $projectName,
                    'alamat_proyek'       => $penawaran->alamat,
                    'deskripsi'           => $penawaran->deskripsi,
                    'kategori'            => 'Pembangunan Rumah',
                    'progress_percentage' => 0,
                    'status'              => 'aktif',
                    'main_image'          => $penawaran->foto,
                    'portfolio_approved'  => false,
                ]);
            }

            // 3. Update status penawaran
            $penawaran->update([
                'status'     => 'approved',
                'project_id' => $project->id,
            ]);

            // Siapkan pesan WA konfirmasi untuk dikirim ke klien
            $phoneForWa = preg_replace('/[^0-9]/', '', $penawaran->telepon);
            if (str_starts_with($phoneForWa, '0')) {
                $phoneForWa = '62' . substr($phoneForWa, 1);
            }
            $loginUrl = url('/login');
            $waMessage = "Halo Bapak/Ibu {$penawaran->nama},\n\n"
                       . "Selamat! Pengajuan penawaran proyek konstruksi Anda telah kami setujui (Approved) dan proyek Anda telah resmi didaftarkan di sistem PT Indo Berkah Konstruksi.\n\n"
                       . "Detail Akun Login Klien Anda:\n"
                       . "🌐 Akses: {$loginUrl}\n"
                       . "👤 Username (Email / No. WA / Nama): {$user->email} / {$penawaran->telepon} / {$user->name}\n"
                       . "🔑 Password: {$plainPassword}\n\n"
                       . "Silakan login untuk memantau perkembangan dan progres proyek Anda secara daring.\n\n"
                       . "Terima kasih,\nPT Indo Berkah Konstruksi";

            $waLink = "https://wa.me/{$phoneForWa}?text=" . urlencode($waMessage);

            return back()->with('approved_success', [
                'user_name'      => $user->name,
                'user_email'     => $user->email,
                'user_phone'     => $user->phone,
                'plain_password' => $plainPassword,
                'project_name'   => $project->nama_proyek,
                'project_id'     => $project->id,
                'wa_link'        => $waLink,
            ]);
        });
    }

    public function reject(Penawaran $penawaran)
    {
        $penawaran->update(['status' => 'rejected']);
        return back()->with('success', 'Status penawaran berhasil diubah menjadi Ditolak / Dibatalkan.');
    }

    public function destroy(Penawaran $penawaran)
    {
        $penawaran->delete();
        return back()->with('success', 'Data penawaran berhasil dihapus.');
    }
}
