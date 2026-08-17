<header class="{{ ((request()->is('admin/*') || request()->is('courier/*')) && !request()->is('admin/login')) ? 'admin-sidebar' : '' }}">
    <nav class="container">
        <div class="logo">
            <div class="logo-icon">DL</div>
            Toko Dewi Lestari 2
        </div>

        @if((request()->is('admin/*') || request()->is('courier/*')) && !request()->is('admin/login'))

            <ul class="nav-links">



                <li>
                    <a href="/admin/supplier-stocks">
                        📥 Manajemen Stok
                    </a>
                </li>

                <li>
                    <a href="/admin/orders">
                        🧾 Pesanan
                    </a>
                </li>

                <li>
                    <a href="/courier/dashboard">
                        🛵 Panel Kurir
                    </a>
                </li>

                <li>
                    <a href="/admin/chat">
                        💬 Chat <span id="adminChatBadge"
                            style="background:#dc3545; color:white; border-radius:50%; padding:2px 6px; font-size:12px; display:none;">0</span>
                    </a>
                </li>

                <li>
                    <a href="/admin/reports">
                        📊 Laporan
                    </a>
                </li>

                <li>
                    <a href="/admin/suppliers">
                        🏭 Supplier Market
                    </a>
                </li>

                <li>
                    <a href="/admin/suppliers/manage">
                        🏷️ Tambah Supplier
                    </a>
                </li>

                <li>
                    <a href="/admin/customers">
                        👥 Pelanggan
                    </a>
                </li>

                <li>
                    <a href="/admin/ratings">
                        ⭐ Rating
                    </a>
                </li>

                <li>
                    <a href="/">
                        🏠 Kembali ke Toko
                    </a>
                </li>

            </ul>

        @else

            <ul class="nav-links">

                <li>
                    <a href="/">
                        🏠 Home
                    </a>
                </li>

                <li>
                    <a href="/#products">
                        📦 Produk
                    </a>
                </li>

                @auth
                    <li>
                        <a href="/customer/dashboard" style="font-weight: bold; color: #fff;">
                            👤 Akun Saya
                        </a>
                    </li>
                    <li>
                        <a href="/logout">
                            🚪 Keluar
                        </a>
                    </li>
                @else
                    <li>
                        <a href="/login">
                            🔑 Masuk
                        </a>
                    </li>
                    <li>
                        <a href="/register">
                            📝 Daftar
                        </a>
                    </li>
                @endauth

                <li>
                    <a href="/admin/login">
                        🔒 Admin
                    </a>
                </li>

                <li style="margin-left: 10px;">
                    <a href="/track"
                        style="background:#ffeb3b; color:#2e7d32; padding:8px 15px; border-radius:18px; font-weight:bold;">
                        🔍 Lacak Pesanan
                    </a>
                </li>

            </ul>

        @endif
    </nav>
</header>