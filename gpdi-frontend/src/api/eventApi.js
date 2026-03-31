import api from './axiosInstance';

export const eventApi = {
    // --- ENDPOINT PUBLIK (Tidak butuh login) ---
    getWorshipSchedules: () => {
        return api.get('/api/public/worship-schedules');
    },
    getActivitySchedules: () => {
        return api.get('/api/public/activity-schedules');
    },

    // --- ENDPOINT PRIVATE (Butuh JWT Token & Role yang sesuai) ---
    
    // Manajemen Ibadah Raya (Hanya lihat)
    getPrivateWorshipSchedules: () => {
        return api.get('/api/event/worship-schedules');
    },
    getPrivateWorshipScheduleById: (id) => {
        return api.get(`/api/event/worship-schedules/${id}`);
    },

    // Manajemen Ibadah Rayon (CRUD)
    getRayonSchedules: () => {
        return api.get('/api/event/rayon-schedules');
    },
    getRayonScheduleById: (id) => {
        return api.get(`/api/event/rayon-schedules/${id}`);
    },
    createRayonSchedule: (data) => {
        return api.post('/api/event/rayon-schedules', data);
    },
    updateRayonSchedule: (id, data) => {
        return api.put(`/api/event/rayon-schedules/${id}`, data);
    },
    deleteRayonSchedule: (id) => {
        return api.delete(`/api/event/rayon-schedules/${id}`);
    }
};