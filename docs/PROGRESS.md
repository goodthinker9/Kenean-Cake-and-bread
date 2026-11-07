# Progress

## Current slice

- **F1 Foundations** — complete
- Branch: `feat/f1-foundations`
- Added shared app configuration with the temporary `Bakery` brand and
  `VITE_API_BASE_URL` support.
- Added centralized API handling for 401, 403, 422, server, and network errors.
- Added shared accessible toast and confirmation dialog hosts; removed native
  alert/confirm calls.
- Lazy-loaded routed layouts and views, removed unused starter/dead views, and
  added ESLint.
- Updated the app title and remaining visible brand references to use config.
- No backend changes or new API contracts were required.

## Completed slices

- **F1 Foundations**
  - `npm run build` — passed.
  - `npm run lint` — passed.
  - Browser smoke: backend-backed storefront rendered data; simulated 401
    redirected to login; simulated 403 and 422 showed toast feedback; simulated
    network failure showed a connection toast.
  - At 360px, verified no horizontal overflow and the 403 toast remained within
    the viewport.
  - `git diff --check` — passed.

## Next

- F2 Auth and guards — not started; awaiting approval to proceed.
