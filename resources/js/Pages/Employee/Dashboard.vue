<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { computed } from 'vue';

const props = defineProps({
    balances: Array,
    requests: Object,
    user: Object,
});

const form = useForm({
    leave_type: '',
    start_date: '',
    end_date: '',
    reason: '',
});

const today = computed(() => new Date().toISOString().split('T')[0]);

function getAvailableBalance(balance) {
    // Backend sends available_days (balance - used - pending request days) when not yet deducted until HR approval
    const available = balance.available_days ?? Math.max(0, balance.balance - balance.used);
    return Math.max(0, available);
}

const availableBalances = computed(() => {
    return props.balances.filter(b => getAvailableBalance(b) > 0);
});

const hasBalance = computed(() => {
    return props.balances.some(b => b.balance > 0);
});

function getBalanceColor(balance) {
    const available = getAvailableBalance(balance);
    const total = balance.balance;
    const percentage = total > 0 ? (available / total) * 100 : 0;
    
    if (available === 0) return 'bg-gray-100 text-gray-400';
    if (percentage > 50) return 'bg-green-50 text-green-700 border-green-200';
    if (percentage >= 10) return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    return 'bg-red-50 text-red-700 border-red-200';
}

function submit() {
    form.post(route('employee.store-request'), {
        onSuccess: () => form.reset(),
    });
}

function cancelRequest(id) {
    if (confirm('Are you sure you want to cancel this request?')) {
        router.patch(route('request.cancel', id));
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
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
    <Head title="Employee Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Employee Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Leave Balances -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Leave Balances</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="balance in balances"
                            :key="balance.id"
                            :class="['rounded-lg border p-4', getBalanceColor(balance)]"
                        >
                            <h4 class="text-sm font-medium">{{ balance.leave_type }}</h4>
                            <p class="mt-2 text-2xl font-bold">{{ getAvailableBalance(balance) }} / {{ balance.balance }}</p>
                            <p class="text-xs opacity-70">Available / Total</p>
                        </div>
                        <p v-if="balances.length === 0" class="col-span-full text-center text-gray-500">
                            No leave balances found.
                        </p>
                    </div>
                </div>

                <!-- Submit Leave Request Form -->
                <div v-if="hasBalance" class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Submit Leave Request</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="leave_type" value="Leave Type" />
                            <select
                                id="leave_type"
                                v-model="form.leave_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">Select Leave Type</option>
                                <option v-for="balance in availableBalances" :key="balance.id" :value="balance.leave_type">
                                    {{ balance.leave_type }} ({{ getAvailableBalance(balance) }} days available)
                                </option>
                            </select>
                            <InputError :message="form.errors.leave_type" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="start_date" value="Start Date" />
                                <TextInput
                                    id="start_date"
                                    type="date"
                                    v-model="form.start_date"
                                    :min="today"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.start_date" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="end_date" value="End Date" />
                                <TextInput
                                    id="end_date"
                                    type="date"
                                    v-model="form.end_date"
                                    :min="form.start_date || today"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.end_date" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="reason" value="Reason" />
                            <textarea
                                id="reason"
                                v-model="form.reason"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter reason for leave"
                                required
                            ></textarea>
                            <InputError :message="form.errors.reason" class="mt-2" />
                        </div>

                        <div>
                            <PrimaryButton :disabled="form.processing">
                                <span v-if="form.processing">Submitting...</span>
                                <span v-else>Submit Request</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div v-else class="rounded-md bg-yellow-50 p-4">
                    <p class="text-sm text-yellow-700">You do not have any available leave balance to submit a request.</p>
                </div>

                <!-- Leave Requests History -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">My Leave Requests</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Start Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">End Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="request in requests.data" :key="request.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ request.leave_type }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ formatDate(request.start_date) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ formatDate(request.end_date) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ request.number_of_days }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="['inline-flex rounded-full px-2 text-xs font-semibold leading-5', getStatusBadge(request.status).style]">
                                            {{ getStatusBadge(request.status).label }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <button
                                            v-if="request.status === 'pending'"
                                            @click="cancelRequest(request.id)"
                                            class="rounded-md border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                        >
                                            Cancel Request
                                        </button>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No leave requests found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
