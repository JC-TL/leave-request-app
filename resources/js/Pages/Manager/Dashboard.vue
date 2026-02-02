<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    pendingRequests: Object,
    teamMembers: Array,
    pendingCount: Number,
    approvedThisMonth: Number,
    teamCount: Number,
    leaveTypes: Array,
    selectedLeaveType: String,
    user: Object,
});

const selectedType = ref(props.selectedLeaveType);

watch(selectedType, (newValue) => {
    router.get(route('manager.dashboard'), { leave_type: newValue }, { preserveState: true });
});

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function getTeamMemberBalance(member) {
    const balance = member.leave_balances?.find(b => b.leave_type === selectedType.value);
    return {
        available: balance ? Math.max(0, balance.balance - balance.used) : 0,
        total: balance ? balance.balance : 0
    };
}

function isLowBalance(member) {
    const { available, total } = getTeamMemberBalance(member);
    return total > 0 && available < 3 && available > 0;
}
</script>

<template>
    <Head title="Manager Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Manager Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Pending Approvals</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600">{{ pendingCount }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Approved This Month</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ approvedThisMonth }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Team Members</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ teamCount }}</dd>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Pending Requests from My Team</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Dates</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="request in pendingRequests.data" :key="request.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ request.employee.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ request.leave_type }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ request.number_of_days }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ formatDate(request.created_at) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <Link
                                            :href="route('manager.show-request', request.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View Details
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="pendingRequests.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No pending requests found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Team Members</h3>
                        <div class="flex items-center gap-2">
                            <label for="leave_type" class="text-sm text-gray-700">Leave type:</label>
                            <select
                                id="leave_type"
                                v-model="selectedType"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="leaveType in leaveTypes" :key="leaveType" :value="leaveType">
                                    {{ leaveType }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Available</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr
                                    v-for="member in teamMembers"
                                    :key="member.id"
                                    :class="{ 'bg-yellow-50': isLowBalance(member) }"
                                >
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ member.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ member.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="isLowBalance(member) ? 'text-yellow-700 font-semibold' : 'text-gray-500'">
                                            {{ getTeamMemberBalance(member).available }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ getTeamMemberBalance(member).total }}</td>
                                </tr>
                                <tr v-if="teamMembers.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No team members found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
