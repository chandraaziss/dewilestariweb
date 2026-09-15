/* ===================================
   TOKO DEWI LESTARI 2 - JAVASCRIPT
   Shopping Cart & Checkout System
   IMPROVED VERSION with localStorage
   =================================== */

function showNotification(message, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed; top:70px; right:20px; z-index:999999; display:flex; flex-direction:column; gap:10px; font-family:sans-serif;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bgColor = type === 'error' ? '#ef4444' : (type === 'success' ? '#22c55e' : '#3b82f6');
    toast.style.cssText = `background:${bgColor}; color:white; padding:12px 18px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-size:14px; font-weight:600; min-width:250px; max-width:350px; opacity:0; transition:all 0.3s ease;`;
    toast.textContent = message;
    
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta && meta.content) {
        return meta.content;
    }
    const input = document.querySelector('input[name="_token"]');
    if (input && input.value) {
        return input.value;
    }
    return '';
}

function handleFetchResponse(response) {
    if (response.status === 419) {
        alert('Sesi Anda telah berakhir (419 Page Expired). Halaman akan diperbarui otomatis.');
        location.reload();
        return Promise.reject(new Error('Session Expired (419)'));
    }
    const contentType = response.headers.get('content-type') || '';
    if (!response.ok || !contentType.includes('application/json')) {
        return response.text().then(text => {
            console.error('API Error Response:', response.status, text);
            if (response.status === 419) {
                alert('Sesi Anda telah berakhir (419 Page Expired). Halaman akan diperbarui otomatis.');
                location.reload();
            }
            try {
                const data = JSON.parse(text);
                if (data && data.message) {
                    return Promise.reject(new Error(data.message));
                }
            } catch(e) {}
            return Promise.reject(new Error('Server Error (' + response.status + ')'));
        });
    }
    return response.json();
}

function parseWeightToGrams(weightStr) {
    if (!weightStr && weightStr !== 0) return 0;
    const str = String(weightStr).trim().toLowerCase();
    if (str.includes('kg') || str.includes('kilogram')) {
        const match = str.match(/([0-9]+(?:\.[0-9]+)?)/);
        return match ? Math.round(parseFloat(match[1]) * 1000) : 0;
    }
    const match = str.match(/([0-9]+(?:\.[0-9]+)?)/);
    return match ? Math.round(parseFloat(match[1])) : 0;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getTotalWeight() {
    if (!Array.isArray(cart)) return 0;
    return cart.reduce((total, item) => {
        const itemWeight = parseWeightToGrams(item.weight);
        const qty = Number(item.quantity) || 0;
        return total + (itemWeight * qty);
    }, 0);
}

window.showNotification = showNotification;
window.parseWeightToGrams = parseWeightToGrams;
window.escapeHtml = escapeHtml;
window.getTotalWeight = getTotalWeight;

// Global Variables
let cart = [];
let cartCount = 0;
let totalAmount = 0;
let deliveryCost = 0;
let storeCoordinates = null;
let customerCoordinates = null;
let deliveryMap = null;
let storeMarker = null;
let customerMarker = null;
let deliveryRouteDistanceKm = 0;
let lastDetectedAddressObj = null;


/* ===================================
   INITIALIZATION
   =================================== */
document.addEventListener('DOMContentLoaded', function () {
    // Load cart from localStorage
    loadCartFromStorage();
    updateCartDisplay();
    initDeliveryMap();
    updateDeliveryInfo();

    const deliveryAddressInput = document.getElementById('deliveryAddress');
    const deliveryCityInput = document.getElementById('deliveryCity');
    if (deliveryAddressInput) {
        deliveryAddressInput.addEventListener('input', function () {
            validateLocalDeliveryCoverage(null, this.value, deliveryCityInput ? deliveryCityInput.value : '');
        });
    }
    if (deliveryCityInput) {
        deliveryCityInput.addEventListener('input', function () {
            validateLocalDeliveryCoverage(null, deliveryAddressInput ? deliveryAddressInput.value : '', this.value);
        });
    }

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');

            if (href === '#' || href.length <= 1) {
                return;
            }

            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Close cart when clicking outside
    document.getElementById('cartModal').addEventListener('click', function (e) {
        if (e.target === this) {
            toggleCart();
        }
    });

    // Keyboard shortcuts (ESC to close cart)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const cartModal = document.getElementById('cartModal');
            if (cartModal.style.display === 'flex') {
                toggleCart();
            }
        }
    });
});

/* ===================================
   LOCALSTORAGE FUNCTIONS
   =================================== */

// Save cart to localStorage
function saveCartToStorage() {
    try {
        localStorage.setItem('dewilestari_cart', JSON.stringify(cart));
        localStorage.setItem('dewilestari_cart_timestamp', Date.now());
    } catch (error) {
        console.error('Error saving cart:', error);
    }
}

// Load cart from localStorage
function loadCartFromStorage() {
    try {
        const savedCart = localStorage.getItem('dewilestari_cart');
        const timestamp = localStorage.getItem('dewilestari_cart_timestamp');
        
        // Check if cart exists and not expired (24 hours)
        if (savedCart && timestamp) {
            const age = Date.now() - parseInt(timestamp);
            const maxAge = 24 * 60 * 60 * 1000; // 24 hours
            
            if (age < maxAge) {
                cart = JSON.parse(savedCart);
                console.log('✅ Cart loaded from storage:', cart);
            } else {
                // Cart expired, clear it
                clearCartStorage();
                console.log('⏰ Cart expired, cleared');
            }
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        cart = [];
    }
}

// Clear cart from localStorage
function clearCartStorage() {
    try {
        localStorage.removeItem('dewilestari_cart');
        localStorage.removeItem('dewilestari_cart_timestamp');
    } catch (error) {
        console.error('Error clearing cart:', error);
    }
}

// Reset cart state in memory and storage
function resetCartState() {
    cart = [];
    cartCount = 0;
    totalAmount = 0;
    deliveryCost = 0;
    clearCartStorage();
    updateCartDisplay();
}

/* ===================================
   CART FUNCTIONS
   =================================== */

function addToCart(
    productId,
    productName,
    price,
    qtyInputId,
    weight
) {
    const quantityInput = document.getElementById(qtyInputId);
    
    if (!quantityInput) {
        showNotification('Error: Input quantity tidak ditemukan', 'error');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);

    // Validation
    if (isNaN(quantity) || quantity < 1 || quantity > 50) {
        showNotification('Jumlah harus antara 1-50 bungkus', 'error');
        return;
    }

    const normalizedProductId = Number(productId);
    const normalizedWeight = parseWeightToGrams(weight);

    // Check if product variant already exists in cart by product ID and name
    const existingItemIndex = cart.findIndex(item => Number(item.id) === normalizedProductId && item.name === productName);

    if (existingItemIndex > -1) {
        const newQuantity = cart[existingItemIndex].quantity + quantity;
        if (newQuantity > 50) {
            showNotification('Total maksimal 50 bungkus per produk', 'error');
            return;
        }
        cart[existingItemIndex].quantity = newQuantity;
    } else {
        cart.push({
            id: normalizedProductId,
            name: productName,
            price: Number(price),
            quantity: quantity,
            weight: normalizedWeight
        });
    }

    // Save to localStorage
    saveCartToStorage();
    updateCartDisplay();

    // Reset quantity input
    quantityInput.value = 1;

    // Show success notification
    showNotification(`${productName} (${quantity}x) berhasil ditambahkan ke keranjang!`, 'success');

    // Animate cart icon
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.style.transform = 'scale(1.2)';
        setTimeout(() => {
            cartIcon.style.transform = 'scale(1)';
        }, 200);
    }
}

window.addToCart = addToCart;

// Update cart display
function updateCartDisplay() {
    const cartItems = Array.isArray(cart) ? cart : [];
    cartCount = cartItems.reduce((total, item) => total + (Number(item.quantity) || 0), 0);
    const subtotal = cartItems.reduce(
        (total, item) => total + ((Number(item.price) || 0) * (Number(item.quantity) || 0)),
        0
    );

    totalAmount = subtotal;

    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }

    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (!cartItemsContainer || !cartTotal || !checkoutBtn) {
        console.error('Cart elements not found');
        return;
    }

    if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <p>🛒 Keranjang masih kosong</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem; color: #999;">Tambahkan produk untuk mulai berbelanja</p>
            </div>
        `;
        cartTotal.style.display = 'none';
        checkoutBtn.style.display = 'none';
    } else {
        let cartHTML = '';
        cartItems.forEach((item, index) => {
            const itemSubtotal = item.price * item.quantity;
            cartHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4>${escapeHtml(item.name)}</h4>
                        <p>Rp ${item.price.toLocaleString('id-ID')} x ${item.quantity}</p>
                        <p style="font-weight: bold; color: #2e7d32;">Subtotal: Rp ${itemSubtotal.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity(${index})" title="Kurangi">-</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="quantity-btn" onclick="increaseQuantity(${index})" title="Tambah">+</button>
                        <button class="quantity-btn remove" onclick="confirmRemoveItem(${index})" title="Hapus">×</button>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = cartHTML;
        document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('id-ID');
        cartTotal.style.display = 'block';
        checkoutBtn.style.display = 'block';
    }

    updateDeliveryInfo();
}

// Increase quantity
function increaseQuantity(index) {
    if (cart[index].quantity >= 50) {
        showNotification('Maksimal 50 bungkus per produk', 'error');
        return;
    }
    cart[index].quantity++;
    saveCartToStorage();
    updateCartDisplay();
}

// Decrease quantity
function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        saveCartToStorage();
        updateCartDisplay();
    } else {
        confirmRemoveItem(index);
    }
}

// Confirm remove item
function confirmRemoveItem(index) {
    const itemName = cart[index].name;
    if (confirm(`Hapus "${itemName}" dari keranjang?`)) {
        removeItem(index);
    }
}

// Remove item from cart
function removeItem(index) {
    const itemName = cart[index].name;
    cart.splice(index, 1);
    saveCartToStorage();
    updateCartDisplay();
    showNotification(`${itemName} dihapus dari keranjang`, 'info');

    // Hide checkout form if cart becomes empty
    if (cart.length === 0) {
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.style.display = 'none';
        }
    }
}

// Clear entire cart
function clearCart() {
    if (cart.length === 0) return;
    
    if (confirm('Kosongkan semua keranjang?')) {
        resetCartState();
        showNotification('Keranjang dikosongkan', 'info');
    }
}

// Toggle cart modal
function toggleCart() {
    const cartModal = document.getElementById('cartModal');
    if (!cartModal) return;
    
    if (cartModal.style.display === 'flex') {
        cartModal.style.display = 'none';
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.style.display = 'none';
        }
    } else {
        cartModal.style.display = 'flex';
        cartModal.classList.add('show');
    }
}

/* ===================================
   CHECKOUT FUNCTIONS
   =================================== */

// Show checkout form
function showCheckoutForm() {
    if (cart.length === 0) {
        showNotification('Keranjang kosong!', 'error');
        return;
    }

    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.style.display = 'block';
        checkoutForm.scrollIntoView({ behavior: 'smooth' });
        updateDeliveryInfo();
    }
}

// Update delivery info
function updateDeliveryInfo() {
    const deliveryOption = document.getElementById('deliveryOption');
    const addressGroup = document.getElementById('addressGroup');
    const distanceGroup = document.getElementById('distanceGroup');
    const deliveryCostElement = document.getElementById('deliveryCost');
    const deliveryAddressInput = document.getElementById('deliveryAddress');
    const deliveryCityInput = document.getElementById('deliveryCity');
    const deliveryDistanceInput = document.getElementById('deliveryDistance');
    const deliveryMapSection = document.getElementById('deliveryMapSection');
    const deliveryHint = document.getElementById('deliveryHint');
    const deliveryOptionDelivery = document.getElementById('deliveryOptionDelivery');
    const expeditionSection = document.getElementById('expeditionSection');
    const expeditionCourierSelect = document.getElementById('expeditionCourier');
    const expeditionZoneSelect = document.getElementById('expeditionZone');
    const expeditionRateText = document.getElementById('expeditionRateText');
    const expeditionWeightText = document.getElementById('expeditionWeightText');

    const totalWeight = getTotalWeight();

    if (!deliveryOption) return;

    if (deliveryOptionDelivery) {
        deliveryOptionDelivery.disabled = false;
    }

    const val = deliveryOption.value;

    if (val === 'pickup') {
        if (deliveryHint) deliveryHint.style.display = 'none';
        if (expeditionSection) expeditionSection.style.display = 'none';
        if (deliveryMapSection) deliveryMapSection.style.display = 'none';
        if (addressGroup) addressGroup.style.display = 'none';
        if (distanceGroup) distanceGroup.style.display = 'none';
        if (deliveryAddressInput) deliveryAddressInput.required = false;
        if (deliveryCityInput) deliveryCityInput.required = false;
        if (deliveryDistanceInput) deliveryDistanceInput.required = false;
        deliveryCost = 0;
    } else if (val === 'delivery' || val === 'expedition') {
        if (deliveryHint) deliveryHint.style.display = 'none';
        if (expeditionSection) expeditionSection.style.display = val === 'expedition' ? 'block' : 'none';
        if (deliveryMapSection) {
            deliveryMapSection.style.display = 'block';
            setTimeout(() => {
                if (deliveryMap) deliveryMap.invalidateSize();
            }, 200);
        }
        if (addressGroup) addressGroup.style.display = 'block';

        if (val === 'expedition') {
            if (distanceGroup) distanceGroup.style.display = 'none';
            const courier = expeditionCourierSelect ? expeditionCourierSelect.value : 'jnt';
            const zone = expeditionZoneSelect ? expeditionZoneSelect.value : 'luar_kota_jawa';
            const weightKg = Math.max(1, Math.ceil(totalWeight / 1000));
            const rates = {
                'jnt': { 'luar_kota_jawa': 18000, 'luar_pulau_jawa': 35000 },
                'jne': { 'luar_kota_jawa': 20000, 'luar_pulau_jawa': 40000 },
                'pos': { 'luar_kota_jawa': 16000, 'luar_pulau_jawa': 32000 }
            };
            const ratePerKg = (rates[courier] && rates[courier][zone]) ? rates[courier][zone] : 18000;
            deliveryCost = weightKg * ratePerKg;

            if (expeditionRateText) expeditionRateText.textContent = `Rp ${ratePerKg.toLocaleString('id-ID')} / kg`;
            if (expeditionWeightText) expeditionWeightText.textContent = `${weightKg} kg (${totalWeight} gram)`;
        }

        // Auto geocode account address if coordinates not set yet and an address is selected
        const savedSelect = document.getElementById('savedAddressSelect');
        if (!customerCoordinates && deliveryAddressInput && deliveryAddressInput.value.trim() && (!savedSelect || savedSelect.value)) {
            autoGeocodeAccountAddress(deliveryAddressInput.value.trim());
        }

        // Validate local delivery coverage
        const isLocalCoverage = validateLocalDeliveryCoverage(lastDetectedAddressObj);

        if (val === 'delivery') {
            const hasLocationInfo = customerCoordinates || (deliveryAddressInput && deliveryAddressInput.value.trim().length > 0) || (deliveryCityInput && deliveryCityInput.value.trim().length > 0);
            if (hasLocationInfo && !isLocalCoverage) {
                showNotification('Kurir Toko hanya melayani pengiriman untuk wilayah Kota Cimahi dan Kota Bandung. Silakan pilih Ekspedisi Pihak Ketiga.', 'error');
                deliveryOption.value = 'expedition';
                updateDeliveryInfo();
                return;
            }

            const distanceValue = deliveryRouteDistanceKm > 0 ? deliveryRouteDistanceKm : 1;
            deliveryCost = Math.max(1, Math.ceil(distanceValue / 5)) * 10000;

            if (distanceGroup) distanceGroup.style.display = 'block';
            if (deliveryDistanceInput) {
                deliveryDistanceInput.value = deliveryRouteDistanceKm > 0 ? `${deliveryRouteDistanceKm.toFixed(1)} km` : '1.0 km';
            }
        }

        if (deliveryAddressInput) deliveryAddressInput.required = true;
        if (deliveryCityInput) deliveryCityInput.required = true;
    } else {
        if (deliveryHint) deliveryHint.style.display = 'none';
        if (expeditionSection) expeditionSection.style.display = 'none';
        if (deliveryMapSection) deliveryMapSection.style.display = 'none';
        if (addressGroup) addressGroup.style.display = 'none';
        if (distanceGroup) distanceGroup.style.display = 'none';
        if (deliveryAddressInput) deliveryAddressInput.required = false;
        if (deliveryCityInput) deliveryCityInput.required = false;
        if (deliveryDistanceInput) deliveryDistanceInput.required = false;
        deliveryCost = 0;
    }

    if (deliveryCostElement) {
        deliveryCostElement.textContent = `Rp ${deliveryCost.toLocaleString('id-ID')}`;
    }
    
    updateOrderSummary();
    updateEstimatedDeliveryDisplay();
}

function formatIndonesianDate(dateObj) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[dateObj.getDay()];
    const dayNum = dateObj.getDate();
    const monthName = months[dateObj.getMonth()];
    const year = dateObj.getFullYear();
    
    return `${dayName}, ${dayNum} ${monthName} ${year}`;
}

function updateEstimatedDeliveryDisplay() {
    const deliveryOption = document.getElementById('deliveryOption');
    const estimatedCard = document.getElementById('estimatedDeliveryCard');
    const cardTitle = document.getElementById('estCardTitle');
    const cardBadge = document.getElementById('estCardBadge');
    const cardEtaText = document.getElementById('estCardEtaText');
    const cardTimeText = document.getElementById('estCardTimeText');
    const summaryEstRow = document.getElementById('summaryEstRow');
    const summaryEstText = document.getElementById('summaryEstText');

    if (!deliveryOption || !estimatedCard) return;

    const val = deliveryOption.value;
    if (!val) {
        estimatedCard.style.display = 'none';
        if (summaryEstRow) summaryEstRow.style.display = 'none';
        return;
    }

    const now = new Date();
    const currentHour = now.getHours();

    let title = '';
    let badgeText = '';
    let badgeBg = '#dcfce7';
    let badgeColor = '#166534';
    let etaText = '';
    let timeText = '';
    let summaryText = '';

    if (val === 'pickup') {
        const readyDate = new Date(now.getTime() + 30 * 60000);
        const startHourStr = String(readyDate.getHours()).padStart(2, '0');
        const startMinStr = String(readyDate.getMinutes()).padStart(2, '0');

        title = '🏬 Ambil Langsung di Toko';
        badgeText = '⚡ Siap Instan (30-60 Menit)';
        badgeBg = '#dcfce7';
        badgeColor = '#15803d';
        etaText = `📍 Siap Diambil: <strong>Hari Ini (${formatIndonesianDate(now)})</strong>`;
        timeText = `🕒 Estimasi Jam: <strong>Pukul ${startHourStr}:${startMinStr} - 20:00 WIB</strong> (Langsung dari Toko)`;
        summaryText = `Hari Ini (${startHourStr}:${startMinStr} WIB)`;
    } else if (val === 'delivery') {
        const dist = (typeof deliveryRouteDistanceKm !== 'undefined' && deliveryRouteDistanceKm > 0) ? deliveryRouteDistanceKm : 1.0;
        let targetDate = new Date(now);
        let timeRangeStr = '';
        let durationLabel = '1 - 2 Jam (Kurir Toko Instan)';

        if (currentHour >= 8 && currentHour < 18) {
            // Diproses langsung saat jam operasional toko (08:00 - 18:00 WIB)
            const estStart = new Date(now.getTime() + 60 * 60000); // 1 jam dari sekarang
            const estEnd = new Date(now.getTime() + 120 * 60000);  // 2 jam dari sekarang
            const startH = String(estStart.getHours()).padStart(2, '0');
            const startM = String(estStart.getMinutes()).padStart(2, '0');
            const endH = String(estEnd.getHours()).padStart(2, '0');
            const endM = String(estEnd.getMinutes()).padStart(2, '0');
            timeRangeStr = `Pukul <strong>${startH}:${startM} - ${endH}:${endM} WIB</strong>`;
        } else {
            // Diproses luar jam operasional (malam hari)
            if (currentHour >= 18) {
                targetDate.setDate(targetDate.getDate() + 1);
            }
            timeRangeStr = `Pukul <strong>09:00 - 10:00 WIB</strong> (1-2 jam setelah toko buka)`;
        }

        const isToday = targetDate.toDateString() === now.toDateString();
        const dateStr = isToday ? `Hari Ini (${formatIndonesianDate(targetDate)})` : formatIndonesianDate(targetDate);

        title = `🛵 Kurir Toko (Jarak: ${dist.toFixed(1)} km)`;
        badgeText = isToday ? '🚀 Tiba Hari Ini (1-2 Jam)' : '📅 Tiba Besok Pagi';
        badgeBg = isToday ? '#dcfce7' : '#fef3c7';
        badgeColor = isToday ? '#15803d' : '#92400e';
        etaText = `📅 Perkiraan Sampai: <strong>${dateStr}</strong>`;
        timeText = `🕒 Jam Tiba: ${timeRangeStr} <span style="color:#64748b; font-size:11.5px;">[${durationLabel}]</span>`;
        summaryText = isToday ? `Hari Ini (${timeRangeStr.replace(/<[^>]*>/g, '')})` : `Besok (${timeRangeStr.replace(/<[^>]*>/g, '')})`;
    } else if (val === 'expedition') {
        const courierSelect = document.getElementById('expeditionCourier');
        const zoneSelect = document.getElementById('expeditionZone');
        const c = courierSelect ? courierSelect.value : 'jnt';
        const z = zoneSelect ? zoneSelect.value : 'luar_kota_jawa';

        let courierName = '🚚 J&T Express';
        let minDays = 1;
        let maxDays = 2;
        let jamKurir = 'Pukul 10:00 - 17:00 WIB';

        if (c === 'jnt') {
            courierName = '🚚 J&T Express';
            jamKurir = 'Pukul 10:00 - 17:00 WIB';
            minDays = z === 'luar_kota_jawa' ? 1 : 2;
            maxDays = z === 'luar_kota_jawa' ? 2 : 4;
        } else if (c === 'jne') {
            courierName = '📦 JNE Express (REG)';
            jamKurir = 'Pukul 11:00 - 18:00 WIB';
            minDays = z === 'luar_kota_jawa' ? 2 : 3;
            maxDays = z === 'luar_kota_jawa' ? 3 : 5;
        } else if (c === 'pos') {
            courierName = '📮 POS Indonesia (Kilat)';
            jamKurir = 'Pukul 09:00 - 16:00 WIB';
            minDays = z === 'luar_kota_jawa' ? 2 : 3;
            maxDays = z === 'luar_kota_jawa' ? 3 : 6;
        }

        const dateMin = new Date(now);
        dateMin.setDate(dateMin.getDate() + minDays);
        const dateMax = new Date(now);
        dateMax.setDate(dateMax.getDate() + maxDays);

        const dateRangeText = (minDays === maxDays)
            ? formatIndonesianDate(dateMin)
            : `${formatIndonesianDate(dateMin)} s/d ${formatIndonesianDate(dateMax)}`;

        title = `${courierName}`;
        badgeText = `📦 Est. ${minDays}-${maxDays} Hari`;
        badgeBg = '#e0f2fe';
        badgeColor = '#0369a1';
        etaText = `📅 Perkiraan Sampai: <strong>${dateRangeText}</strong>`;
        timeText = `🕒 Estimasi Jam Tiba Kurir: <strong>${jamKurir}</strong> <span style="color:#64748b; font-size:11.5px;">[${minDays}-${maxDays} Hari Kerja]</span>`;
        summaryText = `${formatIndonesianDate(dateMin)} (${jamKurir})`;
    }

    if (cardTitle) cardTitle.textContent = title;
    if (cardBadge) {
        cardBadge.textContent = badgeText;
        cardBadge.style.background = badgeBg;
        cardBadge.style.color = badgeColor;
    }
    if (cardEtaText) cardEtaText.innerHTML = etaText;
    if (cardTimeText) cardTimeText.innerHTML = timeText;
    estimatedCard.style.display = 'block';

    if (summaryEstRow && summaryEstText) {
        summaryEstRow.style.display = 'flex';
        summaryEstText.innerHTML = summaryText;
    }
}

// Update order summary
function updateOrderSummary() {
    const orderSummaryList = document.getElementById('orderSummaryList');
    const finalTotal = document.getElementById('finalTotal');

    if (!orderSummaryList || !finalTotal) return;
    if (cart.length === 0) return;

    let summaryHTML = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        summaryHTML += `
            <div class="order-item">
                <span>${escapeHtml(item.name)} x${item.quantity}</span>
                <span>Rp ${itemTotal.toLocaleString('id-ID')}</span>
            </div>
        `;
    });

    orderSummaryList.innerHTML = summaryHTML;
    const grandTotal = totalAmount + deliveryCost;
    finalTotal.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
}

// Submit order
function submitOrder(event) {
    event.preventDefault();

    const buyerName = document.getElementById('buyerName').value.trim();
    const buyerPhone = document.getElementById('buyerPhone').value.trim();
    const deliveryOption = document.getElementById('deliveryOption').value;
    const deliveryAddress = document.getElementById('deliveryAddress').value.trim();
    const deliveryCity = document.getElementById('deliveryCity').value.trim();
    const deliveryDistance = document.getElementById('deliveryDistance').value.trim();
    const orderNotes = document.getElementById('orderNotes').value.trim();
    const expeditionCourier = document.getElementById('expeditionCourier') ? document.getElementById('expeditionCourier').value : '';
    const expeditionZone = document.getElementById('expeditionZone') ? document.getElementById('expeditionZone').value : '';

    // Validation
    if (!buyerName || !buyerPhone || !deliveryOption) {
        showNotification('Mohon lengkapi data yang wajib diisi!', 'error');
        return;
    }

    if ((deliveryOption === 'delivery' || deliveryOption === 'expedition') && !deliveryAddress) {
        showNotification('Alamat pengiriman wajib diisi!', 'error');
        return;
    }

    if ((deliveryOption === 'delivery' || deliveryOption === 'expedition') && !deliveryCity) {
        showNotification('Kota/daerah tujuan wajib diisi!', 'error');
        return;
    }

    // Phone number validation
    const cleanPhone = buyerPhone.replace(/[- ]/g, '');
    if (!/^(08|62)[0-9]{8,}$/.test(cleanPhone)) {
        showNotification('Format nomor WhatsApp tidak valid!', 'error');
        return;
    }

   const totalWeight = getTotalWeight();

    if (deliveryOption === 'delivery') {
        const fullAddressText = (deliveryAddress || '') + ' ' + (deliveryCity || '');
        const isLocalCoverage = checkIsLocalDeliveryArea(lastDetectedAddressObj, fullAddressText, deliveryCity);
        if (!isLocalCoverage) {
            showNotification('Kurir Toko hanya melayani pengiriman untuk wilayah Kota Cimahi dan Kota Bandung. Silakan pilih Ekspedisi Pihak Ketiga.', 'error');
            const deliveryOptionSelect = document.getElementById('deliveryOption');
            if (deliveryOptionSelect) {
                deliveryOptionSelect.value = 'expedition';
                updateDeliveryInfo();
            }
            return;
        }

        if (!customerCoordinates) {
            showNotification('Pilih lokasi tujuan di peta untuk menghitung ongkir otomatis.', 'error');
            return;
        }

        if (!deliveryDistance) {
            showNotification('Jarak pengiriman belum terhitung. Coba pilih lokasi ulang di peta.', 'error');
            return;
        }
    }
    
    const paymentMethodRadio = document.querySelector('input[name="payment_method"]:checked');
    const paymentMethod = paymentMethodRadio ? paymentMethodRadio.value : 'midtrans';

    updateDeliveryInfo();
    const grandTotal = totalAmount + deliveryCost;

    // Check for pending unpaid order first
    const lastOrderId = localStorage.getItem('last_order_id') || '';
    fetch('/check-pending-order?order_id=' + encodeURIComponent(lastOrderId), { cache: 'no-store' })
    .then(res => res.json())
    .then(pendingData => {
        if (pendingData.has_pending && pendingData.snap_token) {
            // Show interactive modal to either continue pending payment or cancel & make new checkout
            showPendingOrderModal(pendingData, function() {
                proceedWithCheckout(buyerName, buyerPhone, deliveryOption, deliveryAddress, deliveryCity, deliveryDistance, orderNotes, expeditionCourier, expeditionZone, grandTotal, paymentMethod);
            });
            return;
        }

        // No pending order - proceed with normal checkout
        proceedWithCheckout(buyerName, buyerPhone, deliveryOption, deliveryAddress, deliveryCity, deliveryDistance, orderNotes, expeditionCourier, expeditionZone, grandTotal, paymentMethod);
    })
    .catch(err => {
        console.error('Pending check error:', err);
        // On error, proceed with checkout anyway
        proceedWithCheckout(buyerName, buyerPhone, deliveryOption, deliveryAddress, deliveryCity, deliveryDistance, orderNotes, expeditionCourier, expeditionZone, grandTotal, paymentMethod);
    });
}

function showPendingOrderModal(pendingData, proceedNewCheckout) {
    const existingModal = document.getElementById('pendingOrderModal');
    if (existingModal) existingModal.remove();

    const formattedAmount = Number(pendingData.total_amount || 0).toLocaleString('id-ID');

    const modalHtml = `
        <div id="pendingOrderModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; display:flex; align-items:center; justify-content:center; font-family:sans-serif; padding:15px;">
            <div style="background:white; padding:25px; border-radius:12px; text-align:center; max-width:440px; width:100%; box-shadow:0 15px 30px rgba(0,0,0,0.3);">
                <h3 style="margin-top:0; color:#d97706; font-size:18px;">⚠️ Ada Pesanan Belum Dibatayarkan</h3>
                <p style="color:#4b5563; font-size:13.5px; line-height:1.5; margin-bottom:15px;">
                    Anda memiliki pesanan <strong>#${pendingData.order_number || pendingData.order_id}</strong> senilai <strong>Rp ${formattedAmount}</strong> yang belum diselesaikan.
                </p>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <button id="btnContinuePending" style="padding:12px; background:#2563eb; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:13.5px;">
                        💳 Lanjutkan Bayar Pesanan Ini
                    </button>
                    <button id="btnCancelAndNew" style="padding:12px; background:#dc2626; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:13.5px;">
                        🗑️ Batalkan & Buat Pesanan Baru
                    </button>
                    <button onclick="document.getElementById('pendingOrderModal').remove();" style="padding:8px; background:none; border:none; color:#6b7280; cursor:pointer; font-size:12px; text-decoration:underline;">
                        Tutup (Nanti Dulu)
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('btnContinuePending').addEventListener('click', function() {
        document.getElementById('pendingOrderModal').remove();
        if (typeof snap !== 'undefined' && pendingData.snap_token) {
            snap.pay(pendingData.snap_token, {
                onSuccess: function(result){ handlePaymentSuccess(result.order_id || pendingData.order_id); },
                onPending: function(result){ showNotification('Menunggu pembayaran.', 'info'); },
                onError: function(result){ showNotification('Pembayaran gagal.', 'error'); },
                onClose: function(){ showNotification('Popup ditutup.', 'info'); }
            });
        }
    });

    document.getElementById('btnCancelAndNew').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerText = 'Memproses pembatalan...';

        fetch('/cancel-pending-order/' + encodeURIComponent(pendingData.order_id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(res => res.json())
    });
}

// Handle successful payment - poll for ticket and show modal
function handlePaymentSuccess(orderId) {
    let attempts = 0;
    const maxAttempts = 10;
    const checkInterval = setInterval(() => {
        attempts++;
        fetch('/check-payment/' + orderId, { cache: 'no-store' })
            .then(res => res.json())
            .then(data => {
                if(data.tracking_ticket_id) {
                    clearInterval(checkInterval);
                    localStorage.setItem('chat_session_id', data.tracking_ticket_id);
                    if(typeof chatSessionId !== 'undefined') {
                        chatSessionId = data.tracking_ticket_id;
                        if(typeof fetchCustomerMessages === 'function') fetchCustomerMessages();
                    }
                    const modalHtml = `
                        <div id="ticketModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; display:flex; align-items:center; justify-content:center; font-family:sans-serif;">
                            <div style="background:white; padding:30px; border-radius:10px; text-align:center; max-width:400px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                                <h2 style="margin-top:0; color:#2e7d32;">🎉 Pembayaran Berhasil!</h2>
                                <p style="color:#555; margin-bottom:20px;">Pesanan Anda sedang diproses. Berikut adalah ID Tiket Pelacakan Anda:</p>
                                <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:25px;">
                                    <input type="text" id="copyTicketId" value="${data.tracking_ticket_id}" readonly style="padding:10px; font-size:18px; font-weight:bold; border:2px dashed #2e7d32; border-radius:5px; text-align:center; width:200px; color:#333; outline:none; background:#f9f9f9;">
                                    <button onclick="document.getElementById('copyTicketId').select(); document.execCommand('copy'); this.innerText='Disalin!'; setTimeout(()=>this.innerText='Salin',2000);" style="padding:10px 15px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Salin</button>
                                </div>
                                <button onclick="document.getElementById('ticketModal').remove(); resetCartState(); if(typeof closeCheckoutModal === 'function') closeCheckoutModal(); location.reload();" style="width:100%; padding:12px; background:#f39c12; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; font-weight:bold;">Tutup & Lanjutkan</button>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    alert('Pembayaran berhasil diproses! Silakan cek status pesanan Anda.');
                    resetCartState();
                    if(typeof closeCheckoutModal === 'function') closeCheckoutModal();
                    location.reload();
                }
            })
            .catch(err => {
                if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    alert('Pembayaran berhasil!');
                    resetCartState();
                    if(typeof closeCheckoutModal === 'function') closeCheckoutModal();
                    location.reload();
                }
            });
    }, 1500);
}

// Proceed with actual checkout (no pending order)
function proceedWithCheckout(buyerName, buyerPhone, deliveryOption, deliveryAddress, deliveryCity, deliveryDistance, orderNotes, expeditionCourier, expeditionZone, grandTotal, paymentMethod = 'midtrans') {

fetch('/checkout', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    },
    body: JSON.stringify({
        customer_name: buyerName,
        customer_phone: buyerPhone,
        delivery_option: deliveryOption,
        courier: expeditionCourier,
        destination_zone: expeditionZone,
        delivery_address: deliveryAddress,
        notes: orderNotes,
        delivery_city: deliveryCity,
        delivery_distance_km: deliveryDistance,
        delivery_cost: deliveryCost,
        items: cart,
        total_amount: grandTotal,
        payment_method: paymentMethod
    })
})
.then(handleFetchResponse)
.then(data => {
    if (!data.success) {
        alert(data.message || 'Gagal membuat transaksi');
        return;
    }

    localStorage.setItem('last_order_id', data.order_id);

    if (data.payment_method === 'transfer_bank') {
        resetCartState();
        showBankTransferModal(data);
    } else if (typeof snap !== 'undefined' && data.snap_token) {
        snap.pay(data.snap_token, {
            onSuccess: function(result){
                handlePaymentSuccess(result.order_id || data.order_id);
            },
            onPending: function(result){
                showNotification('Menunggu pembayaran. Silakan selesaikan pembayaran Anda.', 'info');
                localStorage.setItem('last_order_id', data.order_id);
            },
            onError: function(result){
                showNotification('Pembayaran gagal atau dibatalkan.', 'error');
            },
            onClose: function(){
                showNotification('Anda menutup popup pembayaran. Selesaikan pembayaran di menu Akun Saya.', 'info');
            }
        });
    } else {
        alert('Order berhasil dibuat! ID Order: ' + data.order_id);
    }
})
.catch(err => {
    console.error('Checkout error:', err);
    if (err.message && !err.message.includes('419')) {
        alert(err.message || 'Terjadi kesalahan saat memproses checkout.');
    }
});
}

function showBankTransferModal(data) {
    const existing = document.getElementById('bankTransferModal');
    if (existing) existing.remove();

    const formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(data.total_amount || 0);

    const modalHtml = `
        <div id="bankTransferModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; display:flex; align-items:center; justify-content:center; font-family:sans-serif; padding:16px;">
            <div style="background:white; padding:28px; border-radius:14px; text-align:center; max-width:460px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.3); max-height:90vh; overflow-y:auto;">
                <h2 style="margin-top:0; color:#0369a1; font-size:20px; font-weight:800;">🏦 Instruksi Transfer Bank</h2>
                <p style="color:#475569; font-size:13.5px; margin-bottom:16px;">Pesanan <strong>#${data.order_id}</strong> berhasil dibuat! Silakan lakukan transfer ke nomor rekening berikut:</p>
                
                <div style="background:#f0f9ff; border:2px dashed #0284c7; padding:16px; border-radius:10px; margin-bottom:20px; text-align:left;">
                    <div style="font-size:12px; color:#0369a1; font-weight:bold;">NAMA BANK:</div>
                    <div style="font-size:16px; font-weight:800; color:#1e293b; margin-bottom:8px;">${data.bank_name || 'Bank BCA'}</div>
                    
                    <div style="font-size:12px; color:#0369a1; font-weight:bold;">NOMOR REKENING:</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; background:white; padding:8px 12px; border-radius:6px; border:1px solid #bae6fd; margin-bottom:8px;">
                        <span style="font-size:18px; font-weight:900; color:#0369a1; font-family:monospace;">${data.bank_account || '123-456-7890'}</span>
                        <button onclick="navigator.clipboard.writeText('${data.bank_account || '123-456-7890'}'); alert('Nomor Rekening Disalin!');" style="background:#0284c7; color:white; border:none; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; cursor:pointer;">Salin</button>
                    </div>

                    <div style="font-size:12px; color:#0369a1; font-weight:bold;">ATAS NAMA:</div>
                    <div style="font-size:14px; font-weight:bold; color:#334155; margin-bottom:8px;">${data.account_holder || 'Toko Dewi Lestari 2'}</div>

                    <div style="font-size:12px; color:#0369a1; font-weight:bold;">TOTAL PEMBAYARAN:</div>
                    <div style="font-size:20px; font-weight:900; color:#166534;">${formattedAmount}</div>
                </div>

                <div style="background:#fffbeb; border:1px solid #fde047; padding:12px; border-radius:8px; margin-bottom:20px; color:#b45309; font-size:12px; text-align:left;">
                    ℹ️ Pembayaran akan divalidasi oleh <strong>Kasir Toko</strong>. Anda dapat mengunggah foto struk atau menekan tombol <strong>Simulasi Bayar Instan</strong> di bawah ini.
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <form id="tfUploadForm" onsubmit="handleTfUpload(event, ${data.order_db_id})" style="display:flex; flex-direction:column; gap:8px;">
                        <input type="file" id="tfProofFile" accept="image/*" style="padding:6px; border:1px solid #cbd5e1; border-radius:6px; font-size:12px; width:100%;">
                        <button type="submit" style="width:100%; padding:10px; background:#16a34a; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:13.5px;">📸 Unggah Foto Struk Transfer</button>
                    </form>

                    <button onclick="handleTfSimulation(${data.order_db_id})" style="width:100%; padding:11px; background:#0284c7; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:13.5px; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                        ⚡ Simulasi Transfer Instan (1-Klik)
                    </button>
                    
                    <button onclick="document.getElementById('bankTransferModal').remove(); location.reload();" style="width:100%; padding:8px; background:none; border:none; color:#64748b; font-size:12px; cursor:pointer; text-decoration:underline;">
                        Tutup & Lihat Pesanan Saya
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function handleTfUpload(event, orderDbId) {
    event.preventDefault();
    const fileInput = document.getElementById('tfProofFile');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Silakan pilih foto bukti transfer terlebih dahulu.');
        return;
    }

    const formData = new FormData();
    formData.append('proof_image', fileInput.files[0]);

    fetch('/upload-transfer-proof/' + orderDbId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: formData
    })
    .then(res => res.json())
    .then(resData => {
        if (resData.success) {
            alert('✅ ' + resData.message);
            const modal = document.getElementById('bankTransferModal');
            if (modal) modal.remove();
            window.location.href = '/customer/dashboard';
        } else {
            alert(resData.message || 'Gagal mengunggah foto');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat mengunggah foto.');
    });
}

function handleTfSimulation(orderDbId) {
    const formData = new FormData();
    formData.append('is_simulation', '1');

    fetch('/upload-transfer-proof/' + orderDbId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: formData
    })
    .then(res => res.json())
    .then(resData => {
        if (resData.success) {
            alert('🎉 Simulasi Transfer Berhasil! Status pesanan kini "Menunggu Verifikasi Kasir".');
            const modal = document.getElementById('bankTransferModal');
            if (modal) modal.remove();
            window.location.href = '/customer/dashboard';
        } else {
            alert(resData.message || 'Gagal simulasi pembayaran');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan simulasi.');
    });
}

function checkPaymentStatus()
{
    const orderId =
        localStorage.getItem(
            'last_order_id'
        );

    if (!orderId) {

        alert(
            'Belum ada transaksi'
        );

        return;
    }

    fetch(
        '/check-payment/' +
        orderId,
        { cache: 'no-store' }
    )
    .then(res => res.json())
    .then(data => {

        if (data.payment_status == 'paid') {
            // Clear cart since payment is confirmed
            resetCartState();
            
            if (data.tracking_ticket_id) {
                localStorage.setItem('chat_session_id', data.tracking_ticket_id);
                if(typeof chatSessionId !== 'undefined') {
                    chatSessionId = data.tracking_ticket_id;
                    if(typeof fetchCustomerMessages === 'function') fetchCustomerMessages();
                }
                const modalHtml = `
                    <div id="ticketModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:99999; display:flex; align-items:center; justify-content:center; font-family:sans-serif;">
                        <div style="background:white; padding:30px; border-radius:10px; text-align:center; max-width:400px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                            <h2 style="margin-top:0; color:#2e7d32;">🎉 Pembayaran Berhasil!</h2>
                            <p style="color:#555; margin-bottom:20px;">Pesanan Anda sedang diproses. Berikut adalah ID Tiket Pelacakan Anda:</p>
                            <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:25px;">
                                <input type="text" id="copyTicketId2" value="${data.tracking_ticket_id}" readonly style="padding:10px; font-size:18px; font-weight:bold; border:2px dashed #2e7d32; border-radius:5px; text-align:center; width:200px; color:#333; outline:none; background:#f9f9f9;">
                                <button onclick="document.getElementById('copyTicketId2').select(); document.execCommand('copy'); this.innerText='Disalin!'; setTimeout(()=>this.innerText='Salin',2000);" style="padding:10px 15px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">Salin</button>
                            </div>
                            <button onclick="document.getElementById('ticketModal').remove(); location.reload();" style="width:100%; padding:12px; background:#f39c12; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; font-weight:bold;">Tutup & Lanjutkan</button>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            } else {
                alert('Pembayaran berhasil!');
                location.reload();
            }

        } else {
            if (data.snap_token) {
                if (confirm('Pembayaran belum diselesaikan. Apakah Anda ingin melanjutkan pembayaran sekarang?')) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            handlePaymentSuccess(result.order_id || orderId);
                        },
                        onPending: function(result){
                            showNotification('Menunggu pembayaran!', 'info');
                        },
                        onError: function(result){
                            showNotification('Pembayaran gagal!', 'error');
                        },
                        onClose: function(){
                            showNotification('Anda menutup popup sebelum menyelesaikan pembayaran.', 'error');
                        }
                    });
                }
            } else {
                alert('Pembayaran belum diterima');
            }
        }
    });
}

function selectSavedAddress(addressText) {
    const deliveryAddress = document.getElementById('deliveryAddress');
    const deliveryMapSearch = document.getElementById('deliveryMapSearch');
    const deliveryCity = document.getElementById('deliveryCity');

    if (!addressText) {
        if (deliveryAddress) deliveryAddress.value = '';
        if (deliveryMapSearch) deliveryMapSearch.value = '';
        if (deliveryCity) deliveryCity.value = '';
        customerCoordinates = null;
        if (customerMarker && deliveryMap) {
            deliveryMap.removeLayer(customerMarker);
            customerMarker = null;
        }
        updateDeliveryInfo();
        return;
    }

    if (deliveryAddress) {
        deliveryAddress.value = addressText;
    }
    if (deliveryMapSearch) {
        deliveryMapSearch.value = addressText;
    }
    autoGeocodeAccountAddress(addressText);
}

window.toggleCart = toggleCart;
window.showCheckoutForm = showCheckoutForm;
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
window.confirmRemoveItem = confirmRemoveItem;
window.removeItem = removeItem;
window.clearCart = clearCart;
window.updateDeliveryInfo = updateDeliveryInfo;
window.submitOrder = submitOrder;
window.checkPaymentStatus = checkPaymentStatus;
window.resetCartState = resetCartState;
window.handlePaymentSuccess = handlePaymentSuccess;
window.autoGeocodeAccountAddress = autoGeocodeAccountAddress;
window.selectSavedAddress = selectSavedAddress;

function submitTrackOrder(event) {
    event.preventDefault();
    const ticketId = document.getElementById('trackingTicketId').value.trim();
    if(!ticketId) return;

    const resultDiv = document.getElementById('trackingResult');
    resultDiv.innerHTML = '<span style="color:#666;">Sedang memuat...</span>';

    fetch('/track-order/' + encodeURIComponent(ticketId), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                localStorage.setItem('chat_session_id', ticketId);
                if(typeof chatSessionId !== 'undefined') {
                    chatSessionId = ticketId;
                    if(typeof fetchCustomerMessages === 'function') fetchCustomerMessages();
                }
                
                resultDiv.innerHTML = `
                    <div style="background:#e8f5e9; border:1px solid #2e7d32; padding:10px; border-radius:8px; text-align:left; color:#2e7d32;">
                        <strong>Nomor Order:</strong> ${data.order_number}<br>
                        <strong>Nama:</strong> ${data.customer_name}<br>
                        <strong>Status:</strong> <span style="font-size:1.1em; font-weight:bold;">${data.status_label}</span>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `<span style="color:red;">${data.message}</span>`;
            }
        })
        .catch(err => {
            resultDiv.innerHTML = '<span style="color:red;">Gagal memuat data.</span>';
    });
}

function initDeliveryMap() {
    const mapElement = document.getElementById('deliveryMap');
    const searchInput = document.getElementById('deliveryMapSearch');
    const searchButton = document.getElementById('deliveryMapSearchBtn');

    if (!mapElement || typeof L === 'undefined') return;

    const defaultStore = { lat: -6.8774, lon: 107.5467 };
    storeCoordinates = defaultStore;

    deliveryMap = L.map('deliveryMap').setView([defaultStore.lat, defaultStore.lon], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(deliveryMap);

    storeMarker = L.marker([defaultStore.lat, defaultStore.lon]).addTo(deliveryMap)
        .bindPopup('Toko Dewi Lestari')
        .openPopup();

    deliveryMap.on('click', function (event) {
        const lat = event.latlng.lat;
        const lng = event.latlng.lng;

        setCustomerLocation(lat, lng, 'Mendeteksi lokasi...', false);

        reverseGeocode(lat, lng, function (result) {
            if (result) {
                setCustomerLocation(lat, lng, result.display_name, true, result.address);
            } else {
                setCustomerLocation(lat, lng, 'Lokasi dipilih di peta', true);
            }
        });
    });

    if (searchButton && searchInput) {
        const runSearch = function () {
            const query = searchInput.value.trim();
            if (!query) {
                showNotification('Masukkan alamat tujuan terlebih dahulu.', 'error');
                return;
            }

            geocodeAddress(query, function (result) {
                setCustomerLocation(result.lat, result.lon, result.display_name, true, result.address);
            });
        };

        searchButton.addEventListener('click', function (event) {
            event.preventDefault();
            runSearch();
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                runSearch();
            }
        });
    }

    geocodeAddress('Jl. Raya Cimindi No.59, Cimahi, Jawa Barat, Indonesia', function (result) {
        storeCoordinates = { lat: parseFloat(result.lat), lon: parseFloat(result.lon) };
        if (deliveryMap && storeMarker) {
            deliveryMap.setView([storeCoordinates.lat, storeCoordinates.lon], 13);
            storeMarker.setLatLng([storeCoordinates.lat, storeCoordinates.lon]);
            storeMarker.bindPopup('Toko Dewi Lestari').openPopup();
        }
    });
}

function reverseGeocode(lat, lon, callback) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=${lat}&lon=${lon}`;

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                callback(data);
            } else {
                callback(null);
            }
        })
        .catch(() => {
            callback(null);
        });
}

function geocodeAddress(query, callback) {
    const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&q=${encodeURIComponent(query)}`;

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const result = data[0];
                callback(result);
            } else {
                showNotification('Alamat tidak ditemukan. Coba gunakan kata kunci lain.', 'error');
            }
        })
        .catch(() => {
            showNotification('Gagal menghubungi layanan peta. Coba lagi nanti.', 'error');
        });
}

function autoGeocodeAccountAddress(addressText) {
    if (!addressText || !addressText.trim()) return;

    const deliveryMapSearch = document.getElementById('deliveryMapSearch');
    if (deliveryMapSearch) {
        deliveryMapSearch.value = addressText;
    }

    geocodeAddress(addressText, function (result) {
        setCustomerLocation(result.lat, result.lon, addressText, false, result.address);
    });
}

function checkIsLocalDeliveryArea(addressObj, fullAddressString, cityName) {
    const fullText = ((fullAddressString || '') + ' ' + (cityName || '')).toLowerCase();

    // 1. Check Nominatim address details object
    if (addressObj) {
        const city = (addressObj.city || addressObj.town || addressObj.municipality || '').toLowerCase();
        const county = (addressObj.county || addressObj.regency || addressObj.state_district || '').toLowerCase();

        if (county.includes('bandung barat') || county.includes('kabupaten bandung') || county.includes('kab. bandung')) {
            return false;
        }

        if (city.includes('cimahi') || city.includes('bandung')) {
            return true;
        }

        if (county.includes('cimahi') || county === 'kota bandung') {
            return true;
        }
    }

    // 2. Exclusion keywords for outer regencies/cities
    const outerKeywords = [
        'bandung barat', 'kabupaten bandung', 'kab. bandung', 'kbb',
        'cianjur', 'garut', 'sumedang', 'subang', 'purwakarta', 'tasikmalaya',
        'ciamis', 'majalengka', 'cirebon', 'kuningan', 'indramayu', 'sukabumi',
        'bogor', 'depok', 'bekasi', 'jakarta', 'tangerang', 'serang', 'karawang'
    ];

    for (let i = 0; i < outerKeywords.length; i++) {
        if (fullText.includes(outerKeywords[i])) {
            return false;
        }
    }

    // 3. Must include cimahi or bandung
    if (fullText.includes('cimahi') || fullText.includes('bandung')) {
        return true;
    }

    return false;
}

function validateLocalDeliveryCoverage(addressObj, displayName, cityName) {
    const deliveryOption = document.getElementById('deliveryOption');
    const deliveryOptionDelivery = document.getElementById('deliveryOptionDelivery');
    const localWarning = document.getElementById('localDeliveryWarning');
    const deliveryAddressInput = document.getElementById('deliveryAddress');
    const deliveryCityInput = document.getElementById('deliveryCity');

    const addressText = (displayName || '') + ' ' + (cityName || '') + ' ' + (deliveryAddressInput ? deliveryAddressInput.value : '');
    const isLocal = checkIsLocalDeliveryArea(addressObj || lastDetectedAddressObj, addressText, cityName || (deliveryCityInput ? deliveryCityInput.value : ''));

    const hasLocationInfo = customerCoordinates || (deliveryAddressInput && deliveryAddressInput.value.trim().length > 0) || (deliveryCityInput && deliveryCityInput.value.trim().length > 0);

    if (hasLocationInfo && !isLocal) {
        if (deliveryOptionDelivery) {
            deliveryOptionDelivery.disabled = true;
        }

        const cityDisplayName = cityName || (deliveryCityInput ? deliveryCityInput.value : '') || 'Luar Kota';
        if (localWarning) {
            localWarning.style.display = 'block';
            localWarning.innerHTML = `⚠️ Lokasi Anda (<strong>${escapeHtml(cityDisplayName)}</strong>) di luar jangkauan Kurir Toko. Kurir Toko hanya melayani Kota Cimahi & Kota Bandung. Silakan gunakan <strong>Ekspedisi Pihak Ketiga</strong>.`;
        }

        if (deliveryOption && deliveryOption.value === 'delivery') {
            deliveryOption.value = 'expedition';
            showNotification('Lokasi di luar jangkauan Kurir Toko (hanya Kota Cimahi & Kota Bandung). Metode pengiriman dialihkan ke Ekspedisi Pihak Ketiga.', 'error');
            updateDeliveryInfo();
        }
    } else {
        if (deliveryOptionDelivery) {
            deliveryOptionDelivery.disabled = false;
        }
        if (localWarning) {
            localWarning.style.display = 'none';
        }
    }

    return isLocal;
}

function setCustomerLocation(lat, lon, displayName, updateAddressField = true, addressObj = null) {
    customerCoordinates = { lat: parseFloat(lat), lon: parseFloat(lon) };
    if (addressObj) {
        lastDetectedAddressObj = addressObj;
    }

    if (deliveryMap) {
        if (customerMarker) {
            customerMarker.setLatLng([customerCoordinates.lat, customerCoordinates.lon]);
        } else {
            customerMarker = L.marker([customerCoordinates.lat, customerCoordinates.lon]).addTo(deliveryMap);
        }

        customerMarker.bindPopup(displayName || 'Lokasi tujuan').openPopup();
        deliveryMap.setView([customerCoordinates.lat, customerCoordinates.lon], 14);
    }

    const deliveryAddress = document.getElementById('deliveryAddress');
    const deliveryCity = document.getElementById('deliveryCity');

    if (deliveryAddress && updateAddressField) {
        deliveryAddress.value = displayName || '';
    }

    let cityName = '';
    if (addressObj) {
        cityName = addressObj.city || addressObj.town || addressObj.city_district || addressObj.county || addressObj.regency || '';
    }
    if (!cityName && displayName) {
        cityName = extractCityName(displayName);
    }

    if (deliveryCity && cityName) {
        deliveryCity.value = cityName;
    }

    calculateDeliveryDistance();
    validateLocalDeliveryCoverage(addressObj, displayName, cityName);
}

function extractCityName(displayName) {
    if (!displayName) return '';
    const parts = displayName.split(',').map(part => part.trim());
    return parts[parts.length - 3] || parts[parts.length - 2] || parts[0] || '';
}

function calculateDeliveryDistance() {
    if (!storeCoordinates || !customerCoordinates || typeof fetch === 'undefined') {
        return;
    }

    const url = `https://router.project-osrm.org/route/v1/driving/${storeCoordinates.lon},${storeCoordinates.lat};${customerCoordinates.lon},${customerCoordinates.lat}?overview=false`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.routes && data.routes.length > 0) {
                deliveryRouteDistanceKm = data.routes[0].distance / 1000;
                const deliveryDistance = document.getElementById('deliveryDistance');
                if (deliveryDistance) {
                    deliveryDistance.value = `${deliveryRouteDistanceKm.toFixed(1)} km`;
                }
                updateDeliveryInfo();
            }
        })
        .catch(() => {
            deliveryRouteDistanceKm = 0;
            const deliveryDistance = document.getElementById('deliveryDistance');
            if (deliveryDistance) {
                deliveryDistance.value = '';
            }
        });
}
/* ===================================
   NOTIFICATION FUNCTIONS
   =================================== */

// Show success message
function showSuccessMessage() {
    const successMessage = document.getElementById('successMessage');
    if (!successMessage) return;
    
    successMessage.style.display = 'block';
    successMessage.classList.add('show');

    setTimeout(() => {
        successMessage.style.display = 'none';
        successMessage.classList.remove('show');
    }, 5000);
}

// Add notification animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);

// === CHAT WIDGET LOGIC ===
let chatSessionId = localStorage.getItem('chat_session_id');

// Simpan session ID secara permanen di localStorage agar histori tidak hilang saat refresh
if (!chatSessionId) {
    chatSessionId = 'sess_' + Math.random().toString(36).substr(2, 9);
    localStorage.setItem('chat_session_id', chatSessionId);
}

let isChatOpen = false;

function formatChatTime(dateStr) {
    let d = dateStr ? new Date(dateStr) : new Date();
    if (isNaN(d.getTime())) d = new Date();
    const hours = String(d.getHours()).padStart(2, '0');
    const mins = String(d.getMinutes()).padStart(2, '0');
    return `${hours}:${mins}`;
}

function toggleChat() {
    isChatOpen = !isChatOpen;
    document.getElementById('chatBox').style.display = isChatOpen ? 'flex' : 'none';
    if (isChatOpen) {
        document.getElementById('customerUnreadBadge').style.display = 'none';
        document.getElementById('chatInput').focus();
        fetchCustomerMessages();
    }
}

function sendChatMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if(!message) return;

    input.value = '';
    const nowTime = formatChatTime();
    
    // Optimistic UI update dengan timestamp jam
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML += `
        <div style="align-self: flex-end; max-width: 80%; background: #dcf8c6; padding: 8px 12px; border-radius: 10px; border-top-right-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
            <div style="font-size:13px; color:#333; line-height:1.4;">${escapeHtml(message)}</div>
            <div style="font-size:10px; color:#666; text-align:right; margin-top:4px;">${nowTime} WIB</div>
        </div>
    `;
    chatMessages.scrollTop = chatMessages.scrollHeight;

    fetch('/api/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            session_id: chatSessionId,
            message: message
        })
    }).catch(err => console.error('Chat error:', err));
}

function fetchCustomerMessages() {
    if(!document.getElementById('chatMessages')) return; // not customer page

    fetch('/api/chat/fetch?session_id=' + chatSessionId)
        .then(res => res.json())
        .then(data => {
            const chatMessages = document.getElementById('chatMessages');
            let unreadCount = 0;
            let html = '';
            data.forEach(msg => {
                const msgTime = formatChatTime(msg.created_at);
                const escapedText = escapeHtml(msg.message).replace(/\n/g, '<br>');
                if (msg.sender === 'admin' && !msg.is_read) unreadCount++;
                if (msg.sender === 'customer') {
                    html += `
                        <div style="align-self: flex-end; max-width: 80%; background: #dcf8c6; padding: 8px 12px; border-radius: 10px; border-top-right-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <div style="font-weight:bold; font-size:11px; color:#15803d; margin-bottom:3px;">Anda</div>
                            <div style="font-size:13px; color:#333; line-height:1.4;">${escapedText}</div>
                            <div style="font-size:10px; color:#666; text-align:right; margin-top:4px;">${msgTime} WIB</div>
                        </div>
                    `;
                } else {
                    html += `
                        <div style="align-self: flex-start; max-width: 80%; background: white; padding: 8px 12px; border-radius: 10px; border-top-left-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            <div style="font-weight:bold; font-size:11px; color:#1e3a8a; margin-bottom:3px;">👨‍💼 Admin Toko</div>
                            <div style="font-size:13px; color:#333; line-height:1.4;">${escapedText}</div>
                            <div style="font-size:10px; color:#888; text-align:right; margin-top:4px;">${msgTime} WIB</div>
                        </div>
                    `;
                }
            });
            chatMessages.innerHTML = html;
            
            if (isChatOpen) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else if (unreadCount > 0) {
                const badge = document.getElementById('customerUnreadBadge');
                badge.innerText = unreadCount;
                badge.style.display = 'block';
            }
        }).catch(err => console.error(err));
}

// Admin Unread Polling
function fetchAdminUnreadCount() {
    const badge = document.getElementById('adminChatBadge');
    if (!badge) return;

    fetch('/admin/chat/unread')
        .then(res => res.json())
        .then(data => {
            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }).catch(err => console.error(err));
}

// Poll every 5 seconds
setInterval(() => {
    if (document.getElementById('chatMessages')) {
        fetchCustomerMessages();
    }
    if (document.getElementById('adminChatBadge')) {
        fetchAdminUnreadCount();
    }
}, 5000);