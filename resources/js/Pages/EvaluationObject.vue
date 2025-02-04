<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const selectedYear = ref(1);
const showAddModal = ref(false);
const showCsvModal = ref(false);
const fileInput = ref(null);

// Hardcoded data
const evaluationItems = {
    'Amali Wuduk': [
        { id: 1, title: 'Bacaan Basmalah', type: 'SUNAT', sequence: 1 },
        { id: 2, title: 'Membasuh kedua-dua tangan', type: 'RUKUN', sequence: 2 },
        { id: 3, title: 'Berkumur-kumur', type: 'SUNAT', sequence: 3 },
        { id: 4, title: 'Memasukkan air ke dalam hidung', type: 'RUKUN', sequence: 4 },
        { id: 5, title: 'Membasukkan muka', type: 'RUKUN', sequence: 5 },
        { id: 6, title: 'Niat wuduk', type: 'RUKUN', sequence: 6 }
    ],
    'Amali Solat (Perlakuan)': [
        { id: 7, title: 'Berdiri betul', type: 'RUKUN', sequence: 1 },
        { id: 8, title: 'Kedudukan tangan semasa takbiratulihram', type: 'SUNAT', sequence: 2 },
        { id: 9, title: 'Kedudukan tangan selepas takbiratulihram', type: 'SUNAT', sequence: 3 },
        { id: 10, title: 'Rukuk', type: 'RUKUN', sequence: 4 },
        { id: 11, title: 'Iktidal', type: 'RUKUN', sequence: 5 },
        { id: 12, title: 'Sujud', type: 'RUKUN', sequence: 6 }
    ],
    'Amali Solat (Bacaan)': [
        { id: 13, title: 'Lafaz niat solat Subuh', type: 'SUNAT', sequence: 1 },
        { id: 14, title: 'Lafaz niat solat Zuhur', type: 'SUNAT', sequence: 2 },
        { id: 15, title: 'Lafaz niat solat Asar', type: 'SUNAT', sequence: 3 },
        { id: 16, title: 'Lafaz niat solat Maghrib', type: 'SUNAT', sequence: 4 },
        { id: 17, title: 'Lafaz niat solat Isyak', type: 'SUNAT', sequence: 5 },
        { id: 18, title: 'Takbiratulihram', type: 'RUKUN', sequence: 6 }
    ]
};

const csvForm = useForm({
    file: null,
    year: selectedYear
});

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        csvForm.file = file;
    }
};

const submitCsv = () => {
    if (!csvForm.file) {
        alert('Please select a file first');
        return;
    }
};

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this item?')) {
        console.log('Deleting item:', id);
    }
};
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Objek Penilaian</h1>
                <div class="flex gap-4">
                    <select v-model="selectedYear"
                        class="rounded-lg border-gray-300 focus:border-mint-500 focus:ring-mint-500">
                        <option value="1">Tahun 1</option>
                        <option value="2">Tahun 2</option>
                        <option value="3">Tahun 3</option>
                        <option value="4">Tahun 4</option>
                        <option value="5">Tahun 5</option>
                        <option value="6">Tahun 6</option>
                    </select>
                    <button @click="showAddModal = true"
                        class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
                        Tambah Penilaian
                    </button>
                    <button @click="showCsvModal = true"
                        class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
                        Import CSV
                    </button>
                </div>
            </div>

            <!-- Evaluation Objects List -->
            <div class="space-y-6">
                <div v-for="(items, category) in evaluationItems" :key="category"
                    class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-mint-50">
                        <h2 class="text-lg font-semibold text-mint-800">{{ category }}</h2>
                    </div>
                    <div class="p-6">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th class="pb-2 w-16">No.</th>
                                    <th class="pb-2">Perkara</th>
                                    <th class="pb-2 w-32">Jenis</th>
                                    <th class="pb-2 w-24">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in items" :key="item.id" class="border-t">
                                    <td class="py-3">{{ item.sequence }}</td>
                                    <td class="py-3">{{ item.title }}</td>
                                    <td class="py-3">
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium': true,
                                            'bg-green-100 text-green-800': item.type === 'RUKUN',
                                            'bg-blue-100 text-blue-800': item.type === 'SUNAT'
                                        }">
                                            {{ item.type }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <button @click="deleteItem(item.id)"
                                            class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CSV Import Modal -->
            <div v-if="showCsvModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Import CSV</h2>
                    <form @submit.prevent="submitCsv" class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Select CSV File
                            </label>
                            <input type="file"
                                ref="fileInput"
                                @input="handleFileUpload"
                                accept=".csv"
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-mint-50 file:text-mint-700
                                    hover:file:bg-mint-100">
                            <p class="text-sm text-gray-500">
                                Format: title,type,sequence,category,year
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" @click="showCsvModal = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
                                Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
