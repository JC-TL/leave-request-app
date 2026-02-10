<script setup>
import GuestLayout from "vendor/laravel/breeze/stubs/inertia-react/resources/js/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    departments: Array,
    roles: Array,
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "",
    department_id: "",
});

function submit() {
    form.post(route("hr.store-employee"));
}

function formatRole(role) {
    return role.replace("_", " ").replace(/\b\w/g, (c) => c.toUpperCase());
}
</script>

<template>
    <Head title="Add Employee" />

    <GuestLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('hr.employees')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Add New Employee
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="name" value="Full Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError
                                    :message="form.errors.name"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Address" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError
                                    :message="form.errors.email"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError
                                    :message="form.errors.password"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="password_confirmation"
                                    value="Confirm Password"
                                />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select Role</option>
                                    <option
                                        v-for="role in roles"
                                        :key="role"
                                        :value="role"
                                    >
                                        {{ formatRole(role) }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.role"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="department_id"
                                    value="Department"
                                />
                                <select
                                    id="department_id"
                                    v-model="form.department_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select Department</option>
                                    <option
                                        v-for="dept in departments"
                                        :key="dept.id"
                                        :value="dept.id"
                                    >
                                        {{ dept.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.department_id"
                                    class="mt-2"
                                />
                            </div>
                        </div>

                        <div class="rounded-md bg-blue-50 p-4">
                            <p class="text-sm text-blue-700">
                                Leave balances will be automatically initialized
                                based on current policies.
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('hr.employees')">
                                <SecondaryButton type="button"
                                    >Cancel</SecondaryButton
                                >
                            </Link>
                            <PrimaryButton :disabled="form.processing"
                                >Create Employee</PrimaryButton
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
