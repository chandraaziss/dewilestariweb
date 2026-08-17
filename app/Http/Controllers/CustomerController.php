<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\UserAddress;

class CustomerController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/customer/dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ];
        if (\Schema::hasColumn('users', 'plain_password')) {
            $userData['plain_password'] = $request->password;
        }
        $user = User::create($userData);

        // Save Primary Address
        if (!empty($request->address)) {
            UserAddress::create([
                'user_id' => $user->id,
                'label' => $request->address_label ?: 'Rumah (Utama)',
                'receiver_name' => $user->name,
                'receiver_phone' => $user->phone,
                'address' => $request->address,
                'is_primary' => true,
            ]);
        }

        // Save Extra Addresses if provided
        if ($request->has('extra_addresses') && is_array($request->extra_addresses)) {
            foreach ($request->extra_addresses as $extra) {
                if (!empty($extra['address'])) {
                    UserAddress::create([
                        'user_id' => $user->id,
                        'label' => $extra['label'] ?: 'Alamat Lain',
                        'receiver_name' => $user->name,
                        'receiver_phone' => $user->phone,
                        'address' => $extra['address'],
                        'is_primary' => false,
                    ]);
                }
            }
        }

        Auth::login($user);

        return redirect('/customer/dashboard')->with('success', 'Pendaftaran akun berhasil! Selamat datang.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        // Also sync primary address in user_addresses table
        if (!empty($request->address)) {
            $primary = $user->addresses()->where('is_primary', true)->first();
            if ($primary) {
                $primary->address = $request->address;
                $primary->receiver_name = $request->name;
                $primary->receiver_phone = $request->phone;
                $primary->save();
            } else {
                UserAddress::create([
                    'user_id' => $user->id,
                    'label' => 'Alamat Utama',
                    'receiver_name' => $user->name,
                    'receiver_phone' => $user->phone,
                    'address' => $request->address,
                    'is_primary' => true,
                ]);
            }
        }

        return redirect('/customer/dashboard')->with('success', 'Profil dan alamat berhasil diperbarui.');
    }

    public function storeAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'label' => 'required|string|max:100',
            'address' => 'required|string|max:500',
        ], [
            'address.required' => 'Alamat pengiriman wajib diisi.',
        ]);

        $isPrimary = $user->addresses()->count() === 0 || $request->has('is_primary');

        if ($isPrimary) {
            $user->addresses()->update(['is_primary' => false]);
        }

        UserAddress::create([
            'user_id' => $user->id,
            'label' => $request->label ?: 'Alamat Lain',
            'receiver_name' => $request->receiver_name ?: $user->name,
            'receiver_phone' => $request->receiver_phone ?: $user->phone,
            'address' => $request->address,
            'is_primary' => $isPrimary,
        ]);

        if ($isPrimary) {
            $user->address = $request->address;
            $user->save();
        }

        return redirect('/customer/dashboard')->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function setPrimaryAddress($id)
    {
        $user = Auth::user();
        $targetAddress = UserAddress::where('user_id', $user->id)->findOrFail($id);

        $user->addresses()->update(['is_primary' => false]);
        $targetAddress->is_primary = true;
        $targetAddress->save();

        $user->address = $targetAddress->address;
        $user->save();

        return redirect('/customer/dashboard')->with('success', 'Alamat utama berhasil diperbarui.');
    }

    public function deleteAddress($id)
    {
        $user = Auth::user();
        $targetAddress = UserAddress::where('user_id', $user->id)->findOrFail($id);

        $wasPrimary = $targetAddress->is_primary;
        $targetAddress->delete();

        if ($wasPrimary) {
            $nextPrimary = $user->addresses()->first();
            if ($nextPrimary) {
                $nextPrimary->is_primary = true;
                $nextPrimary->save();
                $user->address = $nextPrimary->address;
            } else {
                $user->address = null;
            }
            $user->save();
        }

        return redirect('/customer/dashboard')->with('success', 'Alamat berhasil dihapus.');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/customer/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/customer/dashboard')->with('success', 'Berhasil masuk ke akun Anda.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah keluar dari akun.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('customer.dashboard', compact('user', 'orders'));
    }

    public function adminIndex()
    {
        // Get all users with their orders count
        $customers = User::withCount('orders')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.customers.index', compact('customers'));
    }

    public function adminEdit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $customer = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;

        if (!empty($request->password)) {
            $customer->password = Hash::make($request->password);
            if (\Schema::hasColumn('users', 'plain_password')) {
                $customer->plain_password = $request->password;
            }
        }

        $customer->save();

        return redirect('/admin/customers')->with('success', 'Akun pelanggan berhasil diperbarui.');
    }

    public function adminDestroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return redirect('/admin/customers')->with('success', 'Akun pelanggan berhasil dihapus.');
    }
}
