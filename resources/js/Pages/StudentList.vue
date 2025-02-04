<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  year: Number,
  classId: Number,
  className: String,
  students: Array
});

const navigateToSemak = (studentId) => {
  router.visit(`/kemaskini/tahun/${props.year}/student/${studentId}/semak`);
};

const showDropdown = ref(false);
const showAddModal = ref(false);
const showCsvModal = ref(false);

// Form for single student
const form = useForm({
  name: '',
  class_id: props.classId
});

// Form for CSV upload
const csvForm = useForm({
  file: null,
  class_id: props.classId
});

const submitStudent = () => {
  form.post(route('students.store'), {
    onSuccess: () => {
      showAddModal.value = false;
      form.reset();
    }
  });
};

const submitCsv = () => {
  let formData = new FormData();
  formData.append('file', csvForm.file);
  formData.append('class_id', props.classId);

  router.post(route('students.import'), formData, {
    forceFormData: true,
    onSuccess: () => {
      showCsvModal.value = false;
      csvForm.reset();
      window.location.reload();
    },
    onError: (errors) => {
      console.error('Import failed:', errors);
    }
  });
};

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const openAddModal = () => {
  showDropdown.value = false;
  showAddModal.value = true;
};

const openCsvModal = () => {
  showDropdown.value = false;
  showCsvModal.value = true;
};

const showDeleteModal = ref(false);
const studentToDelete = ref(null);

const confirmDelete = (studentId) => {
  studentToDelete.value = studentId;
  showDeleteModal.value = true;
};

const deleteStudent = () => {
  router.delete(`/students/${studentToDelete.value}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
      studentToDelete.value = null;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  studentToDelete.value = null;
};

const clearAllStudents = () => {
  if (confirm('Are you sure you want to clear all students from this class? This action cannot be undone.')) {
    router.delete(route('class.clear', props.classId), {
      onSuccess: () => {
        window.location.reload();
      },
      onError: () => {
        alert('Failed to clear students');
      }
    });
  }
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header with Back Button -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
          <Link :href="`/kemaskini/tahun/${year}`"
            class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200 transition-colors">
          <i class="fas fa-arrow-left text-mint-600"></i>
          </Link>
          <div>
            <h1 class="text-2xl font-semibold text-gray-800">{{ className }}</h1>
            <p class="text-sm text-gray-600">Senarai Pelajar</p>
          </div>
        </div>

        <button @click="clearAllStudents" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 
                 transition-colors duration-200 flex items-center gap-2 
                 shadow-sm hover:shadow-md">
          <i class="fas fa-trash"></i>
          <span class="hidden sm:inline">Clear All Students</span>
          <span class="sm:hidden">Clear All</span>
        </button>
      </div>

      <!-- Student List -->
      <div class="bg-white rounded-lg shadow">
        <div v-if="students.length === 0" class="p-4 text-center text-gray-500">
          No students found
        </div>
        <div v-else class="divide-y divide-gray-200">
          <div v-for="student in students" :key="student.id"
            class="p-4 hover:bg-gray-50 flex items-center justify-between">
            <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">{{ student.name }}</h3>
              <p class="text-sm text-gray-500">Tarikh: {{ student.tarikh }}</p>
            </div>
            <div class="flex space-x-2">
              <button @click="navigateToSemak(student.id)" class="p-2 text-mint-600 hover:bg-mint-50 rounded-lg">
                <i class="fas fa-clipboard-check"></i>
              </button>
              <button @click="confirmDelete(student.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Action Button with Dropdown -->
  <div class="fixed bottom-6 right-6">
    <div class="relative">
      <div class="absolute bottom-16 right-0 mb-2 flex flex-col space-y-2" v-if="showDropdown">
        <button @click="openAddModal"
          class="bg-mint-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-mint-700 transition-colors text-sm whitespace-nowrap">
          Add Student
        </button>
        <button @click="openCsvModal"
          class="bg-mint-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-mint-700 transition-colors text-sm whitespace-nowrap">
          Import CSV
        </button>
      </div>
      <button @click="toggleDropdown"
        class="w-14 h-14 bg-mint-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-mint-700 hover:scale-105 transition-all">
        <i class="fas fa-plus text-xl"></i>
      </button>
    </div>
  </div>

  <!-- Single Student Modal -->
  <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
      <h2 class="text-xl font-semibold mb-4">Add New Student</h2>
      <form @submit.prevent="submitStudent" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Student Name</label>
          <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            placeholder="Enter student name">
        </div>
        <div class="flex space-x-3">
          <button type="submit" class="flex-1 p-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
            Save
          </button>
          <button type="button" @click="showAddModal = false"
            class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- CSV Upload Modal -->
  <div v-if="showCsvModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
      <h2 class="text-xl font-semibold mb-4">Import Students from CSV</h2>

      <form @submit.prevent="submitCsv" class="space-y-4" enctype="multipart/form-data">
        <div>
          <label class="block text-sm font-medium text-gray-700">CSV File</label>
          <input type="file" accept=".csv" @input="e => csvForm.file = e.target.files[0]" class="mt-1 block w-full">
          <p class="mt-1 text-sm text-gray-500">File must be CSV with header 'name'</p>
        </div>

        <div class="flex space-x-3">
          <button type="submit" class="flex-1 p-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700"
            :disabled="!csvForm.file">
            Upload
          </button>
          <button type="button" @click="showCsvModal = false"
            class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
      <h2 class="text-xl font-semibold mb-4">Delete Student</h2>
      <p class="text-gray-600 mb-4">Are you sure you want to delete this student? This action cannot be undone.</p>

      <div class="flex space-x-3">
        <button @click="deleteStudent" class="flex-1 p-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
          Delete
        </button>
        <button @click="cancelDelete" class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
      </div>
    </div>
  </div>
</template>
