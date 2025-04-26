<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Schedule a Visit
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        Book an appointment to visit our bakery.
      </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
      <form @submit.prevent="submitAppointment" class="space-y-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <div class="mt-1">
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
              <input
                type="email"
                id="email"
                v-model="form.email"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="appointment_date" class="block text-sm font-medium text-gray-700">Date</label>
            <div class="mt-1">
              <input
                type="date"
                id="appointment_date"
                v-model="form.appointment_date"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                :min="today"
                required
                @change="fetchAvailableSlots"
              />
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="appointment_time" class="block text-sm font-medium text-gray-700">Time</label>
            <div class="mt-1">
              <select
                id="appointment_time"
                v-model="form.appointment_time"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                :disabled="!availableSlots.length"
                required
              >
                <option value="" disabled selected>Select a time</option>
                <option v-for="slot in availableSlots" :key="slot" :value="slot">
                  {{ formatTime(slot) }}
                </option>
              </select>
              <p v-if="noSlotsMessage" class="mt-2 text-sm text-red-600">
                {{ noSlotsMessage }}
              </p>
            </div>
          </div>

          <div class="sm:col-span-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <div class="mt-1">
              <textarea
                id="notes"
                v-model="form.notes"
                rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              ></textarea>
            </div>
            <p class="mt-2 text-sm text-gray-500">Any special requests or details about your visit.</p>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="rounded-md bg-red-50 p-4">
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

        <!-- Success Message -->
        <div v-if="success" class="rounded-md bg-green-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">
                {{ success }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="loading || !availableSlots.length"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Book Appointment
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed, reactive } from 'vue';
import { appointmentsApi } from '../routes';

export default {
  setup() {
    const form = reactive({
      name: '',
      email: '',
      appointment_date: '',
      appointment_time: '',
      notes: ''
    });

    const loading = ref(false);
    const error = ref(null);
    const success = ref(null);
    const availableSlots = ref([]);
    const noSlotsMessage = ref('');

    const today = computed(() => {
      const date = new Date();
      return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    });

    const fetchAvailableSlots = async () => {
      if (!form.appointment_date) return;
      
      loading.value = true;
      availableSlots.value = [];
      noSlotsMessage.value = '';
      error.value = null;
      
      try {
        const response = await appointmentsApi.getAvailableSlots(form.appointment_date);
        availableSlots.value = response.data.available_slots || [];
        
        if (availableSlots.value.length === 0 && response.data.message) {
          noSlotsMessage.value = response.data.message;
        } else if (availableSlots.value.length === 0) {
          noSlotsMessage.value = 'No available time slots on this date.';
        }
      } catch (err) {
        error.value = 'Failed to fetch available time slots. Please try again.';
        console.error('Error fetching time slots:', err);
      } finally {
        loading.value = false;
      }
    };

    const submitAppointment = async () => {
      loading.value = true;
      error.value = null;
      success.value = null;
      
      try {
        const response = await appointmentsApi.create(form);
        success.value = response.data.message || 'Appointment scheduled successfully!';
        
        // Reset form
        form.name = '';
        form.email = '';
        form.appointment_date = '';
        form.appointment_time = '';
        form.notes = '';
        availableSlots.value = [];
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Failed to schedule appointment. Please try again.';
        }
        console.error('Error scheduling appointment:', err);
      } finally {
        loading.value = false;
      }
    };

    const formatTime = (timeString) => {
      // Convert 24-hour time format to 12-hour format
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours, 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const hour12 = hour % 12 || 12;
      return `${hour12}:${minutes} ${ampm}`;
    };

    return {
      form,
      loading,
      error,
      success,
      availableSlots,
      noSlotsMessage,
      today,
      fetchAvailableSlots,
      submitAppointment,
      formatTime
    };
  }
};
</script> 