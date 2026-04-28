/**
 * Axios Instance Configuration
 * Digunakan untuk semua HTTP request ke API Gateway
 */

import axios from "axios";

// Buat instance axios dengan konfigurasi default
const axiosInstance = axios.create({
  baseURL: "http://localhost:8000/api", // URL API Gateway
  timeout: 10000, // Timeout 10 detik
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// ==========================================
// REQUEST INTERCEPTOR - Tambahkan Token ke setiap request
// ==========================================
axiosInstance.interceptors.request.use(
  (config) => {
    // Ambil token dari localStorage (atau sessionStorage)
    const token = localStorage.getItem("token");

    if (token) {
      // Tambahkan Authorization header dengan Bearer token
      config.headers.Authorization = `Bearer ${token}`;
    }

    // PENTING: Pastikan Content-Type selalu application/json
    config.headers["Content-Type"] = "application/json";

    console.log("[Axios Request]", {
      method: config.method,
      url: config.url,
      headers: config.headers,
      data: config.data,
    });

    return config;
  },
  (error) => {
    console.error("[Axios Request Error]", error);
    return Promise.reject(error);
  },
);

// ==========================================
// RESPONSE INTERCEPTOR - Handle response dan error
// ==========================================
axiosInstance.interceptors.response.use(
  (response) => {
    console.log("[Axios Response]", {
      status: response.status,
      data: response.data,
    });
    return response;
  },
  (error) => {
    console.error("[Axios Response Error]", {
      status: error.response?.status,
      statusText: error.response?.statusText,
      data: error.response?.data,
      message: error.message,
    });

    // Handle error 401 (Unauthorized) - redirect ke login
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      window.location.href = "/login";
    }

    return Promise.reject(error);
  },
);

export default axiosInstance;
