<template>
  <div class="py-8 px-4 md:px-8 max-w-6xl mx-auto">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Opening Hours Management</h1>
      <p class="text-gray-600">Configure regular and special opening hours for your bakery.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <admin-opening-hours></admin-opening-hours>
    </div>
  </div>
</template>

<script>
import AdminOpeningHours from './AdminOpeningHours.vue';

export default {
  name: 'AdminOpeningHoursPage',
  components: {
    AdminOpeningHours
  }
}
</script> 