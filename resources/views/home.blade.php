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
    <p id="discountRow" style="display:none;">
        Diskon: Rp <span id="discountAmount">0</span>
    </p>
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
                        <input type="text" id="buyerName" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="form-group">
                        <label for="buyerPhone">No. WhatsApp *</label>
                        <input type="tel" id="buyerPhone" required placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="deliveryOption">Pilihan Pengiriman *</label>
                        <select id="deliveryOption" required onchange="updateDeliveryInfo()">
                            <option value="">-- Pilih Metode --</option>
                            <option value="pickup">Ambil di Toko (Gratis)</option>
                            <option value="delivery">Kirim ke Alamat (+Rp 10.000)</option>
                        </select>
                    </div>

                    <div class="form-group" id="addressGroup" style="display: none;">
                        <label for="deliveryAddress">Alamat Lengkap *</label>
                        <textarea id="deliveryAddress" rows="3"
                            placeholder="Masukkan alamat lengkap untuk pengiriman"></textarea>
                    </div>
                    <input type="text" id="city" placeholder="Masukkan Kota">

                    <div class="form-group">
                        <label for="orderNotes">Catatan Pesanan</label>
                        <textarea id="orderNotes" rows="2" placeholder="Catatan khusus (opsional)"></textarea>
                    </div>

                    <div class="order-summary">
                        <h4>📋 Ringkasan Pesanan</h4>
                        <div id="orderSummaryList"></div>
                        <div class="order-item">
                            <span>Biaya Pengiriman:</span>
                            <span id="deliveryCost">Rp 0</span>
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

    <!-- Custom JavaScript -->
    <script src="js/script.js"></script>
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
        <h2 class="section-title">🌟 Varian Kripik Tempe Premium</h2>

        <!-- Loading State -->
        <div id="products-loading" style="text-align: center; padding: 3rem;">
            <p style="font-size: 1.2rem; color: #2e7d32;">⏳ Memuat produk...</p>
        </div>

        <!-- Products Grid -->
        <div class="product-grid" id="product-grid" style="display: none;"></div>

        <!-- No Products Message -->
        <div id="no-products" style="display: none; text-align: center; padding: 3rem;">
            <p style="font-size: 1.2rem; color: #666;">📦 Belum ada produk tersedia</p>
        </div>

        <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: rgba(46, 125, 50, 0.1); border-radius: 15px;">
            <h3 style="color: #2e7d32; margin-bottom: 1rem;">💡 Informasi Penting</h3>
            <p style="color: #666; font-size: 1.1rem; line-height: 1.6;">
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

                <div style="background: #fff; padding: 1.5rem; border-radius: 10px; margin: 1.5rem 0; border: 2px solid #2e7d32;">
                    <h4 style="color: #2e7d32; margin-bottom: 1rem;">🚚 Info Pengiriman</h4>
                    <p style="color: #666; margin-bottom: 0.5rem;">• Ambil di toko: <strong>GRATIS</strong></p>
                    <p style="color: #666; margin-bottom: 0.5rem;">• Kirim dalam kota: <strong>Rp 10.000</strong></p>
                    <p style="color: #666; margin-bottom: 0.5rem;">• Pengiriman setiap hari (kecuali Minggu)</p>
                    <p style="color: #666;">• Estimasi tiba: 1-2 hari kerja</p>
                </div>
            </div>

            <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <h3 style="color: #2e7d32; margin-bottom: 1.5rem; text-align: center;">💬 Cara Memesan</h3>
                <div style="text-align: left; color: #666; line-height: 1.8;">
                    <p style="margin-bottom: 1rem;"><strong>1.</strong> Pilih produk dan jumlah yang diinginkan</p>
                    <p style="margin-bottom: 1rem;"><strong>2.</strong> Klik "Tambah ke Keranjang"</p>
                    <p style="margin-bottom: 1rem;"><strong>3.</strong> Review pesanan di keranjang</p>
                    <p style="margin-bottom: 1rem;"><strong>4.</strong> Isi data pembeli dan pilih pengiriman</p>
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
// Load products from database
window.loadProductsFromDB = function () {
    const productGrid = document.getElementById('product-grid');
    const loadingDiv = document.getElementById('products-loading');
    const noProductsDiv = document.getElementById('no-products');

     fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.success && data.data && data.data.length > 0) {
                productGrid.style.display = 'grid';
                productGrid.innerHTML = '';

                data.data.forEach((product) => {
                    const qtyId = 'qty-' + product.id;
                    const productCard = document.createElement('div');
                    productCard.className = 'product-card';

                   productCard.innerHTML = `
                   <div class="product-icon">
                    ${product.image_path 
                     ? `<img src="${product.image_path}" alt="${product.name}">` 
                        : '<span style="color: #999; font-size: 2rem;">📦</span>'}
                          </div>
                     <h3>${product.name}</h3>
                     <p style="font-weight:bold;color:#2e7d32;">
                    ⚖️ Berat: ${product.category}
                    </p>
                     <p>${product.description}</p>
                     <div class="price">Rp ${parseInt(product.price).toLocaleString('id-ID')}</div>
                     <p>Stok tersedia: ${product.stock}</p>
                      ${product.stock > 0 ? `
                        <div class="product-quantity">
                            <label>Jumlah:</label>
                            <input type="number" id="${qtyId}" min="1" max="${Math.min(50, product.stock)}" value="1">
                        </div>
                        <button class="order-btn" onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price}, '${qtyId}')">
                            🛒 Tambah ke Keranjang
                        </button>
                    ` : `
        <div style="background: #ffcdd2; color: #c62828; padding: 10px; border-radius: 8px; font-weight: bold;">
            ❌ Stok Habis
        </div>
    `}
`;

                    productGrid.appendChild(productCard);
                });
            } else {
                noProductsDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            loadingDiv.style.display = 'none';
            noProductsDiv.style.display = 'block';
        });
};

setTimeout(window.loadProductsFromDB, 100);
</script>
@endsection