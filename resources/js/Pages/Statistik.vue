<script setup>
import { ref, computed, watch } from 'vue';
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
import axios from 'axios';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement
);

const props = defineProps({
  years: Array,
  classes: Array
});

// Mock data for statistics
const selectedYear = ref(null);
const selectedClass = ref('all');

const filteredClasses = computed(() => {
  if (!selectedYear.value) return [];
  return props.classes.filter(c => c.year_id === selectedYear.value);
});

const COLORS = ['#10B981', '#EF4444', '#6366F1', '#F59E0B'];

const loading = ref(false);
const stats = ref({});

const fetchStats = async () => {
  if (!selectedYear.value) return;

  loading.value = true;
  try {
    const response = await axios.get(route('stats.fetch', {
      year: selectedYear.value,
      class: selectedClass.value
    }));

    stats.value = {
      total: response.data.total,
      categories: response.data.data.reduce((acc, stat) => {
        acc[stat.category] = {
          passed: stat.passed,
          notPassed: stat.not_passed
        };
        return acc;
      }, {})
    };

  } catch (error) {
    console.error('Failed to fetch stats:', error);
  } finally {
    loading.value = false;
  }
};

watch([selectedYear, selectedClass], () => {
  fetchStats();
});

const classStats = computed(() => {
  console.log(stats);

  const total = Object.values(stats.value).reduce((acc, stat) => {
    acc.passed += stat.passed;
    acc.notPassed += stat.notPassed;
    return acc;
  }, { passed: 0, notPassed: 0 });

  return {
    total: total.passed + total.notPassed,
    passed: total.passed,
    notPassed: total.notPassed
  };
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
  return {
    labels: Object.keys(stats.value),
    datasets: [
      {
        label: 'Lulus',
        backgroundColor: COLORS[0],
        data: Object.values(stats.value).map(v => v.passed)
      },
      {
        label: 'Belum Lulus',
        backgroundColor: COLORS[1],
        data: Object.values(stats.value).map(v => v.notPassed)
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

const formatClassName = (name) => {
  return name.split(' ').slice(1).join(' ');
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
                <option v-for="year in years" :key="year.id" :value="year.id">
                  {{ year.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
              <select v-model="selectedClass" class="rounded-md border-gray-300 text-sm">
                <option value="all">Semua Kelas</option>
                <option v-for="class_ in filteredClasses" :key="class_.id" :value="class_.name">
                  {{ formatClassName(class_.name) }}
                </option>
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
              <Pie :data="pieChartData" :options="pieChartOptions" />
            </div>
          </div>

          <!-- Category Progress -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Pencapaian Mengikut Kategori</h3>
            <div class="h-64">
              <Bar :data="categoryChartData" :options="barChartOptions" />
            </div>
          </div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="loading" class="text-center py-4">
          Loading statistics...
        </div>
      </div>
    </div>
  </AppLayout>
</template>
