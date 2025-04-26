<template>
  <div>
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Current Status -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Store Status
                </dt>
                <dd>
                  <store-status></store-status>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/admin/opening-hours" class="font-medium text-indigo-600 hover:text-indigo-500">
              Manage hours &rarr;
            </router-link>
          </div>
        </div>
      </div>

      <!-- Opening Hours summary -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Opening Hours
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                  Monday, Wednesday, Friday: 8:00 AM - 4:00 PM<br>
                  Every other Saturday: 8:00 AM - 4:00 PM
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/admin/opening-hours" class="font-medium text-indigo-600 hover:text-indigo-500">
              Modify schedule &rarr;
            </router-link>
          </div>
        </div>
      </div>

      <!-- Appointments summary -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Appointments
                </dt>
                <dd class="mt-1 text-sm text-gray-900">
                  Manage customer appointments and view upcoming visits
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <router-link to="/admin/appointments" class="font-medium text-indigo-600 hover:text-indigo-500">
              View appointments &rarr;
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h2>
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
          <li>
            <router-link to="/admin/opening-hours" class="block hover:bg-gray-50">
              <div class="px-4 py-4 flex items-center sm:px-6">
                <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <div class="text-sm font-medium text-indigo-600 truncate">
                      Manage opening hours
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                      Configure regular hours, breaks, and biweekly schedules
                    </div>
                  </div>
                </div>
                <div class="ml-5 flex-shrink-0">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/appointments" class="block hover:bg-gray-50">
              <div class="px-4 py-4 flex items-center sm:px-6">
                <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <div class="text-sm font-medium text-indigo-600 truncate">
                      View appointments
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                      See all scheduled customer visits
                    </div>
                  </div>
                </div>
                <div class="ml-5 flex-shrink-0">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </router-link>
          </li>
          <li>
            <a href="/" class="block hover:bg-gray-50">
              <div class="px-4 py-4 flex items-center sm:px-6">
                <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <div class="text-sm font-medium text-indigo-600 truncate">
                      Return to website
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                      View the customer-facing website
                    </div>
                  </div>
                </div>
                <div class="ml-5 flex-shrink-0">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import StoreStatus from './StoreStatus.vue';

export default {
  components: {
    StoreStatus
  }
};
</script> 