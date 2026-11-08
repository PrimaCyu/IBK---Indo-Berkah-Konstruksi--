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
        $request->validate([
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('login') ?? $request->input('email', ''));

        if (empty($loginInput)) {
            return back()->withErrors(['login' => 'Email, No. WhatsApp, atau Nama wajib diisi.'])->withInput();
        }

        $password = $request->input('password');

        // Normalisasi nomor telepon jika input berupa digit angka
        $cleanedPhone = preg_replace('/[^0-9]/', '', $loginInput);
        $phoneVariants = [];
        if (strlen($cleanedPhone) >= 8) {
            $phoneVariants[] = $cleanedPhone;
            if (str_starts_with($cleanedPhone, '0')) {
                $phoneVariants[] = '62' . substr($cleanedPhone, 1);
            } elseif (str_starts_with($cleanedPhone, '62')) {
                $phoneVariants[] = '0' . substr($cleanedPhone, 2);
            }
        }

        // Cari user berdasarkan email, nama, atau no whatsapp/phone
        $user = User::where(function ($query) use ($loginInput, $phoneVariants) {
            $query->where('email', $loginInput)
                  ->orWhere('name', $loginInput);
            if (!empty($phoneVariants)) {
                $query->orWhereIn('phone', $phoneVariants);
            }
        })->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin');
            }

            if (in_array($user->role, ['owner', 'user', 'client'])) {
                return redirect()->intended('/dashbord');
            }

            Auth::logout();
            return back()->with('error', 'Role akun Anda tidak memiliki akses ke portal ini.');
        }

        return back()->with('error', 'Kombinasi akun (Email / No. WA / Nama) atau password salah.')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}