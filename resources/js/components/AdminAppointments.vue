<template>
  <div>
    <div class="flex justify-between items-center mb-5">
      <div class="flex-1">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Upcoming Appointments
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          View and manage customer visits.
        </p>
      </div>
      <div>
        <button 
          @click="fetchAppointments" 
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <div v-if="error" class="rounded-md bg-red-50 p-4 mb-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-red-800">
            {{ error }}
          </p>
        </div>
      </div>
    </div>

    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date &amp; Time
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Notes
                  </th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="loading && !appointments.length">
                  <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                    Loading appointments...
                  </td>
                </tr>
                <tr v-else-if="!appointments.length">
                  <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                    No appointments found.
                  </td>
                </tr>
                <tr v-for="appointment in appointments" :key="appointment.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatDate(appointment.appointment_date) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ formatTime(appointment.appointment_time) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ appointment.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ appointment.email }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">
                      {{ appointment.notes || 'No notes provided' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="cancelAppointment(appointment)" 
                      class="text-red-600 hover:text-red-900"
                    >
                      Cancel
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Modal -->
    <div v-if="showCancelModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showCancelModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Cancel Appointment
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to cancel this appointment? This action cannot be undone.
                </p>
                <div class="mt-4 p-4 bg-gray-50 rounded-md">
                  <p class="text-sm font-medium text-gray-900">
                    {{ selectedAppointment ? selectedAppointment.name : '' }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ selectedAppointment ? formatDate(selectedAppointment.appointment_date) : '' }}, {{ selectedAppointment ? formatTime(selectedAppointment.appointment_time) : '' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button 
              type="button" 
              @click="confirmCancelAppointment" 
              :disabled="cancelLoading" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm"
            >
              <svg v-if="cancelLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Cancel Appointment
            </button>
            <button 
              type="button" 
              @click="showCancelModal = false" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
            >
              Keep Appointment
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { appointmentsApi } from '../routes';

export default {
  setup() {
    const appointments = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const showCancelModal = ref(false);
    const selectedAppointment = ref(null);
    const cancelLoading = ref(false);

    const fetchAppointments = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const response = await appointmentsApi.getAll();
        appointments.value = response.data;
      } catch (err) {
        error.value = 'Failed to fetch appointments. Please try again.';
        console.error('Error fetching appointments:', err);
      } finally {
        loading.value = false;
      }
    };

    const cancelAppointment = (appointment) => {
      selectedAppointment.value = appointment;
      showCancelModal.value = true;
    };

    const confirmCancelAppointment = async () => {
      if (!selectedAppointment.value) return;
      
      cancelLoading.value = true;
      
      try {
        await appointmentsApi.delete(selectedAppointment.value.id);
        // Remove the cancelled appointment from the list
        appointments.value = appointments.value.filter(a => a.id !== selectedAppointment.value.id);
        showCancelModal.value = false;
      } catch (err) {
        error.value = 'Failed to cancel appointment. Please try again.';
        console.error('Error cancelling appointment:', err);
      } finally {
        cancelLoading.value = false;
      }
    };

    const formatDate = (dateString) => {
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    };

    const formatTime = (timeString) => {
      // Convert 24-hour time format to 12-hour format
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours, 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const hour12 = hour % 12 || 12;
      return `${hour12}:${minutes} ${ampm}`;
    };

    onMounted(fetchAppointments);

    return {
      appointments,
      loading,
      error,
      showCancelModal,
      selectedAppointment,
      cancelLoading,
      fetchAppointments,
      cancelAppointment,
      confirmCancelAppointment,
      formatDate,
      formatTime
    };
  }
};
</script> 