/* ===================================
   TOKO DEWI LESTARI 2 - JAVASCRIPT
   Shopping Cart & Checkout System
   IMPROVED VERSION with localStorage
   =================================== */

// Global Variables
let cart = [];
let cartCount = 0;
let totalAmount = 0;
let deliveryCost = 0;
let discountPercent = 10;


/* ===================================
   INITIALIZATION
   =================================== */
document.addEventListener('DOMContentLoaded', function () {
    // Load cart from localStorage
    loadCartFromStorage();
    updateCartDisplay();

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

/* ===================================
   CART FUNCTIONS
   =================================== */

function addToCart(
    productId,
    productName,
    price,
    qtyInputId
) {
    const quantityInput = document.getElementById(qtyInputId);
    
    if (!quantityInput) {
        showNotification('Error: Input quantity tidak ditemukan', 'error');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);

    // Validation
    if (isNaN(quantity) || quantity < 1 || quantity > 50) {
        showNotification('Jumlah harus antara 1-50 pcs', 'error');
        return;
    }

    // Check if product already exists in cart
    const existingItemIndex = cart.findIndex(item => item.name === productName);

    if (existingItemIndex > -1) {
        const newQuantity = cart[existingItemIndex].quantity + quantity;
        if (newQuantity > 50) {
            showNotification('Total maksimal 50 pcs per produk', 'error');
            return;
        }
        cart[existingItemIndex].quantity = newQuantity;
    } else {
        cart.push({
             id: productId,
             name: productName,
             price: price,
             quantity: quantity,
             //weight: productWeight
});
    }

    // Save to localStorage
    saveCartToStorage();
    updateCartDisplay();

    // Reset quantity input
    quantityInput.value = 1;

    // Show success notification
    showNotification(`${productName} (${quantity}x) berhasil ditambahkan!`, 'success');

    // Animate cart icon
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.style.transform = 'scale(1.2)';
        setTimeout(() => {
            cartIcon.style.transform = 'scale(1)';
        }, 200);
    }
}

// Update cart display
function updateCartDisplay() {
    cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const subtotal = cart.reduce(
    (total, item) => total + (item.price * item.quantity),
    0
    
);
if (subtotal >= 100000) {
    discountAmount = subtotal * (discountPercent / 100);
} else {
    discountAmount = 0;
}


totalAmount = subtotal - discountAmount;
const discountEl = document.getElementById('discountAmount');
const discountRow = document.getElementById('discountRow');

if (discountAmount > 0) {
    discountEl.textContent = discountAmount.toLocaleString('id-ID');
    discountRow.style.display = 'block';
} else {
    discountRow.style.display = 'none';
}


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

    if (cart.length === 0) {
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
        cart.forEach((item, index) => {
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

    updateOrderSummary();
}

// Increase quantity
function increaseQuantity(index) {
    if (cart[index].quantity >= 50) {
        showNotification('Maksimal 50 pcs per produk', 'error');
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
        cart = [];
        clearCartStorage();
        updateCartDisplay();
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
    }
}

// Update delivery info
function updateDeliveryInfo() {
    const deliveryOption = document.getElementById('deliveryOption');
    const addressGroup = document.getElementById('addressGroup');
    const deliveryCostElement = document.getElementById('deliveryCost');
    const deliveryAddressInput = document.getElementById('deliveryAddress');

    if (!deliveryOption) return;

    if (deliveryOption.value === 'delivery') {
        deliveryCost = 10000;
        if (addressGroup) addressGroup.style.display = 'block';
        if (deliveryAddressInput) deliveryAddressInput.required = true;
    } else {
        deliveryCost = 0;
        if (addressGroup) addressGroup.style.display = 'none';
        if (deliveryAddressInput) deliveryAddressInput.required = false;
    }

    if (deliveryCostElement) {
        deliveryCostElement.textContent = `Rp ${deliveryCost.toLocaleString('id-ID')}`;
    }
    
    updateOrderSummary();
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
    const orderNotes = document.getElementById('orderNotes').value.trim();

    // Validation
    if (!buyerName || !buyerPhone || !deliveryOption) {
        showNotification('Mohon lengkapi data yang wajib diisi!', 'error');
        return;
    }

    if (deliveryOption === 'delivery' && !deliveryAddress) {
        showNotification('Alamat pengiriman wajib diisi!', 'error');
        return;
    }

    // Phone number validation
    const cleanPhone = buyerPhone.replace(/[- ]/g, '');
    if (!/^(08|62)[0-9]{8,}$/.test(cleanPhone)) {
        showNotification('Format nomor WhatsApp tidak valid!', 'error');
        return;
    }

   const grandTotal = totalAmount + deliveryCost;
   const totalWeight = getTotalWeight();

    if (
    deliveryOption == 'delivery'
    &&
    totalWeight < 5000
    )
    {
    alert(
        'Minimal pembelian 5 Kg untuk layanan antar'
    );

    return;
    }


fetch('/checkout', {
    method: 'POST',

    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN':
            document.querySelector(
                'meta[name="csrf-token"]'
            ).content
    },

    body: JSON.stringify({

        customer_name: buyerName,
        customer_phone: buyerPhone,

        delivery_option: deliveryOption,

        delivery_address: deliveryAddress,

        notes: orderNotes,

        items: cart,

        total_amount: grandTotal
    })
})
.then(response => response.json())
.then(data => {

    if (!data.success) {

        alert('Gagal membuat transaksi');

        return;
    }

    // snap.pay(data.snap_token, {

    //     onSuccess: function(result){

    //         alert('Pembayaran berhasil');

    //         localStorage.removeItem(
    //             'dewilestari_cart'
    //         );

    //         location.reload();
    //     },

    //     onPending: function(result){

    //         alert(
    //             'Menunggu pembayaran'
    //         );
    //     },

    //     onError: function(result){

    //         alert(
    //             'Pembayaran gagal'
    //         );
    //     }
    // });
    snap.pay(data.snap_token, {

    onSuccess: function(result){

        alert('Pembayaran berhasil');
        //cart = [];
        localStorage.removeItem('dewilestari_cart');
        updateCartDisplay();
        closeCheckoutModal();
        setTimeout(() => {
        location.reload();
    }, 1000);
    },

    onPending: function(result){

        alert('Menunggu pembayaran');

        // simpan order id
        localStorage.setItem(
            'last_order_id',
            result.order_id
        );
    }
});
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
        orderId
    )
    .then(res => res.json())
    .then(data => {

        if (
            data.payment_status ==
            'paid'
        ) {

            alert(
                'Pembayaran berhasil'
            );

            location.reload();

        } else {

            alert(
                'Pembayaran belum diterima'
            );
        }
    });
}

});
  
}

function getTotalWeight() {

    let totalWeight = 0;

    cart.forEach(item => {

        totalWeight +=
            parseInt(item.weight)
            *
            item.quantity;

    });

    return totalWeight;
}
/* ===================================
   UTILITY FUNCTIONS
   =================================== */

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications first
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(n => n.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    notification.style.cssText = `
        position: fixed;
        top: 120px;
        right: 20px;
        background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
        color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        z-index: 10000;
        max-width: 300px;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
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