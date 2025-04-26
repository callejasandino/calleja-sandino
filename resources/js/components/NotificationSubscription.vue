<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-8 py-6">
      <h3 class="text-xl leading-6 font-medium text-gray-900">
        Notifications
      </h3>
      <div class="mt-3 max-w-xl text-base text-gray-500">
        <p>
          Subscribe to receive notifications when the bakery reopens.
        </p>
      </div>
      <form class="mt-6 sm:flex sm:items-center" @submit.prevent="subscribeToNotifications">
        <div class="w-full sm:max-w-xs">
          <label for="email" class="sr-only">Email</label>
          <input 
            type="email" 
            name="email" 
            id="email" 
            v-model="email"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full text-base border-gray-300 rounded-md py-3 px-4" 
            placeholder="you@example.com" 
            required
          >
        </div>
        <button 
          type="submit" 
          :disabled="loading" 
          class="mt-3 w-full inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-4 sm:w-auto text-base"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Subscribe
        </button>
      </form>

      <!-- Success Message -->
      <div v-if="success" class="mt-5 rounded-md bg-green-50 p-5">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-base font-medium text-green-800">
              {{ success }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mt-5 rounded-md bg-red-50 p-5">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-base font-medium text-red-800">
              {{ error }}
            </p>
          </div>
        </div>
      </div>

      <!-- Unsubscribe Link -->
      <div class="mt-6">
        <button 
          @click="showUnsubscribeForm = !showUnsubscribeForm" 
          type="button" 
          class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          {{ showUnsubscribeForm ? 'Hide' : 'Unsubscribe' }}
        </button>
      </div>

      <!-- Unsubscribe Form -->
      <div v-if="showUnsubscribeForm" class="mt-4">
        <form @submit.prevent="unsubscribeFromNotifications" class="sm:flex sm:items-center">
          <div class="w-full sm:max-w-xs">
            <label for="unsubscribe-email" class="sr-only">Email</label>
            <input 
              type="email" 
              name="unsubscribe-email" 
              id="unsubscribe-email" 
              v-model="unsubscribeEmail" 
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full text-base border-gray-300 rounded-md py-3 px-4" 
              placeholder="you@example.com" 
              required
            >
          </div>
          <button 
            type="submit" 
            :disabled="unsubscribeLoading" 
            class="mt-3 w-full inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-4 sm:w-auto text-base"
          >
            <svg v-if="unsubscribeLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Unsubscribe
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { notificationsApi } from '../routes';

export default {
  setup() {
    const email = ref('');
    const unsubscribeEmail = ref('');
    const loading = ref(false);
    const unsubscribeLoading = ref(false);
    const success = ref(null);
    const error = ref(null);
    const showUnsubscribeForm = ref(false);

    const subscribeToNotifications = async () => {
      if (!email.value) return;
      
      loading.value = true;
      success.value = null;
      error.value = null;
      
      try {
        const response = await notificationsApi.subscribe(email.value);
        success.value = response.data.message;
        email.value = '';
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Failed to subscribe. Please try again later.';
        }
        console.error('Error subscribing to notifications:', err);
      } finally {
        loading.value = false;
      }
    };

    const unsubscribeFromNotifications = async () => {
      if (!unsubscribeEmail.value) return;
      
      unsubscribeLoading.value = true;
      success.value = null;
      error.value = null;
      
      try {
        const response = await notificationsApi.unsubscribe(unsubscribeEmail.value);
        success.value = response.data.message;
        unsubscribeEmail.value = '';
        showUnsubscribeForm.value = false;
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Failed to unsubscribe. Please try again later.';
        }
        console.error('Error unsubscribing from notifications:', err);
      } finally {
        unsubscribeLoading.value = false;
      }
    };

    return {
      email,
      unsubscribeEmail,
      loading,
      unsubscribeLoading,
      success,
      error,
      showUnsubscribeForm,
      subscribeToNotifications,
      unsubscribeFromNotifications
    };
  }
};
</script>

<style scoped>
/* Ensure text is the right size */
button, input, p, h3 {
  font-size: 16px;
}

/* Improve input styling */
input {
  font-size: 16px;
  height: auto;
  padding: 12px 16px;
}

/* Mobile adjustments */
@media (max-width: 640px) {
  input, button {
    width: 100%;
  }
  
  button[type="submit"] {
    margin-top: 12px;
  }
}
</style> 