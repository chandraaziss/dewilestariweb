const fs = require('fs');
const path = require('path');

const interfaces = [
  {
    id: 1,
    num: "4. 43",
    title: "1. Antarmuka Login",
    name: "Login",
    caption: "Gambar 4. 43 Implementasi Antarmuka Login",
    intro: "Berikut merupakan implementasi antarmuka login yang digunakan pengguna untuk mengakses website.",
    svg: `<svg viewBox="0 0 500 340" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="340" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <!-- Window Bar -->
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/login</text>
      <!-- Login Card -->
      <rect x="110" y="45" width="280" height="275" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" filter="drop-shadow(0 4px 6px rgba(0,0,0,0.05))"/>
      <!-- Card Header -->
      <path d="M 110 53 A 8 8 0 0 1 118 45 L 382 45 A 8 8 0 0 1 390 53 L 390 85 L 110 85 Z" fill="#1b5e20"/>
      <text x="250" y="70" font-family="Arial, sans-serif" font-weight="bold" font-size="14" fill="#ffffff" text-anchor="middle">Selamat Datang</text>
      <!-- Form Body -->
      <text x="130" y="108" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#475569">ALAMAT EMAIL</text>
      <rect x="130" y="115" width="240" height="32" rx="4" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1"/>
      <text x="140" y="135" font-family="Arial, sans-serif" font-size="10" fill="#94a3b8">Masukkan akun email yang terdaftar</text>
      
      <text x="130" y="168" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#475569">KATA SANDI (PASSWORD)</text>
      <rect x="130" y="175" width="240" height="32" rx="4" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1"/>
      <text x="140" y="195" font-family="Arial, sans-serif" font-size="12" fill="#334155">• • • • • • • • • •</text>

      <rect x="130" y="222" width="240" height="36" rx="6" fill="#1b5e20"/>
      <text x="250" y="244" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#ffffff" text-anchor="middle">Masuk Sekarang</text>

      <text x="250" y="285" font-family="Arial, sans-serif" font-size="9" fill="#64748b" text-anchor="middle">Belum punya akun? <tspan fill="#1b5e20" font-weight="bold">Daftar di sini</tspan></text>
    </svg>`
  },
  {
    id: 2,
    num: "4. 44",
    title: "2. Antarmuka Registrasi",
    name: "Registrasi",
    caption: "Gambar 4. 44 Implementasi Antarmuka Registrasi",
    intro: "Berikut merupakan implementasi antarmuka registrasi yang digunakan calon pembeli untuk mendaftarkan akun baru pada website.",
    svg: `<svg viewBox="0 0 500 370" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="370" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/register</text>
      
      <rect x="90" y="42" width="320" height="315" rx="8" fill="#ffffff" stroke="#e2e8f0" stroke-width="1"/>
      <path d="M 90 50 A 8 8 0 0 1 98 42 L 402 42 A 8 8 0 0 1 410 50 L 410 78 L 90 78 Z" fill="#1b5e20"/>
      <text x="250" y="63" font-family="Arial, sans-serif" font-weight="bold" font-size="13" fill="#ffffff" text-anchor="middle">Pendaftaran Akun Baru</text>
      
      <text x="110" y="96" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#475569">NAMA LENGKAP</text>
      <rect x="110" y="101" width="280" height="26" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="120" y="118" font-family="Arial, sans-serif" font-size="9" fill="#94a3b8">Masukkan nama lengkap sesuai KTP</text>

      <text x="110" y="141" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#475569">ALAMAT EMAIL</text>
      <rect x="110" y="146" width="280" height="26" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="120" y="163" font-family="Arial, sans-serif" font-size="9" fill="#94a3b8">contoh: pelanggan@gmail.com</text>

      <text x="110" y="186" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#475569">NOMOR WHATSAPP</text>
      <rect x="110" y="191" width="280" height="26" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="120" y="208" font-family="Arial, sans-serif" font-size="9" fill="#94a3b8">081234567890</text>

      <text x="110" y="231" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#475569">KATA SANDI</text>
      <rect x="110" y="236" width="280" height="26" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="120" y="253" font-family="Arial, sans-serif" font-size="11" fill="#334155">• • • • • • • •</text>

      <rect x="110" y="274" width="280" height="32" rx="5" fill="#1b5e20"/>
      <text x="250" y="294" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#ffffff" text-anchor="middle">Daftar Akun Baru</text>

      <text x="250" y="328" font-family="Arial, sans-serif" font-size="9" fill="#64748b" text-anchor="middle">Sudah punya akun? <tspan fill="#1b5e20" font-weight="bold">Masuk di sini</tspan></text>
    </svg>`
  },
  {
    id: 3,
    num: "4. 45",
    title: "3. Antarmuka Beranda dan Katalog Produk",
    name: "Beranda dan Katalog Produk",
    caption: "Gambar 4. 45 Implementasi Antarmuka Beranda dan Katalog Produk",
    intro: "Berikut merupakan implementasi antarmuka beranda dan katalog produk yang digunakan pengguna untuk melihat daftar barang oleh-oleh, varian kemasan, dan harga.",
    svg: `<svg viewBox="0 0 500 360" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="360" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com</text>
      
      <!-- Navbar -->
      <rect x="0" y="28" width="500" height="40" fill="#1b5e20"/>
      <text x="20" y="53" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#ffffff">TOKO DEWI LESTARI 2</text>
      <rect x="220" y="36" width="180" height="24" rx="12" fill="#ffffff" opacity="0.9"/>
      <text x="235" y="52" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Cari kripik, bakpia...</text>
      <circle cx="430" cy="48" r="12" fill="#2e7d32"/>
      <text x="430" y="52" font-family="Arial, sans-serif" font-size="10" fill="#ffffff" text-anchor="middle">🛒 2</text>
      
      <!-- Banner -->
      <rect x="15" y="76" width="470" height="60" rx="6" fill="#e8f5e9" stroke="#c8e6c9"/>
      <text x="30" y="98" font-family="Arial, sans-serif" font-weight="bold" font-size="13" fill="#1b5e20">Oleh-Oleh Khas Asli &amp; Segar!</text>
      <text x="30" y="115" font-family="Arial, sans-serif" font-size="10" fill="#388e3c">Diskon Pengadaan &amp; Jaminan Produk Bebas Expired</text>
      
      <!-- Product Cards -->
      <!-- Card 1 -->
      <rect x="15" y="148" width="145" height="195" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="23" y="156" width="129" height="80" rx="4" fill="#e2e8f0"/>
      <text x="87" y="200" font-family="Arial, sans-serif" font-size="20" text-anchor="middle">🍪</text>
      <text x="23" y="250" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Keripik Tempe Super</text>
      <rect x="23" y="255" width="55" height="14" rx="3" fill="#dcfce7"/>
      <text x="50" y="265" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#166534" text-anchor="middle">Stok: 45 Pcs</text>
      <text x="23" y="285" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">Rp 18.000</text>
      <rect x="23" y="296" width="129" height="22" rx="4" fill="#1b5e20"/>
      <text x="87" y="311" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#ffffff" text-anchor="middle">+ Keranjang</text>

      <!-- Card 2 -->
      <rect x="177" y="148" width="145" height="195" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="185" y="156" width="129" height="80" rx="4" fill="#e2e8f0"/>
      <text x="249" y="200" font-family="Arial, sans-serif" font-size="20" text-anchor="middle">🥮</text>
      <text x="185" y="250" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Bakpia Pathok Original</text>
      <rect x="185" y="255" width="55" height="14" rx="3" fill="#dcfce7"/>
      <text x="212" y="265" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#166534" text-anchor="middle">Stok: 30 Box</text>
      <text x="185" y="285" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">Rp 35.000</text>
      <rect x="185" y="296" width="129" height="22" rx="4" fill="#1b5e20"/>
      <text x="249" y="311" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#ffffff" text-anchor="middle">+ Keranjang</text>

      <!-- Card 3 -->
      <rect x="340" y="148" width="145" height="195" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="348" y="156" width="129" height="80" rx="4" fill="#e2e8f0"/>
      <text x="412" y="200" font-family="Arial, sans-serif" font-size="20" text-anchor="middle">🍬</text>
      <text x="348" y="250" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Dodol Garut Spesial</text>
      <rect x="348" y="255" width="55" height="14" rx="3" fill="#fef3c7"/>
      <text x="375" y="265" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#92400e" text-anchor="middle">Stok: 12 Pcs</text>
      <text x="348" y="285" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">Rp 22.000</text>
      <rect x="348" y="296" width="129" height="22" rx="4" fill="#1b5e20"/>
      <text x="412" y="311" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#ffffff" text-anchor="middle">+ Keranjang</text>
    </svg>`
  },
  {
    id: 4,
    num: "4. 46",
    title: "4. Antarmuka Detail Produk dan Keranjang Belanja",
    name: "Detail Produk dan Keranjang Belanja",
    caption: "Gambar 4. 46 Implementasi Antarmuka Detail Produk dan Keranjang Belanja",
    intro: "Berikut merupakan implementasi antarmuka detail produk dan keranjang belanja yang digunakan pengguna untuk meninjau item pilihan sebelum melakukan checkout.",
    svg: `<svg viewBox="0 0 500 360" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="360" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/cart</text>

      <!-- Background Page Dimmed -->
      <rect x="15" y="40" width="220" height="300" rx="6" fill="#ffffff" stroke="#cbd5e1" opacity="0.6"/>
      <text x="30" y="70" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#334155">Keripik Tempe Super</text>
      <text x="30" y="90" font-family="Arial, sans-serif" font-size="10" fill="#64748b">Varian: Kemasan 250 gram</text>
      <text x="30" y="110" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Rp 18.000</text>
      
      <!-- Drawer Cart Slider -->
      <rect x="220" y="28" width="280" height="332" fill="#ffffff" stroke="#cbd5e1" filter="drop-shadow(-4px 0 8px rgba(0,0,0,0.1))"/>
      <rect x="220" y="28" width="280" height="40" fill="#1b5e20"/>
      <text x="235" y="53" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#ffffff">Keranjang Belanja (2 Item)</text>
      <text x="480" y="53" font-family="Arial, sans-serif" font-size="14" fill="#ffffff" text-anchor="end">✕</text>

      <!-- Item 1 -->
      <rect x="235" y="80" width="250" height="65" rx="6" fill="#f8fafc" stroke="#e2e8f0"/>
      <rect x="245" y="90" width="45" height="45" rx="4" fill="#e2e8f0"/>
      <text x="267" y="117" font-family="Arial, sans-serif" font-size="16" text-anchor="middle">🍪</text>
      <text x="300" y="98" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Keripik Tempe (250g)</text>
      <text x="300" y="113" font-family="Arial, sans-serif" font-size="10" fill="#1b5e20" font-weight="bold">Rp 18.000 x 2</text>
      <text x="470" y="125" font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#334155">Rp 36.000</text>

      <!-- Item 2 -->
      <rect x="235" y="155" width="250" height="65" rx="6" fill="#f8fafc" stroke="#e2e8f0"/>
      <rect x="245" y="165" width="45" height="45" rx="4" fill="#e2e8f0"/>
      <text x="267" y="192" font-family="Arial, sans-serif" font-size="16" text-anchor="middle">🥮</text>
      <text x="300" y="173" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Bakpia Pathok (1 Box)</text>
      <text x="300" y="188" font-family="Arial, sans-serif" font-size="10" fill="#1b5e20" font-weight="bold">Rp 35.000 x 1</text>
      <text x="470" y="200" font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#334155">Rp 35.000</text>

      <!-- Summary Footer -->
      <line x1="235" y1="260" x2="485" y2="260" stroke="#e2e8f0" stroke-width="1"/>
      <text x="235" y="280" font-family="Arial, sans-serif" font-size="10" fill="#64748b">Subtotal Belanja:</text>
      <text x="485" y="280" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="#1b5e20" text-anchor="end">Rp 71.000</text>

      <rect x="235" y="295" width="250" height="36" rx="6" fill="#1b5e20"/>
      <text x="360" y="317" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#ffffff" text-anchor="middle">Lanjut ke Checkout ➔</text>
    </svg>`
  },
  {
    id: 5,
    num: "4. 47",
    title: "5. Antarmuka Checkout Pesanan dan Pembayaran Midtrans",
    name: "Checkout Pesanan dan Pembayaran Midtrans",
    caption: "Gambar 4. 47 Implementasi Antarmuka Checkout dan Pembayaran Midtrans",
    intro: "Berikut merupakan implementasi antarmuka checkout pesanan dan pembayaran Midtrans yang digunakan pengguna untuk memilih alamat, menghitung ongkos kirim RajaOngkir, serta melakukan pembayaran.",
    svg: `<svg viewBox="0 0 500 370" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="370" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/checkout</text>

      <!-- Checkout Left Column -->
      <rect x="15" y="38" width="240" height="315" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="25" y="58" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">1. Alamat Pengiriman</text>
      <rect x="25" y="65" width="220" height="45" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="35" y="80" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#334155">Dewi Lestari (081299998888)</text>
      <text x="35" y="95" font-family="Arial, sans-serif" font-size="8" fill="#64748b">Jl. Malioboro No. 45, Yogyakarta</text>

      <text x="25" y="130" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">2. Kurir Ekspedisi (RajaOngkir)</text>
      <rect x="25" y="138" width="220" height="30" rx="4" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="157" font-family="Arial, sans-serif" font-size="9" fill="#334155">JNE - REG (1-2 Hari) - Rp 15.000</text>

      <text x="25" y="190" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#1b5e20">3. Ringkasan Bayar</text>
      <text x="25" y="210" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Subtotal Barang: Rp 71.000</text>
      <text x="25" y="225" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Ongkos Kirim: Rp 15.000</text>
      <line x1="25" y1="235" x2="245" y2="235" stroke="#e2e8f0"/>
      <text x="25" y="252" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#334155">Total Tagihan:</text>
      <text x="245" y="252" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20" text-anchor="end">Rp 86.000</text>

      <rect x="25" y="270" width="220" height="34" rx="6" fill="#1b5e20"/>
      <text x="135" y="291" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">Bayar via Midtrans 🔒</text>

      <!-- Midtrans Pop-up Modal -->
      <rect x="270" y="45" width="215" height="300" rx="8" fill="#ffffff" stroke="#002855" stroke-width="1.5" filter="drop-shadow(0 4px 12px rgba(0,0,0,0.15))"/>
      <rect x="270" y="45" width="215" height="35" fill="#002855"/>
      <text x="377" y="67" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#ffffff" text-anchor="middle">midtrans Snap Payment</text>

      <text x="282" y="100" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Pilih Metode Pembayaran:</text>
      
      <!-- Option 1: QRIS -->
      <rect x="282" y="110" width="191" height="32" rx="4" fill="#f0fdf4" stroke="#22c55e"/>
      <text x="295" y="130" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#15803d">QRIS (GoPay, ShopeePay)</text>

      <!-- Option 2: VA -->
      <rect x="282" y="150" width="191" height="32" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="295" y="170" font-family="Arial, sans-serif" font-size="9" fill="#334155">BCA Virtual Account</text>

      <!-- QR Preview Box -->
      <rect x="330" y="195" width="95" height="95" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="340" y="205" width="75" height="75" fill="#1e293b"/>
      <rect x="352" y="217" width="51" height="51" fill="#ffffff"/>
      <rect x="362" y="227" width="31" height="31" fill="#1e293b"/>
      
      <text x="377" y="306" font-family="Arial, sans-serif" font-size="8" fill="#64748b" text-anchor="middle">Scan QR di atas untuk bayar</text>
      <text x="377" y="325" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#dc2626" text-anchor="middle">Sisa Waktu: 23:59:12</text>
    </svg>`
  },
  {
    id: 6,
    num: "4. 48",
    title: "6. Antarmuka Pelacakan Pesanan (Order Tracking)",
    name: "Pelacakan Pesanan",
    caption: "Gambar 4. 48 Implementasi Antarmuka Pelacakan Pesanan",
    intro: "Berikut merupakan implementasi antarmuka pelacakan pesanan yang digunakan pengguna untuk memantau status pengiriman barang berdasarkan nomor tiket resi.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/track</text>

      <rect x="30" y="42" width="440" height="290" rx="8" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="50" y="68" font-family="Arial, sans-serif" font-weight="bold" font-size="13" fill="#1b5e20">Lacak Status Pengiriman Resi</text>

      <rect x="50" y="80" width="280" height="32" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="65" y="100" font-family="Arial, sans-serif" font-size="10" fill="#334155">TKT-2026-081299</text>
      <rect x="340" y="80" width="110" height="32" rx="4" fill="#1b5e20"/>
      <text x="395" y="100" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#ffffff" text-anchor="middle">🔍 Cek Resi</text>

      <!-- Timeline Tracker -->
      <line x1="90" y1="160" x2="410" y2="160" stroke="#cbd5e1" stroke-width="3"/>
      <line x1="90" y1="160" x2="300" y2="160" stroke="#1b5e20" stroke-width="3"/>

      <!-- Step 1 -->
      <circle cx="90" cy="160" r="10" fill="#1b5e20"/>
      <text x="90" y="164" font-family="Arial, sans-serif" font-size="9" fill="#ffffff" text-anchor="middle">✓</text>
      <text x="90" y="185" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#1b5e20" text-anchor="middle">Order Dibayar</text>
      <text x="90" y="196" font-family="Arial, sans-serif" font-size="7" fill="#64748b" text-anchor="middle">10 Aug 09:15</text>

      <!-- Step 2 -->
      <circle cx="195" cy="160" r="10" fill="#1b5e20"/>
      <text x="195" y="164" font-family="Arial, sans-serif" font-size="9" fill="#ffffff" text-anchor="middle">✓</text>
      <text x="195" y="185" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#1b5e20" text-anchor="middle">Dikemas Admin</text>
      <text x="195" y="196" font-family="Arial, sans-serif" font-size="7" fill="#64748b" text-anchor="middle">10 Aug 10:30</text>

      <!-- Step 3 -->
      <circle cx="300" cy="160" r="12" fill="#2563eb" stroke="#93c5fd" stroke-width="3"/>
      <text x="300" y="164" font-family="Arial, sans-serif" font-size="9" fill="#ffffff" text-anchor="middle">🚚</text>
      <text x="300" y="185" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#2563eb" text-anchor="middle">Dalam Pengiriman</text>
      <text x="300" y="196" font-family="Arial, sans-serif" font-size="7" fill="#64748b" text-anchor="middle">10 Aug 11:00</text>

      <!-- Step 4 -->
      <circle cx="410" cy="160" r="10" fill="#cbd5e1"/>
      <text x="410" y="185" font-family="Arial, sans-serif" font-size="9" fill="#64748b" text-anchor="middle">Diterima</text>

      <!-- Details Box -->
      <rect x="50" y="220" width="400" height="90" rx="6" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="65" y="240" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#334155">Kurir Ekspedisi: JNE REG | No. Resi: JNE8899120033</text>
      <text x="65" y="258" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Posisi Terakhir: Paket sedang transit di Hub Gudang Transit Sleman</text>
      <text x="65" y="276" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Estimasi Tiba: 11 Agustus 2026</text>
      <rect x="65" y="285" width="110" height="18" rx="3" fill="#dbeafe"/>
      <text x="120" y="297" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e40af" text-anchor="middle">Status: On Process</text>
    </svg>`
  },
  {
    id: 7,
    num: "4. 49",
    title: "7. Antarmuka Dashboard Pelanggan dan Kelola Alamat",
    name: "Dashboard Pelanggan dan Kelola Alamat",
    caption: "Gambar 4. 49 Implementasi Antarmuka Dashboard Pelanggan",
    intro: "Berikut merupakan implementasi antarmuka dashboard pelanggan yang digunakan pengguna untuk mengelola profil pribadi, daftar alamat pengiriman, dan melihat riwayat transaksi.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/customer/dashboard</text>

      <!-- Dashboard Sidebar -->
      <rect x="15" y="38" width="130" height="300" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <circle cx="80" cy="75" r="20" fill="#1b5e20"/>
      <text x="80" y="80" font-family="Arial, sans-serif" font-size="14" fill="#ffffff" text-anchor="middle">👤</text>
      <text x="80" y="110" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#1e293b" text-anchor="middle">Dewi Lestari</text>
      
      <rect x="25" y="130" width="110" height="25" rx="4" fill="#e8f5e9"/>
      <text x="35" y="146" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#1b5e20">👤 Profil Saya</text>
      
      <rect x="25" y="162" width="110" height="25" rx="4" fill="#ffffff"/>
      <text x="35" y="178" font-family="Arial, sans-serif" font-size="9" fill="#64748b">📍 Kelola Alamat</text>
      
      <rect x="25" y="194" width="110" height="25" rx="4" fill="#ffffff"/>
      <text x="35" y="210" font-family="Arial, sans-serif" font-size="9" fill="#64748b">📦 Riwayat Order</text>

      <!-- Main Content Area -->
      <rect x="155" y="38" width="330" height="300" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="175" y="65" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Daftar Alamat Pengiriman</text>
      <rect x="375" y="48" width="95" height="24" rx="4" fill="#1b5e20"/>
      <text x="422" y="64" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#ffffff" text-anchor="middle">+ Tambah Alamat</text>

      <!-- Address Card 1 (Main) -->
      <rect x="175" y="82" width="295" height="90" rx="6" fill="#f0fdf4" stroke="#22c55e"/>
      <rect x="185" y="92" width="70" height="16" rx="3" fill="#22c55e"/>
      <text x="220" y="103" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#ffffff" text-anchor="middle">ALAMAT UTAMA</text>
      <text x="185" y="123" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#1e293b">Rumah Utama (Dewi Lestari - 081299998888)</text>
      <text x="185" y="140" font-family="Arial, sans-serif" font-size="9" fill="#475569">Jl. Malioboro No. 45, Danurejan, Kota Yogyakarta</text>
      <text x="185" y="158" font-family="Arial, sans-serif" font-size="8" fill="#15803d" font-weight="bold">Edit Alamat | Hapus</text>

      <!-- Address Card 2 -->
      <rect x="175" y="185" width="295" height="85" rx="6" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="185" y="208" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#1e293b">Alamat Kantor (081299998888)</text>
      <text x="185" y="225" font-family="Arial, sans-serif" font-size="9" fill="#475569">Gedung Grha Sabha Pramana, Depok, Sleman</text>
      <text x="185" y="250" font-family="Arial, sans-serif" font-size="8" fill="#1b5e20" font-weight="bold">Jadikan Utama | Edit | Hapus</text>
    </svg>`
  },
  {
    id: 8,
    num: "4. 50",
    title: "8. Antarmuka Pemberian Ulasan dan Rating Produk",
    name: "Pemberian Ulasan dan Rating Produk",
    caption: "Gambar 4. 50 Implementasi Antarmuka Pemberian Ulasan dan Rating Produk",
    intro: "Berikut merupakan implementasi antarmuka pemberian ulasan dan rating yang digunakan pembeli untuk memberikan nilai bintang dan ulasan komentar produk.",
    svg: `<svg viewBox="0 0 500 340" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="340" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/customer/rate</text>

      <rect x="70" y="45" width="360" height="275" rx="8" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="90" y="72" font-family="Arial, sans-serif" font-weight="bold" font-size="13" fill="#1b5e20">Beri Ulasan Produk Pesanan</text>
      <text x="90" y="88" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Kode Pesanan: #ORD-2026-0809</text>

      <!-- Product Box -->
      <rect x="90" y="100" width="320" height="45" rx="4" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="105" y="126" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#334155">🍪 Keripik Tempe Super (250g)</text>

      <!-- Star Rating Picker -->
      <text x="90" y="165" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#475569">Pilih Rating Bintang:</text>
      <g transform="translate(90, 175)">
        <text font-family="Arial, sans-serif" font-size="22" fill="#eab308">★ ★ ★ ★ ★</text>
      </g>
      <text x="235" y="192" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#15803d">Sangat Memuaskan (5/5)</text>

      <!-- Review Comment Box -->
      <text x="90" y="218" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#475569">KOMENTAR ULASAN</text>
      <rect x="90" y="225" width="320" height="50" rx="4" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="100" y="243" font-family="Arial, sans-serif" font-size="9" fill="#334155">Keripiknya sangat renyah, gurih, dan tanggal kedaluwarsa masih panjang.</text>
      <text x="100" y="258" font-family="Arial, sans-serif" font-size="9" fill="#334155">Pengiriman JNE juga cepat!</text>

      <rect x="90" y="285" width="320" height="28" rx="5" fill="#1b5e20"/>
      <text x="250" y="303" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">Kirim Ulasan Produk</text>
    </svg>`
  },
  {
    id: 9,
    num: "4. 51",
    title: "9. Antarmuka Live Chat Support Pelanggan",
    name: "Live Chat Support Pelanggan",
    caption: "Gambar 4. 51 Implementasi Antarmuka Live Chat Support Pelanggan",
    intro: "Berikut merupakan implementasi antarmuka live chat support yang digunakan pelanggan untuk berkonsultasi langsung dengan admin toko.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com</text>

      <!-- Floating Chat Widget -->
      <rect x="250" y="45" width="220" height="285" rx="8" fill="#ffffff" stroke="#cbd5e1" filter="drop-shadow(0 4px 10px rgba(0,0,0,0.12))"/>
      <rect x="250" y="45" width="220" height="40" rx="8" fill="#1b5e20"/>
      <circle cx="270" cy="65" r="10" fill="#ffffff"/>
      <text x="270" y="69" font-family="Arial, sans-serif" font-size="10" fill="#1b5e20" text-anchor="middle">🎧</text>
      <text x="290" y="63" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff">Live Chat Admin Toko</text>
      <text x="290" y="75" font-family="Arial, sans-serif" font-size="7" fill="#a7f3d0">● Online (Respon Cepat)</text>

      <!-- Chat Bubbles -->
      <!-- Admin Bubble (Left) -->
      <rect x="260" y="98" width="160" height="35" rx="6" fill="#f1f5f9"/>
      <text x="270" y="112" font-family="Arial, sans-serif" font-size="8" fill="#334155">Halo! Ada yang bisa kami bantu mengenai produk Dewi Lestari?</text>
      <text x="405" y="128" font-family="Arial, sans-serif" font-size="7" fill="#94a3b8">10:40</text>

      <!-- Customer Bubble (Right) -->
      <rect x="295" y="142" width="165" height="35" rx="6" fill="#dcfce7"/>
      <text x="305" y="156" font-family="Arial, sans-serif" font-size="8" fill="#14532d">Apakah Bakpia rasa keju ready stok hari ini kak?</text>
      <text x="445" y="172" font-family="Arial, sans-serif" font-size="7" fill="#166534">10:41</text>

      <!-- Admin Bubble 2 -->
      <rect x="260" y="186" width="170" height="35" rx="6" fill="#f1f5f9"/>
      <text x="270" y="200" font-family="Arial, sans-serif" font-size="8" fill="#334155">Ready kak! Expired date batch baru sampai akhir bulan depan.</text>
      <text x="415" y="216" font-family="Arial, sans-serif" font-size="7" fill="#94a3b8">10:42</text>

      <!-- Chat Input Box -->
      <rect x="260" y="282" width="160" height="32" rx="16" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="275" y="302" font-family="Arial, sans-serif" font-size="8" fill="#94a3b8">Ketik pesan di sini...</text>
      <circle cx="445" cy="298" r="14" fill="#1b5e20"/>
      <text x="445" y="302" font-family="Arial, sans-serif" font-size="10" fill="#ffffff" text-anchor="middle">➤</text>
    </svg>`
  },
  {
    id: 10,
    num: "4. 52",
    title: "10. Antarmuka Login Admin",
    name: "Login Admin",
    caption: "Gambar 4. 52 Implementasi Antarmuka Login Admin",
    intro: "Berikut merupakan implementasi antarmuka login admin yang digunakan pengelola toko untuk memverifikasi hak akses ke sistem backend admin.",
    svg: `<svg viewBox="0 0 500 340" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="340" rx="10" fill="#1e293b" stroke="#0f172a" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#0f172a"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#94a3b8" text-anchor="middle">dewilestari.com/admin/login</text>

      <rect x="110" y="48" width="280" height="260" rx="8" fill="#0f172a" stroke="#334155" filter="drop-shadow(0 4px 10px rgba(0,0,0,0.5))"/>
      <rect x="110" y="48" width="280" height="45" rx="8" fill="#1b5e20"/>
      <text x="250" y="75" font-family="Arial, sans-serif" font-weight="bold" font-size="13" fill="#ffffff" text-anchor="middle">PORTAL LOGIN ADMIN</text>

      <text x="130" y="118" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#94a3b8">USERNAME / EMAIL ADMIN</text>
      <rect x="130" y="125" width="240" height="30" rx="4" fill="#1e293b" stroke="#475569"/>
      <text x="140" y="144" font-family="Arial, sans-serif" font-size="9" fill="#e2e8f0">admin@dewilestari.com</text>

      <text x="130" y="175" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#94a3b8">PASSWORD ADMIN</text>
      <rect x="130" y="182" width="240" height="30" rx="4" fill="#1e293b" stroke="#475569"/>
      <text x="140" y="201" font-family="Arial, sans-serif" font-size="11" fill="#e2e8f0">• • • • • • • • • •</text>

      <rect x="130" y="232" width="240" height="36" rx="6" fill="#1b5e20"/>
      <text x="250" y="254" font-family="Arial, sans-serif" font-weight="bold" font-size="11" fill="#ffffff" text-anchor="middle">Masuk Sistem Backend</text>
    </svg>`
  },
  {
    id: 11,
    num: "4. 53",
    title: "11. Antarmuka Master Data Supplier",
    name: "Master Data Supplier",
    caption: "Gambar 4. 53 Implementasi Antarmuka Master Data Supplier",
    intro: "Berikut merupakan implementasi antarmuka master data supplier yang digunakan admin untuk mengelola daftar mitra supplier pemasok barang.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/suppliers</text>

      <rect x="20" y="42" width="460" height="290" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="68" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Kelola Master Data Supplier Pemasok</text>
      <rect x="360" y="50" width="105" height="26" rx="4" fill="#1b5e20"/>
      <text x="412" y="67" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#ffffff" text-anchor="middle">+ Tambah Supplier</text>

      <!-- Table Header -->
      <rect x="35" y="88" width="430" height="25" fill="#e8f5e9"/>
      <text x="45" y="104" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">KODE</text>
      <text x="95" y="104" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">NAMA SUPPLIER</text>
      <text x="230" y="104" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">KONTAK WHATSAPP</text>
      <text x="345" y="104" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">KOTA GUDANG</text>
      <text x="430" y="104" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">AKSI</text>

      <!-- Row 1 -->
      <text x="45" y="130" font-family="Arial, sans-serif" font-size="8" fill="#334155">SUP-001</text>
      <text x="95" y="130" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">PT Keripik Nusantara</text>
      <text x="230" y="130" font-family="Arial, sans-serif" font-size="8" fill="#334155">081234567890</text>
      <text x="345" y="130" font-family="Arial, sans-serif" font-size="8" fill="#334155">Bantul</text>
      <text x="430" y="130" font-family="Arial, sans-serif" font-size="8" fill="#2563eb" font-weight="bold">Edit | PO</text>
      <line x1="35" y1="140" x2="465" y2="140" stroke="#f1f5f9"/>

      <!-- Row 2 -->
      <text x="45" y="160" font-family="Arial, sans-serif" font-size="8" fill="#334155">SUP-002</text>
      <text x="95" y="160" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">CV Bakpia Pathok 75</text>
      <text x="230" y="160" font-family="Arial, sans-serif" font-size="8" fill="#334155">081987654321</text>
      <text x="345" y="160" font-family="Arial, sans-serif" font-size="8" fill="#334155">Yogyakarta</text>
      <text x="430" y="160" font-family="Arial, sans-serif" font-size="8" fill="#2563eb" font-weight="bold">Edit | PO</text>
      <line x1="35" y1="170" x2="465" y2="170" stroke="#f1f5f9"/>

      <!-- Row 3 -->
      <text x="45" y="190" font-family="Arial, sans-serif" font-size="8" fill="#334155">SUP-003</text>
      <text x="95" y="190" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">UD Dodol Garut Asli</text>
      <text x="230" y="190" font-family="Arial, sans-serif" font-size="8" fill="#334155">085678901234</text>
      <text x="345" y="190" font-family="Arial, sans-serif" font-size="8" fill="#334155">Garut</text>
      <text x="430" y="190" font-family="Arial, sans-serif" font-size="8" fill="#2563eb" font-weight="bold">Edit | PO</text>
    </svg>`
  },
  {
    id: 12,
    num: "4. 54",
    title: "12. Antarmuka Kelola Produk, Varian, dan Alert Expired Batch",
    name: "Kelola Produk dan Alert Expired Batch",
    caption: "Gambar 4. 54 Implementasi Antarmuka Kelola Produk dan Alert Expired",
    intro: "Berikut merupakan implementasi antarmuka kelola produk dan alert expired batch yang digunakan admin untuk mengatur stok varian, harga jual, dan memantau kedaluwarsa barang.",
    svg: `<svg viewBox="0 0 500 360" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="360" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/supplier-stocks</text>

      <rect x="20" y="40" width="460" height="305" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="65" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Kelola Produk Varian &amp; Alert Batch Expired</text>

      <!-- Alert Warning Box -->
      <rect x="35" y="75" width="430" height="36" rx="4" fill="#fef2f2" stroke="#fca5a5"/>
      <text x="50" y="96" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#991b1b">⚠️ NOTIFIKASI EXPIRED: 1 Produk Mendekati Kedaluwarsa (&lt; 30 Hari)!</text>

      <!-- Table Header -->
      <rect x="35" y="122" width="430" height="24" fill="#f1f5f9"/>
      <text x="45" y="137" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#334155">NAMA PRODUK</text>
      <text x="160" y="137" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#334155">VARIAN</text>
      <text x="230" y="137" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#334155">STOK</text>
      <text x="280" y="137" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#334155">EXP DATE</text>
      <text x="375" y="137" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#334155">STATUS BATCH</text>

      <!-- Row 1 -->
      <text x="45" y="160" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">Keripik Tempe</text>
      <text x="160" y="160" font-family="Arial, sans-serif" font-size="8" fill="#475569">250 Gram</text>
      <text x="230" y="160" font-family="Arial, sans-serif" font-size="8" fill="#475569">45 Pcs</text>
      <text x="280" y="160" font-family="Arial, sans-serif" font-size="8" fill="#475569">15 Dec 2026</text>
      <rect x="375" y="148" width="60" height="16" rx="3" fill="#dcfce7"/>
      <text x="405" y="159" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#166534" text-anchor="middle">Aman</text>
      <line x1="35" y1="172" x2="465" y2="172" stroke="#f1f5f9"/>

      <!-- Row 2 -->
      <text x="45" y="195" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">Bakpia Pathok</text>
      <text x="160" y="195" font-family="Arial, sans-serif" font-size="8" fill="#475569">1 Box (Isi 10)</text>
      <text x="230" y="195" font-family="Arial, sans-serif" font-size="8" fill="#475569">12 Box</text>
      <text x="280" y="195" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#dc2626">25 Aug 2026</text>
      <rect x="375" y="183" width="75" height="16" rx="3" fill="#fee2e2"/>
      <text x="412" y="194" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#991b1b" text-anchor="middle">Hampir Expired</text>
    </svg>`
  },
  {
    id: 13,
    num: "4. 55",
    title: "13. Antarmuka Pengadaan Stok (PO Supplier WhatsApp)",
    name: "Pengadaan Stok PO Supplier",
    caption: "Gambar 4. 55 Implementasi Antarmuka Pengadaan Stok PO Supplier",
    intro: "Berikut merupakan implementasi antarmuka pengadaan stok (Purchase Order) yang digunakan admin untuk menerbitkan invoice PO dan mengirimkan format pesanan otomatis ke WhatsApp supplier.",
    svg: `<svg viewBox="0 0 500 360" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="360" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/supplier-orders/create</text>

      <rect x="20" y="40" width="460" height="305" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="65" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Invoice Purchase Order (PO/SUP-001/202608/0012)</text>

      <rect x="35" y="78" width="430" height="70" rx="4" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="45" y="95" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#334155">Supplier Pemasok: PT Keripik Nusantara</text>
      <text x="45" y="110" font-family="Arial, sans-serif" font-size="9" fill="#64748b">No. WhatsApp: 6281234567890</text>
      <text x="45" y="125" font-family="Arial, sans-serif" font-size="9" fill="#64748b">Tanggal PO: 10 Agustus 2026</text>

      <!-- Items Table -->
      <rect x="35" y="158" width="430" height="22" fill="#e8f5e9"/>
      <text x="45" y="173" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">BARANG PO</text>
      <text x="220" y="173" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">QTY PESAN</text>
      <text x="300" y="173" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">HPP SEPAKAT</text>
      <text x="390" y="173" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">TOTAL (RP)</text>

      <text x="45" y="195" font-family="Arial, sans-serif" font-size="8" fill="#334155">Keripik Tempe Super (250g)</text>
      <text x="220" y="195" font-family="Arial, sans-serif" font-size="8" fill="#334155">100 Pcs</text>
      <text x="300" y="195" font-family="Arial, sans-serif" font-size="8" fill="#334155">Rp 12.000</text>
      <text x="390" y="195" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">1.200.000</text>

      <!-- Action Buttons -->
      <rect x="35" y="240" width="200" height="34" rx="6" fill="#25d366"/>
      <text x="135" y="261" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">💬 Kirim PO via WhatsApp</text>

      <rect x="255" y="240" width="210" height="34" rx="6" fill="#1b5e20"/>
      <text x="360" y="261" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">✓ Terima Barang &amp; Restok</text>
    </svg>`
  },
  {
    id: 14,
    num: "4. 56",
    title: "14. Antarmuka Pengajuan dan Tiket Retur Barang Supplier",
    name: "Tiket Retur Barang Supplier",
    caption: "Gambar 4. 56 Implementasi Antarmuka Tiket Retur Barang Supplier",
    intro: "Berikut merupakan implementasi antarmuka kelola tiket retur barang yang digunakan admin untuk mengajukan retur barang rusak atau kedaluwarsa kepada supplier.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/supplier-returns/ticket</text>

      <rect x="30" y="42" width="440" height="290" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="45" y="68" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#991b1b">Tiket Klaim Retur Barang Supplier (#RET/SUP-001/202608/003)</text>

      <rect x="45" y="80" width="410" height="120" rx="4" fill="#fef2f2" stroke="#fca5a5"/>
      <text x="60" y="100" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#7f1d1d">Supplier: PT Keripik Nusantara</text>
      <text x="60" y="118" font-family="Arial, sans-serif" font-size="9" fill="#991b1b">Item Retur: Keripik Tempe Super (250g) - 15 Pcs</text>
      <text x="60" y="136" font-family="Arial, sans-serif" font-size="9" fill="#991b1b">Alasan Klaim: Kemasan bocor saat pengiriman &amp; mendekati expired</text>
      <text x="60" y="154" font-family="Arial, sans-serif" font-size="9" font-weight="bold" fill="#166534">Bukti Foto Kerusakan Physical: attached_proof_01.jpg (Uploaded)</text>
      
      <rect x="60" y="165" width="90" height="20" rx="3" fill="#22c55e"/>
      <text x="105" y="179" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#ffffff" text-anchor="middle">Status: Disetujui</text>

      <rect x="45" y="220" width="410" height="34" rx="6" fill="#1b5e20"/>
      <text x="250" y="241" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">🖨️ Cetak Surat Tiket Retur PDF</text>
    </svg>`
  },
  {
    id: 15,
    num: "4. 57",
    title: "15. Antarmuka Kelola Pesanan Penjualan dan Resi Kurir",
    name: "Kelola Pesanan Penjualan",
    caption: "Gambar 4. 57 Implementasi Antarmuka Kelola Pesanan Penjualan",
    intro: "Berikut merupakan implementasi antarmuka kelola pesanan penjualan yang digunakan admin untuk mengupdate status pengiriman resi kurir dan bukti serah terima paket.",
    svg: `<svg viewBox="0 0 500 360" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="360" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/orders</text>

      <rect x="20" y="40" width="460" height="305" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="65" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Manajemen Order Penjualan &amp; Input Tiket Resi</text>

      <!-- Table Header -->
      <rect x="35" y="80" width="430" height="24" fill="#e8f5e9"/>
      <text x="45" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">NO. ORDER</text>
      <text x="130" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">PEMBELI</text>
      <text x="210" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">BAYAR</text>
      <text x="280" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">RESI KURIR</text>
      <text x="390" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1b5e20">AKSI</text>

      <!-- Row 1 -->
      <text x="45" y="118" font-family="Arial, sans-serif" font-size="8" fill="#334155">#ORD-2026-01</text>
      <text x="130" y="118" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#1e293b">Dewi Lestari</text>
      <rect x="210" y="107" width="45" height="15" rx="3" fill="#dcfce7"/>
      <text x="232" y="118" font-family="Arial, sans-serif" font-size="7" font-weight="bold" fill="#166534" text-anchor="middle">PAID</text>
      <text x="280" y="118" font-family="Arial, sans-serif" font-size="8" fill="#334155">JNE8899120033</text>
      <text x="390" y="118" font-family="Arial, sans-serif" font-size="8" fill="#2563eb" font-weight="bold">Update Resi</text>
      <line x1="35" y1="130" x2="465" y2="130" stroke="#f1f5f9"/>

      <!-- Resi Form Popup Card -->
      <rect x="110" y="145" width="280" height="185" rx="6" fill="#ffffff" stroke="#cbd5e1" filter="drop-shadow(0 4px 8px rgba(0,0,0,0.12))"/>
      <rect x="110" y="145" width="280" height="30" rx="6" fill="#1b5e20"/>
      <text x="250" y="164" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">Form Update Resi &amp; Bukti Kirim</text>

      <text x="125" y="193" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#475569">NOMOR RESI EKSPEDISI</text>
      <rect x="125" y="198" width="250" height="24" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="135" y="214" font-family="Arial, sans-serif" font-size="9" fill="#334155">JNE8899120033</text>

      <text x="125" y="235" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#475569">UPLOAD FOTO BUKTI SERAH TERIMA</text>
      <rect x="125" y="240" width="250" height="24" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="135" y="256" font-family="Arial, sans-serif" font-size="8" fill="#64748b">bukti_kurir_01.jpg (1.2 MB)</text>

      <rect x="125" y="278" width="250" height="28" rx="4" fill="#1b5e20"/>
      <text x="250" y="296" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#ffffff" text-anchor="middle">Simpan Data Resi</text>
    </svg>`
  },
  {
    id: 16,
    num: "4. 58",
    title: "16. Antarmuka Rekapitulasi Laporan Penjualan dan Cetak PDF",
    name: "Rekapitulasi Laporan Penjualan",
    caption: "Gambar 4. 58 Implementasi Antarmuka Rekapitulasi Laporan Penjualan",
    intro: "Berikut merupakan implementasi antarmuka rekapitulasi laporan penjualan yang digunakan admin untuk memantau KPI bisnis dan mengunduh laporan keuangan toko dalam format PDF.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/reports</text>

      <rect x="20" y="42" width="460" height="290" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <text x="35" y="68" font-family="Arial, sans-serif" font-weight="bold" font-size="12" fill="#1b5e20">Laporan Rekapitulasi Penjualan &amp; Laba Toko</text>

      <!-- KPI Summary Cards -->
      <rect x="35" y="80" width="130" height="55" rx="6" fill="#f0fdf4" stroke="#86efac"/>
      <text x="45" y="98" font-family="Arial, sans-serif" font-size="8" fill="#166534">TOTAL OMZET</text>
      <text x="45" y="120" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="#1b5e20">Rp 15.450.000</text>

      <rect x="185" y="80" width="130" height="55" rx="6" fill="#eff6ff" stroke="#93c5fd"/>
      <text x="195" y="98" font-family="Arial, sans-serif" font-size="8" fill="#1e40af">TOTAL HPP MODAL</text>
      <text x="195" y="120" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="#1d4ed8">Rp 10.200.000</text>

      <rect x="335" y="80" width="130" height="55" rx="6" fill="#fefce8" stroke="#fde047"/>
      <text x="345" y="98" font-family="Arial, sans-serif" font-size="8" fill="#854d0e">LABA BERSIH</text>
      <text x="345" y="120" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="#a16207">Rp 5.250.000</text>

      <!-- Date Range Filter Bar -->
      <rect x="35" y="150" width="430" height="30" rx="4" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="45" y="169" font-family="Arial, sans-serif" font-size="9" fill="#475569">Periode: 01 Aug 2026 s/d 10 Aug 2026</text>

      <!-- Action Button -->
      <rect x="35" y="195" width="200" height="34" rx="6" fill="#dc2626"/>
      <text x="135" y="216" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">📄 Export Laporan PDF</text>

      <rect x="255" y="195" width="210" height="34" rx="6" fill="#1b5e20"/>
      <text x="360" y="216" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff" text-anchor="middle">📊 Export Rekap Supplier PDF</text>
    </svg>`
  },
  {
    id: 17,
    num: "4. 59",
    title: "17. Antarmuka Respon Live Chat Admin",
    name: "Respon Live Chat Admin",
    caption: "Gambar 4. 59 Implementasi Antarmuka Respon Live Chat Admin",
    intro: "Berikut merupakan implementasi antarmuka respon live chat admin yang digunakan staf pengelola untuk merespon pesan obrolan dari pelanggan secara real-time.",
    svg: `<svg viewBox="0 0 500 350" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
      <rect width="500" height="350" rx="10" fill="#f4f6f8" stroke="#dcdcdc" stroke-width="1.5"/>
      <rect width="500" height="28" rx="10" fill="#e2e8f0"/>
      <circle cx="20" cy="14" r="5" fill="#ff5f56"/>
      <circle cx="36" cy="14" r="5" fill="#ffbd2e"/>
      <circle cx="52" cy="14" r="5" fill="#27c93f"/>
      <text x="250" y="18" font-family="'Times New Roman', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">dewilestari.com/admin/chat</text>

      <!-- Sidebar Chat Sessions -->
      <rect x="15" y="38" width="150" height="300" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="15" y="38" width="150" height="32" fill="#1b5e20"/>
      <text x="25" y="58" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#ffffff">Daftar Chat Pelanggan</text>

      <!-- Chat Item 1 (Active) -->
      <rect x="20" y="78" width="140" height="42" rx="4" fill="#e8f5e9"/>
      <text x="30" y="94" font-family="Arial, sans-serif" font-weight="bold" font-size="9" fill="#1b5e20">Dewi Lestari</text>
      <text x="30" y="108" font-family="Arial, sans-serif" font-size="8" fill="#475569">Apakah Bakpia ready...</text>
      <circle cx="145" cy="92" r="7" fill="#dc2626"/>
      <text x="145" y="95" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#ffffff" text-anchor="middle">1</text>

      <!-- Main Chat Area -->
      <rect x="175" y="38" width="310" height="300" rx="6" fill="#ffffff" stroke="#cbd5e1"/>
      <rect x="175" y="38" width="310" height="32" fill="#f8fafc" stroke="#e2e8f0"/>
      <text x="190" y="58" font-family="Arial, sans-serif" font-weight="bold" font-size="10" fill="#1e293b">Sesi Chat: Dewi Lestari (Online)</text>

      <rect x="190" y="85" width="180" height="30" rx="6" fill="#f1f5f9"/>
      <text x="200" y="104" font-family="Arial, sans-serif" font-size="8" fill="#334155">Apakah Bakpia rasa keju ready kak?</text>

      <rect x="280" y="125" width="190" height="30" rx="6" fill="#dcfce7"/>
      <text x="290" y="144" font-family="Arial, sans-serif" font-size="8" fill="#14532d">Ready kak! Silahkan langsung diorder.</text>

      <!-- Reply Box -->
      <rect x="190" y="295" width="220" height="28" rx="4" fill="#f8fafc" stroke="#cbd5e1"/>
      <text x="200" y="313" font-family="Arial, sans-serif" font-size="8" fill="#94a3b8">Ketik balasan admin...</text>
      <rect x="420" y="295" width="50" height="28" rx="4" fill="#1b5e20"/>
      <text x="445" y="313" font-family="Arial, sans-serif" font-size="8" font-weight="bold" fill="#ffffff" text-anchor="middle">Kirim</text>
    </svg>`
  }
];

// Generate HTML Content
let htmlContent = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sub-bab 4.6.4 Implementasi Antarmuka</title>
<style>
  body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    line-height: 1.15;
    color: #000000;
    margin: 3cm 2.5cm 2.5cm 3cm;
  }
  h3 {
    font-size: 12pt;
    font-weight: bold;
    margin-top: 12pt;
    margin-bottom: 6pt;
    text-transform: uppercase;
  }
  h4 {
    font-size: 12pt;
    font-weight: bold;
    margin-top: 12pt;
    margin-bottom: 6pt;
  }
  h5 {
    font-size: 12pt;
    font-weight: bold;
    margin-top: 10pt;
    margin-bottom: 4pt;
  }
  p {
    text-align: justify;
    text-indent: 1cm;
    margin-top: 0;
    margin-bottom: 6pt;
  }
  .item-title {
    font-size: 12pt;
    font-weight: bold;
    margin-top: 14pt;
    margin-bottom: 4pt;
  }
  .image-container {
    text-align: center;
    margin-top: 8pt;
    margin-bottom: 4pt;
  }
  .image-container svg {
    max-width: 520px;
    height: auto;
    border: 1px solid #dcdcdc;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
  }
  .caption {
    text-align: center;
    font-weight: bold;
    font-size: 12pt;
    margin-top: 4pt;
    margin-bottom: 14pt;
  }
</style>
</head>
<body>

<h3>4.6.4 Implementasi Antarmuka</h3>

<p>Implementasi antarmuka (*User Interface*) merupakan bentuk wujud visual akhir dari perancangan antarmuka yang memfasilitasi interaksi pengguna dengan Sistem Informasi Penjualan Toko Dewi Lestari 2. Antarmuka ini dibangun menggunakan teknologi **HTML5, CSS3 Vanilla, JavaScript (ES6+), Blade Template Engine, FontAwesome, dan SweetAlert2** serta terintegrasi dengan pengontrol (*Controller*) Laravel dan basis data MySQL secara responsif.</p>

<p>Berikut merupakan urutan dan rincian implementasi antarmuka sistem yang terbagi atas antarmuka untuk peran pelanggan (*customer*) dan antarmuka untuk peran pengelola toko (*admin*).</p>

`;

let mdContent = `# Sub-bab 4.6.4 Implementasi Antarmuka

Implementasi antarmuka (*User Interface*) merupakan bentuk wujud visual akhir dari perancangan antarmuka yang memfasilitasi interaksi pengguna dengan Sistem Informasi Penjualan Toko Dewi Lestari 2. Antarmuka ini dibangun menggunakan teknologi **HTML5, CSS3 Vanilla, JavaScript (ES6+), Blade Template Engine, FontAwesome, dan SweetAlert2** serta terintegrasi dengan pengontrol (*Controller*) Laravel dan basis data MySQL secara responsif.

Berikut merupakan urutan dan rincian implementasi antarmuka sistem yang terbagi atas antarmuka untuk peran pelanggan (*customer*) dan antarmuka untuk peran pengelola toko (*admin*).

---

`;

// Group items
interfaces.forEach((item, index) => {
  if (index === 0) {
    htmlContent += `<h4>4.6.4.1 Implementasi Antarmuka Role Pembeli (Pelanggan)</h4>\n\n`;
    mdContent += `### 4.6.4.1 Implementasi Antarmuka Role Pembeli (Pelanggan)\n\n`;
  } else if (index === 9) {
    htmlContent += `<h4>4.6.4.2 Implementasi Antarmuka Role Admin (Pengelola Toko)</h4>\n\n`;
    mdContent += `### 4.6.4.2 Implementasi Antarmuka Role Admin (Pengelola Toko)\n\n`;
  }

  htmlContent += `
<div class="interface-block">
  <div class="item-title">${item.title}</div>
  <p>${item.intro}</p>
  <div class="image-container">
    ${item.svg}
  </div>
  <div class="caption">${item.caption}</div>
</div>
`;

  mdContent += `#### ${item.title}

${item.intro}

<div align="center">

${item.svg}

**${item.caption}**

</div>

---

`;
});

htmlContent += `</body>\n</html>`;

const htmlPath = path.join(__dirname, '../Sub_Bab_4_6_4_Implementasi_Antarmuka.html');
const mdPath = path.join(__dirname, '../Sub_Bab_4_6_4_Implementasi_Antarmuka.md');

// Also update 4_6_3 files if needed for backwards compatibility
const htmlPathOld = path.join(__dirname, '../Sub_Bab_4_6_3_Implementasi_Antar_Muka.html');
const mdPathOld = path.join(__dirname, '../Sub_Bab_4_6_3_Implementasi_Antar_Muka.md');

fs.writeFileSync(htmlPath, htmlContent, 'utf8');
fs.writeFileSync(mdPath, mdContent, 'utf8');
fs.writeFileSync(htmlPathOld, htmlContent, 'utf8');
fs.writeFileSync(mdPathOld, mdContent, 'utf8');

console.log("Successfully generated implementation interface files!");
