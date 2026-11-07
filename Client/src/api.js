import axios from 'axios';
import { appConfig } from './config';
import { toast } from './services/feedback';

const api = axios.create({
  baseURL: appConfig.apiBaseUrl,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status;
    const responseData = error.response?.data;

    if (status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.dispatchEvent(new Event('app:session-expired'));
    } else if (status === 403) {
      toast.error(responseData?.message || 'You do not have permission to do that.');
    } else if (status === 422) {
      error.fieldErrors = responseData?.errors || {};
      const firstFieldError = Object.values(error.fieldErrors)
        .flat()
        .find((message) => typeof message === 'string');
      toast.error(firstFieldError || responseData?.message || 'Please check the submitted information.');
    } else if (!error.response) {
      toast.error('Unable to connect to the server. Check your connection and try again.');
    } else if (status >= 500) {
      toast.error('The server could not complete your request. Please try again.');
    } else {
      toast.error(responseData?.message || 'The request could not be completed.');
    }

    return Promise.reject(error);
  }
);

export default api;
