<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  teachers: Array,
  availableClasses: Array
});

const showEditModal = ref(false);
const selectedTeacher = ref(null);

const editForm = useForm({
  classes: []
});

const openEditModal = (teacher) => {
  selectedTeacher.value = teacher;
  editForm.reset();
  editForm.classes = teacher.classes.map(c => c.id);
  showEditModal.value = true;
};

const submitEdit = () => {
  editForm.put(route('users.update.classes', selectedTeacher.value.id), {
    onSuccess: () => {
      showEditModal.value = false;
      selectedTeacher.value = null;
      editForm.reset();
      window.location.reload();
    }
  });
};

const editableClasses = computed(() => {
  if (!selectedTeacher.value) return [];
  const teacherClasses = selectedTeacher.value.classes || [];
  const combined = [...teacherClasses, ...props.availableClasses];
  return [...new Map(combined.map(item => [item.id, item])).values()]
    .sort((a, b) => a.name.localeCompare(b.name));
});
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-mint-50 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-800">Senarai Pengguna</h1>
          <p class="text-gray-600">Urus senarai pengguna dan kelas yang diajar</p>
        </div>

        <!-- Teachers Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="teacher in teachers" :key="teacher.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-semibold text-lg">{{ teacher.name }}</h3>
                <p class="text-gray-600 text-sm">{{ teacher.email }}</p>
              </div>
              <div class="flex space-x-2">
                <button @click="openEditModal(teacher)" class="text-mint-600 hover:bg-mint-50 p-2 rounded-full">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </div>

            <!-- Classes Taught -->
            <div class="mt-4">
              <p class="text-sm text-gray-500 mb-2">Kelas Yang Diajar:</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="class_ in teacher.classes" :key="class_.id"
                  class="px-3 py-1 bg-mint-100 text-mint-700 text-sm rounded-full">
                  {{ class_.name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Edit Modal -->
        <div v-if="showEditModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
          <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Edit Classes</h2>

            <form @submit.prevent="submitEdit" class="space-y-4">
              <!-- Read-only info -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="mb-2">
                  <span class="text-sm text-gray-500">Name:</span>
                  <p class="text-gray-900 font-medium">{{ selectedTeacher.name }}</p>
                </div>
                <div>
                  <span class="text-sm text-gray-500">Email:</span>
                  <p class="text-gray-900">{{ selectedTeacher.email }}</p>
                </div>
              </div>

              <!-- Classes selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Classes</label>
                <div class="mt-2 space-y-2">
                  <label v-for="class_ in editableClasses" :key="class_.id" class="flex items-center">
                    <input type="checkbox" v-model="editForm.classes" :value="class_.id"
                      class="rounded border-gray-300 text-mint-600">
                    <span class="ml-2">{{ class_.name }}</span>
                  </label>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex space-x-3">
                <button type="submit" class="flex-1 p-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
                  Update Classes
                </button>
                <button type="button" @click="showEditModal = false"
                  class="flex-1 p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
