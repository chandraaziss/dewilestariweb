@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 60px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div style="width: 100%; max-width: 450px; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #2e7d32; font-size: 24px; font-weight: bold; margin-bottom: 8px;">Masuk Akun Pelanggan</h2>
            <p style="color: #666; font-size: 14px;">Silakan masuk untuk melihat riwayat belanja Anda</p>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Google SSO Login Button -->
        <div style="margin-bottom: 20px;">
            <a href="/auth/google" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; background: #ffffff; color: #374151; border: 1.5px solid #d1d5db; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.04);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#9ca3af';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#d1d5db';">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Masuk dengan Google
            </a>
        </div>

        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="flex: 1; border-bottom: 1px solid #e5e7eb;"></div>
            <span style="padding: 0 12px; color: #9ca3af; font-size: 12px; text-transform: uppercase; font-weight: bold;">Atau Masuk via Email</span>
            <div style="flex: 1; border-bottom: 1px solid #e5e7eb;"></div>
        </div>

        <form method="POST" action="/login">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Kata Sandi</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #4b5563;">
                    <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #2e7d32;">
                    Ingat Saya
                </label>
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(46, 125, 50, 0.2);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 12px rgba(46, 125, 50, 0.3)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(46, 125, 50, 0.2)'">
                Masuk Ke Akun
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 0;">
                Belum punya akun? 
                <a href="/register" style="color: #2e7d32; font-weight: bold; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#1b5e20'" onmouseout="this.style.color='#2e7d32'">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
