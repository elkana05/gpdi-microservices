import axios from 'axios';

// Membuat instance axios dengan Base URL dari .env
const api = axios.create({
    baseURL: import.meta.env.VITE_API_GATEWAY_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Request Interceptor: Otomatis pasang JWT Token jika ada di localStorage
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('jwt_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

// Response Interceptor: Memudahkan pembacaan data sesuai blueprint
api.interceptors.response.use((response) => {
    // Backend GPdI mengembalikan format: { status, message, data, meta }
    // Kita langsung return response.data keseluruhan agar komponen UI bisa membaca data & meta
    return response.data; 
}, (error) => {
    if (error.response && error.response.status === 401) {
        console.error("Token expired atau belum login");
        // Di sini nantinya bisa ditambahkan logika untuk redirect ke halaman Login
    }
    return Promise.reject(error.response ? error.response.data : error);
});

export default api;