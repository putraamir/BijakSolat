<!-- SemakPage.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  student: Object,
  categories: Array,
  existingEvaluations: Object
});

const expandedCategories = ref({});
const scores = ref({});

// Initialize scores with existing evaluations
onMounted(() => {
  if (props.existingEvaluations) {
    Object.entries(props.existingEvaluations).forEach(([itemId, status]) => {
      scores.value[`item_${itemId}`] = status === 'passed' ? 'Passed' : 'Not';
    });
  }
});

const toggleCategory = (categoryId) => {
  expandedCategories.value[categoryId] = !expandedCategories.value[categoryId];
};

const submitEvaluation = () => {
  const formattedScores = {};

  Object.keys(scores.value).forEach(key => {
    const itemId = parseInt(key.replace('item_', ''));
    formattedScores[itemId] = scores.value[key] === 'Passed' ? 'passed' : 'not_passed';
  });

  router.post('/evaluation/store', {
    student_id: props.student.id,
    scores: formattedScores
  }, {
    onSuccess: () => {
      alert('Evaluation saved successfully');
    },
    onError: (errors) => {
      console.error('Evaluation errors:', errors);
      alert('Failed to save evaluation');
    }
  });
};

const goBack = () => {
  router.visit(route('student.list', {
    year: props.student.class.year_id,
    class: props.student.class.id
  }));
};
</script>

<template>
  <div class="flex-1 bg-white-100">
    <div class="p-4 md:p-6 space-y-4 md:space-y-6">
      <!-- Back Button and Title -->
      <div class="flex items-center space-x-4 mb-6">
        <button @click="goBack" class="p-2 rounded-lg bg-mint-100 hover:bg-mint-200
                 transition-colors duration-200 flex items-center
                 justify-center shadow-sm hover:shadow-md">
          <i class="fas fa-arrow-left text-mint-600"></i>
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
          <button @click="toggleCategory(category.id)"
            class="w-full flex justify-between items-center p-6 bg-mint-100 hover:bg-mint-200 transition-colors">
            <h2 class="text-xl font-semibold text-gray-800">{{ category.name }}</h2>
            <i :class="[
              'fas',
              expandedCategories[category.id] ? 'fa-chevron-up' : 'fa-chevron-down'
            ]"></i>
          </button>

          <!-- Category Content (Expandable) -->
          <div v-if="expandedCategories[category.id]" class="p-6 space-y-4">
            <div v-for="item in category.evaluation_items" :key="item.id"
              class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium">{{ item.title }}</p>
                <span :class="[
                  'text-sm px-2 py-0.5 rounded-full',
                  item.type === 'RUKUN' ? 'bg-red-100 text-red-800' :
                    item.type === 'WAJIB' ? 'bg-purple-100 text-purple-800' :
                      'bg-blue-100 text-blue-800'
                ]">
                  {{ item.type }}
                </span>
              </div>
              <div class="flex gap-3">
                <button @click="scores[`item_${item.id}`] = 'Passed'" :class="[
                  'px-4 py-2 rounded-md transition-colors',
                  scores[`item_${item.id}`] === 'Passed' ?
                    'bg-green-500 text-white' : 'bg-gray-100 hover:bg-gray-200'
                ]">
                  Lulus
                </button>
                <button @click="scores[`item_${item.id}`] = 'Not'" :class="[
                  'px-4 py-2 rounded-md transition-colors',
                  scores[`item_${item.id}`] === 'Not' ?
                    'bg-red-500 text-white' : 'bg-gray-100 hover:bg-gray-200'
                ]">
                  Tidak Lulus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end mt-6">
        <button @click="submitEvaluation" class="px-6 py-3 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
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
