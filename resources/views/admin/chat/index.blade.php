@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <div>
            <h1 class="admin-title" style="margin:0; font-size:22px; color:#166534; font-weight:800;">
                💬 Live Chat & Pesan Pelanggan
            </h1>
            <small style="color:#64748b; font-size:13px; font-weight:600; display:block; margin-top:4px;">
                ℹ️ Memantau dan membalas obrolan riil pelanggan toko beserta riwayat percakapan.
            </small>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-weight:bold; border:1px solid #c3e6cb;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x:auto;">
        @if(empty($sessionGroups) || count($sessionGroups) === 0)
            <div style="text-align:center; padding:40px; color:#64748b; font-weight:600;">
                💬 Belum ada percakapan obrolan dari pelanggan.
            </div>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>No HP / WhatsApp</th>
                        <th>Pesanan / Tiket</th>
                        <th>Pesan Terakhir</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessionGroups as $group)
                    @php
                        $cust = $group['customer'];
                        $lastMsg = $group['last_message'];
                        $primarySessId = $group['primary_session_id'];
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:38px; height:38px; border-radius:50%; background:#2e7d32; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:15px; flex-shrink:0;">
                                    {{ strtoupper(substr($cust['name'] ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <strong style="color:#1e293b; font-size:14px; display:block;">{{ $cust['name'] }}</strong>
                                    @if($cust['is_registered'])
                                        <span style="background:#dcfce7; color:#15803d; font-size:10.5px; padding:1px 6px; border-radius:4px; font-weight:bold; border:1px solid #bbf7d0;">
                                            👤 Pelanggan Terdaftar
                                        </span>
                                    @else
                                        <span style="background:#f1f5f9; color:#475569; font-size:10.5px; padding:1px 6px; border-radius:4px; font-weight:600; border:1px solid #cbd5e1;">
                                            🌐 Tamu Pesanan
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($cust['phone'] !== '-')
                                <div style="font-weight:600; color:#334155; font-size:13px;">
                                    {{ $cust['phone'] }}
                                </div>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $cust['phone']);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" style="font-size:11px; color:#16a34a; font-weight:bold; text-decoration:none; display:inline-flex; align-items:center; gap:3px; margin-top:2px;">
                                    📲 Open WA
                                </a>
                            @else
                                <span style="color:#94a3b8; font-size:12px;">-</span>
                            @endif
                        </td>

                        <td>
                            @if($cust['order_number'])
                                <span style="font-family:monospace; background:#fef3c7; color:#92400e; padding:3px 7px; border-radius:4px; font-size:11px; font-weight:bold; border:1px solid #fde047; display:inline-block;">
                                    #{{ $cust['order_number'] }}
                                </span>
                            @endif
                            @if($cust['ticket_id'])
                                <div style="margin-top:2px; font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px; display:inline-block; border:1px solid #bae6fd;">
                                    🎫 {{ $cust['ticket_id'] }}
                                </div>
                            @endif
                            @if(!$cust['order_number'] && !$cust['ticket_id'])
                                <span style="font-size:11px; color:#64748b;">Sesi Live Chat</span>
                            @endif
                        </td>

                        <td style="max-width:240px;">
                            @if($lastMsg)
                                <div style="font-size:12.5px; color:#334155; line-height:1.4;">
                                    @if($lastMsg->sender === 'admin')
                                        <strong style="color:#2563eb;">Anda:</strong>
                                    @else
                                        <strong style="color:#16a34a;">Pelanggan:</strong>
                                    @endif
                                    {{ Str::limit($lastMsg->message, 55) }}
                                </div>
                            @else
                                <span style="color:#94a3b8; font-size:12px;">-</span>
                            @endif
                        </td>

                        <td style="font-size:12px; color:#64748b; white-space:nowrap;">
                            @if($lastMsg)
                                {{ \Carbon\Carbon::parse($lastMsg->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB<br>
                                <small style="color:#94a3b8;">({{ \Carbon\Carbon::parse($lastMsg->created_at)->diffForHumans() }})</small>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($group['unread_count'] > 0)
                                <span style="background:#fee2e2; color:#dc2626; padding:4px 10px; border-radius:12px; font-size:11.5px; font-weight:bold; border:1px solid #fca5a5; display:inline-block;">
                                    🔔 {{ $group['unread_count'] }} Pesan Baru
                                </span>
                            @else
                                <span style="background:#f1f5f9; color:#64748b; padding:4px 10px; border-radius:12px; font-size:11.5px; font-weight:600; display:inline-block;">
                                    ✓ Terbaca
                                </span>
                            @endif
                        </td>

                        <td>
                            <a href="/admin/chat/{{ $primarySessId }}" class="btn" style="background:#2563eb; color:white; padding:6px 14px; font-size:12px; font-weight:bold; border-radius:6px; text-decoration:none; display:inline-block;">
                                💬 Buka Obrolan
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
