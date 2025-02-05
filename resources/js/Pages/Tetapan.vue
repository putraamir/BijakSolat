<!-- Tetapan.vue -->
<script setup>
import { ref, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

// Profile Information Form
const profileForm = useForm({
  name: props.user.name,
  email: props.user.email,
});

const updateProfileInformation = () => {
  profileForm.post(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Optional: show success message
    }
  });
};

// Password Change Form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const updatePassword = () => {
  passwordForm.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset('current_password', 'password', 'password_confirmation');
    }
  });
};

// Profile Picture Upload
const avatarPreview = ref(null);
const avatarFile = ref(null);

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // Create preview URL
  avatarPreview.value = URL.createObjectURL(file);

  const formData = new FormData();
  formData.append('avatar', file);

  try {
    await router.post(route('profile.avatar.update'), formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        // Success handling
        console.log('Upload successful');
      },
      onError: (errors) => {
        console.error('Upload failed:', errors);
        avatarPreview.value = null;
      },
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    });
  } catch (error) {
    console.error('Upload error:', error);
    avatarPreview.value = null;
  }
};

onUnmounted(() => {
  // Clean up preview URL
  if (avatarPreview.value) {
    URL.revokeObjectURL(avatarPreview.value);
  }
});
</script>

<template>
  <div class="bg-mint-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto space-y-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Tetapan Akaun</h1>

      <!-- Profile Information Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Maklumat Profil</h2>

        <!-- Profile Picture Upload -->
        <div class="mb-6 flex items-center space-x-6">
          <div class="flex-shrink-0">
            <img
              :src="avatarPreview || user.avatar || '/default-avatar.png'"
              alt="Profile Picture"
              class="w-24 h-24 rounded-full object-cover"
            />
          </div>
          <div>
            <input
              type="file"
              accept="image/*"
              @change="handleAvatarUpload"
              class="hidden"
              ref="avatarInput"
            />
            <button
              @click="$refs.avatarInput.click()"
              class="px-4 py-2 bg-mint-600 text-white rounded-md hover:bg-mint-700"
            >
              Tukar Gambar Profil
            </button>
            <p class="text-sm text-gray-500 mt-2">JPG atau PNG, tidak lebih dari 5MB</p>
          </div>
        </div>

        <!-- Profile Information Form -->
        <form @submit.prevent="updateProfileInformation" class="space-y-4">
          <div>
            <InputLabel for="name" value="Nama" />
            <TextInput
              id="name"
              v-model="profileForm.name"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="profileForm.errors.name" class="mt-2" />
          </div>

          <div>
            <InputLabel for="email" value="Email" />
            <TextInput
              id="email"
              v-model="profileForm.email"
              type="email"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="profileForm.errors.email" class="mt-2" />
          </div>

          <div class="flex justify-end">
            <PrimaryButton
              :disabled="profileForm.processing"
              class="bg-mint-600 hover:bg-mint-700"
            >
              Kemaskini Profil
            </PrimaryButton>
          </div>
        </form>
      </div>

      <!-- Change Password Section -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Tukar Kata Laluan</h2>
        <form @submit.prevent="updatePassword" class="space-y-4">
          <div>
            <InputLabel for="current_password" value="Kata Laluan Semasa" />
            <TextInput
              id="current_password"
              v-model="passwordForm.current_password"
              type="password"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="passwordForm.errors.current_password" class="mt-2" />
          </div>

          <div>
            <InputLabel for="password" value="Kata Laluan Baru" />
            <TextInput
              id="password"
              v-model="passwordForm.password"
              type="password"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="passwordForm.errors.password" class="mt-2" />
          </div>

          <div>
            <InputLabel for="password_confirmation" value="Sahkan Kata Laluan Baru" />
            <TextInput
              id="password_confirmation"
              v-model="passwordForm.password_confirmation"
              type="password"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="passwordForm.errors.password_confirmation" class="mt-2" />
          </div>

          <div class="flex justify-end">
            <PrimaryButton
              :disabled="passwordForm.processing"
              class="bg-mint-600 hover:bg-mint-700"
            >
              Tukar Kata Laluan
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
