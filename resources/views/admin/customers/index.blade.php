@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title" style="margin: 0;">👥 Daftar Akun Pelanggan Terdaftar</h1>
    </div>

    @if(session('success'))
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 140px;">Kode Pelanggan</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat Email</th>
                    <th>Tanggal Bergabung</th>
                    <th style="width: 150px;">Jumlah Belanja</th>
                    <th style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $index => $customer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span style="font-family: monospace; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12.5px; font-weight: 800; border: 1px solid #7dd3fc;">
                                PLG-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td style="text-align: left; font-weight: bold; padding-left: 16px;">{{ $customer->name }}</td>
                        <td style="text-align: left; padding-left: 16px;">{{ $customer->email }}</td>
                        <td>{{ $customer->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 12px; font-size: 13px; font-weight: bold; border: 1px solid #cbd5e1;">
                                {{ $customer->orders_count }} Pesanan
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="/admin/customers/{{ $customer->id }}/edit" class="btn btn-warning" style="padding: 6px 12px; font-size: 13px;">✏️ Edit</a>
                                <form action="/admin/customers/{{ $customer->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pelanggan {{ $customer->name }}? Semua data pesanan yang bersangkutan akan kehilangan relasi ke akun ini.');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 13px; border: none; font-weight: bold;">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 30px; color: #666; font-style: italic;">Belum ada pelanggan terdaftar</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
