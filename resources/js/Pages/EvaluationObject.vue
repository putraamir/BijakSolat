<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const selectedYear = ref(1);
const showAddModal = ref(false);
const showCsvModal = ref(false);

const props = defineProps({
    items: Object,
    year: Number
});

const csvForm = useForm({
    file: null
});

const submitCsv = () => {
    csvForm.post(route('evaluation.import'), {
        preserveScroll: true,
        onSuccess: () => {
            showCsvModal.value = false;
            csvForm.reset();
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Objek Penilaian</h1>
                <button @click="showCsvModal = true"
                    class="px-4 py-2 bg-mint-600 text-white rounded-lg">
                    Import CSV
                </button>
            </div>

            <div v-for="(categoryItems, category) in items" :key="category"
                class="mb-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">{{ category }}</h2>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left">No.</th>
                            <th class="text-left">Item</th>
                            <th class="text-left">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in categoryItems" :key="item.id">
                            <td class="py-2">{{ item.sequence }}</td>
                            <td>{{ item.title }}</td>
                            <td>{{ item.type }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- CSV Import Modal -->
            <div v-if="showCsvModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Import CSV</h2>
                    <form @submit.prevent="submitCsv">
                        <input type="file"
                            @input="e => csvForm.file = e.target.files[0]"
                            accept=".csv"
                            class="mb-4">
                        <div class="flex justify-end gap-2">
                            <button type="button"
                                @click="showCsvModal = false"
                                class="px-4 py-2 border rounded">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-mint-600 text-white rounded">
                                Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
