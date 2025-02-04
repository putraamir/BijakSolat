<script setup>
import { Link, router } from '@inertiajs/vue3'; // Add router import
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  year: Number,
  classId: Number,
  className: String,
  students: {
    type: Array,
    default: () => [
      { id: 1, name: 'Ahmad Bin Abdullah', tarikh: '12/01/2024', tahap: 1 },
      { id: 2, name: 'Sarah Binti Omar', tarikh: '12/01/2024', tahap: 1 },
    ]
  }
});

const navigateToSemak = (studentId) => {
  router.visit(`/kemaskini/tahun/${props.year}/student/${studentId}/semak`);
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header with Back Button -->
      <div class="flex items-center mb-8">
        <Link :href="`/kemaskini/tahun/${year}`" class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200">
          <i class="fas fa-arrow-left"></i>
        </Link>
        <div>
          <h1 class="text-2xl font-semibold text-gray-800">{{ className }}</h1>
          <p class="text-sm text-gray-600">Senarai Pelajar</p>
        </div>
      </div>

      <!-- Student List -->
      <div class="bg-white rounded-lg shadow">
        <div class="divide-y divide-gray-200">
          <div v-for="student in students" :key="student.id"
               class="p-4 hover:bg-gray-50 flex items-center justify-between">
            <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">{{ student.name }}</h3>
              <p class="text-sm text-gray-500">Tarikh: {{ student.tarikh }}</p>
            </div>
            <div class="flex space-x-2">
              <button
                @click="navigateToSemak(student.id)"
                class="p-2 text-mint-600 hover:bg-mint-50 rounded-lg"
              >
                <i class="fas fa-clipboard-check"></i>
              </button>
              <button
                @click="deleteStudent(student.id)"
                class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
