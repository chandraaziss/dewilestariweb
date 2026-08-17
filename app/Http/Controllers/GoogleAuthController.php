<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
            return redirect('/login')->withErrors([
                'google' => 'Kunci Google OAuth belum dikonfigurasi di file .env. Silakan lengkapi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET terlebih dahulu.'
            ]);
        }

        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                return redirect('/login')->withErrors([
                    'google' => 'Tidak berhasil mendapatkan data email dari Google.'
                ]);
            }

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update google_id and avatar if missing
                $user->update([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user registered via Google SSO
                $user = User::create([
                    'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pelanggan',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null,
                ]);
            }

            Auth::login($user, true);

            return redirect('/customer/dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda berhasil masuk menggunakan Google.');
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Gagal masuk dengan Google. Silakan coba lagi atau gunakan login manual.'
            ]);
        }
    }
}
