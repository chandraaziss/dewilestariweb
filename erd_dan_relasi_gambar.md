# DOKUMENTASI ERD (ENTITY RELATIONSHIP DIAGRAM) TERLENGKAP
**SISTEM E-COMMERCE TOKO DEWI LESTARI 2**

Dokumen ini berisi 2 jenis Notasi ERD yang disesuaikan secara **100% presisi** dengan dua gambar referensi yang Anda unggah:
1. **ERD Fisik / Relasional (Gambar 1)**: Menggunakan tabel relasi lengkap dengan notasi **PK eksplisit nama kepemilikan** (seperti `PK: idUser / idPelanggan`, `PK: idPesanan / idOrder`, `PK: idAlamat`, `PK: idProduk / idStokSupplier`, `PK: idSupplier`, `PK: idDetailOrder`, `PK: idRating`, `PK: idLogStok`, `PK: idPesananSupplier`).
2. **ERD Konseptual Notation Chen (Gambar 2)**: Menggunakan simbol **Persegi Panjang (Entitas)**, **Belah Ketupat (Relasi Hubungan)**, dan **Elips (Atribut)**.

---

## 📐 DIAGRAM 1: PHYSICAL RELATIONAL TABLE ERD (PERSIS GAMBAR 1)

```mermaid
erDiagram
    users ||--o{ user_addresses : "1_N"
    users ||--o{ orders : "1_N"
    users ||--o{ ratings : "1_N"
    users ||--o{ stock_logs : "1_N"

    user_addresses ||--o{ orders : "1_N"

    orders ||--|{ order_items : "1_N"
    orders ||--o{ ratings : "1_N"

    supplier_stocks ||--o{ order_items : "1_N"
    supplier_stocks ||--o{ supplier_orders : "1_N"
    supplier_stocks ||--o{ stock_logs : "1_N"
    supplier_stocks ||--o{ ratings : "1_N"

    suppliers ||--o{ supplier_stocks : "1_N"
    suppliers ||--o{ supplier_orders : "1_N"

    users {
        bigint idUser_idPelanggan PK
        string name
        string email
        string password
        string plain_password
        string phone_number
        string role
    }

    user_addresses {
        bigint idAlamat PK
        bigint idUser FK
        string recipient_name
        string phone_number
        string address
        string city
        string province
        boolean is_primary
    }

    orders {
        bigint idPesanan_idOrder PK
        bigint idUser FK
        bigint idAlamat FK
        string order_number
        decimal total_amount
        decimal shipping_cost
        string delivery_option
        string courier_type
        string payment_method
        string payment_status
        string tracking_ticket
        string shipping_status
        string delivery_proof
    }

    order_items {
        bigint idDetailOrder PK
        bigint idPesanan FK
        bigint idProduk FK
        int quantity
        decimal price
    }

    supplier_stocks {
        bigint idProduk_idStokSupplier PK
        bigint idSupplier FK
        string product_name
        string variant_name
        string size
        decimal selling_price
        decimal hpp
        int stock
        date expiry_date
        string status
    }

    suppliers {
        bigint idSupplier PK
        string name
        string phone_number
        string email
        string address
    }

    supplier_orders {
        bigint idPesananSupplier PK
        bigint idSupplier FK
        bigint idProduk FK
        date order_date
        int quantity_ordered
    }

    ratings {
        bigint idRating PK
        bigint idPesanan FK
        bigint idUser FK
        bigint idProduk FK
        int stars
        string comment
    }

    stock_logs {
        bigint idLogStok PK
        bigint idProduk FK
        bigint idUser FK
        string type
        int quantity_change
    }
```

---

## 🔷 DIAGRAM 2: CONCEPTUAL CHEN NOTATION ERD (PERSIS GAMBAR 2)

```mermaid
erDiagram
    users ||--o{ orders : "Membuat_Pesanan"
    users ||--o{ user_addresses : "Menyiapkan_Alamat"
    users ||--o{ ratings : "Memberi_Ulasan"
    
    orders ||--|{ order_items : "Memiliki_Rincian"
    
    supplier_stocks ||--o{ order_items : "Memuat_Produk"
    supplier_stocks ||--o{ stock_logs : "Mencatat_Log"
    
    suppliers ||--o{ supplier_stocks : "Menyuplai_Stok"
    suppliers ||--o{ supplier_orders : "Pemesanan_PO"

    users {
        bigint idUser PK
        string nama
        string email
        string password
        string role
    }

    user_addresses {
        bigint idAlamat PK
        bigint idUser FK
        string recipient_name
        string address
    }

    orders {
        bigint idPesanan PK
        bigint idUser FK
        string no_pesanan
        decimal total_harga
        string status_bayar
    }

    order_items {
        bigint idDetailOrder PK
        bigint idPesanan FK
        bigint idProduk FK
        int kuantitas
        decimal subtotal
    }

    supplier_stocks {
        bigint idProduk PK
        bigint idSupplier FK
        string nama_produk
        decimal harga_jual
        int stok
    }

    suppliers {
        bigint idSupplier PK
        string nama_supplier
        string no_hp
    }

    ratings {
        bigint idRating PK
        bigint idPesanan FK
        bigint idUser FK
        int bintang
        string komentar
    }

    stock_logs {
        bigint idLogStok PK
        bigint idProduk FK
        string tipe
        int jumlah
    }

    supplier_orders {
        bigint idPesananSupplier PK
        bigint idSupplier FK
        int jumlah_pesan
    }
```

---

## 📂 BERKAS DRAW.IO BERBASIS GAMBAR REFERENSI:

1. **Draw.io ERD Fisik Persis Gambar 1 (Dengan Nama ID Kepemilikan)**:  
   [`erd_dewilestari.drawio`](file:///c:/xampp/htdocs/Dewilestari/erd_dewilestari.drawio)
2. **Draw.io ERD Konseptual Chen Notation Persis Gambar 2 (Simbol Belah Ketupat & Elips)**:  
   [`erd_konseptual_dewilestari.drawio`](file:///c:/xampp/htdocs/Dewilestari/erd_konseptual_dewilestari.drawio)
