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
    if (!dateString) return 'N/A';
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
    approveForm.patch(route('hr.approve-request', props.leaveRequest.id));
}

function reject() {
    rejectForm.patch(route('hr.reject-request', props.leaveRequest.id));
}

function getStatusBadge(status) {
    const styles = {
        pending: 'bg-yellow-100 text-yellow-800',
        dept_manager_approved: 'bg-blue-100 text-blue-800',
        dept_manager_rejected: 'bg-red-100 text-red-800',
        hr_approved: 'bg-green-100 text-green-800',
        hr_rejected: 'bg-red-100 text-red-800',
    };
    const labels = {
        pending: 'Pending',
        dept_manager_approved: 'Manager Approved',
        dept_manager_rejected: 'Manager Rejected',
        hr_approved: 'Approved',
        hr_rejected: 'Rejected',
    };
    return { style: styles[status] || 'bg-gray-100 text-gray-800', label: labels[status] || status };
}
</script>

<template>
    <Head title="Request Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('hr.dashboard')" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Request Details</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Employee & Request Details -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Employee Details</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ leaveRequest.employee.name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Department</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ leaveRequest.employee.department?.name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ leaveRequest.employee.email }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Balance</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ availableBalance }} / {{ totalBalance }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Request Details</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Leave Type</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ leaveRequest.leave_type }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Dates</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ formatDate(leaveRequest.start_date) }} - {{ formatDate(leaveRequest.end_date) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Days</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ leaveRequest.number_of_days }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Submitted</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ formatDateTime(leaveRequest.created_at) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Reason -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-2 text-lg font-medium text-gray-900">Reason</h3>
                    <p class="text-sm text-gray-700">{{ leaveRequest.reason }}</p>
                </div>

                <!-- Approval Timeline -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Approval Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full" :class="leaveRequest.department_manager ? 'bg-green-100' : 'bg-gray-100'">
                                <svg class="h-4 w-4" :class="leaveRequest.department_manager ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="leaveRequest.department_manager" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Manager Approval</p>
                                <p v-if="leaveRequest.department_manager" class="text-sm text-gray-500">
                                    Approved by {{ leaveRequest.department_manager.name }} on {{ formatDateTime(leaveRequest.approved_by_dept_at) }}
                                </p>
                                <p v-else class="text-sm text-gray-500">Pending manager approval</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full" :class="leaveRequest.hr_approver ? 'bg-green-100' : 'bg-gray-100'">
                                <svg class="h-4 w-4" :class="leaveRequest.hr_approver ? 'text-green-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="leaveRequest.hr_approver" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">HR Approval</p>
                                <p v-if="leaveRequest.hr_approver" class="text-sm text-gray-500">
                                    Approved by {{ leaveRequest.hr_approver.name }} on {{ formatDateTime(leaveRequest.approved_by_hr_at) }}
                                </p>
                                <p v-else class="text-sm text-gray-500">Pending HR approval</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div v-if="leaveRequest.status === 'dept_manager_approved'" class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-6 text-lg font-medium text-gray-900">Actions</h3>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
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

                <div v-else-if="leaveRequest.status === 'pending'" class="rounded-md bg-yellow-50 p-4">
                    <p class="text-sm text-yellow-700">This request is still pending manager approval.</p>
                </div>

                <div v-else class="rounded-md bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">This request has already been processed.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
