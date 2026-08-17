@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 60px; min-height: 85vh; font-family: 'Arial', sans-serif;">
    
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; border: 1px solid #c3e6cb; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; border: 1px solid #f5c6cb; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 30px; align-items: start;">
        
        <!-- Sidebar / Profile Card -->
        <div style="background: white; padding: 30px 24px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; text-align: center;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; font-size: 32px; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 6px 12px rgba(46, 125, 50, 0.2);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h3 style="color: #1f2937; font-size: 18px; font-weight: 700; margin-bottom: 6px; word-break: break-all;">{{ $user->name }}</h3>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px; word-break: break-all;">{{ $user->email }}</p>
            
            <div style="border-top: 1px solid #f3f4f6; padding-top: 16px; text-align: left; font-size: 13.5px; line-height: 1.6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Daftar Alamat Saya</div>
                    <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 10px; font-weight: bold;">{{ $user->addresses->count() ?: 1 }} Alamat</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: #6b7280;">No. HP:</span>
                    <strong style="color: #374151;">{{ $user->phone ?: '-' }}</strong>
                </div>

                <!-- Addresses List -->
                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                    @forelse($user->addresses as $addr)
                        <div style="background: {{ $addr->is_primary ? '#f0fdf4' : '#f9fafb' }}; padding: 10px 12px; border-radius: 8px; font-size: 12px; border: 1px solid {{ $addr->is_primary ? '#bbf7d0' : '#e5e7eb' }}; text-align: left; position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <strong style="color: #166534; font-size: 12.5px;">{{ $addr->label ?: 'Alamat' }}</strong>
                                @if($addr->is_primary)
                                    <span style="background: #2e7d32; color: white; padding: 2px 7px; border-radius: 10px; font-size: 10px; font-weight: bold;">⭐ UTAMA</span>
                                @else
                                    <span style="background: #e5e7eb; color: #4b5563; padding: 2px 7px; border-radius: 10px; font-size: 10px; font-weight: bold;">ALAMAT LAIN</span>
                                @endif
                            </div>
                            <div style="color: #374151; word-break: break-word; line-height: 1.4;">{{ $addr->address }}</div>

                            <div style="display: flex; gap: 6px; margin-top: 8px; justify-content: flex-end;">
                                @if(!$addr->is_primary)
                                    <form method="POST" action="/customer/addresses/{{ $addr->id }}/primary" style="margin: 0; display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 2px 8px; border-radius: 4px; font-size: 10.5px; font-weight: bold; cursor: pointer;">Jadikan Utama</button>
                                    </form>
                                    <form method="POST" action="/customer/addresses/{{ $addr->id }}" style="margin: 0; display: inline;" onsubmit="return confirm('Hapus alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; padding: 2px 8px; border-radius: 4px; font-size: 10.5px; font-weight: bold; cursor: pointer;">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="background: #f9fafb; padding: 10px; border-radius: 8px; font-size: 12px; color: #6b7280; border: 1px solid #f3f4f6;">
                            <strong>Alamat Utama:</strong>
                            <div style="margin-top: 4px; color: #374151;">{{ $user->address ?: 'Belum menambahkan alamat' }}</div>
                        </div>
                    @endforelse
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: #6b7280;">Bergabung Sejak:</span>
                    <strong style="color: #374151;">{{ $user->created_at->translatedFormat('d M Y') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280;">Total Belanja:</span>
                    <strong style="color: #2e7d32;">{{ count($orders) }} Transaksi</strong>
                </div>
            </div>

            <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                <button type="button" onclick="document.getElementById('addNewAddressModal').style.display='flex'" style="display: block; width: 100%; text-align: center; border: 1px solid #2e7d32; color: #2e7d32; text-decoration: none; padding: 9px; border-radius: 8px; font-size: 13.5px; font-weight: bold; transition: all 0.2s; background: #f0fdf4; cursor: pointer; box-sizing: border-box;" onmouseover="this.style.background='#2e7d32'; this.style.color='white'" onmouseout="this.style.background='#f0fdf4'; this.style.color='#2e7d32'">➕ Tambah Alamat Baru</button>
                <button type="button" onclick="document.getElementById('editAddressModal').style.display='flex'" style="display: block; width: 100%; text-align: center; border: 1px solid #d1d5db; color: #374151; text-decoration: none; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: bold; transition: all 0.2s; background: #f9fafb; cursor: pointer; box-sizing: border-box;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f9fafb'">✏️ Edit Profil & Alamat Utama</button>
                <a href="/logout" style="display: block; width: 100%; text-align: center; border: 1px solid #dc3545; color: #dc3545; text-decoration: none; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: bold; transition: all 0.2s; background: transparent; box-sizing: border-box;" onmouseover="this.style.background='#dc3545'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#dc3545'">Keluar Akun</a>
            </div>
        </div>

        <!-- Edit Profile & Address Modal -->
        <div id="editAddressModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; font-family: 'Arial', sans-serif;">
            <div style="background: white; width: 90%; max-width: 480px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">
                    <h3 style="margin: 0; color: #2e7d32; font-size: 18px; font-weight: bold;">📍 {{ $user->address ? 'Ubah Alamat & Data Akun' : 'Tambah Alamat Akun' }}</h3>
                    <button type="button" onclick="document.getElementById('editAddressModal').style.display='none'" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #9ca3af;">&times;</button>
                </div>
                <form method="POST" action="/customer/profile">
                    @csrf
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Nomor HP / WhatsApp</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 20px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Alamat Lengkap Pengiriman</label>
                        
                        <div style="display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;">
                            <button type="button" id="dashUseGPSBtn" style="padding: 6px 10px; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer;">
                                🎯 Lokasi Saat Ini
                            </button>
                            <button type="button" id="dashToggleMapBtn" style="padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer;">
                                🗺️ Pilih di Peta
                            </button>
                        </div>

                        <div id="dashMapContainer" style="display: none; margin-bottom: 12px; background: #f9fafb; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div style="display: flex; gap: 6px; margin-bottom: 6px;">
                                <input type="text" id="dashMapSearch" placeholder="Cari alamat..." style="flex: 1; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; outline: none;">
                                <button type="button" id="dashMapSearchBtn" style="padding: 6px 12px; background: #2e7d32; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer;">Cari</button>
                            </div>
                            <div id="dashMap" style="height: 180px; border-radius: 6px; border: 1px solid #d1d5db; overflow: hidden;"></div>
                        </div>

                        <textarea id="dashAddress" name="address" rows="3" placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Kec, Kota/Kab)" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; box-sizing: border-box;">{{ old('address', $user->address) }}</textarea>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="document.getElementById('editAddressModal').style.display='none'" style="padding: 10px 18px; border: 1px solid #d1d5db; background: white; color: #4b5563; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px;">Batal</button>
                        <button type="submit" style="padding: 10px 20px; border: none; background: #2e7d32; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add New Address Modal -->
        <div id="addNewAddressModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; font-family: 'Arial', sans-serif;">
            <div style="background: white; width: 90%; max-width: 480px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">
                    <h3 style="margin: 0; color: #2e7d32; font-size: 18px; font-weight: bold;">➕ Tambah Alamat Baru</h3>
                    <button type="button" onclick="document.getElementById('addNewAddressModal').style.display='none'" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #9ca3af;">&times;</button>
                </div>
                <form method="POST" action="/customer/addresses">
                    @csrf
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Label Alamat *</label>
                        <select name="label" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; box-sizing: border-box;">
                            <option value="Rumah">🏠 Alamat Rumah</option>
                            <option value="Kantor">🏢 Alamat Kantor</option>
                            <option value="Toko / Gudang">🏪 Toko / Gudang</option>
                            <option value="Alamat Lain">📍 Alamat Lain</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Nama Penerima (Opsional)</label>
                        <input type="text" name="receiver_name" value="{{ $user->name }}" placeholder="Nama penerima paket" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">No. HP Penerima (Opsional)</label>
                        <input type="tel" name="receiver_phone" value="{{ $user->phone }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 16px; text-align: left;">
                        <label style="display: block; font-weight: bold; color: #374151; font-size: 13px; margin-bottom: 6px;">Alamat Lengkap Pengiriman *</label>
                        <textarea name="address" rows="3" required placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Kec, Kota/Kab)" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; box-sizing: border-box;"></textarea>
                    </div>
                    <div style="margin-bottom: 20px; text-align: left; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="is_primary_chk" name="is_primary" value="1">
                        <label for="is_primary_chk" style="font-size: 13px; color: #374151; cursor: pointer; font-weight: bold;">Jadikan sebagai Alamat Utama</label>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="document.getElementById('addNewAddressModal').style.display='none'" style="padding: 10px 18px; border: 1px solid #d1d5db; background: white; color: #4b5563; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px;">Batal</button>
                        <button type="submit" style="padding: 10px 20px; border: none; background: #2e7d32; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px;">Simpan Alamat Baru</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order History Area -->
        <div style="background: white; padding: 35px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <h2 style="color: #1f2937; font-size: 22px; font-weight: bold; margin-bottom: 24px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <span>🧾 Riwayat Belanja Anda</span>
            </h2>

            @if($orders->isEmpty())
                <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 12px; border: 1px dashed #d1d5db;">
                    <span style="font-size: 40px; display: block; margin-bottom: 16px;">📦</span>
                    <h4 style="color: #4b5563; font-size: 16px; font-weight: 700; margin-bottom: 8px;">Belum Ada Riwayat Pesanan</h4>
                    <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">Anda belum melakukan checkout pesanan produk apapun saat login.</p>
                    <a href="/#products" style="background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(46, 125, 50, 0.2);">Belanja Sekarang</a>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb; color: #4b5563; font-weight: bold;">
                                <th style="padding: 12px 10px;">No. Pesanan</th>
                                <th style="padding: 12px 10px;">Tanggal</th>
                                <th style="padding: 12px 10px;">Total Pembayaran</th>
                                <th style="padding: 12px 10px;">Status Bayar</th>
                                <th style="padding: 12px 10px;">Metode</th>
                                <th style="padding: 12px 10px;">Status Pengiriman</th>
                                <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr style="border-bottom: 1px solid #f3f4f6; color: #374151; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 16px 10px; font-weight: bold; color: #2e7d32;">{{ $order->order_number }}</td>
                                    <td style="padding: 16px 10px;">{{ \Carbon\Carbon::parse($order->paid_at ?? $order->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') }} WIB</td>
                                    <td style="padding: 16px 10px; font-weight: bold;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td style="padding: 16px 10px;">
                                        @if($order->payment_status === 'paid')
                                            <span style="background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">Lunas</span>
                                        @elseif($order->payment_status === 'pending_verification')
                                            <span style="background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; border: 1px solid #fde047;">⏳ Verifikasi Kasir</span>
                                        @elseif($order->payment_status === 'pending')
                                            <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">Menunggu</span>
                                        @else
                                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">{{ ucwords($order->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 10px; text-transform: uppercase; font-size: 12px; color: #6b7280; font-weight: bold;">{{ $order->payment_method ?? '-' }}</td>
                                    <td style="padding: 16px 10px;">
                                        @php
                                            $shipStatus = $order->tracking_status ?? 'diproses';
                                            $shipLabels = [
                                                'pending' => 'Menunggu',
                                                'preparing' => 'Dikemas',
                                                'shipped' => 'Dikirim',
                                                'almost_arrived' => 'Hampir Sampai',
                                                'completed' => 'Selesai',
                                                'returned' => 'Pengembalian',
                                            ];
                                            $shipLabel = $shipLabels[$shipStatus] ?? ucwords(str_replace('_', ' ', $shipStatus));
                                            $badgeBg = '#e5e7eb'; $badgeColor = '#374151';
                                            if ($shipStatus === 'preparing') { $badgeBg = '#e0f2fe'; $badgeColor = '#0369a1'; }
                                            elseif ($shipStatus === 'shipped' || $shipStatus === 'almost_arrived') { $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; }
                                            elseif ($shipStatus === 'completed') { $badgeBg = '#d1fae5'; $badgeColor = '#065f46'; }
                                            elseif ($shipStatus === 'returned') { $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; }
                                        @endphp
                                        <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">{{ $shipLabel }}</span>
                                        @if($order->estimated_delivery)
                                            <div style="font-size: 11px; color: #047857; margin-top: 6px; font-weight: 600;">
                                                📅 {{ $order->estimated_delivery }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 10px; text-align: center;">
                                        <div style="display: flex; flex-direction: column; gap: 6px; align-items: center;">
                                            @if($order->payment_method === 'transfer_bank' && $order->payment_status !== 'paid')
                                                <button onclick="showBankTransferModal({ order_id: '{{ $order->order_number }}', order_db_id: {{ $order->id }}, bank_name: 'Bank BCA', bank_account: '123-456-7890', account_holder: 'Toko Dewi Lestari 2', total_amount: {{ $order->total_amount }} })" style="background: #e0f2fe; color: #0369a1; border: 1px solid #0284c7; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                                                    🏦 Struk / Simulasi Bayar
                                                </button>
                                            @elseif($order->payment_status === 'pending' && $order->snap_token)
                                                <button onclick="payPending('{{ $order->snap_token }}', '{{ $order->order_number }}')" style="background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#f59e0b'; this.style.color='white'" onmouseout="this.style.background='#fef3c7'; this.style.color='#92400e'">⏳ Belum Bayar</button>
                                            @endif

                                            @if($order->tracking_ticket_id)
                                                <a href="/track-order/{{ $order->tracking_ticket_id }}" style="background: #2e7d32; color: white; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#1b5e20'" onmouseout="this.style.background='#2e7d32'">🚚 Lacak Pesanan</a>
                                            @endif

                                            @if($order->tracking_status === 'completed')
                                                @php
                                                    $hasRating = \App\Models\Rating::where('order_id', $order->id)->where('user_id', $user->id)->exists();
                                                @endphp
                                                <a href="/customer/orders/{{ $order->id }}/rate" style="background: {{ $hasRating ? '#d1fae5' : '#fffbeb' }}; color: {{ $hasRating ? '#065f46' : '#b45309' }}; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block; transition: all 0.2s; white-space: nowrap; border: 1px solid {{ $hasRating ? '#a7f3d0' : '#fde68a' }};">
                                                    {{ $hasRating ? '✅ Lihat Rating' : '⭐ Beri Rating' }}
                                                </a>
                                            @endif

                                            @if(!$order->tracking_ticket_id && $order->payment_status !== 'pending')
                                                <span style="color: #9ca3af; font-size: 11px;">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function payPending(snapToken, orderNumber) {
        if (!snapToken) {
            alert('Token pembayaran tidak ditemukan.');
            return;
        }
        snap.pay(snapToken, {
            onSuccess: function(result){
                // Poll check-payment to process order on backend (generate ticket, reduce stock)
                let attempts = 0;
                const maxAttempts = 10;
                const orderId = orderNumber || result.order_id;
                const checkInterval = setInterval(() => {
                    attempts++;
                    fetch('/check-payment/' + orderId, { cache: 'no-store' })
                        .then(res => res.json())
                        .then(data => {
                            if(data.tracking_ticket_id) {
                                clearInterval(checkInterval);
                                // Clear cart in localStorage
                                try {
                                    localStorage.removeItem('dewilestari_cart');
                                    localStorage.removeItem('dewilestari_cart_timestamp');
                                    localStorage.removeItem('last_order_id');
                                } catch(e) {}
                                // Show ticket modal
                                const modalHtml = `
                                    <div id="ticketModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; display:flex; align-items:center; justify-content:center; font-family:sans-serif;">
                                        <div style="background:white; padding:30px; border-radius:10px; text-align:center; max-width:400px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                                            <h2 style="margin-top:0; color:#2e7d32;">🎉 Pembayaran Berhasil!</h2>
                                            <p style="color:#555; margin-bottom:20px;">Pesanan Anda sedang diproses. Berikut adalah ID Tiket Pelacakan Anda:</p>
                                            <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:25px;">
                                                <input type="text" id="copyTicketId" value="${data.tracking_ticket_id}" readonly style="padding:10px; font-size:18px; font-weight:bold; border:2px dashed #2e7d32; border-radius:5px; text-align:center; width:200px; color:#333; outline:none; background:#f9f9f9;">
                                                <button onclick="document.getElementById('copyTicketId').select(); document.execCommand('copy'); this.innerText='Disalin!'; setTimeout(()=>this.innerText='Salin',2000);" style="padding:10px 15px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Salin</button>
                                            </div>
                                            <button onclick="document.getElementById('ticketModal').remove(); location.reload();" style="width:100%; padding:12px; background:#f39c12; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; font-weight:bold;">Tutup & Lanjutkan</button>
                                        </div>
                                    </div>
                                `;
                                document.body.insertAdjacentHTML('beforeend', modalHtml);
                            } else if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                alert('Pembayaran berhasil diproses! Silakan cek status pesanan Anda.');
                                location.reload();
                            }
                        })
                        .catch(err => {
                            if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                alert('Pembayaran berhasil!');
                                location.reload();
                            }
                        });
                }, 1500);
            },
            onPending: function(result){
                alert("Menunggu pembayaran! Silakan selesaikan pembayaran Anda.");
                location.reload();
            },
            onError: function(result){
                alert("Pembayaran gagal!");
                location.reload();
            },
            onClose: function(){
                alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
            }
        });
    }
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let dashMap = null;
    let dashMarker = null;
    const defaultCenter = [-6.8774, 107.5467];

    const toggleBtn = document.getElementById('dashToggleMapBtn');
    const container = document.getElementById('dashMapContainer');
    const gpsBtn = document.getElementById('dashUseGPSBtn');
    const addressArea = document.getElementById('dashAddress');
    const searchInput = document.getElementById('dashMapSearch');
    const searchBtn = document.getElementById('dashMapSearchBtn');

    function initDashMap(lat, lon) {
        if (!dashMap && typeof L !== 'undefined') {
            dashMap = L.map('dashMap').setView([lat, lon], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(dashMap);

            dashMarker = L.marker([lat, lon], { draggable: true }).addTo(dashMap);

            dashMarker.on('dragend', function () {
                const pos = dashMarker.getLatLng();
                updateAddr(pos.lat, pos.lng);
            });

            dashMap.on('click', function (e) {
                setDashLocation(e.latlng.lat, e.latlng.lng, true);
            });
        } else if (dashMap) {
            dashMap.setView([lat, lon], 14);
            if (dashMarker) dashMarker.setLatLng([lat, lon]);
        }

        setTimeout(() => {
            if (dashMap) dashMap.invalidateSize();
        }, 200);
    }

    function setDashLocation(lat, lon, fetchAddr = true) {
        container.style.display = 'block';
        initDashMap(lat, lon);
        if (dashMarker) dashMarker.setLatLng([lat, lon]);
        if (dashMap) dashMap.setView([lat, lon], 15);
        if (fetchAddr) updateAddr(lat, lon);
    }

    function updateAddr(lat, lon) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                addressArea.value = data.display_name;
            }
        })
        .catch(() => {});
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (container.style.display === 'none' || !container.style.display) {
                container.style.display = 'block';
                initDashMap(defaultCenter[0], defaultCenter[1]);
            } else {
                container.style.display = 'none';
            }
        });
    }

    if (gpsBtn) {
        gpsBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
                return;
            }

            const origText = gpsBtn.innerHTML;
            gpsBtn.innerHTML = '⏳ Memuat...';
            gpsBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    gpsBtn.innerHTML = origText;
                    gpsBtn.disabled = false;
                    setDashLocation(pos.coords.latitude, pos.coords.longitude, true);
                },
                function () {
                    gpsBtn.innerHTML = origText;
                    gpsBtn.disabled = false;
                    alert('Gagal mengambil lokasi saat ini. Pastikan izin lokasi (GPS) diizinkan di browser Anda.');
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        });
    }

    if (searchBtn && searchInput) {
        const runSearch = function () {
            const q = searchInput.value.trim();
            if (!q) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    setDashLocation(lat, lon, false);
                    addressArea.value = data[0].display_name;
                } else {
                    alert('Alamat tidak ditemukan.');
                }
            })
            .catch(() => alert('Gagal mencari lokasi.'));
        };

        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            runSearch();
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                runSearch();
            }
        });
    }
});
</script>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
    }
</style>
@endsection
