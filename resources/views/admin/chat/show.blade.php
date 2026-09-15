@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <a href="/admin/chat" class="btn" style="background:#64748b; color:white; padding:7px 14px; border-radius:6px; font-weight:bold; font-size:13px; text-decoration:none;">
                ← Kembali ke Daftar Chat
            </a>
            <h1 class="admin-title" style="margin:0; font-size:20px; color:#166534; font-weight:800;">
                💬 Ruang Obrolan Live Chat
            </h1>
        </div>
        @if(!empty($info['phone']) && $info['phone'] !== '-')
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $info['phone']);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
            @endphp
            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn" style="background:#25d366; color:white; padding:7px 14px; border-radius:6px; font-weight:bold; font-size:13px; text-decoration:none;">
                📲 Chat via WhatsApp Direct
            </a>
        @endif
    </div>

    <!-- Info Pelanggan & Pesanan Header -->
    <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:14px 18px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:46px; height:46px; border-radius:50%; background:#2e7d32; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:18px;">
                {{ strtoupper(substr($info['name'] ?? 'P', 0, 1)) }}
            </div>
            <div>
                <h3 style="margin:0; font-size:16px; color:#1e293b; font-weight:800;">
                    {{ $info['name'] }}
                </h3>
                <div style="font-size:12.5px; color:#475569; margin-top:2px;">
                    📞 Telepon/WA: <strong>{{ $info['phone'] }}</strong>
                    @if(!empty($info['email']) && $info['email'] !== '-')
                        | ✉️ {{ $info['email'] }}
                    @endif
                </div>
            </div>
        </div>

        <div style="text-align:right;">
            @if(!empty($info['order_number']))
                <div style="margin-bottom:3px;">
                    <span style="font-family:monospace; background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:4px; font-size:12px; font-weight:bold; border:1px solid #fde047;">
                        Pesanan: #{{ $info['order_number'] }}
                    </span>
                </div>
            @endif
            @if(!empty($info['ticket_id']))
                <div style="font-size:11.5px; background:#e0f2fe; color:#0369a1; padding:2px 7px; border-radius:4px; border:1px solid #bae6fd; display:inline-block;">
                    🎫 Tiket: {{ $info['ticket_id'] }}
                </div>
            @endif
        </div>
    </div>

    <!-- Container Chat Box -->
    <div style="display:flex; flex-direction:column; height:60vh; max-height:550px; border-radius:12px; border:1px solid #cbd5e1; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

        <!-- List Pesan -->
        <div id="adminChatContainer" style="flex:1; padding:20px; overflow-y:auto; display:flex; flex-direction:column; gap:12px; background:#ece5dd;">
            @forelse($messages as $msg)
                @if($msg->sender == 'customer')
                    <!-- Pesan Masuk dari Pelanggan -->
                    <div style="align-self:flex-start; max-width:75%; background:white; padding:10px 14px; border-radius:12px; border-top-left-radius:0; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <div style="font-weight:bold; font-size:12px; color:#16a34a; margin-bottom:4px;">
                            👤 {{ $info['name'] }}
                        </div>
                        <div style="font-size:13.5px; color:#1e293b; white-space:pre-line; line-height:1.4;">{{ $msg->message }}</div>
                        <div style="font-size:10px; color:#94a3b8; text-align:right; margin-top:4px;">
                            {{ \Carbon\Carbon::parse($msg->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                        </div>
                    </div>
                @else
                    <!-- Pesan Keluar dari Admin -->
                    <div style="align-self:flex-end; max-width:75%; background:#dcf8c6; padding:10px 14px; border-radius:12px; border-top-right-radius:0; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                        <div style="font-weight:bold; font-size:12px; color:#1e3a8a; margin-bottom:4px;">
                            👨‍💼 Anda (Admin Kasir)
                        </div>
                        <div style="font-size:13.5px; color:#1e293b; white-space:pre-line; line-height:1.4;">{{ $msg->message }}</div>
                        <div style="font-size:10px; color:#64748b; text-align:right; margin-top:4px;">
                            {{ \Carbon\Carbon::parse($msg->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                        </div>
                    </div>
                @endif
            @empty
                <div style="text-align:center; padding:30px; color:#64748b;">
                    Belum ada riwayat pesan dalam obrolan ini.
                </div>
            @endforelse
        </div>

        <!-- Form Input Balasan Admin -->
        <div style="padding:14px; background:#f8fafc; border-top:1px solid #cbd5e1;">
            <form action="/admin/chat/{{ $sessionId }}" method="POST" style="display:flex; gap:10px; margin:0;">
                @csrf
                <input type="text" name="message" placeholder="Ketik balasan Anda ke pelanggan..." required autocomplete="off" autofocus
                       style="flex:1; padding:12px 18px; border:1px solid #cbd5e1; border-radius:25px; font-size:14px; outline:none; font-family:sans-serif;">
                <button type="submit" style="background:#166534; color:white; border:none; border-radius:25px; padding:0 24px; font-weight:bold; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                    🚀 Kirim Balasan
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    const chatContainer = document.getElementById('adminChatContainer');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    const customerName = @json($info['name']);

    function formatChatTime(dateStr) {
        let d = dateStr ? new Date(dateStr) : new Date();
        if (isNaN(d.getTime())) d = new Date();
        const hours = String(d.getHours()).padStart(2, '0');
        const mins = String(d.getMinutes()).padStart(2, '0');
        return `${hours}:${mins}`;
    }

    setInterval(() => {
        fetch('/admin/chat/{{ $sessionId }}/fetch')
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(msg => {
                    const time = formatChatTime(msg.created_at);
                    const escapedMsg = msg.message.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                    
                    if (msg.sender === 'customer') {
                        html += `
                        <div style="align-self:flex-start; max-width:75%; background:white; padding:10px 14px; border-radius:12px; border-top-left-radius:0; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                            <div style="font-weight:bold; font-size:12px; color:#16a34a; margin-bottom:4px;">👤 ${customerName}</div>
                            <div style="font-size:13.5px; color:#1e293b; line-height:1.4;">${escapedMsg}</div>
                            <div style="font-size:10px; color:#94a3b8; text-align:right; margin-top:4px;">${time} WIB</div>
                        </div>`;
                    } else {
                        html += `
                        <div style="align-self:flex-end; max-width:75%; background:#dcf8c6; padding:10px 14px; border-radius:12px; border-top-right-radius:0; box-shadow:0 2px 4px rgba(0,0,0,0.08);">
                            <div style="font-weight:bold; font-size:12px; color:#1e3a8a; margin-bottom:4px;">👨‍💼 Anda (Admin Kasir)</div>
                            <div style="font-size:13.5px; color:#1e293b; line-height:1.4;">${escapedMsg}</div>
                            <div style="font-size:10px; color:#64748b; text-align:right; margin-top:4px;">${time} WIB</div>
                        </div>`;
                    }
                });

                if (data.length > 0) {
                    const isScrolledToBottom = chatContainer.scrollHeight - chatContainer.clientHeight <= chatContainer.scrollTop + 20;
                    chatContainer.innerHTML = html;
                    if (isScrolledToBottom) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                }
            })
            .catch(err => console.error(err));
    }, 3000);
</script>
@endsection
