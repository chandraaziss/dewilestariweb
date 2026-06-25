<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dewi Lestari 2</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body{
            background:#f1f8e9;
        }

        .admin-container{
            max-width:1200px;
            margin:120px auto 50px;
            padding:20px;
        }

        .admin-card{
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,.1);
        }

        .admin-title{
            color:#2e7d32;
            margin-bottom:20px;
        }

        .btn{
            padding:10px 18px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            text-decoration:none;
            display:inline-block;
            font-weight:bold;
        }

        .btn-success{
            background:#2e7d32;
            color:white;
        }

        .btn-warning{
            background:#ff9800;
            color:white;
        }

        .btn-danger{
            background:#f44336;
            color:white;
        }

        .admin-table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        .admin-table th{
            background:#2e7d32;
            color:white;
            padding:12px;
            
        }

        .admin-table td{
            padding:12px;
            border-bottom:1px solid #ddd;
            text-align:center;
            vertical-align:middle;
        }

        .admin-table tr:hover{
            background:#f9f9f9;
        }

        .product-image{
            width:80px;
            height:80px;
            object-fit:cover;
            border-radius:10px;
        }

        .form-control{
            width:100%;
            padding:12px;
            border:1px solid #ccc;
            border-radius:8px;
            margin-top:5px;
            margin-bottom:15px;
        }

        .form-group label{
            font-weight:bold;
            color:#2e7d32;
        }

        .admin-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }
    </style>
</head>
<body>

@include('partials.header')

<div class="admin-container">
    @yield('content')
</div>

</body>
</html>