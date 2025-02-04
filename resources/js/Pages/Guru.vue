<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const showEditModal = ref(false);
const selectedTeacher = ref(null);
const selectedClasses = ref([]);

// Local state for teachers
const teachers = ref([
  {
    id: 1,
    name: 'Ustaz Ahmad',
    email: 'ahmad@example.com',
    teacher_classes: [
      { year: 1, class_name: 'Cemerlang' },
      { year: 2, class_name: 'Gemilang' }
    ]
  },
  {
    id: 2,
    name: 'Ustazah Sarah',
    email: 'sarah@example.com',
    teacher_classes: [
      { year: 3, class_name: 'Cemerlang' }
    ]
  },
  {
    id: 3,
    name: 'Ustaz Mahmud',
    email: 'mahmud@example.com',
    teacher_classes: [
      { year: 4, class_name: 'Cemerlang' },
      { year: 5, class_name: 'Gemilang' }
    ]
  }
]);

const openEditModal = (teacher) => {
  selectedTeacher.value = teacher;
  selectedClasses.value = [...teacher.teacher_classes];
  showEditModal.value = true;
};

const addClass = () => {
  selectedClasses.value.push({ year: '', class_name: '' });
};

const removeClass = (index) => {
  selectedClasses.value.splice(index, 1);
};

const closeModal = () => {
  showEditModal.value = false;
  selectedTeacher.value = null;
  selectedClasses.value = [];
};

const saveChanges = () => {
  // Update the local state instead of sending to backend
  const teacherIndex = teachers.value.findIndex(t => t.id === selectedTeacher.value.id);
  if (teacherIndex !== -1) {
    teachers.value[teacherIndex].teacher_classes = [...selectedClasses.value];
  }
  closeModal();
};

</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-mint-50 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-800">Senarai Guru</h1>
          <p class="text-gray-600">Urus senarai guru dan kelas yang diajar</p>
        </div>

        <!-- Teachers Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="teacher in teachers"
               :key="teacher.id"
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-semibold text-lg">{{ teacher.name }}</h3>
                <p class="text-gray-600 text-sm">{{ teacher.email }}</p>
              </div>
              <button @click="openEditModal(teacher)"
                      class="text-mint-600 hover:bg-mint-50 p-2 rounded-full">
                <i class="fas fa-edit"></i>
              </button>
            </div>

            <!-- Classes Taught -->
            <div class="mt-4">
              <p class="text-sm text-gray-500 mb-2">Kelas Yang Diajar:</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="(class_, index) in teacher.teacher_classes"
                      :key="index"
                      class="px-3 py-1 bg-mint-100 text-mint-700 text-sm rounded-full">
                  Tahun {{ class_.year }} {{ class_.class_name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-xl font-semibold">Edit Kelas</h2>
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
              </button>
            </div>

            <div class="space-y-4 mb-6">
              <!-- Teacher Info -->
              <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium">{{ selectedTeacher?.name }}</h3>
                <p class="text-sm text-gray-600">{{ selectedTeacher?.email }}</p>
              </div>

              <!-- Class Assignments -->
              <div v-for="(classItem, index) in selectedClasses"
                   :key="index"
                   class="flex items-center gap-2">
                <select v-model="classItem.year"
                        class="rounded-md border-gray-300 text-sm">
                  <option value="">Pilih Tahun</option>
                  <option v-for="year in 6" :key="year" :value="year">
                    Tahun {{ year }}
                  </option>
                </select>

                <select v-model="classItem.class_name"
                        class="rounded-md border-gray-300 text-sm">
                  <option value="">Pilih Kelas</option>
                  <option value="Cemerlang">Cemerlang</option>
                  <option value="Gemilang">Gemilang</option>
                </select>

                <button @click="removeClass(index)"
                        class="p-2 text-red-500 hover:bg-red-50 rounded-full">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>

              <!-- Add Class Button -->
              <button @click="addClass"
                      class="w-full py-2 border-2 border-dashed border-mint-300 text-mint-600 rounded-lg hover:bg-mint-50 transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Kelas
              </button>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-2">
              <button @click="closeModal"
                      class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Batal
              </button>
              <button @click="saveChanges"
          class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
    Simpan
  </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
