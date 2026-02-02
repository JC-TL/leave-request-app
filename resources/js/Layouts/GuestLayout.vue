<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100/80 pt-6 sm:justify-center sm:pt-0">
        <div>
            <Link href="/">
                <img src="/images/logo.png" alt="Logo" class="h-20 w-auto">
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
