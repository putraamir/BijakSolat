<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Add validation rules
const rules = {
  email: [
    v => !!v || 'Email diperlukan',
    v => /.+@.+\..+/.test(v) || 'Format email tidak sah'
  ],
  password: [
    v => !!v || 'Kata laluan diperlukan',
    v => v.length >= 8 || 'Kata laluan mestilah sekurang-kurangnya 8 aksara',
    v => /[A-Z]/.test(v) || 'Kata laluan mesti mengandungi huruf besar',
    v => /[a-z]/.test(v) || 'Kata laluan mesti mengandungi huruf kecil',
    v => /[0-9]/.test(v) || 'Kata laluan mesti mengandungi nombor'
  ]
}

const hasLength = computed(() => form.password.length >= 8)
const hasUppercase = computed(() => /[A-Z]/.test(form.password))
const hasLowercase = computed(() => /[a-z]/.test(form.password))
const hasNumber = computed(() => /[0-9]/.test(form.password))

const validateEmail = () => {
  const emailRegex = /.+@.+\..+/
  if (!form.email) {
    form.errors.email = 'Email diperlukan'
  } else if (!emailRegex.test(form.email)) {
    form.errors.email = 'Format email tidak sah'
  } else {
    delete form.errors.email
  }
}

const validatePassword = () => {
  if (!hasLength.value) {
    form.errors.password = 'Kata laluan mestilah sekurang-kurangnya 8 aksara'
  } else if (!hasUppercase.value || !hasLowercase.value || !hasNumber.value) {
    form.errors.password = 'Kata laluan mesti mengandungi huruf besar, huruf kecil dan nombor'
  } else {
    delete form.errors.password
  }
}

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar" />

    <div class="min-h-screen flex w-full flex-col justify-center items-center pt-6 sm:pt-0 bg-mint-50">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">
                Daftar
            </h2>

            <form @submit.prevent="submit">
                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Nama" class="form-label" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div class="mt-4">
                    <InputLabel for="email" value="Email" class="form-label" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        @blur="validateEmail"
                    />
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                      {{ form.errors.email }}
                    </p>
                </div>

                <!-- Password Field -->
                <div class="mt-4">
                    <InputLabel for="password" value="Kata Laluan" class="form-label" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        @input="validatePassword"
                    />
                    <div v-if="form.errors.password" class="mt-2">
                      <p class="text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>
                    <!-- Password strength indicators -->
                    <div class="mt-2 text-xs">
                      <p :class="{'text-green-600': hasLength, 'text-red-600': !hasLength}">
                        ✓ Minimum 8 aksara
                      </p>
                      <p :class="{'text-green-600': hasUppercase, 'text-red-600': !hasUppercase}">
                        ✓ Sekurang-kurangnya satu huruf besar
                      </p>
                      <p :class="{'text-green-600': hasLowercase, 'text-red-600': !hasLowercase}">
                        ✓ Sekurang-kurangnya satu huruf kecil
                      </p>
                      <p :class="{'text-green-600': hasNumber, 'text-red-600': !hasNumber}">
                        ✓ Sekurang-kurangnya satu nombor
                      </p>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        value="Sahkan Kata Laluan"
                        class="form-label"
                    />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- Footer Actions -->
                <div class="mt-8">
                    <button type="submit"
                        class="w-full py-2 px-4 bg-mint-700 hover:bg-mint-800 text-white font-semibold rounded-lg shadow-sm">
                        Daftar
                    </button>
                </div>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm text-mint-1000 hover:text-mint-500"
                    >
                        Sudah ada akaun? Log masuk di sini
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Update the InputLabel styles -->
<style>
.form-label {
  @apply text-emerald-700 font-medium;
}
</style>
