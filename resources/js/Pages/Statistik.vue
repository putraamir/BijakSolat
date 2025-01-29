<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Pie, Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement
);

// Mock data for statistics
const selectedYear = ref(1);
const selectedClass = ref('all');

const COLORS = ['#10B981', '#EF4444', '#6366F1', '#F59E0B'];

const overallStats = {
  1: {
    total: 50,
    passed: 35,
    notPassed: 15,
    byClass: {
      'Cemerlang': { total: 25, passed: 20, notPassed: 5 },
      'Gemilang': { total: 25, passed: 15, notPassed: 10 }
    }
  },
  2: {
    total: 48,
    passed: 40,
    notPassed: 8,
    byClass: {
      'Cemerlang': { total: 24, passed: 22, notPassed: 2 },
      'Gemilang': { total: 24, passed: 18, notPassed: 6 }
    }
  }
};

const categoryStats = {
  1: {
    'Amali Wuduk': { passed: 40, notPassed: 10 },
    'Amali Solat': { passed: 35, notPassed: 15 },
    'Bacaan': { passed: 30, notPassed: 20 },
    'Tahfiz': { passed: 25, notPassed: 25 }
  },

  2: {
    'Amali Wuduk': { passed: 40, notPassed: 10 },
    'Amali Solat': { passed: 35, notPassed: 15 },
    'Bacaan': { passed: 30, notPassed: 20 },
    'Tahfiz': { passed: 25, notPassed: 25 }
  }

};


const classStats = computed(() => {
  if (selectedClass.value === 'all') {
    return overallStats[selectedYear.value] || { total: 0, passed: 0, notPassed: 0 };
  }
  return overallStats[selectedYear.value]?.byClass[selectedClass.value] ||
         { total: 0, passed: 0, notPassed: 0 };
});

const pieChartData = computed(() => ({
  labels: ['Lulus', 'Belum Lulus'],
  datasets: [{
    backgroundColor: [COLORS[0], COLORS[1]],
    data: [classStats.value.passed, classStats.value.notPassed]
  }]
}));

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
};

const categoryChartData = computed(() => {
  const stats = categoryStats[selectedYear.value] || {};
  return {
    labels: Object.keys(stats),
    datasets: [
      {
        label: 'Lulus',
        backgroundColor: COLORS[0],
        data: Object.values(stats).map(v => v.passed)
      },
      {
        label: 'Belum Lulus',
        backgroundColor: COLORS[1],
        data: Object.values(stats).map(v => v.notPassed)
      }
    ]
  };
});

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
};
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-mint-50 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-800">Statistik Pencapaian</h1>
          <p class="text-gray-600">Analisis pencapaian pelajar mengikut tahun dan kelas</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
          <div class="flex gap-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1 flex gap-2">Tahun</label>
              <select v-model="selectedYear" class="rounded-md border-gray-300 text-sm">
                <option v-for="year in 6" :key="year" :value="year">
                  Tahun {{ year }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
              <select v-model="selectedClass" class="rounded-md border-gray-300 text-sm">
                <option value="all">Semua Kelas</option>
                <option value="Cemerlang">Cemerlang</option>
                <option value="Gemilang">Gemilang</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid gap-6 md:grid-cols-2">
          <!-- Summary Cards -->
          <div class="md:col-span-2 grid gap-6 md:grid-cols-3">
            <div class="bg-white rounded-lg shadow-md p-6">
              <h4 class="text-sm font-medium text-gray-500">Jumlah Pelajar</h4>
              <p class="text-3xl font-bold mt-2">{{ classStats.total }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
              <h4 class="text-sm font-medium text-gray-500">Lulus</h4>
              <p class="text-3xl font-bold text-green-600 mt-2">{{ classStats.passed }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
              <h4 class="text-sm font-medium text-gray-500">Belum Lulus</h4>
              <p class="text-3xl font-bold text-red-600 mt-2">{{ classStats.notPassed }}</p>
            </div>
          </div>

          <!-- Overall Progress -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Pencapaian Keseluruhan</h3>
            <div class="h-64">
              <Pie
                :data="pieChartData"
                :options="pieChartOptions"
              />
            </div>
          </div>

          <!-- Category Progress -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Pencapaian Mengikut Kategori</h3>
            <div class="h-64">
              <Bar
                :data="categoryChartData"
                :options="barChartOptions"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
