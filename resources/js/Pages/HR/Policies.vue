<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

const props = defineProps({
    policies: Array,
});

const showModal = ref(false);
const editingPolicy = ref(null);
const form = useForm({
    annual_entitlement: 0,
});

function openEditModal(policy) {
    editingPolicy.value = policy;
    form.annual_entitlement = policy.annual_entitlement;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingPolicy.value = null;
    form.reset();
}

function submitUpdate() {
    form.patch(route('hr.update-policy', editingPolicy.value.id), {
        onSuccess: () => closeModal(),
    });
}
</script>

<template>
    <Head title="Leave Policies" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Leave Policies</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Annual Entitlement (Days)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="policy in policies" :key="policy.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ policy.leave_type }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ policy.annual_entitlement }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button @click="openEditModal(policy)" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="policies.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No policies found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Edit Policy: {{ editingPolicy?.leave_type }}</h2>
                <form @submit.prevent="submitUpdate" class="mt-6">
                    <div>
                        <InputLabel for="annual_entitlement" value="Annual Entitlement (Days)" />
                        <TextInput
                            id="annual_entitlement"
                            type="number"
                            v-model="form.annual_entitlement"
                            class="mt-1 block w-full"
                            min="0"
                            max="365"
                            required
                        />
                        <InputError :message="form.errors.annual_entitlement" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Update Policy</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
