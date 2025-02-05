<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { defineProps } from 'vue';
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

const COLORS = [
  '#10B981', '#EF4444', '#6366F1', '#F59E0B',
  '#3B82F6', '#8B5CF6', '#EC4899', '#F97316'
];

const props = defineProps({
  years: Array,
  classes: Array
});

const selectedYear = ref(props.years?.[0]?.id || null);
const selectedClass = ref('all');

const stats = ref({
  total: 0,
  passed: 0,
  failed: 0,
  notEvaluated: 0,
  categoryStats: []
});

const loading = ref(false);
const error = ref(null);

const fetchStats = async () => {
  if (!selectedYear.value) return;

  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get(route('stats.fetch', {
      year: selectedYear.value,
      class: selectedClass.value
    }));
    stats.value = response.data;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch statistics';
    console.error('Failed to fetch stats:', err);
    stats.value = {
      total: 0,
      passed: 0,
      notPassed: 0
    };
  } finally {
    loading.value = false;
  }
};

watch([selectedYear, selectedClass], () => {
  fetchStats();
}, { immediate: true });

const passRate = computed(() => {
  if (stats.value.total === 0) return 0;
  return ((stats.value.passed / stats.value.total) * 100).toFixed(2);
});

const pieChartData = computed(() => ({
  labels: ['Lulus', 'Belum Lulus', 'Belum Disemak'],
  datasets: [{
    backgroundColor: [COLORS[0], COLORS[1], COLORS[2]],
    data: [
      stats.value.passed || 0,
      stats.value.failed || 0,
      stats.value.notEvaluated || 0
    ]
  }]
}));

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        generateLabels: (chart) => {
          const data = chart.data;
          return data.labels.map((label, i) => ({
            text: `${label} (${data.datasets[0].data[i]})`,
            fillStyle: data.datasets[0].backgroundColor[i],
            hidden: false,
            index: i
          }));
        }
      }
    }
  }
};

const categoryBarChartData = computed(() => {
  if (!stats.value.categoryStats) return null;

  return {
    labels: stats.value.categoryStats.map(cat => cat.name),
    datasets: [
      {
        label: 'Lulus',
        backgroundColor: COLORS[0],
        data: stats.value.categoryStats.map(cat => cat.passed)
      },
      {
        label: 'Belum Lulus',
        backgroundColor: COLORS[1],
        data: stats.value.categoryStats.map(cat => cat.failed)
      },
      {
        label: 'Belum Disemak',
        backgroundColor: COLORS[2],
        data: stats.value.categoryStats.map(cat => cat.notEvaluated)
      }
    ]
  };
});

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  },
  scales: {
    y: {
      beginAtZero: true,
      stacked: false
    },
    x: {
      ticks: {
        autoSkip: false,
        maxRotation: 45,
        minRotation: 45
      }
    }
  }
};

const categoryDetailCharts = computed(() => {
  if (!stats.value.categoryDetails) return {};
  return stats.value.categoryDetails;
});

const filteredClasses = computed(() => {
  if (!selectedYear.value) return [];
  return props.classes.filter(c => c.year_id === selectedYear.value);
});

const formatClassName = (name) => {
  return name.split(' ').slice(1).join(' ');
};
</script>



<template>
  
        <div class="flex-1 bg-white-100">
            <div class="p-4 md:p-6 space-y-4 md:space-y-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Statistik Pencapaian</h1>
        <p class="text-gray-600">Analisis pencapaian pelajar mengikut tahun dan kelas</p>
      </div>

      <!-- Filters (Stacked on Mobile) -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <select
              v-model="selectedYear"
              class="w-full rounded-md border-gray-300 text-sm focus:border-mint-500 focus:ring-mint-500"
            >
              <option v-for="year in props.years" :key="year.id" :value="year.id">
                {{ year.name }}
              </option>
            </select>
          </div>
          <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select
              v-model="selectedClass"
              class="w-full rounded-md border-gray-300 text-sm focus:border-mint-500 focus:ring-mint-500"
            >
              <option value="all">Semua Kelas</option>
              <option
                v-for="class_ in filteredClasses"
                :key="class_.id"
                :value="class_.id"
              >
                {{ class_.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Statistics Grid -->
      <div class="grid gap-6">
        <!-- Summary Cards (Stacked on Mobile) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(card, index) in [
              { title: 'Jumlah Pelajar', value: stats.total, color: 'text-gray-900' },
              { title: 'Lulus', value: `${stats.passed} (${passRate}%)`, color: 'text-green-600' },
              { title: 'Belum Lulus dan Belum Disemak', value: stats.failed + stats.notEvaluated, color: 'text-red-600' }
            ]"
            :key="index"
            class="bg-white rounded-lg shadow-md p-4 md:p-6"
          >
            <h4 class="text-sm font-medium text-gray-500">{{ card.title }}</h4>
            <p :class="`text-xl md:text-3xl font-bold mt-2 ${card.color}`">
              {{ card.value }}
            </p>
          </div>
        </div>

        <div v-if="loading" class="text-center py-4">
          Sedang memuatkan statistik...
        </div>

        <!-- Charts (Responsive Layout) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Overall Progress Pie Chart -->
          <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-lg font-semibold mb-4">Pencapaian Keseluruhan</h3>
            <div class="h-48 md:h-64">
              <Pie :data="pieChartData" :options="pieChartOptions" />
            </div>
          </div>

          <!-- Category Progress Bar Chart -->
          <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-lg font-semibold mb-4">Pencapaian Mengikut Kategori</h3>
            <div class="h-48 md:h-64">
              <Bar :data="categoryBarChartData" :options="barChartOptions" />
            </div>
          </div>
        </div>

        <!-- Detailed Category Charts (Responsive Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <template v-for="(categoryData, categoryName) in categoryDetailCharts" :key="categoryName">
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
              <h3 class="text-lg font-semibold mb-4">
                {{ categoryName }} - Pencapaian Terperinci
              </h3>
              <div class="h-48 md:h-64">
                <Bar :data="categoryData" :options="barChartOptions" />
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>
/* Ensure responsive typography */
@media (max-width: 640px) {
  .text-3xl {
    font-size: 1.5rem;
  }
}
</style>
