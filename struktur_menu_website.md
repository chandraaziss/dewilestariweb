# DOKUMENTASI STRUKTUR MENU WEBSITE TERLENGKAP
**SISTEM E-COMMERCE TOKO DEWI LESTARI 2**

---

## 🌳 DIAGRAM HIERARKI STRUKTUR MENU (SESUAI 100% ARSITEKTUR WEBSITE)

```text
                               DIAGRAM STRUKTUR MENU SISTEM TOKO DEWI LESTARI 2
                                                      │
                                   [ SISTEM WEB E-COMMERCE DEWI LESTARI 2 ]
                                                      │
         ┌────────────────────────────────────────────┼────────────────────────────────────────────┐
         │                                            │                                            │
[ AREA PELANGGAN (FRONT-END) ]         [ AREA ADMIN & PENGELOLA TOKO (BACK-END) ]        [ AREA KURIR PENGIRIMAN (COURIER PANEL) ]
         │                                            │                                            │
         ├─► Autentikasi Pelanggan                    ├─► Autentikasi Admin                        └─► Pengantaran Kurir Toko
         │   • Registrasi Akun Pelanggan              │   • Form Login Admin                           • Dashboard Kurir Toko
         │   • Login Email & Google OAuth             │   • Session Logout Admin                       • Daftar Tugas Pengantaran Hari Ini
         │   • Logout Session Pelanggan               │                                                • Rincian Penerima & Peta Alamat Tujuan
         │                                            ├─► Pengelolaan Transaksi                        • Update Status & Proof Photo
         ├─► Katalog & Belanja                        │   • Monitoring Status Transaksi                • Log Riwayat Pengiriman Selesai
         │   • Catalog & Live Search Filter           │   • Detail Pesanan & Alamat
         │   • Varian Berat & Expired Date            │   • Verifikasi Transfer Bank Manual
         │   • Keranjang Belanja & Subtotal           │   • Notifikasi Selisih Transfer
         │   • Live Chat Widget CS                    │   • Penugasan Kurir & Resi Lacak
         │   └─► Dashboard Akun Saya                  │   • Pembatalan & Hapus Transaksi
         │       • Edit Profil Saya & Avatar          │
         │       • Buku Alamat (Primary/Hapus)        ├─► Pengelolaan Stok FIFO
         │       • Riwayat Pesanan Penjualan          │   • Katalog Stok Varian Produk
         │       • Form Penilaian & Rating            │   • Tambah Batch Stok Supplier
         │                                            │   • Queue Batch Input Sekaligus
         ├─► Transaksi & Checkout                     │   • Launching Batch Produk Publik
         │   • Form Checkout & Kurir                  │   • Kelola Barang Expired & Afkir
         │   • Midtrans Snap (VA/QRIS)                │
         │   • Upload Bukti Transfer Manual           ├─► Supplier & PO Pengadaan
         │   • Re-upload Bukti Kirim Ditolak          │   • Master Data Supplier Mitra
         │                                            │   • Surat Pemesanan Barang (PO)
         └─► Pelacakan & Retur Barang                 │   • Kirim Format PO via WhatsApp
             • Form Lacak Tiket Resi                  │   • Cetak Invoice PO Supplier
             • Status Tracking Ongkir Logistics       │   • Tiket Retur Barang Cacat Supplier
             • Pengajuan Retur Barang Cacat           │
                                                      └─► Laporan, Rating & Chat CS
                                                          • Laporan Penjualan & Laba Toko
                                                          • Download Laporan PDF (Hitam/Warna)
                                                          • Export Rekap Data Excel/CSV
                                                          • Master Pelanggan & Moderasi Rating
                                                          • Live Chat Inbox & Unread Badge
```

---

## 📋 DETIL MODUL & HIRARKI FITUR ASLI WEBSITE TOKO DEWI LESTARI 2

### 1. AREA PELANGGAN (FRONT-END ONLINE STORE)

#### A. Autentikasi Pelanggan
- **Registrasi Akun Pelanggan**: pendaftaran akun baru via form web.
- **Login Email & Google OAuth**: autentikasi manual dan sekali klik akun Google.
- **Logout Session Pelanggan**: pengakhiran sesi login pengguna.

#### B. Katalog & Belanja
- **Catalog & Live Search Filter**: pencarian kata kunci dan filter kategori instan via AJAX.
- **Varian Berat & Expired Date**: estimasi tanggal kadaluarsa FIFO dan pilihan varian kemasan.
- **Keranjang Belanja & Subtotal**: drawer keranjang belanja interaktif.
- **Live Chat Widget CS**: obrolan melayang dengan customer service toko.
- **Dashboard Akun Saya**:
  - *Edit Profil Saya & Avatar*: update nama lengkap, telepon, dan foto profil.
  - *Buku Alamat (Primary/Hapus)*: manajemen alamat tujuan pengiriman.
  - *Riwayat Pesanan Penjualan*: riwayat pesanan (Pending, Terverifikasi, Dikemas, Dikirim, Selesai, Ditolak).
  - *Form Penilaian & Rating*: ulasan skor bintang 1-5 dan masukan produk.

#### C. Transaksi & Checkout
- **Form Checkout & Kurir**: pemilihan opsi pengiriman kurir lokal/ekspedisi.
- **Midtrans Snap (VA/QRIS)**: pembayaran otomatis QRIS, GoPay, dan Virtual Account Bank.
- **Upload Bukti Transfer Manual**: pengunggahan struk bukti pembayaran transfer manual.
- **Re-upload Bukti Kirim Ditolak**: pengunggahan ulang bukti transfer jika pesanan sebelumnya ditolak admin.

#### D. Pelacakan & Retur Barang
- **Form Lacak Tiket Resi**: pencarian status pesanan menggunakan Nomor Tiket Pelacakan.
- **Status Tracking Ongkir Logistics**: pelacakan posisi paket dan informasi kurir penanggung jawab.
- **Pengajuan Retur Barang Cacat**: klaim pengembalian produk bermasalah/rusak.

---

### 2. AREA ADMIN & PENGELOLA TOKO (BACK-END CONTROL PANEL)

#### A. Autentikasi Admin
- **Form Login Admin**: pintu masuk otorisasi khusus staf & pengelola toko.
- **Session Logout Admin**: keluar dari panel kontrol admin.

#### B. Pengelolaan Transaksi
- **Monitoring Status Transaksi**: daftar seluruh pesanan penjualan masuk.
- **Detail Pesanan & Alamat**: rincian barang, jumlah, dan snapshot alamat pengiriman.
- **Verifikasi Transfer Bank Manual**: konfirmasi keabsahan dana transfer manual.
- **Notifikasi Selisih Transfer**: pemberitahuan jika jumlah transfer kurang/salah.
- **Penugasan Kurir & Resi Lacak**: penugasan kurir toko atau input resi pelacakan.
- **Pembatalan & Hapus Transaksi**: pembatalan pesanan yang gagal/batal.

#### C. Pengelolaan Stok FIFO
- **Katalog Stok Varian Produk**: daftar stok per varian berat/rasa.
- **Tambah Batch Stok Supplier**: pendaftaran masukan stok produk baru.
- **Queue Batch Input Sekaligus**: masukan antrean stok secara massal.
- **Launching Batch Produk Publik**: aktivasi publik agar stok dapat dipesan di katalog.
- **Kelola Barang Expired & Afkir**: monitoring produk kedaluwarsa dan pemusnahan stok.

#### D. Supplier & PO Pengadaan
- **Master Data Supplier Mitra**: database rekanan suplier toko.
- **Surat Pemesanan Barang (PO)**: penerbitan dokumen resmi Purchase Order.
- **Kirim Format PO via WhatsApp**: pengiriman rincian PO langsung ke nomor WhatsApp suplier.
- **Cetak Invoice PO Supplier**: cetak nota tagihan PO pengadaan.
- **Tiket Retur Barang Cacat Supplier**: klaim pengembalian barang retur ke suplier.

#### E. Laporan, Rating & Chat CS
- **Laporan Penjualan & Laba Toko**: ringkasan omset penjualan, HPP, dan laba bersih.
- **Download Laporan PDF (Hitam/Warna)**: cetak berkas laporan PDF formal.
- **Export Rekap Data Excel/CSV**: unduh data laporan ke format lembar kerja Excel.
- **Master Pelanggan & Moderasi Rating**: kelola akun pelanggan dan ulasan produk.
- **Live Chat Inbox & Unread Badge**: ruang balasan obrolan langsung dengan pelanggan.

---

### 3. AREA KURIR PENGIRIMAN (COURIER PANEL)

#### A. Pengantaran Kurir Toko
- **Dashboard Kurir Toko**: ringkasan beban pengantaran hari ini.
- **Daftar Tugas Pengantaran Hari Ini**: daftar alamat pengiriman yang harus diproses.
- **Rincian Penerima & Peta Alamat Tujuan**: rincian nama penerima, HP, dan titik lokasi peta.
- **Update Status & Proof Photo**: ubah status menjadi 'Diterima' dan unggah foto Bukti Penerimaan Barang (*Proof of Delivery*).
- **Log Riwayat Pengiriman Selesai**: arsip pengiriman yang telah berhasil diantar.

---

### 📂 Berkas Terkini:

1. **File Visual Draw.io (Persis Format Box Gambar Referensi)**:  
   [`struktur_menu_dewilestari.drawio`](file:///c:/xampp/htdocs/Dewilestari/struktur_menu_dewilestari.drawio)
2. **File Dokumentasi Markdown**:  
   [`struktur_menu_website.md`](file:///c:/xampp/htdocs/Dewilestari/struktur_menu_website.md)
