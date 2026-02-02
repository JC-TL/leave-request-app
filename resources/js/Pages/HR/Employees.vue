<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    employees: Object,
});

function formatRole(role) {
    const roles = {
        employee: 'Employee',
        dept_manager: 'Manager',
        hr_admin: 'HR Admin',
        ceo: 'CEO',
    };
    return roles[role] || role;
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="employee in employees.data" :key="employee.id">
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
                                        {{ employee.department?.name ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ employee.manager?.name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr v-if="employees.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No employees found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
