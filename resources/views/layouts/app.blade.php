<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dewi Lestari 2</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>
<body>

@include('partials.header')

@yield('content')

@include('partials.footer')

<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>