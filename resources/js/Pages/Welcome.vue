<template>
    <div class="flex">
      <!-- Sidebar Component -->
      <sidebar></sidebar>

      <!-- Main Content Area -->
      <div class="flex-1 p-6 bg-gray-100">
        <h1 class="text-3xl font-bold">Welcome to the Home Page</h1>
        <p>This is the main content area.</p>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'Home',
  };
  </script>

  <style scoped>
  /* Optional styling for the home page */
  </style>
