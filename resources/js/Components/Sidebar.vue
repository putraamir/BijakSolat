<!-- resources/js/Components/Sidebar.vue -->
<template>
    <div class="relative min-h-screen">
      <!-- Mobile Menu Button -->
      <button
        @click="toggleMobile"
        class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-lg bg-mint-50"
      >
        <i class="fas fa-chevron-right h-5 w-5 text-mint-800" v-if="!isMobileOpen"></i>
        <i class="fas fa-times h-6 w-6 text-mint-600" v-else></i>
      </button>

      <!-- Backdrop for mobile -->
      <div
        v-if="isMobileOpen"
        @click="toggleMobile"
        class="lg:hidden fixed inset-0 bg-black/50 z-30"
      ></div>

      <!-- Sidebar -->
      <aside
        :class="`
          fixed top-0 left-0 h-full z-40
          ${isExpanded ? 'w-64' : 'w-20'}
          ${isMobileOpen ? 'translate-x-0' : '-translate-x-full'}
          lg:translate-x-0
          transition-all duration-300
          bg-mint-50 text-mint-900
        `"
      >
        <!-- Profile Section or Login Button -->
        <div class="p-4 border-b border-mint-200">
          <template v-if="isAuthenticated">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-mint-200 flex items-center justify-center">
                <img
                  :src="userAvatar || '/default-avatar.png'"
                  alt="profile"
                  class="rounded-full"
                />
              </div>
              <div v-if="isExpanded" class="flex-1">
                <h3 class="font-medium">{{ userName }}</h3>
                <p class="text-sm text-mint-600">{{ userRole }}</p>
              </div>
            </div>
          </template>
          <template v-else>
            <Link
              href="/login"
              class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-mint-600 hover:bg-mint-700 text-white rounded-lg transition-colors"
            >
              <i class="fas fa-sign-in-alt"></i>
              <span v-if="isExpanded">Log Masuk</span>
            </Link>
          </template>
        </div>

        <!-- Toggle Button (only show if authenticated) -->
        <button
          v-if="isAuthenticated"
          @click="toggleSidebar"
          class="hidden lg:block absolute -right-3 top-16 p-1.5 rounded-full bg-mint-200 text-mint-600"
        >
          <i
            :class="[
              'fas',
              isExpanded ? 'fa-chevron-left' : 'fa-chevron-right',
              'h-4 w-4'
            ]"
          ></i>
        </button>

        <!-- Main Menu -->
        <div class="p-3">
          <p class="px-3 py-2 text-xs font-medium text-mint-600">MAIN</p>
          <nav class="space-y-1">
            <template v-if="isAuthenticated">
              <Link
                v-for="item in menuItems"
                :key="item.name"
                :href="item.path"
                :class="`
                  flex items-center gap-3 px-3 py-2 rounded-lg
                  ${isCurrentRoute(item.path) ? 'bg-mint-200' : 'hover:bg-mint-100'}
                  text-mint-700
                `"
              >
                <i :class="[item.icon, 'h-5 w-5']"></i>
                <span v-if="isExpanded">{{ item.name }}</span>
              </Link>
            </template>
            <template v-else>
              <!-- Guest Menu Items -->
              <Link
                v-for="item in guestMenuItems"
                :key="item.name"
                :href="item.path"
                :class="`
                  flex items-center gap-3 px-3 py-2 rounded-lg
                  ${isCurrentRoute(item.path) ? 'bg-mint-200' : 'hover:bg-mint-100'}
                  text-mint-700
                `"
              >
                <i :class="[item.icon, 'h-5 w-5']"></i>
                <span v-if="isExpanded">{{ item.name }}</span>
              </Link>
            </template>
          </nav>
        </div>

        <!-- Bottom Menu -->
        <div class="absolute bottom-0 left-0 right-0 p-3">
          <div class="p-4 border-b border-mint-200"></div>
          <p class="px-1 py-2 text-xs font-medium text-mint-600">TETAPAN</p>
          <nav class="space-y-1">
            <template v-if="isAuthenticated">
              <Link
                v-for="item in bottomMenuItems"
                :key="item.name"
                :href="item.path"
                :class="`
                  flex items-center gap-3 px-4 py-3 rounded-lg
                  ${isCurrentRoute(item.path) ? 'bg-mint-200' : 'hover:bg-mint-100'}
                  text-mint-700
                `"
              >
                <i :class="[item.icon, 'h-5 w-5']"></i>
                <span v-if="isExpanded">{{ item.name }}</span>
              </Link>
            </template>
            <template v-else>
              <Link
                v-for="item in guestBottomMenuItems"
                :key="item.name"
                :href="item.path"
                :class="`
                  flex items-center gap-3 px-4 py-3 rounded-lg
                  ${isCurrentRoute(item.path) ? 'bg-mint-200' : 'hover:bg-mint-100'}
                  text-mint-700
                `"
              >
                <i :class="[item.icon, 'h-5 w-5']"></i>
                <span v-if="isExpanded">{{ item.name }}</span>
              </Link>
            </template>
          </nav>
        </div>
      </aside>

      <!-- Main Content -->
      <main
        :class="`
          min-h-screen
          ${isExpanded ? 'lg:ml-64' : 'lg:ml-20'}
          transition-all duration-300
        `"
      >
        <div class="p-6">
          <slot></slot>
        </div>
      </main>
    </div>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3'

  export default {
    name: 'Sidebar',

    components: {
      Link
    },

    props: {
      isAuthenticated: {
        type: Boolean,
        default: false
      },
      userName: {
        type: String,
        default: ''
      },
      userRole: {
        type: String,
        default: ''
      },
      userAvatar: {
        type: String,
        default: null
      }
    },

    data() {
      return {
        isExpanded: true,
        isMobileOpen: false,
        // Menu items for authenticated users
        menuItems: [
          { name: 'Dashboard', path: '/dashboard', icon: 'fas fa-home' },
          { name: 'Tahun', path: '/tahun', icon: 'fas fa-book' },
          { name: 'Guru', path: '/guru', icon: 'fas fa-users' },
          { name: 'Pautan', path: '/pautan', icon: 'fas fa-calendar' },
          { name: 'Jadual', path: '/jadual', icon: 'fas fa-calendar-alt' },
          { name: 'Statistik', path: '/statistik', icon: 'fas fa-chart-bar' }
        ],
        // Menu items for guests
        guestMenuItems: [
          { name: 'Home', path: '/', icon: 'fas fa-home' },
          { name: 'About', path: '/about', icon: 'fas fa-info-circle' },
          { name: 'Contact', path: '/contact', icon: 'fas fa-envelope' }
        ],
        // Bottom menu for authenticated users
        bottomMenuItems: [
          { name: 'Tetapan', path: '/tetapan', icon: 'fas fa-cog' },
          { name: 'Bantuan', path: '/bantuan', icon: 'fas fa-question-circle' },
          { name: 'Log Keluar', path: '/logout', icon: 'fas fa-sign-out-alt' }
        ],
        // Bottom menu for guests
        guestBottomMenuItems: [
          { name: 'Bantuan', path: '/bantuan', icon: 'fas fa-question-circle' },
          { name: 'Daftar', path: '/register', icon: 'fas fa-user-plus' }
        ]
      }
    },

    methods: {
      toggleSidebar() {
        this.isExpanded = !this.isExpanded
      },

      toggleMobile() {
        this.isMobileOpen = !this.isMobileOpen
      },

      isCurrentRoute(path) {
        return window.location.pathname === path
      }
    },

    mounted() {
      document.addEventListener('click', (e) => {
        if (
          this.isMobileOpen &&
          !e.target.closest('aside') &&
          !e.target.closest('button')
        ) {
          this.isMobileOpen = false
        }
      })
    },

    beforeUnmount() {
      document.removeEventListener('click', this.handleClickOutside)
    }
  }
  </script>
