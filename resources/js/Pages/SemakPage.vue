<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  student: Object,
  evaluationItems: Array,
  categories: Array
});

const scores = ref({});

onMounted(() => {
  if (props.student.evaluations) {
    props.student.evaluations.forEach(evaluation => {
      scores.value[`item_${evaluation.evaluation_item_id}`] = evaluation.score;
    });
  }
});

const submitEvaluation = () => {
  router.post(route('student.evaluation.store'), {
    student_id: props.student.id,
    scores: scores.value
  });
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Categories -->
      <div v-for="category in categories" :key="category.id" class="mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">{{ category.name }}</h2>
          <div class="space-y-4">
            <div v-for="item in evaluationItems.filter(i => i.category_id === category.id)"
                 :key="item.id"
                 class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium">{{ item.title }}</p>
                <span class="text-sm text-gray-500">{{ item.type }}</span>
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
