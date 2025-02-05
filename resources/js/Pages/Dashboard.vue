<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Pie } from 'vue-chartjs';
import { PrayerTimes, Coordinates, CalculationMethod } from 'adhan';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  Title
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend, Title);

const props = defineProps({
  auth: {
    type: Object,
    required: true
  },
  teacherClasses: {
    type: Array,
    required: true
  }
});

const currentTime = ref(new Date());
const coordinates = ref({ latitude: 3.139003, longitude: 101.686855 }); // KL coordinates
const prayerTimes = ref({});
const classStats = ref({});

// Compute total students across all classes
const totalStudents = computed(() => {
  return props.teacherClasses.reduce((total, cls) => total + cls.students_count, 0);
});

// Chart configuration
const createChartData = (stats) => ({
  labels: ['Lulus', 'Belum Lulus', 'Belum Disemak'],
  datasets: [{
    data: [stats.passed || 0, stats.failed || 0, stats.notEvaluated || 0],
    backgroundColor: ['#10B981', '#EF4444', '#6366F1'],
    borderColor: ['#064E3B', '#991B1B', '#4338CA'],
    borderWidth: 1
  }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const dataset = context.dataset;
          const total = dataset.data.reduce((acc, data) => acc + data, 0);
          const value = dataset.data[context.dataIndex];
          const percentage = total > 0 ? ((value * 100) / total).toFixed(1) : 0;
          return `${context.label}: ${value} (${percentage}%)`;
        }
      }
    }
  }
};

const loadClassStats = async (classId, yearId) => {
  try {
    const response = await window.axios.get(route('stats.fetch', {
      class: classId,
      year: yearId
    }));
    classStats.value[classId] = response.data;
  } catch (error) {
    console.error(`Failed to load stats for class ${classId}:`, error);
  }
};

const getPrayerTimes = () => {
  const date = new Date();
  const coords = new Coordinates(coordinates.value.latitude, coordinates.value.longitude);
  const params = CalculationMethod.MoonsightingCommittee();
  const prayers = new PrayerTimes(coords, date, params);

  prayerTimes.value = {
    Subuh: prayers.fajr.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    Zohor: prayers.dhuhr.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    Asar: prayers.asr.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    Maghrib: prayers.maghrib.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
    Isyak: prayers.isha.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
  };
};

const getGreeting = () => {
  const hour = currentTime.value.getHours();
  if (hour < 12) return 'Selamat Pagi';
  if (hour < 15) return 'Selamat Tengah Hari';
  if (hour < 19) return 'Selamat Petang';
  return 'Selamat Malam';
};

onMounted(() => {
  // Load class stats
  props.teacherClasses.forEach(cls => loadClassStats(cls.id, cls.year_id));

  // Initialize prayer times
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        coordinates.value = {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude
        };
        getPrayerTimes();
      },
      (error) => {
        console.error("Error getting location:", error);
        getPrayerTimes(); // Use default KL coordinates
      }
    );
  } else {
    getPrayerTimes();
  }

  // Update time every minute
  setInterval(() => {
    currentTime.value = new Date();
    getPrayerTimes();
  }, 60000);
});
</script>

<template>

      <div class="p-4 md:p-6 space-y-4 md:space-y-6">
        <!-- Greeting Section -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
          <h1 class="text-xl md:text-2xl font-bold text-gray-800">
            Assalamualaikum, {{ auth.user.name }}
          </h1>
          <p class="text-sm md:text-base text-gray-600">{{ getGreeting() }}</p>
        </div>

        <!-- Stats Grid - 1 column on mobile, 2 on larger screens -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
          <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Jumlah Pelajar</h3>
            <p class="text-2xl md:text-3xl font-bold text-mint-600">{{ totalStudents }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-gray-800">Kelas</h3>
            <p class="text-2xl md:text-3xl font-bold text-mint-600">{{ teacherClasses.length }}</p>
          </div>
        </div>

        <!-- Prayer Times - 2 columns on mobile, 5 on larger screens -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
          <h2 class="text-lg md:text-xl font-semibold mb-4">Waktu Solat</h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 md:gap-4">
            <div v-for="(time, prayer) in prayerTimes" :key="prayer"
                 class="text-center p-3 md:p-4 bg-mint-50 rounded-lg">
              <h3 class="text-sm md:text-base font-semibold text-mint-800">{{ prayer }}</h3>
              <p class="text-sm md:text-base text-mint-600">{{ time }}</p>
            </div>
          </div>
        </div>

        <!-- Class Statistics -->
        <div class="space-y-4 md:space-y-6">
          <div v-for="cls in teacherClasses" :key="cls.id"
               class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <h3 class="text-lg md:text-xl font-semibold mb-4">{{ cls.name }}</h3>

            <div v-if="classStats[cls.id]" class="space-y-4">
              <!-- Status Cards - Stack on mobile -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                <div class="bg-green-50 p-3 md:p-4 rounded-lg">
                  <p class="text-xs md:text-sm text-green-600">Lulus</p>
                  <div class="flex items-baseline">
                    <p class="text-xl md:text-2xl font-bold text-green-700">
                      {{ classStats[cls.id].passed }}
                    </p>
                    <span class="text-xs md:text-sm font-normal ml-1" v-if="classStats[cls.id].total > 0">
                      ({{ ((classStats[cls.id].passed / classStats[cls.id].total) * 100).toFixed(1) }}%)
                    </span>
                  </div>
                </div>

                <div class="bg-red-50 p-3 md:p-4 rounded-lg">
                  <p class="text-xs md:text-sm text-red-600">Belum Lulus</p>
                  <div class="flex items-baseline">
                    <p class="text-xl md:text-2xl font-bold text-red-700">
                      {{ classStats[cls.id].failed }}
                    </p>
                    <span class="text-xs md:text-sm font-normal ml-1" v-if="classStats[cls.id].total > 0">
                      ({{ ((classStats[cls.id].failed / classStats[cls.id].total) * 100).toFixed(1) }}%)
                    </span>
                  </div>
                </div>

                <div class="bg-indigo-50 p-3 md:p-4 rounded-lg">
                  <p class="text-xs md:text-sm text-indigo-600">Belum Disemak</p>
                  <div class="flex items-baseline">
                    <p class="text-xl md:text-2xl font-bold text-indigo-700">
                      {{ classStats[cls.id].notEvaluated }}
                    </p>
                    <span class="text-xs md:text-sm font-normal ml-1" v-if="classStats[cls.id].total > 0">
                      ({{ ((classStats[cls.id].notEvaluated / classStats[cls.id].total) * 100).toFixed(1) }}%)
                    </span>
                  </div>
                </div>
              </div>

              <!-- Charts and Categories - Stack on mobile -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                <!-- Pie Chart -->
                <div class="bg-white p-4 rounded-lg shadow-sm">
                  <h4 class="text-base md:text-lg font-semibold mb-4">Keseluruhan</h4>
                  <div class="h-48 md:h-64">
                    <Pie
                      :data="createChartData(classStats[cls.id])"
                      :options="chartOptions"
                    />
                  </div>
                </div>

                <!-- Category Stats -->
                <div class="bg-white p-4 rounded-lg shadow-sm">
                  <h4 class="text-base md:text-lg font-semibold mb-4">Mengikut Kategori</h4>
                  <div class="space-y-3">
                    <div v-for="category in classStats[cls.id].categoryStats"
                         :key="category.name"
                         class="p-3 bg-gray-50 rounded-lg">
                      <p class="text-sm md:text-base font-medium mb-2">{{ category.name }}</p>
                      <div class="flex flex-wrap gap-2 text-xs md:text-sm">
                        <span class="text-green-600">Lulus: {{ category.passed }}</span>
                        <span class="text-red-600">Belum Lulus: {{ category.failed }}</span>
                        <span class="text-indigo-600">Belum Disemak: {{ category.notEvaluated }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-6 text-sm text-gray-500">
              Loading statistics...
            </div>
          </div>
        </div>
      </div>
    
  </template>
