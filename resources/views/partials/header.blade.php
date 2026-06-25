<header>
    <nav class="container">
        <div class="logo">
            <div class="logo-icon">DL</div>
            Toko Dewi Lestari 2
        </div>

        @if(request()->is('admin/*'))

<ul class="nav-links">

    <li>
        <a href="/admin/products">
            📦 Produk
        </a>
    </li>

    <li>
        <a href="/admin/orders">
            🧾 Pesanan
        </a>
    </li>

    <li>
        <a href="/admin/reports">
            📊 Laporan
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
        <a href="#products">
            📦 Produk
        </a>
    </li>

    <li>
        <a href="#contact">
            📞 Kontak
        </a>
    </li>

    <li>
        <a href="/admin/login">
            🔒 Admin
        </a>
    </li>

</ul>

@endif
    </nav>
</header>