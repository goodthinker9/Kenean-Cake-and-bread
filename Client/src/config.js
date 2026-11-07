export const appConfig = Object.freeze({
  brandName: 'Bakery',
  currency: 'ETB',
  apiBaseUrl: import.meta.env.VITE_API_BASE_URL?.trim() || '/api',
});
