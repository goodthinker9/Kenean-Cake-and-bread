# Golden Crust — Multi-Branch Cake & Bread Shop Management System
**Specification Version 3.0 Implementation**

A full-stack, enterprise-grade multi-branch bakery platform built with **Laravel**, **Vue.js 3**, and **MySQL**, adhering to the complete system specification and UI/UX design mockups.

---

## 🏗️ Architecture & Technology Stack

- **Backend:** Laravel 13 (PHP 8.5) with Laravel Sanctum, Eloquent ORM, Database Transactions with Row Locks (`lockForUpdate()`), and RESTful Controllers.
- **Frontend:** Vue 3 (Composition API), Vite, Tailwind CSS, Pinia State Management, and Vue Router.
- **Database:** MySQL / MariaDB (InnoDB engine, strict foreign key constraints, indexes, UTC timestamps, auto-increment integer PKs).
- **Zero Hardcoding:** All figures, inventory levels, order items, prices, transfer numbers, delivery fees, and audit histories are generated dynamically and stored directly in MySQL (`kenan_cake_db`).

---

## 🗄️ Database Design (28 Tables)

Conforming to **Section 11** of the specification:

1. `branches` — Main branch (`MAIN01`) and regular branches (`BR001` Bole, `BR002` Megenagna, `BR003` Piassa).
2. `users` — 5 RBAC roles (`Super Admin`, `Main Branch Manager`, `Branch Admin`, `Branch Staff`, `Customer`).
3. `addresses` — Customer delivery addresses with default flags.
4. `delivery_zones` — Branch-specific delivery zones with localized delivery fees (e.g. Bole Medhanialem ETB 60).
5. `categories` — Cakes, Bread, Pastries, and Custom Cakes.
6. `products` — Product catalog with base prices and shelf life in hours.
7. `product_images` — Image URLs and sort orders.
8. `branch_product_prices` — Decision D4: Optional active branch price overrides (e.g. Piassa Chocolate Cake ETB 880).
9. `branch_inventory` — Isolated per-branch stock: `quantity_on_hand`, `reserved_quantity`, and `reorder_level`.
10. `inventory_transactions` — Traceable audit log for all stock movements (production, transfers, orders, damage, manual adjustments).
11. `stock_transfers` — Main-to-branch distribution with unique TRF numbers (`TRF-2026-0001` to `0005`).
12. `stock_transfer_items` — Transfer item lines tracking `quantity_sent`, `quantity_received`, `damaged_quantity`, and `missing_quantity`.
13. `stock_requests` — Branch-initiated replenishment requests.
14. `stock_request_items` — Requested items and quantities.
15. `carts` — Customer active cart scoped to a chosen branch.
16. `cart_items` — Product and quantity selections.
17. `orders` — Orders with unique `ORD-xxxx` numbers, snapshot prices, and branch binding.
18. `order_items` — Snapshot product names, quantities, unit prices, and subtotals.
19. `order_status_history` — Immutable status progression audit trail.
20. `payments` — Gateways (`Chapa`, `Telebirr`, Pay on pickup) with unique transaction references.
21. `custom_cake_requests` — Custom celebratory cake specifications (size, flavor, design, message, needed date).
22. `custom_cake_images` — Reference design image URLs.
23. `reviews` — Product ratings and customer reviews.
24. `promotions` — Promotional discounts.
25. `promotion_products` — Pivot associations.
26. `notifications` — Categorized alerts (`Stock`, `Transfers`, `Discrepancies`, `Orders`, `System`).
27. `password_reset_tokens` — Single-use password tokens.
28. `audit_logs` — Tamper-evident read-only audit log of all system changes.
29. `app_settings` — Dynamic key-value store for business rules (thresholds, timeouts, defaults).

---

## 🎨 UI/UX Mockup Implementations

The frontend reproduces the design mockups in pixel-accurate detail:

| View | Route | Specification & UI Match |
|---|---|---|
| **Inventory by branch** | `/admin/inventory` | 4 summary metric cards, branch filter pills, stock status badges (`Healthy`, `Low`, `Sold out`), and right-hand stock adjustment drawer with on-hand vs reserved validation rules. |
| **Branch Distribution** | `/admin/distribution` | Two-row stat cards (Main branch stock, Sent today/week/month, In transit, Disputed), Latest transfer per branch with actions (`Resolve`, `Remind`, `Dispatch`, `View`), stock requests conversion table, and rules card. |
| **New Transfer** | `/admin/transfers/new` | 3-step transfer wizard (1. Route, 2. Products to send with live Main stock deduction, 3. Receiving at destination preview with `received + damaged + missing = sent` check, and workflow step sidebar). |
| **All Orders** | `/admin/orders` | Status tab bar (`All`, `Pending`, `Confirmed`, `Preparing`, `Ready`, `Out for delivery`, `Completed`, `Cancelled`), filter bar, orders table, and interactive right-hand order detail panel with items snapshot and status history timeline. |
| **Custom Cake Requests** | `/admin/custom-cakes` | 4 summary cards (`Submitted`, `Quoted`, `Confirmed`, `Ready this week`), requests table, and right-hand quoting drawer with reference image gallery, price/deposit form, and rejection reason. |
| **Payments** | `/admin/payments` | Revenue cards (online revenue, pay on pickup, failed, refunds), gateway transactions table, and refund processing drawer. |
| **Reports** | `/admin/reports` | 8 report tabs, date/branch/category filters, 4 metric cards, horizontal bar chart for revenue by branch, top products table, and branch performance breakdown. |
| **Notifications** | `/admin/notifications` | Categorized alerts (`All`, `Stock`, `Transfers`, `Discrepancies`, `Orders`, `System`), mark all as read, and right-hand alert rule configuration panel. |
| **Settings** | `/admin/settings` | Configurable rules for unpaid order reservations (15m), default reorder level (10), approval threshold (200 units), unreceived reminder (24h), gateways, currency (ETB), 2FA, and backups. |
| **Audit Log** | `/admin/audit-logs` | Read-only system audit log tracking who changed what and when with multi-field filtering and CSV export. |
| **Customer Storefront** | `/store` | Branch selection modal with persistent header control, product browsing with branch stock checking, cart drawer, and checkout with delivery zone fee calculation. |
| **Order Tracking** | `/track-order` | Order status search and visual milestone progress stepper. |
| **Custom Cake Studio** | `/custom-cake` | Customer cake builder allowing design notes, cake sizes, flavor selections, date selection, and image references. |

---

## 👥 Seeded User Accounts (5 Roles)

All accounts use the password: `password123`

1. **Super Admin:** `admin@goldencrust.com` (Unrestricted access across all branches, settings, and audit logs)
2. **Main Branch Manager:** `main@goldencrust.com` (Main branch stock, transfer creation and dispatches)
3. **Branch Admin (Bole):** `bole.admin@goldencrust.com` (Assigned to Bole branch; scoped inventory, receipts, and order management)
4. **Branch Admin (Megenagna):** `megenagna.admin@goldencrust.com` (Assigned to Megenagna branch)
5. **Branch Admin (Piassa):** `piassa.admin@goldencrust.com` (Assigned to Piassa branch)
6. **Branch Staff (Bole):** `bole.staff@goldencrust.com` (Assigned to Bole branch; sales and order preparation)
7. **Customer:** `customer@gmail.com` (Sara Mengistu; customer orders and cake requests)

> **Role Switcher:** An interactive role switcher dropdown is available at the top of the admin interface to test different permission scopes immediately without manual re-login.

---

## 🚀 Running the Project

### 1. Database
Make sure MySQL is running on port 3306 with the `kenan_cake_db` database:
```bash
# Started via mysqld standalone with defaults
C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone
```

### 2. Backend (Laravel API)
```bash
cd "C:\Kenan Cake\Backend"
php artisan serve --port=8000
```
Runs at: `http://127.0.0.1:8000`

### 3. Frontend (Vue 3 + Vite)
```bash
cd "C:\Kenan Cake\Client"
npm run dev -- --host
```
Runs at: `http://localhost:5173`
Proxy routes all `/api` requests seamlessly to `http://127.0.0.1:8000`.
