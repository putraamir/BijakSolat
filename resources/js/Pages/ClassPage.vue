<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  year: Number,
  classes: {
    type: Array,
    default: () => [
      {id: 1, name: '1 Cemerlang', students: '20 Pelajar'},
      {id: 2, name: '1 Gemilang', students: '23 Pelajar'}
    ]
  }
});

const showModal = ref(false);
const navigateToAddStudent = () => {
  window.location.href = `/kemaskini/tahun/${props.year}/add-student`;
  showModal.value = false;
};
</script>



<template>
   <div class="bg-mint-50 min-h-screen p-6 relative">
       <div class="max-w-4xl mx-auto">
           <div class="flex items-center mb-8">
               <Link href="/kemaskini" class="mr-4 p-2 rounded-lg bg-mint-100 hover:bg-mint-200">
                   <i class="fas fa-arrow-left"></i>
               </Link>
               <h1 class="text-2xl font-semibold text-gray-800">KEMASKINI BACAAN TAHUN {{year}}</h1>
           </div>

           <div class="space-y-6">
               <Link
                   v-for="class_ in classes"
                   :key="class_.id"
                   :href="`/kemaskini/tahun/${year}/class/${class_.id}`"
                   class="block bg-white rounded-lg shadow-md p-6 hover:bg-gray-50"
               >
                   <div class="flex items-center">
                       <div class="w-12 h-12 bg-mint-100 rounded-full flex items-center justify-center text-mint-600 mr-4">
                           {{ class_.id }}
                       </div>
                       <div>
                           <h3 class="text-lg font-medium text-gray-900">{{ class_.name }}</h3>
                           <p class="text-sm text-gray-600">{{ class_.students }}</p>
                       </div>
                   </div>
               </Link>
           </div>
       </div>

     <!-- Floating Action Button -->
<button
    @click="showModal = true"
    class="fixed bottom-6 right-6 w-14 h-14 bg-mint-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-mint-700"
>
    <i class="fas fa-plus text-xl"></i>
</button>

<!-- Modal -->
<div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-xl font-semibold mb-4">Add New</h2>
        <div class="space-y-3">
            <button
                class="w-full p-3 text-left rounded-lg hover:bg-mint-50 flex items-center"
                @click="closeModal"
            >
                <i class="fas fa-users-class mr-3 text-mint-600"></i>
                Add New Class
            </button>

            <button
                class="w-full p-3 text-left rounded-lg hover:bg-mint-50 flex items-center"
                @click="navigateToAddStudent"
            >
                <i class="fas fa-user-plus mr-3 text-mint-600"></i>
                Add New Student
            </button>
        </div>

        <button
            @click="closeModal"
            class="mt-6 w-full p-2 border border-gray-300 rounded-lg hover:bg-gray-50"
        >
            Cancel
        </button>
    </div>
</div>
   </div>
</template>
