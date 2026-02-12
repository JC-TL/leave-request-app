<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { getRelationName } from '@/utils/relations';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    employees: Object,
    pendingRegistrations: Array,
});

function deleteEmployee(employee) {
    if (confirm(`Are you sure you want to delete "${employee.name}"? This action cannot be undone.`)) {
        router.delete(route('hr.destroy-employee', employee.emp_id));
    }
}

function formatRole(role) {
    const roles = {
        employee: 'Employee',
        dept_manager: 'Manager',
        hr_admin: 'HR Admin',
        ceo: 'CEO',
    };
    return roles[role] || role;
}

function approveRegistration(pending) {
    if (confirm(`Approve registration for "${pending.name}"? They will be able to sign in.`)) {
        router.patch(route('hr.approve-registration', pending.id));
    }
}

function rejectRegistration(pending) {
    if (confirm(`Reject registration for "${pending.name}"? This will remove their application.`)) {
        router.delete(route('hr.reject-registration', pending.id));
    }
}

</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Employees</h2>
                <Link :href="route('hr.create-employee')">
                    <PrimaryButton>Add Employee</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Pending Registrations -->
                <div v-if="pendingRegistrations?.length > 0" class="bg-white p-6 shadow sm:rounded-lg border-l-4 border-amber-500">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Pending Registrations</h3>
                    <p class="mb-4 text-sm text-gray-500">These users registered from the public page and are awaiting approval.</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Applied</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="pending in pendingRegistrations" :key="pending.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ pending.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ pending.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="[
                                            'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                            pending.role === 'dept_manager' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ formatRole(pending.role) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ getRelationName(pending, 'department') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ pending.created_at ? new Date(pending.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button
                                            type="button"
                                            class="mr-3 text-green-600 hover:text-green-900 font-medium"
                                            title="Approve"
                                            @click="approveRegistration(pending)"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-900 font-medium"
                                            title="Reject"
                                            @click="rejectRegistration(pending)"
                                        >
                                            Reject
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Employees List -->
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Manager</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="employee in employees.data" :key="employee.emp_id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ employee.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ employee.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="[
                                            'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                                            employee.role === 'dept_manager' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ formatRole(employee.role) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ getRelationName(employee, 'department') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ getRelationName(employee, 'manager') ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <Link
                                            :href="route('hr.edit-employee', employee.emp_id)"
                                            class="mr-3 inline-block text-indigo-600 hover:text-indigo-900"
                                            title="Edit"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </Link>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                            @click="deleteEmployee(employee)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="employees.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No employees found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
