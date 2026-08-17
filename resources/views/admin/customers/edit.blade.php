@extends('admin.layout')

@section('content')
<div class="admin-card" style="max-width: 600px; margin: 120px auto 50px;">
    <h1 class="admin-title">✏️ Edit Akun Pelanggan</h1>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/admin/customers/{{ $customer->id }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap *</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Alamat Email *</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi Baru (Opsional)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah kata sandi">
            <small style="color: #666; display: block; margin-top: 5px; margin-bottom: 15px;">Minimal 6 karakter.</small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
            <a href="/admin/customers" class="btn" style="background: #e5e7eb; color: #374151;">Batal</a>
        </div>
    </form>
</div>
@endsection
