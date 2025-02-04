<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    years: Array,
    evaluationData: Array,
    existingCategories: Array
});

const selectedYear = ref(1);

// Add watch effect for year changes
watch(selectedYear, (newYear) => {
    router.get(route('evaluation.index'),
        { year: newYear },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['evaluationData', 'existingCategories']
        }
    );
});

// Reset form on year change
watch(selectedYear, () => {
    form.reset();
    form.year_id = selectedYear.value;
});

const showAddModal = ref(false);
const showCsvModal = ref(false);
const fileInput = ref(null);

const formattedItems = computed(() => {
    const formatted = {};
    props.evaluationData.forEach(category => {
        formatted[category.name] = category.evaluation_items.map(item => ({
            id: item.id,
            title: item.title,
            type: item.type,
            sequence: item.sequence
        }));
    });
    return formatted;
});

const csvForm = useForm({
    file: null,
    year: selectedYear
});

const handleFileUpload = (e) => {
    csvForm.file = e.target.files[0];
};

const submitCsv = () => {
    if (!csvForm.file) {
        alert('Please select a file first');
        return;
    }

    let formData = new FormData();
    formData.append('file', csvForm.file);
    formData.append('year', csvForm.year);

    router.post(route('evaluation.import'), formData, {
        onSuccess: () => {
            showCsvModal.value = false;
            window.location.reload();
        },
        onError: () => {
            alert('Failed to import CSV');
        }
    });
};

const deleteItem = (itemId) => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(route('evaluation.destroy', itemId), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Item deleted successfully');
            },
            onError: (errors) => {
                console.error('Delete failed:', errors);
                alert('Failed to delete item');
            }
        });
    }
};

const isNewCategory = ref(true);
const selectedCategory = ref(null);
const form = useForm({
    category: '',
    categoryId: null,
    year_id: selectedYear,
    items: [{ title: '', type: 'RUKUN', sequence: 1 }]
});

watch(selectedCategory, (newVal) => {
    if (newVal) {
        const category = props.evaluationData.find(c => c.id === newVal);
        if (category) {
            form.categoryId = category.id;
            form.category = category.name;
        }
    }
});

const addItem = () => {
    form.items.push({
        title: '',
        type: 'RUKUN',
        sequence: form.items.length + 1
    });
};

const submitForm = () => {
    form.year_id = selectedYear.value;

    form.post(route('evaluation.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            window.location.reload();
        }
    });
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
                        <option v-for="year in years" :key="year.id" :value="year.id">
                            {{ year.name }}
                        </option>
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
                <div v-for="(items, category) in formattedItems" :key="category"
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
                                <tr v-for="(item, index) in items" :key="item.id" class="border-t">
                                    <td class="py-3">{{ index + 1 }}</td>
                                    <td class="py-3">{{ item.title }}</td>
                                    <td class="py-3">
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium': true,
                                            'bg-green-100 text-green-800': item.type === 'RUKUN',
                                            'bg-blue-100 text-blue-800': item.type === 'SUNAT',
                                            'bg-purple-100 text-purple-800': item.type === 'WAJIB'
                                        }">
                                            {{ item.type }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add Evaluation Modal -->
            <div v-if="showAddModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center px-4 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
                    <h2 class="text-xl font-semibold mb-4">Tambah Penilaian</h2>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <!-- Category Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="isNewCategory" :value="true" class="form-radio">
                                    <span class="ml-2">New Category</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="isNewCategory" :value="false" class="form-radio">
                                    <span class="ml-2">Existing Category</span>
                                </label>
                            </div>

                            <div v-if="isNewCategory">
                                <label class="block text-sm font-medium text-gray-700">New Category Name</label>
                                <input v-model="form.category" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>

                            <div v-else>
                                <label class="block text-sm font-medium text-gray-700">Select Category</label>
                                <select v-model="selectedCategory" class="mt-1 block w-full rounded-md border-gray-300"
                                    required>
                                    <option value="">Select a category</option>
                                    <option v-for="category in existingCategories" :key="category.id"
                                        :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Items -->
                        <div v-for="(item, index) in form.items" :key="index" class="space-y-3">
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input v-model="item.title" type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300" required>
                                </div>
                                <div class="w-32">
                                    <label class="block text-sm font-medium text-gray-700">Type</label>
                                    <select v-model="item.type" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="RUKUN">RUKUN</option>
                                        <option value="SUNAT">SUNAT</option>
                                        <option value="WAJIB">WAJIB</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="addItem" class="text-mint-600">
                            + Add Item
                        </button>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="submit" class="px-4 py-2 bg-mint-600 text-white rounded-lg">Save</button>
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 border rounded-lg">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CSV Import Modal -->
            <div v-if="showCsvModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Import CSV</h2>
                    <form @submit.prevent="submitCsv" class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Select CSV File
                            </label>
                            <input type="file" ref="fileInput" @input="handleFileUpload" accept=".csv" class="block w-full text-sm text-gray-500
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
                            <button type="submit" class="px-4 py-2 bg-mint-600 text-white rounded-lg hover:bg-mint-700">
                                Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
