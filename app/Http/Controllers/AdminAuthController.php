<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        if (
            $username == 'admin'
            &&
            $password == 'admin123'
        ) {
            session([
                'admin_logged_in' => true
            ]);

            return redirect('/admin/products');
        }

        return back()->with(
            'error',
            'Username atau password salah'
        );
    }

    public function logout()
    {
        session()->forget(
            'admin_logged_in'
        );

        return redirect('/');
    }
}