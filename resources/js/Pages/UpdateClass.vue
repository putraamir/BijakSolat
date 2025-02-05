<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  years: Array
});

const showModal = ref(false);

const form = useForm({
  name: '',
  desc: ''
});

const submit = () => {
  form.post(route('years.store'), {
    onSuccess: () => {
      showModal.value = false;
      form.reset();
    }
  });
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};
</script>

<template>
 <div class="flex-1 bg-white-100">
    <div class="flex-1 bg-white-100">
        <div class="p-4 md:p-6 space-y-4 md:space-y-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Kemaskini</h1>

      <div class="grid gap-4">
        <Link
          v-for="year in years"
          :key="year.id"
          :href="`/kemaskini/tahun/${year.id}`"
          class="bg-white rounded-lg shadow-md p-4 flex items-center hover:bg-gray-50 transition-colors"
        >
          <div class="w-12 h-12 bg-mint-200 text-xl rounded-full flex items-center justify-center text-xl font-bold mr-4">
            {{ year.id }}
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ year.name }}</h2>
            <p class="text-gray-600">{{ year.desc }}</p>
          </div>
        </Link>
      </div>
    </div>

   <!-- Floating Action Button -->
   <button
     @click="showModal = true"
     class="fixed bottom-6 right-6 w-14 h-14 bg-mint-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-mint-700 hover:scale-105 transition-all"
   >
     <i class="fas fa-plus text-xl"></i>
   </button>

   <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
      <h2 class="text-xl font-semibold mb-4">Add New Year</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input
            v-model="form.name"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            placeholder="TAHUN 1"
          >
          <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <input
            v-model="form.desc"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            placeholder="KEMASKINI KELAS TAHUN 1"
          >
          <div v-if="form.errors.desc" class="text-red-500 text-sm mt-1">{{ form.errors.desc }}</div>
        </div>

        <div class="flex space-x-3">
          <button
            type="submit"
            class="flex-1 p-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700"
            :disabled="form.processing"
          >
            Save
          </button>
          <button
            type="button"
            @click="closeModal"
            class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
 </div>
</div>
</template>
