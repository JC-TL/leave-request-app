<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    urgent: false,
});

const submit = () => {
    form.post(route('contact-admin.store'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Contact Admin" />

        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Contact Admin</h2>
            <Link :href="route('login')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </Link>
        </div>

        <p class="mb-6 text-sm text-gray-600">
            Having trouble signing in? Please submit a ticket below and our admin team will assist you.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Your Name" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="subject" value="Subject" />
                <select
                    id="subject"
                    v-model="form.subject"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="">Select an issue</option>
                    <option value="login_issue">Cannot Sign In</option>
                    <option value="password_reset">Password Reset Request</option>
                    <option value="account_access">Account Access Issue</option>
                    <option value="other">Other</option>
                </select>
                <InputError class="mt-2" :message="form.errors.subject" />
            </div>

            <div>
                <InputLabel for="message" value="Message" />
                <textarea
                    id="message"
                    v-model="form.message"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Please describe your issue in detail..."
                    required
                ></textarea>
                <InputError class="mt-2" :message="form.errors.message" />
            </div>

            <div>
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.urgent"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-600">Mark as urgent</span>
                </label>
            </div>

            <div class="flex gap-3">
                <PrimaryButton class="flex-1 justify-center" :disabled="form.processing">
                    Submit Ticket
                </PrimaryButton>
                <Link :href="route('login')" class="flex-1">
                    <SecondaryButton type="button" class="w-full justify-center">
                        Back to Login
                    </SecondaryButton>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
