<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dewi Lestari 2</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>
<body class="{{ (request()->is('admin/*') && !request()->is('admin/login')) ? 'admin-body' : '' }}">

@include('partials.header')

@yield('content')

@include('partials.footer')
@if(!request()->is('admin/*') && !request()->is('login') && !request()->is('register'))
<div id="customerChatWidget" style="position:fixed; bottom:20px; right:20px; z-index:9999; font-family:sans-serif;">
    <div id="chatIcon" onclick="toggleChat()" style="background:#2e7d32; color:white; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.3); font-size:24px; position:relative;">
        💬
        <span id="customerUnreadBadge" style="position:absolute; top:-2px; right:-2px; background:#dc3545; color:white; font-size:12px; padding:2px 6px; border-radius:50%; display:none;">0</span>
    </div>

    <div id="chatBox" style="display:none; width:300px; height:400px; background:white; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2); flex-direction:column; position:absolute; bottom:80px; right:0; overflow:hidden;">
        <div style="background:#2e7d32; color:white; padding:15px; font-weight:bold; display:flex; justify-content:space-between; align-items:center;">
            <span>Tanya Admin</span>
            <span onclick="toggleChat()" style="cursor:pointer; padding:5px;">✖</span>
        </div>
        <div id="chatMessages" style="flex:1; padding:15px; overflow-y:auto; background:#ece5dd; display:flex; flex-direction:column; gap:10px;">
        </div>
        <form onsubmit="sendChatMessage(event)" style="display:flex; border-top:1px solid #ddd; background:white; margin:0;">
            <input type="text" id="chatInput" placeholder="Ketik pesan..." required autocomplete="off" style="flex:1; border:none; padding:12px; outline:none; font-size:14px;">
            <button type="submit" style="background:#2e7d32; color:white; border:none; padding:0 15px; cursor:pointer; font-weight:bold;">Kirim</button>
        </form>
    </div>
</div>
@endif

<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>