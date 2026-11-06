# Golden Crust — Architecture Decisions
> Spec Version 3.0

Decisions listed here are proposed defaults. Items marked **❓ OPEN** need your confirmation before coding.

---

## Confirmed Decisions

| ID | Area | Decision | Rationale |
|----|------|----------|-----------|
| D1 | DB | Supabase PostgreSQL via **session pooler** (port 5432, SSL `require`) | Spec §non-negotiable: session pooler/direct + SSL |
| D2 | DB | `DB_SSLMODE=require` set in `config/database.php` pgsql block | Supabase rejects plain connections |
| D3 | Auth | Laravel Sanctum tokens only; Supabase Auth is NOT used | Spec explicitly states "auth is Laravel Sanctum" |
| D4 | Pricing | `branch_product_prices` stores optional price overrides; falls back to `products.base_price` | Matches screenshot (Piassa choc cake ETB 880) |
| D5 | Roles | Role lives on `users.role` column, never in request body | Spec §non-negotiable: authorization only on server |
| D6 | Stock | All stock mutations: `DB::transaction` + `lockForUpdate` + `inventory_transactions` row | Spec §non-negotiable |
| D7 | Search | Use `ilike` (Postgres case-insensitive LIKE) everywhere, never `like` | Spec §non-negotiable |
| D8 | RLS | `ALTER TABLE ... ENABLE ROW LEVEL SECURITY` on every table, **no policies** (Laravel owns all queries) | Spec §non-negotiable |
| D9 | Seed | `DatabaseSeeder` uses `updateOrCreate` for idempotent re-seeding | Prevents duplicate-key errors on re-seed |
| D10 | Cache/Queue/Session | Switch from `database` driver (SQLite) to `database` driver (Postgres) — same driver, different DB | No Redis needed in Slice 0; revisit in Slice 7 |

---

## Open Decisions

| ID | Area | Question | Proposed Default |
|----|------|----------|-----------------|
| OD1 | Supabase | Will you provide the **Supabase connection string / credentials** so I can fill `.env`? The `.env.example` will show placeholders. | Placeholders until you supply credentials |
| OD2 | SSL cert | Does your Windows PHP have a valid CA bundle for SSL? (`curl.cainfo` / `openssl.cafile` in `php.ini`) | Will document how to set `PGSQL_ATTR_OPTIONS` if needed |
| OD3 | Migrations | The existing `enum` columns (e.g. `users.role`, `stock_transfers.status`) use Laravel `Blueprint::enum()`. On PostgreSQL this creates a **check constraint**, not a native PG enum type. This is fine but irreversible without a new migration. Accept? | Yes — check constraints are simpler to ALTER later |
| OD4 | Seeder | The seeder uses fixed order numbers (`ORD-1001`). Slice 7 replaces these with sequence-based numbering. The seed data will be replaced at that point. Accept? | Yes — seed numbers are for demo only |
| OD5 | `manual` reference_type | `InventoryTransaction` seed row has `reference_type => 'manual'` but the column has no enum constraint currently. Slice 7 adds DB CHECKs. For now leave as free text. | Yes |

---

## Postgres-Specific Notes

- `Schema::create` with `Blueprint::enum()` on Postgres creates an **unnamed check constraint** (not a PG `CREATE TYPE`). This is intentional — avoids migration pain.
- `ilike` is Postgres-only. All existing `where('column', 'like', ...)` calls must be replaced with `whereRaw('column ilike ?', ...)` or `DB::raw`. Tracked per-controller in Slice 1+.
- `lockForUpdate()` works on Postgres with the standard `FOR UPDATE` clause.
- Supabase's session pooler does not support advisory locks or `LISTEN/NOTIFY` — avoid those.
