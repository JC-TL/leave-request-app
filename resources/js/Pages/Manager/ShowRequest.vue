<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { computed } from 'vue';

const props = defineProps({
    leaveRequest: Object,
    balance: Object,
});

const approveForm = useForm({ comment: '' });
const rejectForm = useForm({ reason: '' });

const availableBalance = computed(() => {
    if (!props.balance) return 0;
    return Math.max(0, props.balance.balance - props.balance.used);
});

const totalBalance = computed(() => props.balance?.balance ?? 0);

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

function approve() {
    approveForm.patch(route('manager.approve-request', props.leaveRequest.id));
}

function reject() {
    rejectForm.patch(route('manager.reject-request', props.leaveRequest.id));
}
</script>

<template>
    <Head title="Request Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('manager.dashboard')" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Request Details</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Employee Details -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Employee Details</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.employee.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.employee.department?.name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.employee.email }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Request Details -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Request Details</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.leave_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dates</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(leaveRequest.start_date) }} - {{ formatDate(leaveRequest.end_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Number of Days</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.number_of_days }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Balance</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ availableBalance }} / {{ totalBalance }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Submitted</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(leaveRequest.created_at) }}</dd>
                        </div>
                        <div class="sm:col-span-3">
                            <dt class="text-sm font-medium text-gray-500">Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ leaveRequest.reason }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div v-if="leaveRequest.status === 'pending'" class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-6 text-lg font-medium text-gray-900">Actions</h3>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Approve Form -->
                        <form @submit.prevent="approve" class="space-y-4">
                            <div>
                                <InputLabel for="comment" value="Comment (Optional)" />
                                <textarea
                                    id="comment"
                                    v-model="approveForm.comment"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Add an optional comment..."
                                ></textarea>
                            </div>
                            <PrimaryButton :disabled="approveForm.processing" class="w-full justify-center bg-green-600 hover:bg-green-700">
                                Approve Request
                            </PrimaryButton>
                        </form>

                        <!-- Reject Form -->
                        <form @submit.prevent="reject" class="space-y-4">
                            <div>
                                <InputLabel for="reason" value="Reason (Required)" />
                                <textarea
                                    id="reason"
                                    v-model="rejectForm.reason"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Please provide a reason for rejection..."
                                    required
                                ></textarea>
                                <InputError :message="rejectForm.errors.reason" class="mt-2" />
                            </div>
                            <DangerButton :disabled="rejectForm.processing" class="w-full justify-center">
                                Reject Request
                            </DangerButton>
                        </form>
                    </div>
                </div>

                <div v-else class="rounded-md bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">This request has already been processed.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
