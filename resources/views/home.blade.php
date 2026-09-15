@extends('layouts.app')

@section('content')

    <div class="cart-icon" id="cartIcon" onclick="toggleCart()">
        🛒
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <div class="cart-header">
                <h3>🛒 Keranjang Belanja</h3>
                <button class="close-cart" onclick="toggleCart()">&times;</button>
            </div>

            <div class="success-message" id="successMessage">
                <h4>✅ Pesanan Berhasil Dikirim!</h4>
                <p>Terima kasih! Kami akan menghubungi Anda segera untuk konfirmasi pesanan.</p>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <p>🛒 Keranjang masih kosong</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem; color: #999;">Tambahkan produk untuk mulai
                        berbelanja</p>
                </div>
            </div>

            <div class="cart-total" id="cartTotal" style="display: none;">
                <h3>💰 Total: Rp <span id="totalAmount">0</span></h3>
            </div>

            <button class="checkout-btn" id="checkoutBtn" onclick="showCheckoutForm()" style="display: none;">
                🚀 Lanjut ke Checkout
            </button>

            <!-- Checkout Form -->
            <div class="checkout-form" id="checkoutForm">
                <h4>📝 Data Pembeli</h4>
                <form onsubmit="submitOrder(event)">
                    <div class="form-group">
                        <label for="buyerName">Nama Lengkap *</label>
                        <input type="text" id="buyerName" required placeholder="Masukkan nama lengkap"
                            value="{{ auth()->check() ? auth()->user()->name : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="buyerPhone">No. WhatsApp *</label>
                        <input type="tel" id="buyerPhone" required placeholder="08xxxxxxxxxx"
                            value="{{ auth()->check() ? (auth()->user()->phone ?: (auth()->user()->orders()->first() ? auth()->user()->orders()->first()->customer_phone : '')) : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="deliveryOption">Pilihan Pengiriman *</label>
                        <select id="deliveryOption" required onchange="updateDeliveryInfo()">
                            <option value="">-- Pilih Metode --</option>
                            <option value="pickup">Ambil di Toko (Gratis)</option>
                            <option value="delivery" id="deliveryOptionDelivery">Kurir Toko (Lokal Cimahi / Bandung)
                            </option>
                            <option value="expedition">Ekspedisi Pihak Ketiga (J&T, JNE, POS - Luar Kota / Luar Pulau)
                            </option>
                        </select>
                        <small id="deliveryHint" style="display:none; color:#15803d; margin-top:6px;">ℹ️ Ongkir Kurir Toko
                            Lokal: Rp 10.000 per 5 km dari lokasi toko.</small>
                        <small id="localDeliveryWarning"
                            style="display:none; color:#dc2626; background:#fef2f2; border:1px solid #fca5a5; padding:8px 10px; border-radius:6px; margin-top:8px; font-size:12px; line-height:1.4;"></small>
                    </div>

                    <!-- Section for Expedition Third Party Courier options -->
                    <div id="expeditionSection"
                        style="display:none; background:#f0fdf4; border:1px solid #bbf7d0; padding:15px; border-radius:10px; margin-bottom:15px;">
                        <h5 style="color:#166534; margin:0 0 10px 0; font-size:14px; font-weight:bold;">📦 Pengiriman
                            Ekspedisi Pihak Ketiga</h5>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label for="expeditionCourier" style="font-size:13px; font-weight:600; color:#374151;">Pilih
                                Pihak Ketiga / Kurir Ekspedisi *</label>
                            <select id="expeditionCourier" class="form-control"
                                style="width:100%; padding:8px; border-radius:8px; border:1px solid #2e7d32; outline:none;"
                                onchange="updateDeliveryInfo()">
                                <option value="jnt">🚚 J&T Express</option>
                                <option value="jne">🚚 JNE (Reguler)</option>
                                <option value="pos">🚚 POS Indonesia</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label for="expeditionZone" style="font-size:13px; font-weight:600; color:#374151;">Wilayah /
                                Zona Pengiriman *</label>
                            <select id="expeditionZone" class="form-control"
                                style="width:100%; padding:8px; border-radius:8px; border:1px solid #2e7d32; outline:none;"
                                onchange="updateDeliveryInfo()">
                                <option value="luar_kota_jawa">📍 Luar Kota Bandung (Pulau Jawa)</option>
                                <option value="luar_pulau_jawa">🏝️ Luar Pulau Jawa</option>
                            </select>
                        </div>

                        <div id="expeditionTariffInfo"
                            style="font-size:12px; color:#15803d; background:white; padding:8px 12px; border-radius:6px; border:1px dashed #86efac;">
                            ℹ️ <strong>Tarif Ekspedisi:</strong> <span id="expeditionRateText">Rp 18.000 / kg</span>
                            (Hitungan: <span id="expeditionWeightText">1 kg</span>)
                        </div>
                    </div>

                    <div id="deliveryMapSection" style="display:none;">
                        <div class="form-group">
                            <label>Lokasi Tujuan (Peta) *</label>
                            <div style="display:flex; gap:8px; margin-bottom:8px;">
                                <input type="text" id="deliveryMapSearch" style="flex:1;"
                                    placeholder="Cari alamat tujuan..."
                                    value="{{ (auth()->check() && auth()->user()->addresses->count() > 0) ? '' : (auth()->check() ? auth()->user()->primary_address : '') }}">
                                <button type="button" id="deliveryMapSearchBtn"
                                    style="padding:8px 12px; border:none; border-radius:8px; background:#2e7d32; color:white; cursor:pointer;">Cari</button>
                            </div>
                            <div id="deliveryMap"
                                style="height:280px; border-radius:10px; overflow:hidden; border:1px solid #ccc;"></div>
                            <small style="display:block; margin-top:8px; color:#666;">Klik peta atau cari alamat untuk
                                menandai lokasi.</small>
                        </div>

                        <div class="form-group" id="addressGroup" style="display: none;">
                            <label for="deliveryAddress">Alamat Lengkap *</label>

                            @if(auth()->check() && auth()->user()->addresses->count() > 0)
                                <div
                                    style="margin-bottom: 10px; background: #f0fdf4; padding: 10px 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                    <label for="savedAddressSelect"
                                        style="font-weight: bold; color: #166534; font-size: 12px; display: block; margin-bottom: 4px;">
                                        📍 Pilih dari Alamat Tersimpan:
                                    </label>
                                    <select id="savedAddressSelect"
                                        style="width: 100%; padding: 7px 10px; border-radius: 6px; border: 1px solid #2e7d32; font-size: 12.5px; outline: none; background: white; cursor: pointer;"
                                        onchange="selectSavedAddress(this.value)">
                                        <option value="">-- Pilih Alamat Pengiriman --</option>
                                        @foreach(auth()->user()->addresses as $savedAddr)
                                            <option value="{{ $savedAddr->address }}">
                                                {{ $savedAddr->is_primary ? '⭐' : '📌' }} {{ $savedAddr->label ?: 'Alamat' }}:
                                                {{ Str::limit($savedAddr->address, 50) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <textarea id="deliveryAddress" rows="3"
                                placeholder="Masukkan alamat lengkap pengiriman (Jalan, RT/RW, Kecamatan, Kota/Kab, Provinsi)">{{ (auth()->check() && auth()->user()->addresses->count() > 0) ? '' : (auth()->check() ? auth()->user()->primary_address : '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="deliveryCity">Kota/Daerah Tujuan *</label>
                            <input type="text" id="deliveryCity" placeholder="Masukkan / pilih kota tujuan di peta">
                        </div>

                        <div class="form-group" id="distanceGroup" style="display: none;">
                            <label for="deliveryDistance">Jarak Otomatis (km)</label>
                            <input type="text" id="deliveryDistance" readonly placeholder="Akan dihitung otomatis">
                        </div>
                    </div>

                    <!-- Card Perkiraan Waktu Tiba Barang -->
                    <div id="estimatedDeliveryCard"
                        style="display: none; background: #f0f9ff; border: 1.5px solid #0284c7; padding: 14px 16px; border-radius: 10px; margin-bottom: 16px;">
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; flex-wrap: wrap; gap: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 18px;">⏱️</span>
                                <h5 id="estCardTitle" style="margin: 0; color: #0369a1; font-size: 14px; font-weight: 800;">
                                    Perkiraan Waktu Tiba Barang</h5>
                            </div>
                            <span id="estCardBadge"
                                style="font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; background: #dcfce7; color: #166534; border: 1px solid #86efac;">Tiba
                                Hari Ini</span>
                        </div>
                        <div id="estCardEtaText" style="font-size: 13px; color: #0f172a; margin-bottom: 4px;">
                            <!-- Perkiraan Sampai: Senin, 24 Agustus 2026 -->
                        </div>
                        <div id="estCardTimeText" style="font-size: 12.5px; color: #334155;">
                            <!-- Pukul 10:00 - 17:00 WIB [1-2 Hari Kerja] -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="orderNotes">Catatan Pesanan</label>
                        <textarea id="orderNotes" rows="2" placeholder="Catatan khusus (opsional)"></textarea>
                    </div>

                    <!-- Pilihan Metode Pembayaran -->
                    <div class="form-group"
                        style="background: #f8fafc; padding: 14px; border-radius: 10px; border: 1px solid #cbd5e1; margin-bottom: 16px;">
                        <label
                            style="font-weight: bold; color: #1e293b; font-size: 13.5px; display: block; margin-bottom: 10px;">
                            💳 Pilih Metode Pembayaran *
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label
                                style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 2px solid #2e7d32; border-radius: 8px; background: #f0fdf4; cursor: pointer; font-weight: 600; font-size: 13px;">
                                <input type="radio" name="payment_method" value="midtrans" checked
                                    onchange="togglePaymentMethodInfo('midtrans')">
                                <span>💳 Pembayaran Online / QRIS / E-Wallet (Midtrans)</span>
                            </label>
                            <label
                                style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: 2px solid #0284c7; border-radius: 8px; background: #f0f9ff; cursor: pointer; font-weight: 600; font-size: 13px;">
                                <input type="radio" name="payment_method" value="transfer_bank"
                                    onchange="togglePaymentMethodInfo('transfer_bank')">
                                <span>🏦 Transfer Bank Manual (No. Rekening BCA - Verifikasi Kasir)</span>
                            </label>
                        </div>
                        <div id="transferBankInfo"
                            style="display: none; margin-top: 10px; background: #ffffff; padding: 10px 12px; border-radius: 6px; border: 1px dashed #0284c7; color: #0369a1; font-size: 12px;">
                            ℹ️ Transfer ke <strong>BCA 123-456-7890 a.n Toko Dewi Lestari 2</strong>. Setelah checkout, Anda
                            dapat mengunggah struk atau menekan <strong>Simulasi Bayar Instan</strong> untuk verifikasi
                            kasir.
                        </div>
                    </div>

                    <script>
                        function togglePaymentMethodInfo(method) {
                            const info = document.getElementById('transferBankInfo');
                            if (info) {
                                info.style.display = method === 'transfer_bank' ? 'block' : 'none';
                            }
                        }
                    </script>

                    <div class="order-summary">
                        <h4>📋 Ringkasan Pesanan</h4>
                        <div id="orderSummaryList"></div>
                        <div class="order-item">
                            <span>Biaya Pengiriman:</span>
                            <span id="deliveryCost">Rp 0</span>
                        </div>
                        <div class="order-item" id="summaryEstRow"
                            style="display: none; border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 4px;">
                            <span style="font-size: 12.5px; color: #475569;">⏱️ Perkiraan Tiba:</span>
                            <span id="summaryEstText" style="font-weight: bold; color: #0284c7; font-size: 12.5px;">-</span>
                        </div>
                        <div class="order-item">
                            <span>Total Pembayaran:</span>
                            <span id="finalTotal">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-order-btn">
                        Bayar sekarang
                    </button>
                    <button onclick="checkPaymentStatus()"> ✅ Cek Status Pembayaran </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Header Component -->
    <div id="header"></div>

    <!-- Content Component -->
    <div id="content"></div>

    <!-- Footer Component -->
    <div id="footer"></div>

    <!-- Load Components via jQuery -->
    <script>
        $(document).ready(function () {
            $("#header").load("header.html");
            $("#content").load("content.html");
            $("#footer").load("footer.html");
        });
    </script>

    <!-- Leaflet / OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Custom JavaScript -->
    <script src="js/script.js?v=20260629-1"></script>
    //
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>🍃 Toko Dewi Lestari 2</h1>
                <p>Kripik Tempe Premium - Kriuknya Bikin Nagih!</p>
                <a href="#products" class="cta-button">🛒 Belanja Sekarang</a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products">
        <div class="container">
            <h2 class="section-title"> Jajan Makanan khas Bandung DIDIEU 🤘</h2>

            <!-- Search & Filter Controls -->
            <div class="product-search-wrapper">
                <div class="search-bar-box">
                    <div class="search-input-group">
                        <span class="search-icon-inside">🔍</span>
                        <input type="text" id="productSearchInput" class="product-search-input"
                            placeholder="Cari produk (misal: Tempe, Dodol, Koin)..." oninput="window.filterProducts()">
                        <button type="button" id="clearSearchBtn" class="clear-search-btn" style="display: none;"
                            onclick="window.clearSearch()">✖</button>
                    </div>
                    <select id="productSortSelect" class="product-sort-select" onchange="window.filterProducts()">
                        <option value="default">↕️ Urutan Default</option>
                        <option value="price-low">💰 Harga: Terendah</option>
                        <option value="price-high">💎 Harga: Tertinggi</option>
                        <option value="name-asc">🔤 Nama: A - Z</option>
                    </select>
                </div>
                <div id="searchStats" class="search-stats-text" style="display: none;"></div>
            </div>

            <!-- Loading State -->
            <div id="products-loading" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: #2e7d32;">⏳ Memuat produk...</p>
            </div>

            <!-- Products Grid -->
            <div class="product-grid" id="product-grid" style="display: none;"></div>

            <!-- No Products Message -->
            <div id="no-products" style="display: none;">
                <div class="no-products-box">
                    <div style="font-size: 3.5rem; margin-bottom: 0.5rem;">📦</div>
                    <h3 id="no-products-title" style="color: #2e7d32; font-size: 1.3rem; margin-bottom: 0.5rem;">Belum ada
                        produk tersedia</h3>
                    <p id="no-products-subtitle" style="color: #666; font-size: 0.95rem;">Produk yang Anda cari saat ini
                        tidak ditemukan.</p>
                    <button type="button" id="resetSearchBtn" class="reset-search-btn" style="display: none;"
                        onclick="window.clearSearch()">🔄 Tampilkan Semua Produk</button>
                </div>
            </div>

            <div
                style="text-align: center; margin-top: 3rem; padding: 2rem; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <h3 style="color: #2e7d32; margin-bottom: 1rem; font-weight: 700;">💡 Informasi Penting</h3>
                <p style="color: #2e7d32; font-size: 1.1rem; line-height: 1.8; font-weight: 500;">
                    ✅ Minimal pembelian 1 pack<br>
                    ✅ Harga belum termasuk ongkos kirim<br>
                    ✅ Produk fresh dan higienis<br>
                    ✅ Garansi kepuasan 100%
                </p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>🏪 Tentang Toko Dewi Lestari 2</h2>
                    <p>Toko Dewi Lestari adalah produsen kripik tempe premium yang dibuat dengan resep rahasia turun
                        temurun. Kami menggunakan tempe segar berkualitas tinggi dan bumbu pilihan untuk menghasilkan
                        keripik yang kriuk dan nikmat.</p>
                    <p>Setiap gigitan memberikan pengalaman rasa yang tak terlupakan, membuatmu ingin terus kembali lagi dan
                        lagi!</p>
                    <ul class="features">
                        <li>Dibuat dari tempe segar berkualitas tinggi</li>
                        <li>Bumbu rahasia yang bikin nagih</li>
                        <li>Tekstur super kriuk dan renyah</li>
                        <li>Tanpa pengawet berbahaya</li>
                        <li>Dikemas higienis dan fresh</li>
                        <li>Halal dan aman dikonsumsi</li>
                    </ul>
                </div>
                <div class="about-image">
                    <div style="background: rgba(255,235,59,0.2); padding: 3rem; border-radius: 20px; text-align: center;">
                        <div style="font-size: 8rem; margin-bottom: 1rem;">👍</div>
                        <h3 style="font-size: 2rem; margin-bottom: 1rem;">100% Halal & Aman</h3>
                        <p style="font-size: 1.2rem;">Diproduksi dengan standar kebersihan tinggi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">📞 Hubungi Kami</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <h3>📍 Informasi Kontak</h3>
                    <p>📱 <strong>WhatsApp:</strong> 081221956759</p>
                    <p>📧 <strong>Email:</strong> info@dewilestari2.com</p>
                    <p>📍 <strong>Alamat:</strong> Jl. Raya Cimindi No.59, RW.2, Cigugur Tengah, Kec. Cimahi Tengah, Kota
                        Cimahi, Jawa Barat 40535</p>
                    <p>🕒 <strong>Jam Operasional:</strong> 08:00 - 20:00 WIB</p>

                    <div
                        style="background: #fff; padding: 1.5rem; border-radius: 10px; margin: 1.5rem 0; border: 2px solid #2e7d32;">
                        <h4 style="color: #2e7d32; margin-bottom: 1rem;">🚚 Info Pengiriman</h4>
                        <p style="color: #666; margin-bottom: 0.5rem;">• Ambil di toko: <strong>GRATIS</strong></p>
                        <p style="color: #666; margin-bottom: 0.5rem;">• Kirim dalam kota: <strong>Rp 10.000/5KM</strong>
                        </p>
                        <p style="color: #666; margin-bottom: 0.5rem;">• Pengiriman setiap hari</p>
                        <p style="color: #666;">• Estimasi tiba: 1-2 jam</p>
                    </div>
                </div>

                <div
                    style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <h3 style="color: #2e7d32; margin-bottom: 1.5rem; text-align: center;">💬 Cara Memesan</h3>
                    <div style="text-align: left; color: #666; line-height: 1.8;">
                        <p style="margin-bottom: 1rem;"><strong>1.</strong> Pilih produk dan jumlah yang diinginkan</p>
                        <p style="margin-bottom: 1rem;"><strong>2.</strong> Klik "Tambah ke Keranjang"</p>
                        <p style="margin-bottom: 1rem;"><strong>3.</strong> Review pesanan di keranjang</p>
                        <p style="margin-bottom: 1rem;"><strong>4.</strong> pilih pengiriman</p>
                        <p style="margin-bottom: 1rem;"><strong>5.</strong> Bayar sekarang</p>
                        <p style="margin-bottom: 1rem;"><strong>6.</strong> Tunggu konfirmasi dan lakukan pembayaran</p>
                        <p><strong>7.</strong> Pesanan siap diantar/diambil! 🎉</p>
                    </div>

                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="https://wa.me/6281221956759?text=Halo%20Toko%20Dewi%20Lestari%202!%20Saya%20mau%20tanya%20tentang%20produk"
                            style="display: inline-block; background: #25d366; color: white; padding: 12px 24px; text-decoration: none; border-radius: 25px; font-weight: bold;"
                            target="_blank">
                            💬 Chat WhatsApp Langsung
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Load products from database
        window.allProductsData = [];

        window.loadProductsFromDB = function () {
            const loadingDiv = document.getElementById('products-loading');
            const noProductsDiv = document.getElementById('no-products');

            fetch('/api/products')
                .then(response => response.json())
                .then(data => {
                    loadingDiv.style.display = 'none';

                    if (data.success && data.data && data.data.length > 0) {
                        window.allProductsData = data.data;
                        window.filterProducts();
                    } else {
                        window.allProductsData = [];
                        document.getElementById('product-grid').style.display = 'none';
                        noProductsDiv.style.display = 'block';
                        document.getElementById('no-products-title').textContent = 'Belum ada produk tersedia';
                        document.getElementById('no-products-subtitle').textContent = 'Silakan cek kembali nanti.';
                        document.getElementById('resetSearchBtn').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    loadingDiv.style.display = 'none';
                    noProductsDiv.style.display = 'block';
                });
        };

        window.filterProducts = function () {
            const searchInput = document.getElementById('productSearchInput');
            const clearBtn = document.getElementById('clearSearchBtn');
            const sortSelect = document.getElementById('productSortSelect');
            const searchStats = document.getElementById('searchStats');
            const productGrid = document.getElementById('product-grid');
            const noProductsDiv = document.getElementById('no-products');
            const resetSearchBtn = document.getElementById('resetSearchBtn');
            const noProductsTitle = document.getElementById('no-products-title');
            const noProductsSubtitle = document.getElementById('no-products-subtitle');

            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const sortValue = sortSelect ? sortSelect.value : 'default';

            if (clearBtn) {
                clearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }

            let filtered = window.allProductsData.filter(product => {
                if (!query) return true;
                const nameMatch = (product.name || '').toLowerCase().includes(query);
                const descMatch = (product.description || '').toLowerCase().includes(query);
                const variantMatch = (product.variants || []).some(v => (v.weight || '').toLowerCase().includes(query));
                return nameMatch || descMatch || variantMatch;
            });

            if (sortValue === 'price-low') {
                filtered.sort((a, b) => {
                    const minA = Math.min(...a.variants.map(v => v.price));
                    const minB = Math.min(...b.variants.map(v => v.price));
                    return minA - minB;
                });
            } else if (sortValue === 'price-high') {
                filtered.sort((a, b) => {
                    const maxA = Math.max(...a.variants.map(v => v.price));
                    const maxB = Math.max(...b.variants.map(v => v.price));
                    return maxB - maxA;
                });
            } else if (sortValue === 'name-asc') {
                filtered.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            }

            if (searchStats) {
                if (query.length > 0) {
                    searchStats.style.display = 'block';
                    searchStats.textContent = `Menampilkan ${filtered.length} produk untuk "${query}"`;
                } else {
                    searchStats.style.display = 'none';
                }
            }

            if (filtered.length > 0) {
                productGrid.style.display = 'grid';
                noProductsDiv.style.display = 'none';
                window.renderProducts(filtered);
            } else {
                productGrid.style.display = 'none';
                productGrid.innerHTML = '';
                noProductsDiv.style.display = 'block';
                if (query.length > 0) {
                    noProductsTitle.textContent = `Produk "${query}" Tidak Ditemukan`;
                    noProductsSubtitle.textContent = 'Coba gunakan kata kunci lain atau periksa ejaan Anda.';
                    if (resetSearchBtn) resetSearchBtn.style.display = 'inline-block';
                } else {
                    noProductsTitle.textContent = 'Belum ada produk tersedia';
                    noProductsSubtitle.textContent = 'Silakan cek kembali nanti.';
                    if (resetSearchBtn) resetSearchBtn.style.display = 'none';
                }
            }
        };

        window.renderProducts = function (products) {
            const productGrid = document.getElementById('product-grid');
            productGrid.innerHTML = '';

            products.forEach((product, index) => {
                const selectedVariant = product.variants.find(v => v.stock > 0) || product.variants[0];
                const productCard = document.createElement('div');
                productCard.className = 'product-card';

                const optionsHtml = product.variants.map(v => {
                    const isSelected = (v === selectedVariant) ? 'selected' : '';
                    const isDisabled = v.stock === 0 ? 'disabled style="color: #ccc;"' : '';
                    const priceText = v.stock === 0 ? '(Stok Habis)' : `- Rp ${parseInt(v.price).toLocaleString('id-ID')}`;
                    return `<option value="${v.id}" data-price="${v.price}" data-stock="${v.stock}" data-weight="${v.weight}" ${isSelected} ${isDisabled}>${v.weight} ${priceText}</option>`;
                }).join('');

                let badgeTag = '<div class="product-badge-tag">🍃 ALAMI</div>';
                const pNameLower = product.name.toLowerCase();
                if (index === 0 || pNameLower.includes('original') || pNameLower.includes('sagu')) {
                    badgeTag = '<div class="product-badge-tag" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); font-weight:800;">🔥 BEST SELLER</div>';
                } else if (pNameLower.includes('pedas')) {
                    badgeTag = '<div class="product-badge-tag" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">🌶️ HOT PEDAS</div>';
                } else if (pNameLower.includes('wajik') || pNameLower.includes('garut')) {
                    badgeTag = '<div class="product-badge-tag" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%);">⭐ FAVORIT</div>';
                }

                productCard.innerHTML = `
                                    ${badgeTag}
                                    <div class="product-icon">
                                        ${product.image_path
                        ? `<img src="${product.image_path}" alt="${product.name}">`
                        : '<span style="color: #999; font-size: 2rem;">📦</span>'}
                                    </div>
                                    <h3>${product.name}</h3>

                                    <div class="product-variant" style="margin-bottom: 0.8rem; text-align: left;">
                                        <label style="font-weight: bold; color: #166534; display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">⚖️ Pilih Ukuran:</label>
                                        <select class="variant-select" onchange="window.updateCardVariant(this)">
                                            ${optionsHtml}
                                        </select>
                                    </div>

                                    <p>${product.description}</p>
                                    <div class="price">Rp ${parseInt(selectedVariant.price).toLocaleString('id-ID')}</div>
                                    <p class="stock-display">Stok tersedia: ${selectedVariant.stock}</p>

                                    ${selectedVariant.stock > 0 && selectedVariant.stock <= 10
                        ? `<p class="stock-warning" style="margin:0.5rem 0 0 0; color:#d32f2f; font-weight:bold;">⚠️ Stok hampir habis (${selectedVariant.stock} bungkus tersisa)</p>`
                        : ''}

                                    <div class="product-action-section" style="margin-top: 1rem;">
                                        ${product.total_stock > 0 ? `
                                            <div class="product-quantity">
                                                <label>Jumlah:</label>
                                                <input type="number" class="qty-input" min="1" max="${Math.min(50, selectedVariant.stock)}" value="1" style="width: 60px; padding: 5px; border-radius: 5px; border: 1px solid #ccc; text-align: center;">
                                            </div>
                                            <button type="button" class="order-btn" onclick="window.addToCartFromCard(this, '${product.name.replace(/'/g, "\\'")}')">
                                                🛒 Tambah ke Keranjang
                                            </button>
                                        ` : `
                                            <div style="background: #ffcdd2; color: #c62828; padding: 10px; border-radius: 8px; font-weight: bold; text-align: center;">
                                                ❌ Stok Habis
                                            </div>
                                        `}
                                    </div>
                                    `;

                productGrid.appendChild(productCard);
            });
        };

        window.clearSearch = function () {
            const searchInput = document.getElementById('productSearchInput');
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            window.filterProducts();
        };

        window.updateCardVariant = function (select) {
            const card = select.closest('.product-card');
            const selectedOption = select.options[select.selectedIndex];
            const price = Number(selectedOption.getAttribute('data-price'));
            const stock = Number(selectedOption.getAttribute('data-stock'));

            // Update price display
            const priceDiv = card.querySelector('.price');
            if (priceDiv) {
                priceDiv.textContent = 'Rp ' + price.toLocaleString('id-ID');
            }

            // Update stock display
            const stockP = card.querySelector('.stock-display');
            if (stockP) {
                stockP.textContent = 'Stok tersedia: ' + stock;
            }

            // Update qty input max limit
            const qtyInput = card.querySelector('.qty-input');
            if (qtyInput) {
                qtyInput.max = Math.min(50, stock);
                if (parseInt(qtyInput.value) > stock) {
                    qtyInput.value = stock;
                }
            }

            // Update warning if stock is low
            let warningP = card.querySelector('.stock-warning');
            if (stock > 0 && stock <= 10) {
                if (!warningP) {
                    warningP = document.createElement('p');
                    warningP.className = 'stock-warning';
                    warningP.style.cssText = 'margin:0.5rem 0 0 0; color:#d32f2f; font-weight:bold;';
                    card.insertBefore(warningP, card.querySelector('.product-action-section'));
                }
                warningP.textContent = `⚠️ Stok hampir habis (${stock} bungkus tersisa)`;
            } else {
                if (warningP) {
                    warningP.remove();
                }
            }
        };

        window.addToCartFromCard = function (button, productName) {
            const card = button.closest('.product-card');
            const select = card.querySelector('.variant-select');
            if (!select) return;

            const selectedOption = select.options[select.selectedIndex];
            const variantId = selectedOption.value;
            const price = Number(selectedOption.getAttribute('data-price'));
            const weight = selectedOption.getAttribute('data-weight');
            const stock = Number(selectedOption.getAttribute('data-stock'));

            const qtyInput = card.querySelector('.qty-input');
            if (!qtyInput) return;
            const quantity = parseInt(qtyInput.value);

            if (isNaN(quantity) || quantity < 1 || quantity > 50) {
                showNotification('Jumlah harus antara 1-50 bungkus', 'error');
                return;
            }
            if (quantity > stock) {
                showNotification('Jumlah melebihi stok yang tersedia', 'error');
                return;
            }

            // Create or get dummy hidden input for the original addToCart logic to read from
            const tempInputId = 'temp-qty-' + variantId;
            let tempInput = document.getElementById(tempInputId);
            if (!tempInput) {
                tempInput = document.createElement('input');
                tempInput.type = 'hidden';
                tempInput.id = tempInputId;
                document.body.appendChild(tempInput);
            }
            tempInput.value = quantity;

            // Add to cart with formatted name
            window.addToCart(variantId, productName + ' (' + weight + ')', price, tempInputId, weight);
        };

        setTimeout(window.loadProductsFromDB, 100);
    </script>
@endsection