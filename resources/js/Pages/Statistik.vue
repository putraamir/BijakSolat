<script setup>
import { ref, computed } from 'vue';
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

// Hardcoded evaluation items and their mock data
const evaluationCategories = {
  'Amali Wuduk': [
    'Bacaan Basmalah', 'Membasuh kedua-dua tangan', 'Berkumur-kumur',
    'Memasukkan air ke dalam hidung', 'Membasuh muka', 'Niat wuduk',
    'Membasuh kedua-dua tangan hingga ke siku', 'Mengusap sebahagian kepala',
    'Mengusap kedua telinga', 'Membasuh kedua-dua kaki hingga buku lali',
    'Tertib', 'Doa selepas wuduk'
  ],
  'Amali Solat (Perlakuan)': [
    'Berdiri betul', 'Kedudukan tangan semasa takbiratulihram',
    'Kedudukan tangan selepas takbiratulihram', 'Rukuk', 'Iktidal',
    'Sujud', 'Duduk antara dua sujud', 'Duduk tahiyat akhir',
    'Tertib', 'Tuma\'ninah'
  ],
  'Amali Solat (Bacaan)': [
    'Takbiratulihram', 'Fatihah', 'Bacaan Rukuk', 'Bacaan Iktidal',
    'Bacaan Sujud', 'Bacaan Duduk Antara Dua Sujud', 'Tahiyat Akhir',
    'Selawat Ibrahimiah'
  ]
};

const COLORS = [
  '#10B981', '#EF4444', '#6366F1', '#F59E0B',
  '#3B82F6', '#8B5CF6', '#EC4899', '#F97316'
];

// Hardcoded mock data
const mockStats = {
  'Amali Wuduk': {
    passed: 35,
    notPassed: 10,
    unreviewed: 5,
    items: [
      { name: 'Bacaan Basmalah', passed: 40, notPassed: 10, unreviewed: 0 },
      { name: 'Membasuh kedua-dua tangan', passed: 38, notPassed: 7, unreviewed: 5 },
      { name: 'Berkumur-kumur', passed: 35, notPassed: 8, unreviewed: 7 },
      { name: 'Memasukkan air ke dalam hidung', passed: 37, notPassed: 6, unreviewed: 7 },
      { name: 'Membasuh muka', passed: 36, notPassed: 9, unreviewed: 5 },
      { name: 'Niat wuduk', passed: 39, notPassed: 6, unreviewed: 5 },
      { name: 'Membasuh kedua-dua tangan hingga ke siku', passed: 35, notPassed: 10, unreviewed: 5 },
      { name: 'Mengusap sebahagian kepala', passed: 33, notPassed: 12, unreviewed: 5 },
      { name: 'Mengusap kedua telinga', passed: 36, notPassed: 9, unreviewed: 5 },
      { name: 'Membasuh kedua-dua kaki hingga buku lali', passed: 34, notPassed: 11, unreviewed: 5 },
      { name: 'Tertib', passed: 37, notPassed: 8, unreviewed: 5 },
      { name: 'Doa selepas wuduk', passed: 35, notPassed: 10, unreviewed: 5 }
    ]
  },
  'Amali Solat (Perlakuan)': {
    passed: 40,
    notPassed: 5,
    unreviewed: 5,
    items: [
      { name: 'Berdiri betul', passed: 42, notPassed: 3, unreviewed: 5 },
      { name: 'Kedudukan tangan semasa takbiratulihram', passed: 39, notPassed: 6, unreviewed: 5 },
      { name: 'Kedudukan tangan selepas takbiratulihram', passed: 41, notPassed: 4, unreviewed: 5 },
      { name: 'Rukuk', passed: 38, notPassed: 7, unreviewed: 5 },
      { name: 'Iktidal', passed: 40, notPassed: 5, unreviewed: 5 },
      { name: 'Sujud', passed: 37, notPassed: 8, unreviewed: 5 },
      { name: 'Duduk antara dua sujud', passed: 39, notPassed: 6, unreviewed: 5 },
      { name: 'Duduk tahiyat akhir', passed: 41, notPassed: 4, unreviewed: 5 },
      { name: 'Tertib', passed: 40, notPassed: 5, unreviewed: 5 },
      { name: 'Tuma\'ninah', passed: 36, notPassed: 9, unreviewed: 5 }
    ]
  },
  'Amali Solat (Bacaan)': {
    passed: 30,
    notPassed: 15,
    unreviewed: 5,
    items: [
      { name: 'Takbiratulihram', passed: 35, notPassed: 10, unreviewed: 5 },
      { name: 'Fatihah', passed: 32, notPassed: 13, unreviewed: 5 },
      { name: 'Bacaan Rukuk', passed: 28, notPassed: 17, unreviewed: 5 },
      { name: 'Bacaan Iktidal', passed: 30, notPassed: 15, unreviewed: 5 },
      { name: 'Bacaan Sujud', passed: 27, notPassed: 18, unreviewed: 5 },
      { name: 'Bacaan Duduk Antara Dua Sujud', passed: 29, notPassed: 16, unreviewed: 5 },
      { name: 'Tahiyat Akhir', passed: 33, notPassed: 12, unreviewed: 5 },
      { name: 'Selawat Ibrahimiah', passed: 31, notPassed: 14, unreviewed: 5 }
    ]
  }
};

const selectedYear = ref(1);
const selectedClass = ref('all');

const totalStats = computed(() => {
  const total = Object.values(mockStats).reduce((acc, category) => {
    acc.passed += category.passed;
    acc.notPassed += category.notPassed;
    acc.unreviewed += category.unreviewed;
    return acc;
  }, { passed: 0, notPassed: 0, unreviewed: 0 });

  return {
    total: total.passed + total.notPassed + total.unreviewed,
    passed: total.passed,
    notPassed: total.notPassed,
    unreviewed: total.unreviewed,
    passRate: ((total.passed / (total.passed + total.notPassed + total.unreviewed)) * 100).toFixed(2)
  };
});

const pieChartData = computed(() => ({
  labels: ['Lulus', 'Belum Lulus', 'Belum Disemak'],
  datasets: [{
    backgroundColor: [COLORS[0], COLORS[1], COLORS[2]],
    data: [
      totalStats.value.passed,
      totalStats.value.notPassed,
      totalStats.value.unreviewed
    ]
  }]
}));

const pieChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  }
};

const categoryBarChartData = computed(() => ({
  labels: Object.keys(mockStats),
  datasets: [
    {
      label: 'Lulus',
      backgroundColor: COLORS[0],
      data: Object.values(mockStats).map(category => category.passed)
    },
    {
      label: 'Belum Lulus',
      backgroundColor: COLORS[1],
      data: Object.values(mockStats).map(category => category.notPassed)
    },
    {
      label: 'Belum Disemak',
      backgroundColor: COLORS[2],
      data: Object.values(mockStats).map(category => category.unreviewed)
    }
  ]
}));

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
};

const categoryDetailCharts = computed(() => {
  return Object.entries(mockStats).reduce((acc, [categoryName, categoryData]) => {
    acc[categoryName] = {
      labels: categoryData.items.map(item => item.name),
      datasets: [
        {
          label: 'Lulus',
          backgroundColor: COLORS[0],
          data: categoryData.items.map(item => item.passed)
        },
        {
          label: 'Belum Lulus',
          backgroundColor: COLORS[1],
          data: categoryData.items.map(item => item.notPassed)
        },
        {
          label: 'Belum Disemak',
          backgroundColor: COLORS[2],
          data: categoryData.items.map(item => item.unreviewed)
        }
      ]
    };
    return acc;
  }, {});
});

const years = [
  { id: 1, name: 'Tahun 1' },
  { id: 2, name: 'Tahun 2' },
  { id: 3, name: 'Tahun 3' }
];

const classes = [
  { id: 1, name: '1 Cemerlang', year_id: 1 },
  { id: 2, name: '1 Gemilang', year_id: 1 },
  { id: 3, name: '2 Cemerlang', year_id: 2 },
  { id: 4, name: '2 Gemilang', year_id: 2 }
];

const filteredClasses = computed(() => {
  return classes.filter(c => c.year_id === selectedYear.value);
});

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
          <div class="grid gap-6">
            <!-- Summary Cards -->
            <div class="grid gap-6 md:grid-cols-3">
              <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-sm font-medium text-gray-500">Jumlah Pelajar</h4>
                <p class="text-3xl font-bold mt-2">{{ totalStats.total }}</p>
              </div>
              <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-sm font-medium text-gray-500">Lulus</h4>
                <p class="text-3xl font-bold text-green-600 mt-2">
                  {{ totalStats.passed }} ({{ totalStats.passRate }}%)
                </p>
              </div>
              <div class="bg-white rounded-lg shadow-md p-6">
                <h4 class="text-sm font-medium text-gray-500">Belum Lulus dan Belum Disemak</h4>
                <p class="text-3xl font-bold text-red-600 mt-2">
                  {{ totalStats.notPassed + totalStats.unreviewed }}
                </p>
              </div>
            </div>

            <!-- Overview Charts -->
            <div class="grid md:grid-cols-2 gap-6">
              <!-- Overall Progress Pie Chart -->
              <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Pencapaian Keseluruhan</h3>
                <div class="h-64">
                  <Pie :data="pieChartData" :options="pieChartOptions" />
                </div>
              </div>

              <!-- Category Progress Bar Chart -->
              <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Pencapaian Mengikut Kategori</h3>
                <div class="h-64">
                  <Bar :data="categoryBarChartData" :options="barChartOptions" />
                </div>
              </div>
            </div>

            <!-- Detailed Category Charts -->
            <div class="grid md:grid-cols-2 gap-6">
              <template v-for="(categoryData, categoryName) in categoryDetailCharts" :key="categoryName">
                <div class="bg-white rounded-lg shadow-md p-6">
                  <h3 class="text-lg font-semibold mb-4">
                    {{ categoryName }} - Pencapaian Terperinci
                  </h3>
                  <div class="h-64">
                    <Bar
                      :data="{
                        labels: categoryData.labels,
                        datasets: categoryData.datasets
                      }"
                      :options="barChartOptions"
                    />
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
