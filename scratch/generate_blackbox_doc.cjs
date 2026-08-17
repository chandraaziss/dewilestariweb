const fs = require('fs');
const path = require('path');

const htmlContent = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Metode Pengujian Black Box - Toko Dewi Lestari 2</title>
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
  .quote-box {
    background-color: #f9f9f9;
    border-left: 4px solid #1b5e20;
    padding: 10px 15px;
    margin: 10px 0;
    font-style: normal;
  }
</style>
</head>
<body>

<h3>4.5.1 Rencana dan Metode Pengujian Black Box</h3>

<p>Pengujian perangkat lunak pada Sistem Informasi Penjualan Toko Dewi Lestari 2 dilakukan menggunakan pendekatan <i>Black Box Testing</i> (Pengujian Kotak Hitam). Pengujian ini berfokus pada evaluasi fungsionalitas dan keluaran (*output*) sistem dari sudut pandang pengguna tanpa menguji baris kode internal (*source code*). Metode pengujian <i>Black Box</i> yang digunakan dalam penelitian skripsi sistem informasi ini terdiri dari 2 (dua) metode utama, yaitu <b>Equivalence Partitioning (EP)</b> dan <b>Boundary Value Analysis (BVA)</b>.</p>

<h4>1. Metode Equivalence Partitioning (EP)</h4>
<p><b>Definisi:</b> <i>Equivalence Partitioning</i> adalah teknik pengujian dengan cara membagi domain data masukan (*input domain*) ke dalam kelas-kelas ekuivalensi (*equivalence classes*), yaitu kelompok data valid (diterima oleh sistem) dan kelompok data tidak valid (ditolak oleh sistem). Setiap kelas data diwakili oleh sampel data uji untuk memastikan sistem memberikan respon yang sesuai.</p>

<p><b>Alasan Pemilihan Metode EP:</b></p>
<ul>
  <li><b>Efisiensi Kasus Uji (*Test Efficiency*):</b> Penguji tidak perlu menguji seluruh kemungkinan data satu per satu, melainkan cukup mengambil perwakilan sampel data valid dan tidak valid sehingga proses pengujian skripsi menjadi efisien namun tetap komprehensif.</li>
  <li><b>Sangat Efektif untuk Validasi Formulir Input &amp; Otentikasi:</b> Sistem Informasi memiliki banyak formulir masukan seperti Form Login, Registrasi Akun, Form Alamat, dan Input Supplier. Metode EP memastikan bahwa data yang sah berhasil diproses dan data yang keliru/salah format ditolak dengan pesan peringatan (*error message*) yang sesuai.</li>
</ul>

<p><b>Contoh Penerapan Kasus Uji EP pada Sistem Toko Dewi Lestari 2:</b></p>
<ul>
  <li><b>Pengujian Form Login/Registrasi:</b>
    <ul>
      <li><i>Input Valid:</i> Mengisi email berformat benar (contoh: <code>pelanggan@gmail.com</code>). Hasil: Login/Registrasi berhasil diproses.</li>
      <li><i>Input Tidak Valid:</i> Mengisi email tanpa simbol '@' atau domain (contoh: <code>pelanggangmail.com</code>). Hasil: Sistem menolak dan menampilkan pesan peringatan <i>"Format email tidak valid"</i>.</li>
    </ul>
  </li>
  <li><b>Pengujian Pencarian Tiket Resi Pelacakan Pesanan:</b>
    <ul>
      <li><i>Input Valid:</i> Memasukkan Kode Tiket Resi terdaftar (contoh: <code>TKT-2026-081299</code>). Hasil: Sistem menampilkan linimasa status pengiriman.</li>
      <li><i>Input Tidak Valid:</i> Memasukkan Kode Tiket Resi fiktif (contoh: <code>TKT-9999-XXXXXX</code>). Hasil: Sistem menampilkan notifikasi <i>"Nomor resi tidak ditemukan"</i>.</li>
    </ul>
  </li>
</ul>

<h4>2. Metode Boundary Value Analysis (BVA)</h4>
<p><b>Definisi:</b> <i>Boundary Value Analysis</i> adalah teknik pengujian yang berfokus pada nilai-nilai batas (*edge cases*) dari kisaran batasan data yang diperbolehkan oleh sistem. Pengujian dilakukan pada titik batas minimum, tepat pada batas minimum/maksimum, dan di luar batas minimum/maksimum.</p>

<p><b>Alasan Pemilihan Metode BVA:</b></p>
<ul>
  <li><b>Mendeteksi Error pada Titik Paling Rentan (*Edge Cases*):</b> Berdasarkan prinsip rekayasa perangkat lunak, bug terbanyak pada aplikasi terjadi pada nilai ambang batas (*boundary condition*) akibat kesalahan logika perbandingan percabangan (misalnya penggunaan operator <code>&lt;</code> yang seharusnya <code>&lt;=</code>).</li>
  <li><b>Menguji Aturan Bisnis &amp; Restriksi Sistem (*Business Rules*):</b> Sangat ideal untuk menguji fitur dengan parameter kriteria batas, seperti batas minimal panjang kata sandi, batas maksimal ukuran file gambar yang diunggah, serta batas jumlah kuantitas stok barang.</li>
</ul>

<p><b>Contoh Penerapan Kasus Uji BVA pada Sistem Toko Dewi Lestari 2:</b></p>
<ul>
  <li><b>Pengujian Panjang Kata Sandi (Password):</b>
    <ul>
      <li><i>Syarat Sistem:</i> Panjang kata sandi minimal 6 karakter.</li>
      <li><i>Uji Batas Bawah (5 karakter):</i> Sistem menolak pendaftaran dan menampilkan pesan <i>"Password minimal 6 karakter"</i>.</li>
      <li><i>Uji Tepat Batas (6 karakter):</i> Sistem menerima kata sandi dan pendaftaran berhasil.</li>
    </ul>
  </li>
  <li><b>Pengujian Ukuran Unggah Foto Bukti Serah Terima Paket / Produk:</b>
    <ul>
      <li><i>Syarat Sistem:</i> Maksimal ukuran file 2 MB (2048 KB).</li>
      <li><i>Uji Tepat Batas (1.9 MB):</i> Berkas foto berhasil diunggah ke server.</li>
      <li><i>Uji Melebihi Batas (2.5 MB):</i> Sistem menolak berkas dengan pesan <i>"Ukuran foto melebihi batas 2MB"</i>.</li>
    </ul>
  </li>
  <li><b>Pengujian Kuantitas Pesanan Barang terhadap Sisa Stok:</b>
    <ul>
      <li><i>Syarat Sistem:</i> Jumlah beli minimal 1 pcs dan maksimal sejumlah sisa stok tersedia (misal sisa stok: 10 pcs).</li>
      <li><i>Uji Batas Maksimum (10 pcs):</i> Item berhasil ditambahkan ke keranjang belanja.</li>
      <li><i>Uji Melebihi Stok (11 pcs):</i> Sistem menolak dengan notifikasi <i>"Stok produk tidak mencukupi"</i>.</li>
    </ul>
  </li>
</ul>

<p class="caption">Tabel 4. 21 Matriks Pemilihan Metode Pengujian Black Box Sistem</p>

<table>
  <thead>
    <tr>
      <th style="width: 5%;">No</th>
      <th style="width: 25%;">Metode Black Box</th>
      <th style="width: 35%;">Fokus Pengujian</th>
      <th style="width: 35%;">Alasan Utama Pemilihan</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="center">1</td>
      <td><b>Equivalence Partitioning (EP)</b></td>
      <td>Pengelompokan data masukan ke dalam kelas data Valid dan Tidak Valid.</td>
      <td>Memaksimalkan efisiensi kasus uji formulir input tanpa mengurangi cakupan uji fungsionalitas utama.</td>
    </tr>
    <tr>
      <td class="center">2</td>
      <td><b>Boundary Value Analysis (BVA)</b></td>
      <td>Pengujian nilai pada batas ambang minimum, maksimum, dan batas ekstrim.</td>
      <td>Mendeteksi kesalahan logika perbandingan pada nilai batas (*edge cases*) dan menguji restriksi aturan bisnis sistem.</td>
    </tr>
  </tbody>
</table>

<br>

<div class="quote-box">
  <p style="text-indent:0; font-weight:bold; margin-bottom:4pt;">Draf Paragraf Laporan Skripsi (Siap Salin Ke Bab IV):</p>
  <p><i>"Pengujian perangkat lunak pada Sistem Informasi Penjualan Toko Dewi Lestari 2 dilakukan menggunakan metode Black Box Testing yang berfokus pada pengujian fungsionalitas sistem dari sudut pandang pengguna. Pengujian ini menerapkan 2 (dua) metode utama, yaitu Equivalence Partitioning (EP) dan Boundary Value Analysis (BVA). Metode Equivalence Partitioning digunakan untuk menguji validasi masukan data formulir seperti login, registrasi, alamat pengiriman, dan tiket pelacakan pesanan dengan membaginya ke dalam kelompok data valid dan tidak valid. Sementara itu, metode Boundary Value Analysis digunakan untuk menguji aturan bisnis pada nilai ambang batas (*boundary condition*), seperti batas minimal karakter kata sandi (minimal 6 karakter), batas jumlah kuantitas pesanan terhadap ketersediaan stok produk, serta batas maksimal ukuran berkas unggahan foto bukti serah terima (maksimal 2MB)."</i></p>
</div>

</body>
</html>`;

const mdContent = `# Metode Pengujian Black Box - Toko Dewi Lestari 2

### 4.5.1 Rencana dan Metode Pengujian Black Box

Pengujian perangkat lunak pada Sistem Informasi Penjualan Toko Dewi Lestari 2 dilakukan menggunakan pendekatan *Black Box Testing* (Pengujian Kotak Hitam). Pengujian ini berfokus pada evaluasi fungsionalitas dan keluaran (*output*) sistem dari sudut pandang pengguna tanpa menguji baris kode internal (*source code*). Metode pengujian *Black Box* yang digunakan dalam penelitian skripsi sistem informasi ini terdiri dari 2 (dua) metode utama, yaitu **Equivalence Partitioning (EP)** dan **Boundary Value Analysis (BVA)**.

---

#### 1. Metode Equivalence Partitioning (EP)
**Definisi:** *Equivalence Partitioning* adalah teknik pengujian dengan cara membagi domain data masukan (*input domain*) ke dalam kelas-kelas ekuivalensi (*equivalence classes*), yaitu kelompok data valid (diterima oleh sistem) dan kelompok data tidak valid (ditolak oleh sistem). Setiap kelas data diwakili oleh sampel data uji untuk memastikan sistem memberikan respon yang sesuai.

**Alasan Pemilihan Metode EP:**
- **Efisiensi Kasus Uji (*Test Efficiency*):** Penguji tidak perlu menguji seluruh kemungkinan data satu per satu, melainkan cukup mengambil perwakilan sampel data valid dan tidak valid sehingga proses pengujian skripsi menjadi efisien namun tetap komprehensif.
- **Sangat Efektif untuk Validasi Formulir Input & Otentikasi:** Sistem Informasi memiliki banyak formulir masukan seperti Form Login, Registrasi Akun, Form Alamat, dan Input Supplier. Metode EP memastikan bahwa data yang sah berhasil diproses dan data yang keliru/salah format ditolak dengan pesan peringatan (*error message*) yang sesuai.

**Contoh Penerapan Kasus Uji EP pada Sistem Toko Dewi Lestari 2:**
- **Pengujian Form Login/Registrasi:**
  - *Input Valid:* Mengisi email berformat benar (contoh: \`pelanggan@gmail.com\`). Hasil: Login/Registrasi berhasil diproses.
  - *Input Tidak Valid:* Mengisi email tanpa simbol '@' atau domain (contoh: \`pelanggangmail.com\`). Hasil: Sistem menolak dan menampilkan pesan peringatan *"Format email tidak valid"*.
- **Pengujian Pencarian Tiket Resi Pelacakan Pesanan:**
  - *Input Valid:* Memasukkan Kode Tiket Resi terdaftar (contoh: \`TKT-2026-081299\`). Hasil: Sistem menampilkan linimasa status pengiriman.
  - *Input Tidak Valid:* Memasukkan Kode Tiket Resi fiktif (contoh: \`TKT-9999-XXXXXX\`). Hasil: Sistem menampilkan notifikasi *"Nomor resi tidak ditemukan"*.

---

#### 2. Metode Boundary Value Analysis (BVA)
**Definisi:** *Boundary Value Analysis* adalah teknik pengujian yang berfokus pada nilai-nilai batas (*edge cases*) dari kisaran batasan data yang diperbolehkan oleh sistem. Pengujian dilakukan pada titik batas minimum, tepat pada batas minimum/maksimum, dan di luar batas minimum/maksimum.

**Alasan Pemilihan Metode BVA:**
- **Mendeteksi Error pada Titik Paling Rentan (*Edge Cases*):** Berdasarkan prinsip rekayasa perangkat lunak, bug terbanyak pada aplikasi terjadi pada nilai ambang batas (*boundary condition*) akibat kesalahan logika perbandingan percabangan (misalnya penggunaan operator \`<\` yang seharusnya \`<=\`).
- **Menguji Aturan Bisnis & Restriksi Sistem (*Business Rules*):** Sangat ideal untuk menguji fitur dengan parameter kriteria batas, seperti batas minimal panjang kata sandi, batas maksimal ukuran file gambar yang diunggah, serta batas jumlah kuantitas stok barang.

**Contoh Penerapan Kasus Uji BVA pada Sistem Toko Dewi Lestari 2:**
- **Pengujian Panjang Kata Sandi (Password):**
  - *Syarat Sistem:* Panjang kata sandi minimal 6 karakter.
  - *Uji Batas Bawah (5 karakter):* Sistem menolak pendaftaran dan menampilkan pesan *"Password minimal 6 karakter"*.
  - *Uji Tepat Batas (6 karakter):* Sistem menerima kata sandi dan pendaftaran berhasil.
- **Pengujian Ukuran Unggah Foto Bukti Serah Terima Paket / Produk:**
  - *Syarat Sistem:* Maksimal ukuran file 2 MB (2048 KB).
  - *Uji Tepat Batas (1.9 MB):* Berkas foto berhasil diunggah ke server.
  - *Uji Melebihi Batas (2.5 MB):* Sistem menolak berkas dengan pesan *"Ukuran foto melebihi batas 2MB"*.
- **Pengujian Kuantitas Pesanan Barang terhadap Sisa Stok:**
  - *Syarat Sistem:* Jumlah beli minimal 1 pcs dan maksimal sejumlah sisa stok tersedia (misal sisa stok: 10 pcs).
  - *Uji Batas Maksimum (10 pcs):* Item berhasil ditambahkan ke keranjang belanja.
  - *Uji Melebihi Stok (11 pcs):* Sistem menolak dengan notifikasi *"Stok produk tidak mencukupi"*.

---

### Tabel 4. 21 Matriks Pemilihan Metode Pengujian Black Box Sistem

| No | Metode Black Box | Fokus Pengujian | Alasan Utama Pemilihan |
| :---: | :--- | :--- | :--- |
| 1 | **Equivalence Partitioning (EP)** | Pengelompokan data masukan ke dalam kelas data Valid dan Tidak Valid. | Memaksimalkan efisiensi kasus uji formulir input tanpa mengurangi cakupan uji fungsionalitas utama. |
| 2 | **Boundary Value Analysis (BVA)** | Pengujian nilai pada batas ambang minimum, maksimum, dan batas ekstrim. | Mendeteksi kesalahan logika perbandingan pada nilai batas (*edge cases*) dan menguji restriksi aturan bisnis sistem. |

---

> **Draf Paragraf Laporan Skripsi (Siap Salin Ke Bab IV):**
>
> *"Pengujian perangkat lunak pada Sistem Informasi Penjualan Toko Dewi Lestari 2 dilakukan menggunakan metode Black Box Testing yang berfokus pada pengujian fungsionalitas sistem dari sudut pandang pengguna. Pengujian ini menerapkan 2 (dua) metode utama, yaitu Equivalence Partitioning (EP) dan Boundary Value Analysis (BVA). Metode Equivalence Partitioning digunakan untuk menguji validasi masukan data formulir seperti login, registrasi, alamat pengiriman, dan tiket pelacakan pesanan dengan membaginya ke dalam kelompok data valid dan tidak valid. Sementara itu, metode Boundary Value Analysis digunakan untuk menguji aturan bisnis pada nilai ambang batas (\`boundary condition\`), seperti batas minimal karakter kata sandi (minimal 6 karakter), batas jumlah kuantitas pesanan terhadap ketersediaan stok produk, serta batas maksimal ukuran berkas unggahan foto bukti serah terima (maksimal 2MB)."*
`;

const htmlPath = path.join(__dirname, '../Metode_Pengujian_Blackbox.html');
const docPath = path.join(__dirname, '../Metode_Pengujian_Blackbox.doc');
const mdPath = path.join(__dirname, '../Metode_Pengujian_Blackbox.md');

fs.writeFileSync(htmlPath, htmlContent, 'utf8');
fs.writeFileSync(docPath, htmlContent, 'utf8');
fs.writeFileSync(mdPath, mdContent, 'utf8');

console.log("Successfully created Metode_Pengujian_Blackbox files!");
