<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  student: {
    type: Object,
    required: true
  },
  year: {
    type: Number,
    required: true
  }
});

const scores = ref({});

const yearlyEvaluations = {
  1: {
    wuduk: [
      { id: 1, title: 'Bacaan Basmalah', type: 'SUNAT' },
      { id: 2, title: 'Membasuh kedua-dua tangan', type: 'SUNAT' },
      { id: 3, title: 'Berkumur-kumur', type: 'SUNAT' },
      { id: 4, title: 'Memasukkan air ke dalam hidung', type: 'RUKUN' },
      { id: 5, title: 'Niat wuduk', type: 'RUKUN' }
    ],
    solat: [
      { id: 1, title: 'Berdiri betul', type: 'RUKUN' },
      { id: 2, title: 'Takbiratul ihram', type: 'RUKUN' },
      { id: 3, title: 'Doa Iftitah', type: 'SUNAT' },
      { id: 4, title: 'Al-Fatihah', type: 'RUKUN' },
      { id: 5, title: 'Rukuk', type: 'RUKUN' }
    ]
  }
};

const submitEvaluation = () => {
  router.post(route('submit.evaluation'), {
    studentId: props.student.id,
    scores: scores.value
  });
};
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center mb-8">
        <Link :href="`/kemaskini/tahun/${year}`" class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200">
          <i class="fas fa-arrow-left"></i>
        </Link>
        <div>
          <h1 class="text-2xl font-semibold text-gray-800">Penilaian Pelajar</h1>
          <p class="text-gray-600">{{ student.name }}</p>
        </div>
      </div>

      <!-- Evaluation Form -->
      <div class="space-y-6">
        <!-- Wuduk Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Amali Wuduk</h2>
          <div class="space-y-4">
            <div v-for="item in yearlyEvaluations[year]?.wuduk" :key="item.id"
                 class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium">{{ item.title }}</p>
                <span class="text-sm text-gray-500">{{ item.type }}</span>
              </div>
              <div class="flex gap-3">
                <button
                  @click="scores[`wuduk_${item.id}`] = 'Passed'"
                  :class="`px-4 py-2 rounded-md ${scores[`wuduk_${item.id}`] === 'Passed' ? 'bg-green-500 text-white' : 'bg-gray-100'}`"
                >
                  Passed
                </button>
                <button
                  @click="scores[`wuduk_${item.id}`] = 'Not'"
                  :class="`px-4 py-2 rounded-md ${scores[`wuduk_${item.id}`] === 'Not' ? 'bg-red-500 text-white' : 'bg-gray-100'}`"
                >
                  Not
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Solat Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Amali Solat</h2>
          <div class="space-y-4">
            <div v-for="item in yearlyEvaluations[year]?.solat" :key="item.id"
                 class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium">{{ item.title }}</p>
                <span class="text-sm text-gray-500">{{ item.type }}</span>
              </div>
              <div class="flex gap-3">
                <button
                  @click="scores[`solat_${item.id}`] = 'Passed'"
                  :class="`px-4 py-2 rounded-md ${scores[`solat_${item.id}`] === 'Passed' ? 'bg-green-500 text-white' : 'bg-gray-100'}`"
                >
                  Passed
                </button>
                <button
                  @click="scores[`solat_${item.id}`] = 'Not'"
                  :class="`px-4 py-2 rounded-md ${scores[`solat_${item.id}`] === 'Not' ? 'bg-red-500 text-white' : 'bg-gray-100'}`"
                >
                  Not
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            @click="submitEvaluation"
            class="px-6 py-3 bg-mint-600 text-white rounded-lg hover:bg-mint-700"
          >
            Simpan Penilaian
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
