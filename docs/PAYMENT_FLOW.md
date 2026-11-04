# QRIS Payment Flow - Complete Guide

This document describes the complete QRIS payment flow for unauthenticated customers in JASTIP (NITIP DI END).

## Overview

The payment system allows customers to:
1. Browse and add products to cart (without login)
2. Checkout with customer details
3. Proceed to QRIS payment page with 24-hour countdown
4. Download QRIS image
5. Upload payment proof (screenshot/receipt)
6. Admin verifies payment and confirms order
7. Customer receives notification when verified
8. Order proceeds to fulfillment workflow

## 1. Customer Flow

### 1.1 Cart & Checkout
- Customer adds products to cart via `POST /titipan/{product:id}/tambah`
- Proceeds to `/checkout`
- Fills checkout form with:
  - Name (nama)
  - WhatsApp number (whatsapp)
  - Address (alamat)
  - Notes (catatan) - optional
  - Shipping method (ongkir)
- Submits form via `POST /checkout`

**Response:** Redirects to `/payment/{order}/waiting`

### 1.2 QRIS Payment Page
**Route:** `GET /payment/{order}/waiting`

Displays:
- QRIS image from admin settings
- Countdown timer (24 hours from order creation)
- Download QRIS button
- Order summary (items, subtotal, shipping, fee, total)
- Payment proof upload area
- Instructions for payment

**Frontend Features:**
- Real-time countdown timer (updates every 1 second)
- Drag-drop or click file upload
- Validates file: `image/jpeg`, `image/png`, max 5MB
- Shows success message after upload
- Polls status every 5 seconds for admin verification

### 1.3 Payment Proof Upload
**Route:** `POST /payment/{order}/upload-proof`

**Request:**
```json
{
  "proof": "<file>"
}
```

**Validation:**
- File must be image (jpg/png)
- Max 5MB
- MIME type: image/jpeg, image/png

**Response:**
```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diunggah. Tunggu konfirmasi admin.",
  "proof_id": 1
}
```

**Database:**
- Creates record in `payment_proofs` table
- Status: `pending`
- File stored at `storage/app/public/payment-proofs/{filename}`
- Old proofs deleted if customer re-uploads

### 1.4 Download QRIS
**Route:** `GET /payment/{order}/download-qris`

Downloads QRIS image as `QRIS-{order_number}.png`

### 1.5 Status Polling
**Route:** `GET /payment/{order}/status`

Called every 5 seconds from payment waiting page.

**Response:**
```json
{
  "order_number": "ND20261007-ABC123",
  "status": "awaiting_payment",
  "has_proof": true,
  "proof_status": "pending",
  "expires_at": "2026-10-08T14:30:00Z",
  "remaining_seconds": 86400,
  "is_expired": false
}
```

If `proof_status === "verified"`, page redirects to `/checkout/confirmation`

## 2. Admin Verification Flow

### 2.1 Pending Proofs List
**Route:** `GET /admin/bukti-pembayaran` (with optional `?status=pending`)

Filters available:
- `all` - All proofs
- `pending` - Awaiting verification
- `verified` - Approved payments
- `rejected` - Denied payments

**Dashboard Widget:**
- Shows count of pending/verified/rejected proofs
- Quick links to verification pages
- Recent pending proofs in dashboard

### 2.2 Proof Verification Page
**Route:** `GET /admin/bukti-pembayaran/{paymentProof}`

Displays:
- Payment proof image (full size)
- File info (name, size, upload time)
- Order details (number, customer, total)
- Admin notes (if already processed)
- Customer WhatsApp link
- Order detail link

**Actions (if status === pending):**
- Verify button: Accept payment
- Reject button: Deny payment with reason

### 2.3 Verify Payment
**Route:** `POST /admin/bukti-pembayaran/{paymentProof}/verify`

**Request:**
```json
{
  "notes": "Pembayaran valid, alamat sesuai"
}
```

**Actions:**
1. Update `payment_proofs.status = 'verified'`
2. Update `payment_proofs.verified_at = now()`
3. Update `payment_proofs.admin_notes` (optional)
4. Update `orders.status = 'confirmed'`
5. Dispatch `OrderStatusChanged` event
6. Event triggers `SendOrderStatusNotification` listener
7. Generate WhatsApp notification link for customer

**Notification:**
```
Halo 👋

Pembayaran untuk pesanan ND20261007-ABC123 telah berhasil diverifikasi! ✓

Pesanan kamu sudah dikonfirmasi dan akan segera diproses.

Cek status pesananmu di sini:
🔗 https://jastip.test/order/ND20261007-ABC123

Terima kasih 🙏
```

### 2.4 Reject Payment
**Route:** `POST /admin/bukti-pembayaran/{paymentProof}/reject`

**Request:**
```json
{
  "notes": "Nomor tidak terlihat jelas, silakan upload ulang"
}
```

**Actions:**
1. Update `payment_proofs.status = 'rejected'`
2. Update `payment_proofs.admin_notes` (required)
3. Update `orders.status = 'awaiting_payment'`
4. Dispatch `OrderStatusChanged` event
5. Generate WhatsApp notification link for customer

**Notification:**
```
Halo 👋

Sayangnya bukti pembayaran untuk pesanan ND20261007-ABC123 tidak dapat diterima.

Alasan: Nomor tidak terlihat jelas, silakan upload ulang

Silakan unggah bukti pembayaran yang benar melalui:
🔗 https://jastip.test/payment/1/waiting

Jika ada pertanyaan, hubungi kami ya. Terima kasih 🙏
```

### 2.5 Download Proof
**Route:** `GET /admin/bukti-pembayaran/{paymentProof}/download`

Downloads the uploaded payment proof file.

## 3. Order Status Tracking

### 3.1 Customer Order Page
**Route:** `GET /order/{orderNumber}`

Displays:
- Order timeline (awaiting_payment → confirmed → processing → shipped → completed)
- Current status with badge
- All items in order
- Order summary
- Customer information
- Auto-refresh every 10 seconds

### 3.2 Order Status API
**Route:** `GET /order/{orderNumber}/status`

**Response:**
```json
{
  "order_number": "ND20261007-ABC123",
  "customer_name": "John Doe",
  "status": "confirmed",
  "status_label": "Dikonfirmasi",
  "total": 500000,
  "created_at": "2026-10-07T14:30:00Z",
  "paid_at": "2026-10-07T15:15:00Z",
  "payment_method": "qris",
  "has_proof": true,
  "proof_status": "verified",
  "expires_at": "2026-10-08T14:30:00Z",
  "remaining_seconds": 0,
  "is_expired": false,
  "items_count": 2
}
```

## 4. Order Status Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ awaitning_payment                                           │
│ Customer uploads proof, admin verifies within 24 hours      │
│ Timeout: Auto-cancel if not paid in 24 hours              │
└─────────────────┬───────────────────────────────────────────┘
                  │ Admin verifies payment
                  │ OrderStatusChanged event dispatched
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ confirmed                                                   │
│ Payment verified, order ready for fulfillment              │
│ Customer notified via WhatsApp                              │
└─────────────────┬───────────────────────────────────────────┘
                  │ Admin processes order
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ processing                                                  │
│ Items being packed/prepared                                 │
└─────────────────┬───────────────────────────────────────────┘
                  │ Items ready for shipment
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ ready                                                       │
│ Items ready to be picked up or shipped                      │
└─────────────────┬───────────────────────────────────────────┘
                  │ Handed off to courier
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ shipped                                                     │
│ Order in transit                                            │
│ Customer notified with tracking number                      │
└─────────────────┬───────────────────────────────────────────┘
                  │ Delivered to customer
                  ▼
┌─────────────────────────────────────────────────────────────┐
│ completed                                                   │
│ Order fulfilled                                             │
└─────────────────────────────────────────────────────────────┘
```

## 5. Database Schema

### orders table
```sql
- id: bigint (PK)
- order_number: string (unique, e.g., "ND20261007-ABC123")
- customer_name: string
- customer_whatsapp: string
- customer_address: text
- customer_notes: text
- shipping_method: string
- payment_method: enum('qris')
- subtotal: integer (IDR)
- shipping_cost: integer (IDR)
- fee: integer (IDR)
- total: integer (IDR)
- status: enum('awaiting_payment', 'pending', 'confirmed', 'processing', 'ready', 'shipped', 'completed', 'cancelled')
- paid_at: timestamp (nullable)
- payment_hash: string (SHA256 hash for integrity)
- admin_notes: text
- created_at, updated_at: timestamp
```

### payment_proofs table
```sql
- id: bigint (PK)
- order_id: bigint (FK to orders)
- file_path: string (path to uploaded file)
- file_name: string (original filename)
- file_size: integer (bytes)
- status: enum('pending', 'verified', 'rejected')
- admin_notes: text (nullable)
- verified_at: timestamp (nullable)
- created_at, updated_at: timestamp
```

## 6. File Locations

- **Payment waiting view:** `resources/views/frontend/payment/waiting.blade.php`
- **Order status view:** `resources/views/frontend/order-status.blade.php`
- **Admin proof index:** `resources/views/admin/payment-proofs/index.blade.php`
- **Admin proof detail:** `resources/views/admin/payment-proofs/show.blade.php`
- **Payment controller:** `app/Http/Controllers/PaymentController.php`
- **Payment proof controller:** `app/Http/Controllers/Admin/PaymentProofController.php`
- **Order status controller:** `app/Http/Controllers/OrderStatusController.php`
- **Proof model:** `app/Models/PaymentProof.php`
- **Order model:** `app/Models/Order.php`
- **Events:** `app/Events/OrderStatusChanged.php`
- **Listeners:** `app/Listeners/SendOrderStatusNotification.php`
- **Uploaded files:** `storage/app/public/payment-proofs/`

## 7. Configuration

### Admin Settings (in database)

Required settings:
- `qris_image`: Path to QRIS image file
- `qris_merchant_name`: Merchant name displayed on payment page
- `whatsapp`: Admin WhatsApp number for initial order notification

Example environment:
```env
QRIS_IMAGE=qris/default.png
QRIS_MERCHANT_NAME=NITIP DI END
WHATSAPP=6281234567890
```

## 8. Testing

### Test Flow

1. **Clear Database**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Add QRIS Image**
   - Go to Admin Settings
   - Upload QRIS image

3. **Customer Checkout**
   - Add product to cart
   - Checkout with customer details
   - Should redirect to payment waiting page

4. **Payment Waiting Page**
   - QRIS should display
   - Countdown timer should work
   - Should be able to download QRIS

5. **Upload Proof**
   - Select JPG/PNG image (max 5MB)
   - Click upload
   - Should see success message

6. **Admin Verification**
   - Go to Admin > Bukti Pembayaran
   - Should see pending proof
   - Click to view proof detail
   - Download proof to verify
   - Click Verify with notes
   - Order status should change to confirmed

7. **Customer Notification**
   - Payment proof verified notification URL should be generated
   - Contains tracking link to order page

8. **Order Status Tracking**
   - Go to `/order/{order_number}`
   - Should show order timeline
   - Should show confirmed status
   - Auto-refresh should work

## 9. Helper Functions

```php
// Format price as IDR
format_price($amount)  // "Rp 500.000"

// Format bytes to human-readable
format_bytes($bytes)   // "2.5 MB"

// WhatsApp URL generation
whatsapp_url($phone, $message)

// Generate order number
Order::generateOrderNumber()  // "ND20261007-ABC123"

// Generate payment hash
$order->generatePaymentHash()

// Check payment integrity
$order->isPaymentValid()
```

## 10. Troubleshooting

### QRIS Image Not Showing
- Check if QRIS file is uploaded in admin settings
- Verify file path in settings table
- Check storage permissions

### Payment Proof Upload Fails
- Check file size (max 5MB)
- Verify file is JPG/PNG
- Check storage disk permissions
- See Laravel logs for details

### Notification Not Sent
- Check WhatsApp number format (should be 62xxx)
- Verify SMS/WhatsApp gateway configuration (not implemented yet)
- Check logs for errors

### Order Not Auto-Expiring
- Manual check: Go to payment page when 24 hours passed
- Should see "Waktu Habis" countdown
- Order should show cancelled status

## Notes

- Payment gateway integration (actual payment processing) is not implemented
- WhatsApp notifications currently generate URLs for manual sharing
- For production: Integrate with WhatsApp Business API or Twilio
- For production: Add email notifications as fallback
- Consider adding SMS notifications for non-WhatsApp users
