<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
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
    first_name: "",
    last_name: "",
    gender: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "",
    dept_id: "",
});

function submit() {
    form.post(route("register.store"));
}

function formatRole(role) {
    return role.replace("_", " ").replace(/\b\w/g, (c) => c.toUpperCase());
}
</script>

<template>
    <Head title="Register" />

    <GuestLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    Create Account
                </h2>
                <Link
                    :href="route('login')"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                >
                    Back to Sign In
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="first_name" value="First Name" />
                        <TextInput
                            id="first_name"
                            type="text"
                            v-model="form.first_name"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.first_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="last_name" value="Last Name" />
                        <TextInput
                            id="last_name"
                            type="text"
                            v-model="form.last_name"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.last_name" class="mt-2" />
                    </div>
                </div>

                <div>
                    <InputLabel for="gender" value="Gender (optional)" />
                    <select
                        id="gender"
                        v-model="form.gender"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Select</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
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
                    <InputError :message="form.errors.email" class="mt-2" />
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
                    <InputError :message="form.errors.password" class="mt-2" />
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
                        <option v-for="role in roles" :key="role" :value="role">
                            {{ formatRole(role) }}
                        </option>
                    </select>
                    <InputError :message="form.errors.role" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="dept_id" value="Department" />
                    <select
                        id="dept_id"
                        v-model="form.dept_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Select Department</option>
                        <option
                            v-for="dept in departments"
                            :key="dept.dept_id"
                            :value="dept.dept_id"
                        >
                            {{ dept.name }}
                        </option>
                    </select>
                    <InputError
                        :message="form.errors.dept_id"
                        class="mt-2"
                    />
                </div>

                <div class="rounded-md bg-blue-50 p-3">
                    <p class="text-sm text-blue-700">
                        Leave balances will be initialized based on current
                        policies.
                    </p>
                </div>

                <div
                    class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end"
                >
                    <Link :href="route('login')" class="order-2 sm:order-1">
                        <SecondaryButton type="button" class="w-full sm:w-auto"
                            >Cancel</SecondaryButton
                        >
                    </Link>
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                        class="order-1 w-full sm:order-2 sm:w-auto"
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
