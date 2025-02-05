<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  year: Number,
  classes: Array
});

const showModal = ref(false);

const form = useForm({
  name: '',
  year_id: props.year
});

const submit = () => {
  form.post(route('classes.store'), {
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

const deleteClass = async (classId) => {
  if (confirm('Are you sure you want to delete this class?')) {
    try {
      await axios.delete(`/api/classes/${classId}`);
      window.location.reload();
    } catch (error) {
      console.error('Error deleting class:', error);
    }
  }
};
</script>



<template>
  <div class="bg-mint-50 min-h-screen p-6 relative">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center mb-8">
        <Link href="/kemaskini" class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200">
        <i class="fas fa-arrow-left"></i>
        </Link>
        <h1 class="text-2xl font-semibold text-gray-800">KEMASKINI BACAAN TAHUN {{ year }}</h1>
      </div>

      <div class="space-y-6">
        <div v-for="(class_, index) in classes" :key="class_.id" class="flex items-center justify-between bg-white rounded-lg shadow-md p-6 hover:bg-gray-50">
          <Link :href="`/kemaskini/tahun/${year}/class/${class_.id}`" class="flex-1">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-mint-100 rounded-full flex items-center justify-center text-mint-600 mr-4">
                {{ index + 1 }}
              </div>
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ class_.name }}</h3>
                <p class="text-sm text-gray-600">{{ class_.students }}</p>
              </div>
            </div>
          </Link>
          <button @click="deleteClass(class_.id)" class="p-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
            <i class="fas fa-trash-alt"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Floating Action Button -->
    <button @click="showModal = true"
      class="fixed bottom-6 right-6 w-14 h-14 bg-mint-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-mint-700 hover:scale-105 transition-all">
      <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-xl font-semibold mb-4">Add New Class</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Class Name</label>
            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
              placeholder="e.g. 1 Cemerlang">
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="flex space-x-3">
            <button type="submit" class="flex-1 p-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700"
              :disabled="form.processing">
              Save
            </button>
            <button type="button" @click="closeModal"
              class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
