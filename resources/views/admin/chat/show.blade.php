@extends('layouts.app')

@section('content')
<div class="admin-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h1 class="admin-title" style="margin:0;">💬 Obrolan Pelanggan</h1>
        <a href="/admin/chat" class="btn-edit" style="background:#666;">← Kembali</a>
    </div>

    <div class="card" style="display:flex; flex-direction:column; height: 60vh; max-height: 600px; padding:0; overflow:hidden;">
        
        <!-- Chat Header -->
        <div style="padding: 15px; background: #f8f9fa; border-bottom: 1px solid #ddd; font-weight: bold;">
            Sesi Pelanggan: {{ substr($sessionId, 0, 8) }}...
        </div>

        <!-- Chat Messages -->
        <div id="adminChatContainer" style="flex:1; padding: 20px; overflow-y:auto; display:flex; flex-direction:column; gap:15px; background: #ece5dd;">
            @foreach($messages as $msg)
                @if($msg->sender == 'customer')
                    <!-- Pesan Masuk -->
                    <div style="align-self: flex-start; max-width: 70%; background: white; padding: 10px 15px; border-radius: 10px; border-top-left-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        <div style="font-weight:bold; font-size:12px; color:#2e7d32; margin-bottom:5px;">Pelanggan</div>
                        <div style="font-size:14px; color:#333;">{{ $msg->message }}</div>
                        <div style="font-size:10px; color:#999; text-align:right; margin-top:5px;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                @else
                    <!-- Pesan Keluar (Admin) -->
                    <div style="align-self: flex-end; max-width: 70%; background: #dcf8c6; padding: 10px 15px; border-radius: 10px; border-top-right-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        <div style="font-weight:bold; font-size:12px; color:#333; margin-bottom:5px;">Anda (Admin)</div>
                        <div style="font-size:14px; color:#333;">{{ $msg->message }}</div>
                        <div style="font-size:10px; color:#999; text-align:right; margin-top:5px;">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input -->
        <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #ddd;">
            <form action="/admin/chat/{{ $sessionId }}" method="POST" style="display:flex; gap:10px; margin:0;">
                @csrf
                <input type="text" name="message" placeholder="Ketik balasan Anda..." required autocomplete="off" autofocus
                       style="flex:1; padding:12px; border:1px solid #ccc; border-radius:25px; font-size:14px; outline:none;">
                <button type="submit" style="background:#2e7d32; color:white; border:none; border-radius:25px; padding:0 25px; font-weight:bold; cursor:pointer;">
                    Kirim
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    // Auto scroll to bottom
    const chatContainer = document.getElementById('adminChatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    // Auto refresh via AJAX every 3 seconds
    setInterval(() => {
        fetch('/admin/chat/{{ $sessionId }}/fetch')
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(msg => {
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    if(msg.sender == 'customer') {
                        html += `
                        <div style="align-self: flex-start; max-width: 70%; background: white; padding: 10px 15px; border-radius: 10px; border-top-left-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <div style="font-weight:bold; font-size:12px; color:#2e7d32; margin-bottom:5px;">Pelanggan</div>
                            <div style="font-size:14px; color:#333;">${msg.message}</div>
                            <div style="font-size:10px; color:#999; text-align:right; margin-top:5px;">${time}</div>
                        </div>`;
                    } else {
                        html += `
                        <div style="align-self: flex-end; max-width: 70%; background: #dcf8c6; padding: 10px 15px; border-radius: 10px; border-top-right-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <div style="font-weight:bold; font-size:12px; color:#333; margin-bottom:5px;">Anda (Admin)</div>
                            <div style="font-size:14px; color:#333;">${msg.message}</div>
                            <div style="font-size:10px; color:#999; text-align:right; margin-top:5px;">${time}</div>
                        </div>`;
                    }
                });
                
                // Cek apakah scroll sedang berada di paling bawah
                const isScrolledToBottom = chatContainer.scrollHeight - chatContainer.clientHeight <= chatContainer.scrollTop + 10;
                
                chatContainer.innerHTML = html;
                
                if(isScrolledToBottom) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            })
            .catch(err => console.error(err));
    }, 3000);
</script>
@endsection
