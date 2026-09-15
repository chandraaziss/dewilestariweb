# DOKUMENTASI NORMALISASI DATABASE (UNF -> 1NF -> 2NF -> 3NF)
**SISTEM E-COMMERCE TOKO DEWI LESTARI 2**

Keterangan Simbol:
- `*` = Primary Key (PK)
- `**` = Foreign Key (FK)

---

## 1. Bentuk Belum Ternormalisasi (UNF - Unnormalized Form)
Pada tahap UNF (Unnormalized Form), seluruh data transaksi dan inventaris sistem dicatat ke dalam satu dokumen mentah tanpa aturan relasional. Dokumen ini masih mengandung kelompok atribut berulang (*repeating groups*) untuk item produk yang dibeli dalam satu transaksi pesanan.

**(order_id, order_number, order_date, total_amount, shipping_cost, delivery_option, courier_type, payment_method, payment_status, snap_token, tracking_ticket, shipping_status, delivery_proof, user_id, user_name, user_email, user_password, user_phone, user_address, city, province, postal_code, { supplier_stock_id, product_name, variant_name, size, selling_price, hpp, stock, expiry_date, item_quantity, item_subtotal, supplier_id, supplier_name, supplier_phone, supplier_email, supplier_address }, supplier_order_id, po_date, po_qty, po_status, supplier_return_id, return_date, return_qty, reason, stock_log_id, log_type, qty_change, log_description, rating_id, stars, comment)**

---

## 2. Bentuk Normal Pertama (1NF)
Pada tahap 1NF, kelompok atribut berulang (*repeating groups*) dihilangkan. Setiap atribut memiliki nilai atomik (tidak dapat dibagi lagi) dan setiap baris record memiliki kunci utama (Primary Key) yang mengidentifikasinya secara unik.

**(order_id*, order_number, order_date, total_amount, shipping_cost, delivery_option, courier_type, payment_method, payment_status, snap_token, tracking_ticket, shipping_status, delivery_proof, user_id*, user_name, user_email, user_password, user_phone, user_address, city, province, postal_code, supplier_stock_id*, product_name, variant_name, size, selling_price, hpp, stock, expiry_date, item_quantity, item_subtotal, supplier_id*, supplier_name, supplier_phone, supplier_email, supplier_address, supplier_order_id*, po_date, po_qty, po_status, supplier_return_id*, return_date, return_qty, reason, stock_log_id*, log_type, qty_change, log_description, rating_id*, stars, comment)**

---

## 3. Bentuk Normal Kedua (2NF)
Pada tahap 2NF, seluruh ketergantungan parsial (*Partial Dependencies*) dihilangkan. Tabel-tabel yang ada di 1NF dipecah menjadi entitas-entitas spesifik berdasarkan Primary Key masing-masing agar atribut non-key bergantung penuh pada seluruh Primary Key-nya.

a. **User** (`user_id*`, `name`, `email`, `password`, `phone_number`, `role`, `address`, `city`, `province`, `postal_code`)

b. **Supplier** (`supplier_id*`, `name`, `phone_number`, `email`, `address`)

c. **Supplier_stocks** (`supplier_stock_id*`, `supplier_id**`, `product_name`, `variant_name`, `size`, `selling_price`, `hpp`, `stock`, `expiry_date`, `status`)

d. **Orders** (`order_id*`, `user_id**`, `order_number`, `total_amount`, `shipping_cost`, `delivery_option`, `courier_type`, `payment_method`, `payment_status`, `snap_token`, `tracking_ticket`, `shipping_status`, `delivery_proof`)

e. **order_items** (`order_item_id*`, `order_id**`, `supplier_stock_id**`, `quantity`, `price`, `subtotal`)

f. **supplier_orders** (`supplier_order_id*`, `supplier_id**`, `supplier_stock_id**`, `order_date`, `quantity_ordered`, `status`)

g. **supplier_returns** (`supplier_return_id*`, `supplier_id**`, `supplier_stock_id**`, `return_date`, `quantity_returned`, `reason`)

h. **stock_logs** (`stock_log_id*`, `supplier_stock_id**`, `user_id**`, `type`, `quantity_change`, `description`)

i. **ratings** (`rating_id*`, `order_id**`, `user_id**`, `supplier_stock_id**`, `stars`, `comment`)

---

## 4. Bentuk Normal Ketiga (3NF)
Pada tahap 3NF, ketergantungan transitif (*Transitive Dependencies*) dihilangkan. Atribut yang bergantung pada atribut non-key lainnya dipisah ke dalam tabel lain.

1. **users** (`user_id*`, `name`, `email`, `password`, `phone_number`, `role`)

2. **user_addresses** (`address_id*`, `user_id**`, `recipient_name`, `phone_number`, `address`, `city`, `province`, `postal_code`, `is_primary`)

3. **suppliers** (`supplier_id*`, `name`, `phone_number`, `email`, `address`)

4. **supplier_stocks** (`supplier_stock_id*`, `supplier_id**`, `product_name`, `variant_name`, `size`, `selling_price`, `hpp`, `stock`, `expiry_date`, `status`)

5. **orders** (`order_id*`, `user_id**`, `address_id**`, `order_number`, `total_amount`, `shipping_cost`, `delivery_option`, `courier_type`, `payment_method`, `payment_status`, `snap_token`, `tracking_ticket`, `shipping_status`, `delivery_proof`)

6. **order_items** (`order_item_id*`, `order_id**`, `supplier_stock_id**`, `quantity`, `price`, `subtotal`)

7. **supplier_orders** (`supplier_order_id*`, `supplier_id**`, `supplier_stock_id**`, `order_date`, `quantity_ordered`, `status`)

8. **supplier_returns** (`supplier_return_id*`, `supplier_id**`, `supplier_stock_id**`, `return_date`, `quantity_returned`, `reason`)

9. **stock_logs** (`stock_log_id*`, `supplier_stock_id**`, `user_id**`, `type`, `quantity_change`, `description`)

10. **ratings** (`rating_id*`, `order_id**`, `user_id**`, `supplier_stock_id**`, `stars`, `comment`)

11. **couriers** (`courier_id*`, `name`, `phone_number`, `vehicle_number`, `is_active`)

12. **messages** (`message_id*`, `user_id**`, `order_id**`, `session_id`, `sender_type`, `message_text`)
