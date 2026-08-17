const fs = require('fs');
const path = require('path');

const htmlContent = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sub-bab 4.6.6 Penggunaan Program - Toko Dewi Lestari 2</title>
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
  ul, ol {
    margin-top: 0;
    margin-bottom: 6pt;
    padding-left: 1.25cm;
  }
  li {
    text-align: justify;
    margin-bottom: 4pt;
  }
  .caption {
    font-weight: bold;
    font-size: 12pt;
    margin-top: 12pt;
    margin-bottom: 6pt;
    text-indent: 0;
  }
  table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 6pt;
    margin-bottom: 12pt;
  }
  table, th, td {
    border: 1px solid #000000;
  }
  th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: center;
    padding: 6px;
    font-size: 11pt;
  }
  td {
    padding: 6px;
    font-size: 11pt;
    vertical-align: top;
  }
  .center {
    text-align: center;
  }
</style>
</head>
<body>

<h3>4.6.6 Penggunaan Program</h3>

<p>Penggunaan program (*User Manual / Operational Guide*) merupakan bagian yang menjelaskan petunjuk dan alur tata cara pengoperasian Sistem Informasi Penjualan Toko Dewi Lestari 2. Petunjuk penggunaan ini disusun secara terstruktur berdasarkan hak akses pengguna (*User Roles*), yaitu panduan penggunaan untuk <b>Role Pembeli (Pelanggan)</b> pada antarmuka *front-end* dan panduan penggunaan untuk <b>Role Admin (Pengelola Toko)</b> pada antarmuka *back-end*.</p>

<h4>4.6.6.1 Penggunaan Program Sisi Pembeli (Pelanggan)</h4>

<p>Penggunaan program pada sisi pembeli ditujukan untuk memberikan kemudahan dalam pendaftaran akun, pencarian produk oleh-oleh, proses checkout pesanan, pembayaran otomatis Midtrans, serta pelacakan resi pesanan secara *real-time*. Langkah-langkah penggunaannya adalah sebagai berikut:</p>

<ol>
  <li><b>Pendaftaran Akun Baru dan Login Pelanggan:</b>
    <ul>
      <li>Pengunjung membuka peramban (*web browser*) dan mengakses alamat domain utama website <code>http://dewilestari2.site.je</code> (atau <code>http://127.0.0.1:8000</code> pada lingkungan lokal).</li>
      <li>Untuk mendaftar akun baru, pembeli menekan menu <b>Daftar</b> (URL: <code>/register</code>), lalu mengisi formulir registrasi yang terdiri dari Nama Lengkap, Alamat Email, Nomor WhatsApp, dan Kata Sandi (minimal 6 karakter). Setelah data terisi, tekan tombol <b>Daftar Akun Baru</b>.</li>
      <li>Untuk masuk ke akun yang sudah terdaftar, pembeli menekan menu <b>Masuk</b> (URL: <code>/login</code>), mengisikan Alamat Email dan Kata Sandi, kemudian menekan tombol <b>Masuk Ke Akun</b>.</li>
    </ul>
  </li>

  <li><b>Eksplorasi Katalog Produk, Varian Kemasan, dan Stok:</b>
    <ul>
      <li>Setelah berhasil masuk, pembeli diarahkan ke halaman Beranda Utama yang menampilkan katalog produk oleh-oleh khas (kripik tempe, bakpia pathok, dodol garut, dll).</li>
      <li>Pembeli dapat mencari produk tertentu menggunakan *Search Bar* pencarian kata kunci atau memilih kategori produk.</li>
      <li>Pada kartu produk (*product card*), pembeli memilih varian berat kemasan (misal: 250 gram, 500 gram, 1 Box) untuk melihat penyesuaian harga jual, indikator ketersediaan stok *real-time*, dan batas tanggal kedaluwarsa batch (*expiry date*).</li>
    </ul>
  </li>

  <li><b>Pengelolaan Keranjang Belanja (*Cart Drawer*):</b>
    <ul>
      <li>Pembeli menentukan jumlah barang yang ingin dibeli, lalu menekan tombol <b>+ Keranjang</b>.</li>
      <li>Sistem akan membuka *Drawer Side Cart* di pojok kanan layar yang menampilkan rincian barang, harga varian, adjuster jumlah (+ / -), dan subtotal harga belanja.</li>
      <li>Pembeli dapat menambah item produk lain atau langsung menekan tombol <b>Lanjut Checkout</b> untuk memproses pesanan.</li>
    </ul>
  </li>

  <li><b>Pengisian Alamat, Pilihan Kurir Pengiriman, dan Checkout:</b>
    <ul>
      <li>Pada modal *Checkout Pesanan*, pembeli memilih metode pengiriman:
        <ul>
          <li><b>Ambil di Toko (*Store Pickup*):</b> Bebas biaya pengiriman. Pembeli mengambil pesanan langsung di toko fisik.</li>
          <li><b>Kurir Toko (Lokal Cimahi / Bandung):</b> Pembeli mengisi alamat lengkap dan menentukan titik lokasi tujuan pada peta interaktif Leaflet/OSM untuk menghitung jarak dan tarif ongkos kirim otomatis secara akurat.</li>
          <li><b>Ekspedisi (J&T, JNE, POS):</b> Pembeli memilih opsi kurir ekspedisi dan zona pengiriman (Luar Kota atau Luar Pulau Jawa) untuk menghitung ongkir per kg.</li>
        </ul>
      </li>
      <li>Pembeli memeriksa kembali total belanjaan dan total tagihan ongkos kirim, lalu menekan tombol <b>Bayar Sekarang</b>.</li>
    </ul>
  </li>

  <li><b>Pembayaran Otomatis via Midtrans Payment Gateway:</b>
    <ul>
      <li>Sistem secara otomatis menampilkan pop-up modal <i>Midtrans Snap Payment</i>.</li>
      <li>Pembeli memilih metode pembayaran yang diinginkan, yaitu:
        <ul>
          <li><b>QRIS:</b> Scan kode QR menggunakan aplikasi GoPay, ShopeePay, OVO, Dana, atau Mobile Banking.</li>
          <li><b>Virtual Account (VA):</b> Pembayaran via Nomor VA Bank BCA, Mandiri, BNI, BRI, atau Permata.</li>
        </ul>
      </li>
      <li>Pembeli melakukan transfer sesuai nominal tagihan sebelum batas waktu (*countdown timer*) berakhir. Setelah pembayaran berhasil, status transaksi otomatis berubah menjadi <b>PAID / Lunas</b>.</li>
    </ul>
  </li>

  <li><b>Pelacakan Status Pengiriman Pesanan (*Order Tracking*):</b>
    <ul>
      <li>Pembeli dapat memantau perjalanan paket barang dengan mengakses menu <b>Lacak Pesanan</b> (URL: <code>/track</code>).</li>
      <li>Masukkan Nomor Tiket Resi transaksi (contoh: <code>TKT-2026-081299</code>) dan tekan tombol <b>Cek Resi</b>.</li>
      <li>Sistem menampilkan linimasa progres pengiriman secara *real-time* (1. Order Dibayar &rarr; 2. Dikemas Admin &rarr; 3. Dalam Pengiriman &rarr; 4. Pesanan Diterima) serta foto bukti penyerahan barang yang diunggah kurir/admin.</li>
    </ul>
  </li>

  <li><b>Pemberian Rating Ulasan Produk dan Live Chat Support:</b>
    <ul>
      <li>Jika pesanan telah berstatus 'diterima', pembeli dapat masuk ke Dashboard Pelanggan menu *Riwayat Pesanan* dan menekan tombol <b>Beri Ulasan</b> untuk memberikan penilaian rating bintang 1–5 serta komentar ulasan produk.</li>
      <li>Pembeli dapat menggunakan widget *Floating Live Chat* di pojok kanan bawah untuk berkonsultasi atau bertanya secara langsung kepada admin toko.</li>
    </ul>
  </li>
</ol>

<h4>4.6.6.2 Penggunaan Program Sisi Pengelola (Admin)</h4>

<p>Penggunaan program pada sisi pengelola toko (*Admin*) difokuskan pada manajemen master data supplier, kontrol varian produk dan alert kedaluwarsa batch, eksekusi Purchase Order (PO) otomatis via WhatsApp, penanganan klaim retur barang supplier, verifikasi resi pengiriman, serta cetak laporan penjualan PDF. Langkah-langkah pengoperasiannya adalah sebagai berikut:</p>

<ol>
  <li><b>Autentikasi Login Portal Admin:</b>
    <ul>
      <li>Admin membuka peramban (*web browser*) dan mengakses URL khusus portal admin <code>http://dewilestari2.site.je/admin/login</code>.</li>
      <li>Admin mengisikan Username/Email Admin (<code>admin@dewilestari.com</code>) dan Kata Sandi Admin, lalu menekan tombol <b>Masuk Sistem Backend</b>. Sistem melakukan otentikasi hak akses *role admin*.</li>
    </ul>
  </li>

  <li><b>Manajemen Master Data Supplier Pemasok:</b>
    <ul>
      <li>Admin mengakses menu <b>Master Supplier</b> (URL: <code>/admin/suppliers</code>).</li>
      <li>Untuk menambah mitra pemasok baru, tekan tombol <b>+ Tambah Supplier</b>, lalu isikan Nama Perusahaan Supplier, Nomor Telepon/WhatsApp (format: 628xxx), Alamat Email, dan Alamat Gudang Supplier, kemudian tekan <b>Simpan Data</b>.</li>
      <li>Admin dapat mengedit atau menghapus data supplier serta melihat riwayat transaksi pengadaan barang per supplier.</li>
    </ul>
  </li>

  <li><b>Manajemen Produk, Varian, dan Pemantauan Alert Expired Batch:</b>
    <ul>
      <li>Admin mengakses menu <b>Kelola Stok Produk</b> (URL: <code>/admin/supplier-stocks</code>).</li>
      <li>Untuk menginput produk baru, tekan tombol <b>+ Tambah Produk</b>, pilih Supplier Acuan, isi Nama Produk, Deskripsi, Unggah Foto Produk (maksimal 2MB), serta tambahkan varian kemasan/berat, Harga HPP Modal, Harga Jual, Jumlah Stok Awal, dan Tanggal Kedaluwarsa Batch (*Expiry Date*).</li>
      <li>Admin memantau banner <i>Alert Warning Batch Expired</i> (warna merah/kuning) pada bagian atas tabel yang secara otomatis mendeteksi produk yang mendekati tanggal kedaluwarsa (&lt; 30 hari) agar dapat segera dilakukan penanganan promosi atau pengajuan retur.</li>
    </ul>
  </li>

  <li><b>Eksekusi Pengadaan Stok (*Purchase Order*) via Direct WhatsApp Supplier:</b>
    <ul>
      <li>Admin membuka detail profil supplier pada menu <b>Kelola Supplier</b>.</li>
      <li>Pilih barang pasokan, masukkan kuantitas barang PO yang dipesan, dan harga HPP kesepakatan untuk menerbitkan Surat Invoice PO otomatis dengan penomoran <code>PO/SUP-XXX/YYYYMM/XXXX</code>.</li>
      <li>Admin menekan tombol <b>💬 Kirim PO via WhatsApp</b>. Sistem secara otomatis membuka aplikasi/tautan WhatsApp Web dengan pesan draf format pesanan PO yang terisi secara otomatis menuju nomor HP supplier.</li>
      <li>Jika barang pasokan telah dikirim dan diterima di gudang toko, admin menekan tombol <b>✓ Terima Barang &amp; Restok</b> untuk menambahkan jumlah kuantitas ke katalog stok produk secara otomatis.</li>
    </ul>
  </li>

  <li><b>Pengajuan dan Cetak Tiket Retur Barang Supplier:</b>
    <ul>
      <li>Jika terdapat barang pasokan yang rusak (*damaged*) atau kedaluwarsa (*expired*), admin mengakses menu <b>Retur Supplier</b> (URL: <code>/admin/supplier-returns/store</code>).</li>
      <li>Admin memilih PO acuan, memilih produk/varian retur, menginput jumlah fisik rusak, mengisi alasan retur, serta mengunggah foto bukti fisik kerusakan.</li>
      <li>Sistem menerbitkan Surat Tiket Retur PDF dengan penomoran <code>RET/SUP-XXX/YYYYMM/XXXX</code>. Admin menekan tombol <b>🖨️ Cetak Surat Tiket Retur PDF</b> untuk dilampirkan kepada supplier saat pengembalian barang.</li>
    </ul>
  </li>

  <li><b>Verifikasi Transaksi Penjualan dan Input Resi Pengiriman Kurir:</b>
    <ul>
      <li>Admin mengakses menu <b>Manajemen Order</b> (URL: <code>/admin/orders</code>) untuk memantau transaksi pesanan pembeli yang masuk.</li>
      <li>Admin memverifikasi status pembayaran Midtrans (berstatus <b>PAID / Lunas</b>).</li>
      <li>Admin mengemas barang, lalu menekan tombol <b>Update Resi</b> untuk menginput Nomor Resi Ekspedisi (JNE/POS/TIKI) atau Nomor Resi Kurir Toko (format: <code>JNE8899XXXXXX</code>), serta mengunggah file foto bukti serah terima paket ke kurir. Status pesanan otomatis diperbarui menjadi <b>Dalam Pengiriman</b>.</li>
    </ul>
  </li>

  <li><b>Rekapitulasi Laporan Penjualan dan Cetak PDF:</b>
    <ul>
      <li>Admin mengakses menu <b>Rekapitulasi Laporan</b> (URL: <code>/admin/reports</code>).</li>
      <li>Admin menentukan filter rentang periode laporan (*Start Date* s/d *End Date*).</li>
      <li>Sistem secara otomatis mengkalkulasi dan menampilkan *Card KPI Summary* (Total Omzet Penjualan, Total HPP Modal, Akumulasi Laba Bersih, dan Total Order Selesai).</li>
      <li>Admin menekan tombol <b>📄 Export Laporan Penjualan PDF</b> atau <b>📊 Export Rekap Supplier PDF</b> untuk mencetak dokumen laporan keuangan toko yang sah siap diserahkan kepada pemilik toko.</li>
    </ul>
  </li>

  <li><b>Respon Chat Live Customer Support Admin:</b>
    <ul>
      <li>Admin mengakses menu <b>Live Chat Admin</b> (URL: <code>/admin/chat</code>).</li>
      <li>Admin memantau indikator *Unread Counter* pesan masuk dari pelanggan.</li>
      <li>Pilih sesi obrolan pelanggan, lalu ketikkan balasan pesan pada kolom input balasan dan tekan tombol <b>Kirim Balasan</b> untuk merespon pertanyaan pembeli secara *real-time*.</li>
    </ul>
  </li>
</ol>

<p class="caption">Tabel 4. 23 Matriks Ringkasan Fitur dan Penggunaan Program</p>

<table>
  <thead>
    <tr>
      <th style="width: 6%;">No</th>
      <th style="width: 20%;">Peran Pengguna</th>
      <th style="width: 30%;">Fitur Utama</th>
      <th style="width: 44%;">Fungsi &amp; Hasil Penggunaan Program</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="center">1</td>
      <td><b>Pelanggan (Pembeli)</b></td>
      <td>Katalog, Cart Drawer, Checkout, Midtrans &amp; Tracking</td>
      <td>Mencari produk oleh-oleh, memilih varian kemasan, menghitung ongkir RajaOngkir/Kurir Toko, membayar via QRIS/VA, dan melacak status resi pengiriman paket.</td>
    </tr>
    <tr>
      <td class="center">2</td>
      <td><b>Admin (Pengelola)</b></td>
      <td>Master Supplier, Kelola Stok &amp; Batch Expired, PO WhatsApp, Tiket Retur, Resi Kurir, Cetak PDF, dan Chat Support</td>
      <td>Mengelola stok varian, memantau alert produk expired, membuat PO supplier via WA, mengklaim tiket retur barang rusak, menginput resi kurir, mengunduh laporan penjualan PDF, dan membalas chat pembeli.</td>
    </tr>
  </tbody>
</table>

</body>
</html>`;

const mdContent = `# Sub-bab 4.6.6 Penggunaan Program

### 4.6.6 Penggunaan Program

Penggunaan program (*User Manual / Operational Guide*) merupakan bagian yang menjelaskan petunjuk dan alur tata cara pengoperasian Sistem Informasi Penjualan Toko Dewi Lestari 2. Petunjuk penggunaan ini disusun secara terstruktur berdasarkan hak akses pengguna (*User Roles*), yaitu panduan penggunaan untuk **Role Pembeli (Pelanggan)** pada antarmuka *front-end* dan panduan penggunaan untuk **Role Admin (Pengelola Toko)** pada antarmuka *back-end*.

---

#### 4.6.6.1 Penggunaan Program Sisi Pembeli (Pelanggan)

Penggunaan program pada sisi pembeli ditujukan untuk memberikan kemudahan dalam pendaftaran akun, pencarian produk oleh-oleh, proses checkout pesanan, pembayaran otomatis Midtrans, serta pelacakan resi pesanan secara *real-time*. Langkah-langkah penggunaannya adalah sebagai berikut:

1. **Pendaftaran Akun Baru dan Login Pelanggan:**
   - Pengunjung membuka peramban (*web browser*) dan mengakses alamat domain utama website \`http://dewilestari2.site.je\` (atau \`http://127.0.0.1:8000\` pada lingkungan lokal).
   - Untuk mendaftar akun baru, pembeli menekan menu **Daftar** (URL: \`/register\`), lalu mengisi formulir registrasi yang terdiri dari Nama Lengkap, Alamat Email, Nomor WhatsApp, dan Kata Sandi (minimal 6 karakter). Setelah data terisi, tekan tombol **Daftar Akun Baru**.
   - Untuk masuk ke akun yang sudah terdaftar, pembeli menekan menu **Masuk** (URL: \`/login\`), mengisikan Alamat Email dan Kata Sandi, kemudian menekan tombol **Masuk Ke Akun**.

2. **Eksplorasi Katalog Produk, Varian Kemasan, dan Stok:**
   - Setelah berhasil masuk, pembeli diarahkan ke halaman Beranda Utama yang menampilkan katalog produk oleh-oleh khas (kripik tempe, bakpia pathok, dodol garut, dll).
   - Pembeli dapat mencari produk tertentu menggunakan *Search Bar* pencarian kata kunci atau memilih kategori produk.
   - Pada kartu produk (*product card*), pembeli memilih varian berat kemasan (misal: 250 gram, 500 gram, 1 Box) untuk melihat penyesuaian harga jual, indikator ketersediaan stok *real-time*, dan batas tanggal kedaluwarsa batch (*expiry date*).

3. **Pengelolaan Keranjang Belanja (*Cart Drawer*):**
   - Pembeli menentukan jumlah barang yang ingin dibeli, lalu menekan tombol **+ Keranjang**.
   - Sistem akan membuka *Drawer Side Cart* di pojok kanan layar yang menampilkan rincian barang, harga varian, adjuster jumlah (+ / -), dan subtotal harga belanja.
   - Pembeli dapat menambah item produk lain atau langsung menekan tombol **Lanjut Checkout** untuk memproses pesanan.

4. **Pengisian Alamat, Pilihan Kurir Pengiriman, dan Checkout:**
   - Pada modal *Checkout Pesanan*, pembeli memilih metode pengiriman:
     - **Ambil di Toko (*Store Pickup*):** Bebas biaya pengiriman. Pembeli mengambil pesanan langsung di toko fisik.
     - **Kurir Toko (Lokal Cimahi / Bandung):** Pembeli mengisi alamat lengkap dan menentukan titik lokasi tujuan pada peta interaktif Leaflet/OSM untuk menghitung jarak dan tarif ongkos kirim otomatis secara akurat.
     - **Ekspedisi (J&T, JNE, POS):** Pembeli memilih opsi kurir ekspedisi dan zona pengiriman (Luar Kota atau Luar Pulau Jawa) untuk menghitung ongkir per kg.
   - Pembeli memeriksa kembali total belanjaan dan total tagihan ongkos kirim, lalu menekan tombol **Bayar Sekarang**.

5. **Pembayaran Otomatis via Midtrans Payment Gateway:**
   - Sistem secara otomatis menampilkan pop-up modal *Midtrans Snap Payment*.
   - Pembeli memilih metode pembayaran yang diinginkan, yaitu:
     - **QRIS:** Scan kode QR menggunakan aplikasi GoPay, ShopeePay, OVO, Dana, atau Mobile Banking.
     - **Virtual Account (VA):** Pembayaran via Nomor VA Bank BCA, Mandiri, BNI, BRI, atau Permata.
   - Pembeli melakukan transfer sesuai nominal tagihan sebelum batas waktu (*countdown timer*) berakhir. Setelah pembayaran berhasil, status transaksi otomatis berubah menjadi **PAID / Lunas**.

6. **Pelacakan Status Pengiriman Pesanan (*Order Tracking*):**
   - Pembeli dapat memantau perjalanan paket barang dengan mengakses menu **Lacak Pesanan** (URL: \`/track\`).
   - Masukkan Nomor Tiket Resi transaksi (contoh: \`TKT-2026-081299\`) dan tekan tombol **Cek Resi**.
   - Sistem menampilkan linimasa progres pengiriman secara *real-time* (1. Order Dibayar &rarr; 2. Dikemas Admin &rarr; 3. Dalam Pengiriman &rarr; 4. Pesanan Diterima) serta foto bukti penyerahan barang yang diunggah kurir/admin.

7. **Pemberian Rating Ulasan Produk dan Live Chat Support:**
   - Jika pesanan telah berstatus 'diterima', pembeli dapat masuk ke Dashboard Pelanggan menu *Riwayat Pesanan* dan menekan tombol **Beri Ulasan** untuk memberikan penilaian rating bintang 1–5 serta komentar ulasan produk.
   - Pembeli dapat menggunakan widget *Floating Live Chat* di pojok kanan bawah untuk berkonsultasi atau bertanya secara langsung kepada admin toko.

---

#### 4.6.6.2 Penggunaan Program Sisi Pengelola (Admin)

Penggunaan program pada sisi pengelola toko (*Admin*) difokuskan pada manajemen master data supplier, kontrol varian produk dan alert kedaluwarsa batch, eksekusi Purchase Order (PO) otomatis via WhatsApp, penanganan klaim retur barang supplier, verifikasi resi pengiriman, serta cetak laporan penjualan PDF. Langkah-langkah pengoperasiannya adalah sebagai berikut:

1. **Autentikasi Login Portal Admin:**
   - Admin membuka peramban (*web browser*) dan mengakses URL khusus portal admin \`http://dewilestari2.site.je/admin/login\`.
   - Admin mengisikan Username/Email Admin (\`admin@dewilestari.com\`) dan Kata Sandi Admin, lalu menekan tombol **Masuk Sistem Backend**. Sistem melakukan otentikasi hak akses *role admin*.

2. **Manajemen Master Data Supplier Pemasok:**
   - Admin mengakses menu **Master Supplier** (URL: \`/admin/suppliers\`).
   - Untuk menambah mitra pemasok baru, tekan tombol **+ Tambah Supplier**, lalu isikan Nama Perusahaan Supplier, Nomor Telepon/WhatsApp (format: 628xxx), Alamat Email, dan Alamat Gudang Supplier, kemudian tekan **Simpan Data**.
   - Admin dapat mengedit atau menghapus data supplier serta melihat riwayat transaksi pengadaan barang per supplier.

3. **Manajemen Produk, Varian, dan Pemantauan Alert Expired Batch:**
   - Admin mengakses menu **Kelola Stok Produk** (URL: \`/admin/supplier-stocks\`).
   - Untuk menginput produk baru, tekan tombol **+ Tambah Produk**, pilih Supplier Acuan, isi Nama Produk, Deskripsi, Unggah Foto Produk (maksimal 2MB), serta tambahkan varian kemasan/berat, Harga HPP Modal, Harga Jual, Jumlah Stok Awal, dan Tanggal Kedaluwarsa Batch (*Expiry Date*).
   - Admin memantau banner *Alert Warning Batch Expired* (warna merah/kuning) pada bagian atas tabel yang secara otomatis mendeteksi produk yang mendekati tanggal kedaluwarsa (&lt; 30 hari) agar dapat segera dilakukan penanganan promosi atau pengajuan retur.

4. **Eksekusi Pengadaan Stok (*Purchase Order*) via Direct WhatsApp Supplier:**
   - Admin membuka detail profil supplier pada menu **Kelola Supplier**.
   - Pilih barang pasokan, masukkan kuantitas barang PO yang dipesan, dan harga HPP kesepakatan untuk menerbitkan Surat Invoice PO otomatis dengan penomoran \`PO/SUP-XXX/YYYYMM/XXXX\`.
   - Admin menekan tombol **💬 Kirim PO via WhatsApp**. Sistem secara otomatis membuka aplikasi/tautan WhatsApp Web dengan pesan draf format pesanan PO yang terisi secara otomatis menuju nomor HP supplier.
   - Jika barang pasokan telah dikirim dan diterima di gudang toko, admin menekan tombol **✓ Terima Barang & Restok** untuk menambahkan jumlah kuantitas ke katalog stok produk secara otomatis.

5. **Pengajuan dan Cetak Tiket Retur Barang Supplier:**
   - Jika terdapat barang pasokan yang rusak (*damaged*) atau kedaluwarsa (*expired*), admin mengakses menu **Retur Supplier** (URL: \`/admin/supplier-returns/store\`).
   - Admin memilih PO acuan, memilih produk/varian retur, menginput jumlah fisik rusak, mengisi alasan retur, serta mengunggah foto bukti fisik kerusakan.
   - Sistem menerbitkan Surat Tiket Retur PDF dengan penomoran \`RET/SUP-XXX/YYYYMM/XXXX\`. Admin menekan tombol **🖨️ Cetak Surat Tiket Retur PDF** untuk dilampirkan kepada supplier saat pengembalian barang.

6. **Verifikasi Transaksi Penjualan dan Input Resi Pengiriman Kurir:**
   - Admin mengakses menu **Manajemen Order** (URL: \`/admin/orders\`) untuk memantau transaksi pesanan pembeli yang masuk.
   - Admin memverifikasi status pembayaran Midtrans (berstatus **PAID / Lunas**).
   - Admin mengemas barang, lalu menekan tombol **Update Resi** untuk menginput Nomor Resi Ekspedisi (JNE/POS/TIKI) atau Nomor Resi Kurir Toko (format: \`JNE8899XXXXXX\`), serta mengunggah file foto bukti serah terima paket ke kurir. Status pesanan otomatis diperbarui menjadi **Dalam Pengiriman**.

7. **Rekapitulasi Laporan Penjualan dan Cetak PDF:**
   - Admin mengakses menu **Rekapitulasi Laporan** (URL: \`/admin/reports\`).
   - Admin menentukan filter rentang periode laporan (*Start Date* s/d *End Date*).
   - Sistem secara otomatis mengkalkulasi dan menampilkan *Card KPI Summary* (Total Omzet Penjualan, Total HPP Modal, Akumulasi Laba Bersih, dan Total Order Selesai).
   - Admin menekan tombol **📄 Export Laporan Penjualan PDF** atau **📊 Export Rekap Supplier PDF** untuk mencetak dokumen laporan keuangan toko yang sah siap diserahkan kepada pemilik toko.

8. **Respon Chat Live Customer Support Admin:**
   - Admin mengakses menu **Live Chat Admin** (URL: \`/admin/chat\`).
   - Admin memantau indikator *Unread Counter* pesan masuk dari pelanggan.
   - Pilih sesi obrolan pelanggan, lalu ketikkan balasan pesan pada kolom input balasan dan tekan tombol **Kirim Balasan** untuk merespon pertanyaan pembeli secara *real-time*.

---

### Tabel 4. 23 Matriks Ringkasan Fitur dan Penggunaan Program

| No | Peran Pengguna | Fitur Utama | Fungsi & Hasil Penggunaan Program |
| :---: | :--- | :--- | :--- |
| 1 | **Pelanggan (Pembeli)** | Katalog, Cart Drawer, Checkout, Midtrans & Tracking | Mencari produk oleh-oleh, memilih varian kemasan, menghitung ongkir RajaOngkir/Kurir Toko, membayar via QRIS/VA, dan melacak status resi pengiriman paket. |
| 2 | **Admin (Pengelola)** | Master Supplier, Kelola Stok & Batch Expired, PO WhatsApp, Tiket Retur, Resi Kurir, Cetak PDF, dan Chat Support | Mengelola stok varian, memantau alert produk expired, membuat PO supplier via WA, mengklaim tiket retur barang rusak, menginput resi kurir, mengunduh laporan penjualan PDF, dan membalas chat pembeli. |
`;

const htmlPath = path.join(__dirname, '../Sub_Bab_4_6_6_Penggunaan_Program.html');
const docPath = path.join(__dirname, '../Sub_Bab_4_6_6_Penggunaan_Program.doc');
const mdPath = path.join(__dirname, '../Sub_Bab_4_6_6_Penggunaan_Program.md');

fs.writeFileSync(htmlPath, htmlContent, 'utf8');
fs.writeFileSync(docPath, htmlContent, 'utf8');
fs.writeFileSync(mdPath, mdContent, 'utf8');

console.log("Successfully generated Sub_Bab_4_6_6_Penggunaan_Program files!");
