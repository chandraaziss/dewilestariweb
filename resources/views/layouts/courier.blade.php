<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurir - Dewi Lestari 2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981; /* Emerald */
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --secondary: #3b82f6; /* Blue */
            --dark: #1f2937;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-800: #1f2937;
            --white: #ffffff;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--gray-100);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 70px; /* space for bottom nav */
        }

        /* Top Header */
        .top-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 1.5rem 1rem;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }
        
        .header-user {
            font-size: 0.875rem;
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            backdrop-filter: blur(4px);
        }

        /* Main Content Area */
        .main-content {
            padding: 1.5rem 1rem;
            flex: 1;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--white);
            display: flex;
            justify-content: space-around;
            padding: 0.5rem 0;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
            z-index: 50;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--gray-500);
            font-size: 0.75rem;
            padding: 0.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 4px;
            transition: transform 0.3s ease;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-item.active i {
            transform: translateY(-2px);
        }

        .nav-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Utility Classes */
        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:active {
            transform: scale(0.98);
        }

        .btn {
            display: inline-block;
            text-align: center;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:active {
            background: var(--primary-dark);
            transform: translateY(2px);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: var(--white);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #d97706;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #059669;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
    </style>
    @stack('styles')
</head>
<body>

    <header class="top-header">
        <div class="header-title">Dewi Lestari <span style="font-weight: 300;">Kurir</span></div>
        <div class="header-user">
            <i class="fas fa-user-circle mr-1"></i> Admin
        </div>
    </header>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </main>

    <nav class="bottom-nav">
        <a href="{{ route('courier.dashboard') }}" class="nav-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('courier.deliveries.index') }}" class="nav-item {{ request()->routeIs('courier.deliveries.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i>
            <span>Tugas</span>
        </a>
        <a href="{{ route('courier.history') }}" class="nav-item {{ request()->routeIs('courier.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="/admin/orders" class="nav-item">
            <i class="fas fa-arrow-left"></i>
            <span>Admin</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
