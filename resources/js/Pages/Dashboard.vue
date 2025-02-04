<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
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
    default: () => []
  }
});

const currentTime = ref(new Date());
const coordinates = ref({ latitude: 3.139003, longitude: 101.686855 }); // KL coordinates
const prayerTimes = ref({});

// Compute total students and stats for teacher's classes
const totalStudents = computed(() => {
  return props.teacherClasses.reduce((total, cls) => total + cls.students_count, 0);
});

const chartData = {
  labels: ['Amali Wuduk', 'Amali Solat', 'Bacaan', 'Tahfiz'],
  datasets: [{
    label: 'Lulus',
    data: [20, 18, 15, 22],
    backgroundColor: '#10B981'
  }, {
    label: 'Belum Lulus',
    data: [5, 7, 10, 3],
    backgroundColor: '#EF4444'
  }]
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      max: 25
    }
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

  setInterval(() => {
    currentTime.value = new Date();
    getPrayerTimes(); // Update prayer times every minute
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
        <p class="text-3xl font-bold text-mint-600">25</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800">Kelas</h3>
        <p class="text-3xl font-bold text-mint-600">{{ teacherClasses.length }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800">Lulus</h3>
        <p class="text-3xl font-bold text-green-600">75%</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800">Belum Lulus</h3>
        <p class="text-3xl font-bold text-red-600">25%</p>
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

    <!-- Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4">Statistik Pencapaian</h2>
      <div class="relative" style="min-height: 300px">
        <Bar :data="chartData" :options="chartOptions" />
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Link
        v-for="cls in teacherClasses"
        :key="cls.id"
        :href="`/kemaskini/tahun/${cls.year_id}/class/${cls.id}`"
        class="bg-white rounded-lg shadow-md p-6 hover:bg-gray-50"
      >
        <h3 class="text-lg font-semibold text-mint-600">Kelas {{ cls.name }}</h3>
        <p class="text-gray-600">{{ cls.students_count }} Pelajar</p>
      </Link>
    </div>
  </div>
</template>
