<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    employee: Object,
    departments: Array,
    roles: Array,
    employees: Array,
});

const form = useForm({
    role: props.employee.role,
    dept_id: props.employee.dept_id ?? '',
    manager_id: props.employee.manager_id ?? '',
});

form.transform((data) => ({
    ...data,
    dept_id: data.dept_id === '' || data.dept_id === null ? null : Number(data.dept_id),
    manager_id: data.manager_id === '' || data.manager_id === null ? null : Number(data.manager_id),
}));

const employeesForDepartment = computed(() => {
    if (!form.dept_id) return [];
    return (props.employees ?? []).filter(emp => emp.dept_id == form.dept_id && emp.emp_id !== props.employee.emp_id);
});

function submit() {
    form.patch(route('hr.update-employee', props.employee.emp_id));
}

function formatRole(role) {
    return role.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
}
</script>

<template>
    <Head title="Edit Employee" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('hr.employees')" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Employee: {{ employee.name }}</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="rounded-md bg-gray-50 p-4">
                            <p class="text-sm text-gray-600">{{ employee.name }} ({{ employee.email }})</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="role" value="Role" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="role in roles" :key="role" :value="role">{{ formatRole(role) }}</option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="dept_id" value="Department" />
                                <select
                                    id="dept_id"
                                    v-model="form.dept_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">None</option>
                                    <option v-for="dept in departments" :key="dept.dept_id" :value="dept.dept_id">{{ dept.name }}</option>
                                </select>
                                <InputError :message="form.errors.dept_id" class="mt-2" />
                            </div>

                            <div v-if="form.role === 'employee'" class="sm:col-span-2">
                                <InputLabel for="manager_id" value="Manager" />
                                <select
                                    id="manager_id"
                                    v-model="form.manager_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Select employee as manager (optional – defaults to dept manager)</option>
                                    <option v-for="emp in employeesForDepartment" :key="emp.emp_id" :value="emp.emp_id">{{ emp.name }}</option>
                                </select>
                                <InputError :message="form.errors.manager_id" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('hr.employees')">
                                <SecondaryButton type="button">Cancel</SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">Update Employee</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
