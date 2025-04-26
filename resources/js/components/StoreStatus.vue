<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center">
        <div :class="[isOpen ? 'bg-green-100' : 'bg-red-100', 'flex-shrink-0 rounded-md p-3']">
          <svg 
            :class="[isOpen ? 'text-green-600' : 'text-red-600', 'h-6 w-6']" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor">
            <path v-if="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
          </svg>
        </div>
        <div class="ml-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Store Status
          </h3>
          <div class="mt-2">
            <p :class="[isOpen ? 'text-green-700' : 'text-red-700', 'text-xl font-semibold']">
              {{ isOpen ? 'Open Now' : 'Currently Closed' }}
            </p>
            <p v-if="!isOpen && nextOpenDate" class="text-gray-600 mt-1">
              Next Open: {{ formatNextOpenDate(nextOpenDate) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { openingHoursApi } from '../routes';

export default {
  setup() {
    const isOpen = ref(false);
    const nextOpenDate = ref(null);
    const statusInterval = ref(null);

    const fetchStatus = async () => {
      try {
        const response = await openingHoursApi.getStatus();
        isOpen.value = response.data.is_open;
        nextOpenDate.value = response.data.next_open_date;
      } catch (error) {
        console.error('Error fetching store status:', error);
      }
    };

    const formatNextOpenDate = (date) => {
      const nextOpen = new Date(date);
      const options = { 
        weekday: 'long', 
        month: 'short', 
        day: 'numeric', 
        hour: 'numeric', 
        minute: '2-digit' 
      };
      return nextOpen.toLocaleDateString('en-US', options);
    };

    onMounted(() => {
      // Initial fetch
      fetchStatus();
      
      // Refresh status every minute
      statusInterval.value = setInterval(fetchStatus, 60000);
    });

    onUnmounted(() => {
      // Clear interval when component is destroyed
      if (statusInterval.value) {
        clearInterval(statusInterval.value);
      }
    });

    return {
      isOpen,
      nextOpenDate,
      formatNextOpenDate
    };
  }
};
</script> 