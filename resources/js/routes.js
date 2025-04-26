import axios from 'axios';

// Set default axios headers
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Get CSRF token from the meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Opening Hours API Routes
export const openingHoursApi = {
    getAll: () => axios.get('/api/admin/opening-hours'),
    get: (id) => axios.get(`/api/admin/opening-hours/${id}`),
    create: (data) => axios.post('/api/admin/opening-hours', data),
    update: (id, data) => axios.put(`/api/admin/opening-hours/${id}`, data),
    bulkUpdate: (data) => axios.post('/api/admin/opening-hours/bulk', data),
    delete: (id) => axios.delete(`/api/admin/opening-hours/${id}`),
    getStatus: () => axios.get('/api/status'),
    checkDate: (date) => axios.post('/api/check-date', { date }),
};

// Notifications API Routes
export const notificationsApi = {
    subscribe: (email) => axios.post('/api/notifications/subscribe', { email }),
    unsubscribe: (email) => axios.post('/api/notifications/unsubscribe', { email }),
    sendAll: () => axios.get('/api/admin/notifications/send'),
};

// Appointments API Routes
export const appointmentsApi = {
    getAll: () => axios.get('/api/appointments'),
    get: (id) => axios.get(`/api/appointments/${id}`),
    create: (data) => axios.post('/api/appointments', data),
    update: (id, data) => axios.put(`/api/appointments/${id}`, data),
    delete: (id) => axios.delete(`/api/appointments/${id}`),
    getAvailableSlots: (date) => axios.post('/api/appointments/available-slots', { date }),
}; 