<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { getRelationName } from '@/utils/relations';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const props = defineProps({
    departments: Array,
});

const showModal = ref(false);
const editingDept = ref(null);

const form = useForm({
    name: '',
    color: '#3b82f6',
});

const presetColors = [
    { value: '#3b82f6', label: 'Blue' },
    { value: '#10b981', label: 'Green' },
    { value: '#8b5cf6', label: 'Purple' },
    { value: '#f59e0b', label: 'Amber' },
    { value: '#ef4444', label: 'Red' },
    { value: '#06b6d4', label: 'Cyan' },
];

function openModal(dept = null) {
    editingDept.value = dept;
    if (dept) {
        form.name = dept.name;
        form.color = dept.color ?? '#3b82f6';
    } else {
        form.reset();
        form.color = '#3b82f6';
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingDept.value = null;
    form.reset();
}

function submit() {
    if (editingDept.value) {
        form.patch(route('hr.update-department', editingDept.value.dept_id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('hr.store-department'), {
            onSuccess: () => closeModal(),
        });
    }
}

function deleteDept(dept) {
    if (confirm(`Are you sure you want to delete "${dept.name}"? Employees in this department will have their department set to unassigned.`)) {
        router.delete(route('hr.destroy-department', dept.dept_id));
    }
}
</script>

<template>
    <Head title="Departments" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Departments</h2>
                <PrimaryButton @click="openModal">Add Department</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Color</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Manager</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="dept in departments" :key="dept.dept_id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ dept.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-block h-6 w-6 rounded border border-gray-200"
                                            :style="{ backgroundColor: dept.color }"
                                            :title="dept.color"
                                        />
                                        <span class="ml-2 text-sm text-gray-500">{{ dept.color }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ getRelationName(dept, 'manager') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button
                                            type="button"
                                            class="mr-3 text-indigo-600 hover:text-indigo-900"
                                            title="Edit"
                                            @click="openModal(dept)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                            @click="deleteDept(dept)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="departments.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No departments found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add / Edit Department Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ editingDept ? 'Edit Department' : 'Add Department' }}</h2>
                <form @submit.prevent="submit" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Department Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. Marketing"
                            maxlength="30"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="Color" />
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="preset in presetColors"
                                :key="preset.value"
                                type="button"
                                :class="[
                                    'h-8 w-8 rounded border-2 transition',
                                    form.color === preset.value ? 'border-gray-900 scale-110' : 'border-gray-200 hover:border-gray-400'
                                ]"
                                :style="{ backgroundColor: preset.value }"
                                :title="preset.label"
                                @click="form.color = preset.value"
                            />
                        </div>
                        <TextInput
                            v-model="form.color"
                            type="text"
                            class="mt-2 block w-full font-mono text-sm"
                            placeholder="#3b82f6"
                            maxlength="7"
                        />
                        <InputError :message="form.errors.color" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">{{ editingDept ? 'Update Department' : 'Add Department' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
