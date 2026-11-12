<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return redirect('/penawaran')->with('info', 'Pendaftaran akun klien IBK diproses otomatis setelah pengajuan penawaran proyek Anda disetujui.');
    }

    public function login(Request $request)
    {
        $request->merge(['login' => $request->input('login') ?? $request->input('email')]);
        $validated = $request->validate([
            'login' => 'required|string|max:255',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email, No. WhatsApp, atau Nama wajib diisi.',
            'login.string' => 'Email, No. WhatsApp, atau Nama harus berupa teks.',
            'login.max' => 'Identitas login maksimal 255 karakter.',
        ]);

        $loginInput = trim($validated['login']);
        $phone = $this->normalizePhone($loginInput);
        $email = mb_strtolower($loginInput);
        $users = User::where(function ($query) use ($email, $phone) {
            $query->whereRaw('LOWER(email) = ?', [$email])->orWhereRaw('LOWER(name) = ?', [$email]);
            if ($phone !== null) {
                $query->orWhereNotNull('phone');
            }
        })->get()->filter(function (User $user) use ($loginInput, $email, $phone) {
            return mb_strtolower($user->email) === $email
                || mb_strtolower($user->name) === mb_strtolower($loginInput)
                || ($phone !== null && $this->normalizePhone($user->phone) === $phone);
        });

        if ($users->count() > 1) {
            return back()->withErrors(['login' => 'Identitas ini cocok dengan beberapa akun. Gunakan email akun Anda.'])
                ->withInput(['login' => $loginInput]);
        }

        $user = $users->first();
        if ($user && Hash::check($validated['password'], $user->password)) {
            $destination = match ($user->role) {
                'superadmin', 'admin' => '/admin',
                'accounting' => '/admin/keuangan-proyek',
                'owner', 'user', 'client' => '/dashbord',
                default => null,
            };

            if ($destination === null) {
                return back()->with('error', 'Role akun Anda tidak memiliki akses ke portal ini.');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $intended = $request->session()->pull('url.intended');
            if (is_string($intended)) {
                $parts = parse_url($intended);
                $base = parse_url(url('/'));
                $path = $parts['path'] ?? '';
                $local = is_array($parts)
                    && ! isset($parts['user']) && ! isset($parts['pass'])
                    && ! str_contains($intended, '\\')
                    && ! str_contains($path, '%')
                    && ! preg_match('~(?:^|/)\.{1,2}(?:/|$)~', $path)
                    && ! preg_match('/[\x00-\x20\x7f]/', $intended)
                    && ! str_starts_with($intended, '//')
                    && (isset($parts['host'])
                        ? ($parts['host'] === ($base['host'] ?? null)
                            && ($parts['scheme'] ?? null) === ($base['scheme'] ?? null)
                            && ($parts['port'] ?? null) === ($base['port'] ?? null))
                        : ! isset($parts['scheme']));
                $allowed = match ($user->role) {
                    'superadmin', 'admin' => $path === '/admin' || str_starts_with($path, '/admin/'),
                    'accounting' => $path === '/admin/keuangan-proyek' || str_starts_with($path, '/admin/keuangan-proyek/')
                        || $path === '/admin/keuangan-umum' || str_starts_with($path, '/admin/keuangan-umum/')
                        || $path === '/admin/kontak-klien' || str_starts_with($path, '/admin/kontak-klien/')
                        || str_starts_with($path, '/admin/projects/'),
                    default => $path === '/dashbord' || str_starts_with($path, '/dashbord/'),
                };
                if ($local && $allowed) {
                    return redirect()->to($intended);
                }
            }

            return redirect($destination);
        }

        return back()->with('error', 'Kombinasi akun (Email / No. WA / Nama) atau password salah.')
            ->withInput(['login' => $loginInput]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/[\s().-]+/', '', trim($phone ?? ''));

        return preg_match('/\A(?:0|\+?62)([1-9][0-9]{7,11})\z/', $phone, $parts) ? '62'.$parts[1] : null;
    }
}
