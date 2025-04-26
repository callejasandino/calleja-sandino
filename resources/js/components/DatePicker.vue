<template>
  <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
    <div class="px-8 py-6 sm:px-8">
      <h3 class="text-xl leading-6 font-medium text-gray-900">
        Check Opening Hours
      </h3>
      <p class="mt-2 text-base text-gray-500">
        Select a date to check when the bakery is open.
      </p>
    </div>
    <div class="px-8 py-6 sm:p-8">
      <div class="flex flex-col space-y-5">
        <div>
          <label for="date" class="block text-base font-medium text-gray-700 mb-2">Date</label>
          <input 
            type="date" 
            id="date" 
            v-model="selectedDate" 
            class="date-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-3 px-4"
            :min="today"
          >
        </div>
        <div class="mt-2">
          <button 
            @click="checkDate" 
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Check Availability
          </button>
        </div>
      </div>

      <!-- Results -->
      <div v-if="status" class="mt-8">
        <div v-if="status.is_open" class="bg-green-50 p-6 rounded-md">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-green-800">
                Open on {{ formatDate(selectedDate) }}
              </h3>
              <div class="mt-3 text-base text-green-700">
                <p>Hours: {{ formatTime(status.open_time) }} - {{ formatTime(status.close_time) }}</p>
                <div v-if="status.break_times && status.break_times.length > 0" class="mt-2">
                  <p>Breaks:</p>
                  <ul class="list-disc pl-5 mt-2">
                    <li v-for="(breakTime, index) in status.break_times" :key="index" class="text-base mb-1">
                      {{ formatTime(breakTime.start_time) }} - {{ formatTime(breakTime.end_time) }}
                    </li>
                  </ul>
                </div>
                <p v-if="status.popularity_insight" class="mt-3 italic font-medium text-indigo-600 text-base">
                  {{ status.popularity_insight }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="bg-red-50 p-6 rounded-md">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-red-800">
                Closed on {{ formatDate(selectedDate) }}
              </h3>
              <div class="mt-3 text-base text-red-700">
                <p>{{ status.message }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { openingHoursApi } from '../routes';

export default {
  setup() {
    const selectedDate = ref('');
    const status = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const today = computed(() => {
      const date = new Date();
      return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    });

    const checkDate = async () => {
      if (!selectedDate.value) return;
      
      loading.value = true;
      error.value = null;
      
      try {
        const response = await openingHoursApi.checkDate(selectedDate.value);
        status.value = response.data;
      } catch (err) {
        error.value = 'Failed to fetch store hours. Please try again.';
        console.error('Error checking date:', err);
      } finally {
        loading.value = false;
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

    return {
      selectedDate,
      status,
      loading,
      error,
      today,
      checkDate,
      formatDate,
      formatTime
    };
  }
};
</script>

<style scoped>
/* Improve date picker styling */
.date-input {
  font-size: 16px;
  height: auto;
  padding: 12px 16px;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 20px 20px;
  padding-right: 40px;
}

/* Fix Firefox date input styling */
.date-input::-webkit-calendar-picker-indicator {
  width: 20px;
  height: 20px;
  opacity: 0;
  cursor: pointer;
}

/* Ensure text is the right size */
button, input, p, li, label, h3 {
  font-size: 16px;
}

@media (max-width: 640px) {
  .date-input {
    padding: 12px 16px;
  }
}
</style> 