<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const showModal = ref(false);

const years = [
{ id: 1, name: 'TAHUN 1', desc: 'KEMASKINI KELAS TAHUN 1', icon: '1' },
{ id: 2, name: 'TAHUN 2', desc: 'KEMASKINI KELAS TAHUN 2', icon: '2' },
{ id: 3, name: 'TAHUN 3', desc: 'KEMASKINI KELAS TAHUN 3', icon: '3' },
{ id: 4, name: 'TAHUN 4', desc: 'KEMASKINI KELAS TAHUN 4', icon: '4' },
{ id: 5, name: 'TAHUN 5', desc: 'KEMASKINI KELAS TAHUN 5', icon: '5' },
{ id: 6, name: 'TAHUN 6', desc: 'KEMASKINI KELAS TAHUN 6', icon: '6' }
];

const closeModal = () => {
 showModal.value = false;
};
</script>

<template>
 <div class="bg-mint-50 min-h-screen p-6 relative">
   <div class="max-w-4xl mx-auto">
     <h1 class="text-3xl font-bold text-gray-800 mb-8">Kemaskini</h1>

     <div class="grid gap-4">
       <Link
         v-for="year in years"
         :key="year.id"
         :href="`/kemaskini/tahun/${year.id}`"
         class="bg-white rounded-lg shadow-md p-4 flex items-center hover:bg-gray-50 transition-colors"
       >
         <div class="w-12 h-12 bg-mint-200 text-xl rounded-full flex items-center justify-center text-xl font-bold mr-4">
           {{ year.icon }}
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
       <h2 class="text-xl font-semibold mb-4">Add New</h2>

       <div class="space-y-3">
         <button
           class="w-full p-3 text-left rounded-lg hover:bg-mint-50 flex items-center"
           @click="closeModal"
         >
           <i class="fas fa-users-class mr-3 text-mint-600"></i>
           Add New Year
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
