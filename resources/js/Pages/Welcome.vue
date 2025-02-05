<template>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <div class="relative overflow-hidden">
        <div class="absolute inset-y-0 h-full w-full bg-gradient-to-r from-mint-50 to-mint-100" aria-hidden="true"/>
        <div class="relative pt-6 pb-16 sm:pb-24">
          <main class="mt-16 mx-auto max-w-7xl px-4 sm:mt-24">
            <div class="text-center">
              <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Sistem Pengurusan</span>
                <span class="block text-mint-600">Amali Solat</span>
              </h1>
              <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Penyelesaian digital lengkap untuk pengurusan sekolah yang lebih efisien dan berkesan.
              </p>
              <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                <div class="rounded-md shadow">
                  <Link
                    href="/register"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-mint-600 hover:bg-mint-700 md:py-4 md:text-lg md:px-10"
                  >
                    Daftar Sekarang
                  </Link>
                </div>
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                  <Link
                    href="/login"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-mint-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10"
                  >
                    Log Masuk
                  </Link>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>

      <!-- Feature Section -->
      <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-mint-600 font-semibold tracking-wide uppercase">Ciri-ciri</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              Pengurusan Pemarkahan Amali Solat yang Lebih Baik
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
              Memudahkan pengurusan amali solat di sekolah anda.
            </p>
          </div>

          <div class="mt-10">
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
              <div class="relative">
                <dt>
                  <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-mint-500 text-white">
                    <i class="fas fa-users text-xl"></i>
                  </div>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Pengurusan Guru</p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  Urus maklumat dan jadual guru dengan mudah dan sistematik.
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-mint-500 text-white">
                    <i class="fas fa-calendar-alt text-xl"></i>
                  </div>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Jadual Waktu</p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  Susun dan urus jadual waktu dengan lebih efisien.
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-mint-500 text-white">
                    <i class="fas fa-chart-bar text-xl"></i>
                  </div>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Statistik & Analisis</p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  Pantau prestasi dan analisis data dengan mudah.
                </dd>
              </div>

              <div class="relative">
                <dt>
                  <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-mint-500 text-white">
                    <i class="fas fa-book text-xl"></i>
                  </div>
                  <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Pengurusan Tahun</p>
                </dt>
                <dd class="mt-2 ml-16 text-base text-gray-500">
                  Urus maklumat tahun persekolahan dengan sistematik.
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="bg-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
          <div class="flex justify-center space-x-6 md:order-2">
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <i class="fab fa-facebook text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <i class="fab fa-instagram text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <i class="fab fa-twitter text-xl"></i>
            </a>
          </div>
          <div class="mt-8 md:mt-0 md:order-1">
            <p class="text-center text-base text-gray-400">
              &copy; {{ new Date().getFullYear() }} SprotStat Hak Cipta Terpelihara.
            </p>
          </div>
        </div>
      </footer>
    </div>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3'

  export default {
    components: {
      Link
    }
  }
  </script>
