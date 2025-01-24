<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const showSingleForm = ref(false);
const showBulkForm = ref(false);

const studentForm = ref({
  name: '',
  class_id: '',
  // Add other student fields
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  // Handle CSV upload
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center mb-8">
        <Link :href="`/kemaskini/tahun/${year}`" class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200">
          <i class="fas fa-arrow-left"></i>
        </Link>
        <h1 class="text-2xl font-semibold text-gray-800">Tambah Pelajar</h1>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <!-- Single Student Option -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <button @click="showSingleForm = true" class="w-full p-4 border-2 border-mint-200 rounded-lg hover:bg-mint-50">
            <i class="fas fa-user-plus text-2xl text-mint-600 mb-2"></i>
            <h3 class="text-lg font-medium">Tambah Pelajar </h3>
          </button>
        </div>

        <!-- Bulk Upload Option -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <button @click="showBulkForm = true" class="w-full p-4 border-2 border-mint-200 rounded-lg hover:bg-mint-50">
            <i class="fas fa-file-upload text-2xl text-mint-600 mb-2"></i>
            <h3 class="text-lg font-medium">Tambah Secara Pukal</h3>
          </button>
        </div>
      </div>

      <!-- Single Student Form Modal -->
      <div v-if="showSingleForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h2 class="text-xl font-semibold mb-4">Tambah Maklumat Pelajar</h2>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama</label>
              <input type="text" v-model="studentForm.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <!-- Add other form fields -->
            <div class="flex justify-end gap-4">
              <button @click="showSingleForm = false" class="px-4 py-2 border rounded-md">Batal</button>
              <button type="submit" class="px-4 py-2 bg-mint-600 text-white rounded-md">Simpan</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Bulk Upload Modal -->
      <div v-if="showBulkForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h2 class="text-xl font-semibold mb-4">Upload Student Data</h2>
          <div class="space-y-4">
            <input type="file" accept=".csv" @change="handleFileUpload" class="block w-full">
            <p class="text-sm text-gray-600">Please upload a CSV file with student data</p>
            <div class="flex justify-end gap-4">
              <button @click="showBulkForm = false" class="px-4 py-2 border rounded-md">Cancel</button>
              <button class="px-4 py-2 bg-mint-600 text-white rounded-md">Upload</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
