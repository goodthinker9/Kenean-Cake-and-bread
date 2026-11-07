# Decisions

- Work one approved slice at a time on its own Git branch.
- Keep existing frontend visual styling unless the active slice requires a
  change.
- Centralize frontend API requests and common HTTP error handling without
  changing existing backend contracts.
- Do not modify the backend as part of F1.
- Keep the placeholder brand name (`Bakery`), currency (`ETB`), and API base URL
  in `Client/src/config.js`; use `/api` as the default API base.
- On 401, clear local auth state and redirect to `/login`; show shared toast
  feedback for 403, validation, server, and network errors. Preserve 422 field
  errors on the Axios error object as `fieldErrors`.
- Use globally mounted accessible toast and confirmation dialog components
  rather than browser-native alert/confirm dialogs.
- Lazy-load routed layouts and pages. Remove only unused starter/legacy views
  that have no route or import references.
- At narrow widths, compact customer header labels while retaining accessible
  labels and the selected branch context.
