import './bootstrap';
import { createApp } from 'vue';
import VCalendar from 'v-calendar';
import 'v-calendar/style.css';

// Import components
import StoreStatus from './components/StoreStatus.vue';
import DatePicker from './components/DatePicker.vue';
import NotificationSubscription from './components/NotificationSubscription.vue';
import AppointmentForm from './components/AppointmentForm.vue';
import AdminOpeningHours from './components/AdminOpeningHours.vue';
import AdminOpeningHoursPage from './components/AdminOpeningHoursPage.vue';
import AdminAppointments from './components/AdminAppointments.vue';

// Create Vue app
const app = createApp({});

// Use VCalendar
app.use(VCalendar, {});

// Register components
app.component('store-status', StoreStatus);
app.component('date-picker', DatePicker);
app.component('notification-subscription', NotificationSubscription);
app.component('appointment-form', AppointmentForm);
app.component('admin-opening-hours', AdminOpeningHours);
app.component('admin-opening-hours-page', AdminOpeningHoursPage);
app.component('admin-appointments', AdminAppointments);

// Mount the app
app.mount('#app');
