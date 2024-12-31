<!-- resources/js/Pages/Auth/Register.vue -->
<template>
    <div class="min-h-screen flex items-center justify-center bg-mint-50 py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
          <h2 class="mt-6 text-3xl font-bold text-mint-900">
            Buat Akaun Baru
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Atau
            <Link href="/login" class="font-medium text-mint-600 hover:text-mint-500">
              log masuk jika anda sudah mempunyai akaun
            </Link>
          </p>
        </div>

        <!-- Google Login Button -->
        <div class="mt-8">
          <Link
            href="/auth/google"
            class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-mint-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mint-500 transition-colors"
          >
            <i class="fab fa-google text-xl text-mint-700"></i>
            <span>Daftar dengan Google</span>
          </Link>
        </div>

        <!-- Divider -->
        <div class="relative mt-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-mint-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-gray-50 text-mint-600">Atau daftar dengan</span>
          </div>
        </div>

        <!-- Registration Form -->
        <form class="mt-8 space-y-6" @submit.prevent="submit">
          <div class="rounded-md shadow-sm space-y-4">
            <!-- Username Field -->
            <div>
              <label for="username" class="block text-sm font-medium text-gray-700">
                Username
              </label>
              <input
                id="username"
                v-model="form.username"
                type="text"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-mint-500 focus:border-mint-500"
                :class="{ 'border-red-500': errors.username }"
              />
              <p v-if="errors.username" class="mt-1 text-sm text-red-600">
                {{ errors.username }}
              </p>
            </div>

            <!-- Name Field -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">
                Nama Penuh
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-mint-500 focus:border-mint-500"
                :class="{ 'border-red-500': errors.name }"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                {{ errors.name }}
              </p>
            </div>

            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">
                Kata Laluan
              </label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-mint-500 focus:border-mint-500"
                  :class="{ 'border-red-500': errors.password }"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                  <i
                    :class="[
                      'fas',
                      showPassword ? 'fa-eye-slash' : 'fa-eye',
                      'text-gray-400 hover:text-gray-500'
                    ]"
                  ></i>
                </button>
              </div>
              <p v-if="errors.password" class="mt-1 text-sm text-red-600">
                {{ errors.password }}
              </p>
            </div>

            <!-- Password Confirmation Field -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                Sahkan Kata Laluan
              </label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-mint-500 focus:border-mint-500"
                />
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="processing"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-mint-600 hover:bg-mint-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mint-500 transition-colors"
              :class="{ 'opacity-75 cursor-not-allowed': processing }"
            >
              <i v-if="processing" class="fas fa-spinner fa-spin mr-2"></i>
              {{ processing ? 'Mendaftar...' : 'Daftar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3'
  import { useForm } from '@inertiajs/vue3'

  export default {
    components: {
      Link
    },

    setup() {
      const form = useForm({
        username: '',
        name: '',
        password: '',
        password_confirmation: ''
      })

      return { form }
    },

    data() {
      return {
        showPassword: false,
        processing: false,
        errors: {}
      }
    },

    methods: {
      async submit() {
        this.processing = true
        this.errors = {}

        try {
          await this.form.post('/register', {
            onSuccess: () => {
              // Redirect will be handled by Laravel
            },
            onError: (errors) => {
              this.errors = errors
            }
          })
        } finally {
          this.processing = false
        }
      }
    }
  }
  </script>
