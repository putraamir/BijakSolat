//AddStudent.vue
<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const showSingleForm = ref(false);
const showBulkForm = ref(false);

const studentForm = ref({
  name: '',
  tahun: '',
  kelas: '',
  class_id: '',
});

// Add data for dropdowns
const tahunOptions = [
  { id: 1, name: 'Tahun 1' },
  { id: 2, name: 'Tahun 2' },
  { id: 3, name: 'Tahun 3' },
  { id: 4, name: 'Tahun 4' },
  { id: 5, name: 'Tahun 5' },
  { id: 6, name: 'Tahun 6' },
];

const kelasOptions = [
  { id: 1, name: 'Cemerlang' },
  { id: 2, name: 'Gemilang' },
];

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  // Handle CSV upload
};

const submitForm = () => {
  // Handle form submission
  console.log('Form submitted:', studentForm.value);
};

defineProps({
  year: {
    type: String,
    required: true
  }
});
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
            <!-- Name Field -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama</label>
              <input
                type="text"
                v-model="studentForm.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                required
              >
            </div>

            <!-- Tahun Dropdown -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Tahun</label>
              <select
                v-model="studentForm.tahun"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                required
              >
                <option value="">Pilih Tahun</option>
                <option
                  v-for="tahun in tahunOptions"
                  :key="tahun.id"
                  :value="tahun.id"
                >
                  {{ tahun.name }}
                </option>
              </select>
            </div>

            <!-- Kelas Dropdown -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Kelas</label>
              <select
                v-model="studentForm.kelas"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                required
              >
                <option value="">Pilih Kelas</option>
                <option
                  v-for="kelas in kelasOptions"
                  :key="kelas.id"
                  :value="kelas.id"
                >
                  {{ kelas.name }}
                </option>

            </select>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end gap-4">
              <button
                @click="showSingleForm = false"
                type="button"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-mint-600 text-white rounded-md hover:bg-mint-700"
              >
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Bulk Upload Modal -->
      <div v-if="showBulkForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h2 class="text-xl font-semibold mb-4">Upload Student Data</h2>
          <div class="space-y-4">
            <div class="flex flex-col gap-2">
              <!-- Tahun Dropdown for Bulk Upload -->
              <label class="block text-sm font-medium text-gray-700">Tahun</label>
              <select
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                required
              >
                <option value="">Pilih Tahun</option>
                <option
                  v-for="tahun in tahunOptions"
                  :key="tahun.id"
                  :value="tahun.id"
                >
                  {{ tahun.name }}
                </option>
              </select>

              <!-- Kelas Dropdown for Bulk Upload -->
              <label class="block text-sm font-medium text-gray-700">Kelas</label>
              <select
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-mint-500 focus:ring-mint-500"
                required
              >
                <option value="">Pilih Kelas</option>
                <option
                  v-for="kelas in kelasOptions"
                  :key="kelas.id"
                  :value="kelas.id"
                >
                  {{ kelas.name }}
                </option>
              </select>

              <!-- File Upload -->
              <label class="block text-sm font-medium text-gray-700">File CSV</label>
              <input
                type="file"
                accept=".csv"
                @change="handleFileUpload"
                class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-md file:border-0
                  file:text-sm file:font-semibold
                  file:bg-mint-50 file:text-mint-700
                  hover:file:bg-mint-100"
              >
            </div>
            <p class="text-sm text-gray-600">Please upload a CSV file with student data</p>
            <div class="flex justify-end gap-4">
              <button
                @click="showBulkForm = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                class="px-4 py-2 bg-mint-600 text-white rounded-md hover:bg-mint-700"
              >
                Upload
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
