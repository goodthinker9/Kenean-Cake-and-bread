# Golden Crust — Progress Log
> Spec Version 3.0 | Started: 2026-11-06

---

## Current State (pre-Slice 0)

### Backend (Laravel 13 / PHP 8.3)
- **DB:** SQLite (`database/database.sqlite`) — NOT yet on Supabase PostgreSQL.
- **Auth:** Sanctum token-auth wired up (`login`, `register`, `logout`, `user`). Works correctly.
- **Migrations:** 10 migration files covering all 29 tables. Never run against Supabase yet.
- **Seeder:** `DatabaseSeeder.php` populates branches, users (7 accounts), categories, products, inventory, transfers, orders, payments, notifications, audit_logs, settings. Rich and realistic.
- **Controllers:** All 13 controllers present. Business logic partially implemented (some stubs, some full).
- **Middleware:** `RoleAuthorizationMiddleware` registered as `admin.auth` and `role` aliases. Contains a **local/testing Super Admin fallback** (security risk — removed in Slice 1).
- **Known issues:**
  - `DB_CONNECTION=sqlite` in `.env` — must switch to `pgsql` pointing at Supabase.
  - `README.md` still mentions MySQL/XAMPP.
  - `.env.example` has no Supabase variables.
  - Two unauthenticated public routes that must be removed later: `/auth/switch-role`, `/auth/users`.
  - Customer-facing order/cake routes at bottom of `api.php` have **no auth middleware** (Slice 3).
  - `config/database.php` `pgsql` block uses `sslmode=prefer`; Supabase requires `require`.

### Frontend (Vue 3 + Vite + Pinia + Tailwind)
- All views scaffolded and visually functional using fake/hardcoded state.
- `auth.js` store seeds a **hardcoded Super Admin user object** as default state — no real auth yet (Slice 2).
- Router guard uses `role = user?.role || 'Super Admin'` fallback — no real guard (Slice 2).
- `api.js` Axios client correctly reads Bearer token from `localStorage`.
- Vite proxy routes `/api` → `http://127.0.0.1:8000`.

---

## Slice History

| # | Branch | Status | Date | Summary |
|---|--------|--------|------|---------|
| 0 | `slice/0-supabase-connection` | 🔄 In progress | 2026-11-06 | Supabase connection, migrate, seed, update README/.env.example |

---

## Next Slice

**Slice 1** — `slice/1-remove-dev-backdoors`
- Remove `/auth/switch-role` and `/auth/users` routes + controller methods.
- Remove the `local/testing Super Admin fallback` in `RoleAuthorizationMiddleware`.
- Remove the admin role-switcher UI component from `AdminLayout.vue`.
- Unauthenticated requests to protected routes must return 401 (no fallback).
