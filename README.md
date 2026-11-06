# Golden Crust — Multi-Branch Cake & Bread Shop Management System
**Specification Version 3.0 Implementation**

A full-stack, enterprise-grade multi-branch bakery platform built with **Laravel**, **Vue.js 3**, and **Supabase PostgreSQL**, adhering to the complete system specification and UI/UX design mockups.

---

## 🏗️ Architecture & Technology Stack

- **Backend:** Laravel 13 (PHP 8.3+) with Laravel Sanctum, Eloquent ORM, Database Transactions with Row Locks (`lockForUpdate()`), and RESTful Controllers.
- **Frontend:** Vue 3 (Composition API), Vite, Tailwind CSS, Pinia State Management, and Vue Router.
- **Database:** Supabase PostgreSQL — session pooler, SSL required, RLS enabled on all tables.
- **Auth:** Laravel Sanctum (Bearer tokens). Supabase Auth is **not** used.

---

## 🗄️ Database Design (29+ Tables)

Conforming to **Section 11** of the specification:

1. `branches` — Main branch (`MAIN01`) and regular branches (`BR001` Bole, `BR002` Megenagna, `BR003` Piassa).
2. `users` — 5 RBAC roles (`Super Admin`, `Main Branch Manager`, `Branch Admin`, `Branch Staff`, `Customer`).
3. `addresses` — Customer delivery addresses with default flags.
4. `delivery_zones` — Branch-specific delivery zones with localized delivery fees (e.g. Bole Medhanialem ETB 60).
5. `categories` — Cakes, Bread, Pastries.
6. `products` — Product catalog with base prices and shelf life in hours.
7. `product_images` — Image URLs and sort orders.
8. `branch_product_prices` — Optional active branch price overrides (e.g. Piassa Chocolate Cake ETB 880).
9. `branch_inventory` — Isolated per-branch stock: `quantity_on_hand`, `reserved_quantity`, and `reorder_level`.
10. `inventory_transactions` — Traceable audit log for all stock movements.
11. `stock_transfers` — Main-to-branch distribution with unique TRF numbers.
12. `stock_transfer_items` — Transfer item lines tracking quantities and discrepancies.
13. `stock_requests` — Branch-initiated replenishment requests.
14. `stock_request_items` — Requested items and quantities.
15. `carts` — Customer active cart scoped to a chosen branch.
16. `cart_items` — Product and quantity selections.
17. `orders` — Orders with unique `ORD-xxxx` numbers, snapshot prices, and branch binding.
18. `order_items` — Snapshot product names, quantities, unit prices, and subtotals.
19. `order_status_history` — Immutable status progression audit trail.
20. `payments` — Gateways (`Chapa`, `Telebirr`, Pay on pickup) with unique transaction references.
21. `custom_cake_requests` — Custom celebratory cake specifications.
22. `custom_cake_images` — Reference design image URLs.
23. `reviews` — Product ratings and customer reviews.
24. `promotions` — Promotional discounts.
25. `promotion_products` — Pivot associations.
26. `notifications` — Categorized alerts (`Stock`, `Transfers`, `Discrepancies`, `Orders`, `System`).
27. `password_reset_tokens` — Single-use password tokens.
28. `audit_logs` — Tamper-evident read-only audit log of all system changes.
29. `app_settings` — Dynamic key-value store for business rules.

---

## 🚀 Setup & Running the Project

### Prerequisites
- PHP 8.3+ with `pdo_pgsql` extension enabled
- Composer
- Node.js 20+ and npm
- A Supabase project (free tier works for development)

### 1. Configure the Database (Supabase)

1. Create a project at [supabase.com](https://supabase.com).
2. Go to **Project Settings → Database → Connection pooling**.
3. Select **Session mode** (port 5432).
4. Copy the host, project ref, and password.

### 2. Backend Setup

```bash
cd Backend

# Install PHP dependencies
composer install

# Copy and fill in your environment file
cp .env.example .env
# Edit .env — replace all YOUR_* values with your Supabase credentials

# Generate application key (if not already set)
php artisan key:generate

# Run all migrations against Supabase
php artisan migrate

# Seed the database with realistic demo data
php artisan db:seed

# Start the API server
php artisan serve --port=8000
```

API runs at: `http://127.0.0.1:8000`

> **Windows SSL note:** If you get SSL errors connecting to Supabase, download
> [cacert.pem](https://curl.se/ca/cacert.pem) and add this to your `php.ini`:
> ```
> openssl.cafile = "C:/path/to/cacert.pem"
> ```

### 3. Frontend Setup

```bash
cd Client

# Install Node dependencies
npm install

# Start the development server
npm run dev
```

Runs at: `http://localhost:5173`

The Vite dev server proxies all `/api` requests to `http://127.0.0.1:8000`.

---

## 👥 Seeded User Accounts (7 Users, 5 Roles)

All demo accounts use the password: `password123`

| Email | Role | Branch |
|---|---|---|
| `admin@goldencrust.com` | Super Admin | All branches |
| `main@goldencrust.com` | Main Branch Manager | Main Branch |
| `bole.admin@goldencrust.com` | Branch Admin | Bole |
| `megenagna.admin@goldencrust.com` | Branch Admin | Megenagna |
| `piassa.admin@goldencrust.com` | Branch Admin | Piassa |
| `bole.staff@goldencrust.com` | Branch Staff | Bole |
| `customer@gmail.com` | Customer | — |

---

## 🔐 Security Architecture

- **Authorization is server-only.** Role and branch come from the authenticated user record, never from request fields.
- **RLS enabled** on all Postgres tables. No RLS policies — Laravel connects as the DB owner and bypasses RLS by design.
- **Stock mutations** use `DB::transaction` + `lockForUpdate` + an `inventory_transactions` audit row on every change.
- **Postgres conventions:** `ilike` for case-insensitive search, `CHECK` constraints via `DB::statement`, SSL required.

---

## 📂 Project Structure

```
Kenean-Cake-and-bread/
├── Backend/          # Laravel 13 API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Http/Middleware/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/api.php
├── Client/           # Vue 3 + Vite frontend
│   └── src/
│       ├── views/admin/
│       ├── views/customer/
│       ├── stores/
│       └── router/
└── docs/
    ├── PROGRESS.md   # Slice-by-slice progress log
    └── DECISIONS.md  # Architecture decisions & open questions
```
