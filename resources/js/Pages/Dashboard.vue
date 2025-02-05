<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Bar } from 'vue-chartjs';
import { PrayerTimes, Coordinates, CalculationMethod } from 'adhan';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

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
    backgroundColor: ['#10B981', '#EF4444', '#6B7280']
  }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
};

const loadClassStats = async (classId) => {
  try {
    const response = await window.axios.get(route('stats.fetch', {
      class: classId
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

onMounted(() => {
  // Load stats for each class
  props.teacherClasses.forEach(cls => loadClassStats(cls.id));

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

const getGreeting = () => {
  const hour = currentTime.value.getHours();
  if (hour < 12) return 'Selamat Pagi';
  if (hour < 15) return 'Selamat Tengah Hari';
  if (hour < 19) return 'Selamat Petang';
  return 'Selamat Malam';
};
</script>

<template>
  <div class="p-6 space-y-6">
    <!-- Greeting Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h1 class="text-2xl font-bold text-gray-800">
        Assalamualaikum, {{ auth.user.name }}
      </h1>
      <p class="text-gray-600">{{ getGreeting() }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800">Jumlah Pelajar</h3>
        <p class="text-3xl font-bold text-mint-600">{{ totalStudents }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800">Kelas</h3>
        <p class="text-3xl font-bold text-mint-600">{{ teacherClasses.length }}</p>
      </div>
    </div>

    <!-- Prayer Times -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4">Waktu Solat</h2>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div v-for="(time, prayer) in prayerTimes" :key="prayer" class="text-center p-4 bg-mint-50 rounded-lg">
          <h3 class="font-semibold text-mint-800">{{ prayer }}</h3>
          <p class="text-mint-600">{{ time }}</p>
        </div>
      </div>
    </div>

    <!-- Class Statistics -->
    <div class="space-y-6">
      <div v-for="cls in teacherClasses" :key="cls.id" class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold mb-4">{{ cls.name }}</h3>

        <div v-if="classStats[cls.id]" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg">
              <p class="text-sm text-green-600">Lulus</p>
              <p class="text-2xl font-bold text-green-700">
                {{ classStats[cls.id].passed }}
                <span class="text-sm font-normal ml-1">
                  ({{ ((classStats[cls.id].passed / classStats[cls.id].total) * 100).toFixed(1) }}%)
                </span>
              </p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
              <p class="text-sm text-red-600">Belum Lulus</p>
              <p class="text-2xl font-bold text-red-700">
                {{ classStats[cls.id].failed }}
                <span class="text-sm font-normal ml-1">
                  ({{ ((classStats[cls.id].failed / classStats[cls.id].total) * 100).toFixed(1) }}%)
                </span>
              </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm text-gray-600">Belum Disemak</p>
              <p class="text-2xl font-bold text-gray-700">
                {{ classStats[cls.id].notEvaluated }}
                <span class="text-sm font-normal ml-1">
                  ({{ ((classStats[cls.id].notEvaluated / classStats[cls.id].total) * 100).toFixed(1) }}%)
                </span>
              </p>
            </div>
          </div>

          <div class="h-64">
            <Bar
              :data="createChartData(classStats[cls.id])"
              :options="chartOptions"
            />
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          Loading statistics...
        </div>
      </div>
    </div>

  </div>
</template>
