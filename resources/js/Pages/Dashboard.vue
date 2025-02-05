<script setup>
import { ref, onMounted } from 'vue';
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

const currentTime = ref(new Date());
const coordinates = ref({ latitude: 3.139003, longitude: 101.686855 }); // KL coordinates
const prayerTimes = ref({});

const teacherStats = ref({
  studentCount: 0,
  classCount: 0,
  passedCount: 0,
  failedCount: 0,
  achievements: {
    amaliWuduk: { passed: 0, failed: 0 },
    amaliSolat: { passed: 0, failed: 0 },
    bacaan: { passed: 0, failed: 0 },
    tahfiz: { passed: 0, failed: 0 }
  }
});

const chartData = ref({
  labels: ['Amali Wuduk', 'Amali Solat', 'Bacaan', 'Tahfiz'],
  datasets: [{
    label: 'Lulus',
    data: [],
    backgroundColor: '#10B981'
  }, {
    label: 'Belum Lulus',
    data: [],
    backgroundColor: '#EF4444'
  }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      max: 100
    }
  }
};

// Add new refs for loading and error states
const loading = ref(true);
const error = ref(null);

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

// Modify the fetchTeacherStats function
const fetchTeacherStats = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/teacher/dashboard-stats');
    teacherStats.value = response.data;

    // Update chart data
    chartData.value.datasets[0].data = [
      response.data.achievements.amaliWuduk.passed,
      response.data.achievements.amaliSolat.passed,
      response.data.achievements.bacaan.passed,
      response.data.achievements.tahfiz.passed
    ];

    chartData.value.datasets[1].data = [
      response.data.achievements.amaliWuduk.failed,
      response.data.achievements.amaliSolat.failed,
      response.data.achievements.bacaan.failed,
      response.data.achievements.tahfiz.failed
    ];
  } catch (err) {
    error.value = "Failed to load statistics";
    console.error('Error fetching teacher statistics:', err);
  } finally {
    loading.value = false;
  }
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

  fetchTeacherStats();
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
    <!-- Add loading and error states -->
    <div v-if="loading" class="text-center py-4">
      <p class="text-gray-600">Loading statistics...</p>
    </div>

    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg">
      {{ error }}
    </div>

    <div v-else>
      <!-- Greeting Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800">
          Assalamualaikum, {{ $page.props.auth.user.name }}
        </h1>
        <p class="text-gray-600">{{ getGreeting() }}</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800">Jumlah Pelajar</h3>
          <p class="text-3xl font-bold text-mint-600">{{ teacherStats.studentCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800">Kelas</h3>
          <p class="text-3xl font-bold text-mint-600">{{ teacherStats.classCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800">Lulus</h3>
          <p class="text-3xl font-bold text-green-600">
            {{ ((teacherStats.passedCount / (teacherStats.passedCount + teacherStats.failedCount)) * 100).toFixed(0) }}%
          </p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800">Belum Lulus</h3>
          <p class="text-3xl font-bold text-red-600">
            {{ ((teacherStats.failedCount / (teacherStats.passedCount + teacherStats.failedCount)) * 100).toFixed(0) }}%
          </p>
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
        <div class="relative" style="min-height: 300px"> <!-- Fixed height container -->
          <Bar :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Link href="/kemaskini" class="bg-white rounded-lg shadow-md p-6 hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-mint-600">Kemaskini Bacaan</h3>
          <p class="text-gray-600">Urus dan nilai bacaan pelajar</p>
        </Link>
        <Link href="/guru" class="bg-white rounded-lg shadow-md p-6 hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-mint-600">Urus Guru</h3>
          <p class="text-gray-600">Tetapkan guru dan kelas</p>
        </Link>
        <Link href="/statistik" class="bg-white rounded-lg shadow-md p-6 hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-mint-600">Lihat Statistik</h3>
          <p class="text-gray-600">Analisis prestasi pelajar</p>
        </Link>
      </div>
    </div>
  </div>
</template>
