<!-- SemakPage.vue -->
<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Mock Categories based on CSV data
const categories = [
  { id: 1, name: 'Amali Wuduk' },
  { id: 2, name: 'Amali Solat (Perlakuan)' },
  { id: 3, name: 'Amali Solat (Bacaan)' }
];

// Mock evaluation items from CSV
const evaluationItems = [
  // Amali Wuduk items
  { id: 1, title: 'Bacaan Basmalah', type: 'SUNAT', category_id: 1 },
  { id: 2, title: 'Membasuh kedua-dua tangan', type: 'RUKUN', category_id: 1 },
  { id: 3, title: 'Berkumur-kumur', type: 'SUNAT', category_id: 1 },
  { id: 4, title: 'Memasukkan air ke dalam hidung', type: 'RUKUN', category_id: 1 },
  { id: 5, title: 'Membasuh muka', type: 'RUKUN', category_id: 1 },
  { id: 6, title: 'Niat wuduk', type: 'RUKUN', category_id: 1 },
  { id: 7, title: 'Membasuh kedua-dua tangan hingga ke siku', type: 'RUKUN', category_id: 1 },
  { id: 8, title: 'Mengusap sebahagian kepala', type: 'RUKUN', category_id: 1 },
  { id: 9, title: 'Mengusap kedua telinga', type: 'SUNAT', category_id: 1 },
  { id: 10, title: 'Membasuh kedua-dua kaki hingga buku lali', type: 'RUKUN', category_id: 1 },
  { id: 11, title: 'Tertib', type: 'RUKUN', category_id: 1 },
  { id: 12, title: 'Doa selepas wuduk', type: 'SUNAT', category_id: 1 },

  // Amali Solat (Perlakuan) items
  { id: 13, title: 'Berdiri betul', type: 'RUKUN', category_id: 2 },
  { id: 14, title: 'Kedudukan tangan semasa takbiratulihram', type: 'SUNAT', category_id: 2 },
  { id: 15, title: 'Kedudukan tangan selepas takbiratulihram', type: 'SUNAT', category_id: 2 },
  { id: 16, title: 'Rukuk', type: 'RUKUN', category_id: 2 },
  { id: 17, title: 'Iktidal', type: 'RUKUN', category_id: 2 },
  { id: 18, title: 'Sujud', type: 'RUKUN', category_id: 2 },
  { id: 19, title: 'Duduk antara dua sujud', type: 'RUKUN', category_id: 2 },
  { id: 20, title: 'Duduk tahiyat akhir', type: 'RUKUN', category_id: 2 },
  { id: 21, title: 'Tertib', type: 'RUKUN', category_id: 2 },
  { id: 22, title: 'Tuma\'ninah', type: 'RUKUN', category_id: 2 },

  // Amali Solat (Bacaan) items
  { id: 23, title: 'Takbiratulihram', type: 'RUKUN', category_id: 3 },
  { id: 24, title: 'Fatihah', type: 'RUKUN', category_id: 3 },
  { id: 25, title: 'Bacaan Rukuk', type: 'SUNAT', category_id: 3 },
  { id: 26, title: 'Bacaan Iktidal', type: 'SUNAT', category_id: 3 },
  { id: 27, title: 'Bacaan Sujud', type: 'SUNAT', category_id: 3 },
  { id: 28, title: 'Bacaan Duduk Antara Dua Sujud', type: 'SUNAT', category_id: 3 },
  { id: 29, title: 'Tahiyat Akhir', type: 'RUKUN', category_id: 3 },
  { id: 30, title: 'Selawat Ibrahimiah', type: 'SUNAT', category_id: 3 }
];

const props = defineProps({
  year: {
    type: [String, Number],
    required: true
  },
  student: {
    type: Object,
    required: true
  }
});

// State to track expanded categories
const expandedCategories = ref({
  1: false,  // Amali Wuduk
  2: false,  // Amali Solat (Perlakuan)
  3: false   // Amali Solat (Bacaan)
});

// Scores for evaluation
const scores = ref({});

// Toggle category expansion
const toggleCategory = (categoryId) => {
  expandedCategories.value[categoryId] = !expandedCategories.value[categoryId];
};

const submitEvaluation = () => {
  console.log('Submitting scores:', scores.value);
  // TODO: Implement actual submission logic
  router.post(route('submit.evaluation'), {
    student_id: props.student.id,
    scores: scores.value
  });
};

const goBack = () => {
  router.back();
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button and Title -->
      <div class="flex items-center mb-6">
        <button
          @click="goBack"
          class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200"
        >
          <i class="fas fa-arrow-left"></i>
        </button>
        <div>
          <h1 class="text-2xl font-semibold text-gray-800">{{ student.class.name }}</h1>
          <p class="text-sm text-gray-600">Senarai Pelajar</p>
        </div>
      </div>

      <!-- Student Info -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ student.name }}</h1>
        <p class="text-gray-600">{{ student.class.year.name }} | {{ student.class.name }}</p>
      </div>

      <!-- Categories with Accordion -->
      <div v-for="category in categories" :key="category.id" class="mb-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <!-- Category Header (Accordion Trigger) -->
          <button
            @click="toggleCategory(category.id)"
            class="w-full flex justify-between items-center p-6 bg-mint-100 hover:bg-mint-200 transition-colors"
          >
            <h2 class="text-xl font-semibold text-gray-800">{{ category.name }}</h2>
            <i
              :class="[
                'fas',
                expandedCategories[category.id] ? 'fa-chevron-up' : 'fa-chevron-down'
              ]"
            ></i>
          </button>

          <!-- Category Content (Expandable) -->
          <div
            v-if="expandedCategories[category.id]"
            class="p-6 space-y-4"
          >
            <div
              v-for="item in evaluationItems.filter(i => i.category_id === category.id)"
              :key="item.id"
              class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg"
            >
              <div>
                <p class="font-medium">{{ item.title }}</p>
                <span
                  :class="[
                    'text-sm px-2 py-0.5 rounded-full',
                    item.type === 'RUKUN'
                      ? 'bg-red-100 text-red-800'
                      : 'bg-blue-100 text-blue-800'
                  ]"
                >
                  {{ item.type }}
                </span>
              </div>
              <div class="flex gap-3">
                <button
                  @click="scores[`item_${item.id}`] = 'Passed'"
                  class="px-4 py-2 rounded-md transition-colors"
                  :class="scores[`item_${item.id}`] === 'Passed'
                    ? 'bg-green-500 text-white'
                    : 'bg-gray-100 hover:bg-gray-200'"
                >
                  Passed
                </button>
                <button
                  @click="scores[`item_${item.id}`] = 'Not'"
                  class="px-4 py-2 rounded-md transition-colors"
                  :class="scores[`item_${item.id}`] === 'Not'
                    ? 'bg-red-500 text-white'
                    : 'bg-gray-100 hover:bg-gray-200'"
                >
                  Not
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end mt-6">
        <button
          @click="submitEvaluation"
          class="px-6 py-3 bg-mint-600 text-white rounded-lg hover:bg-mint-700"
        >
          Simpan Penilaian
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Optional: Add smooth transition for expanding/collapsing */
.category-content-enter-active,
.category-content-leave-active {
  transition: all 0.3s ease;
}
.category-content-enter-from,
.category-content-leave-to {
  opacity: 0;
  max-height: 0;
}
.category-content-enter-to,
.category-content-leave-from {
  opacity: 1;
  max-height: 1000px;
}
</style>
