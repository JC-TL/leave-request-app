<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const departmentColor = computed(() => page.props.auth.departmentColor);

// Helper function to darken color for hover/active states
const darkenColor = (hex, percent) => {
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) - amt;
    const G = (num >> 8 & 0x00FF) - amt;
    const B = (num & 0x0000FF) - amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255))
        .toString(16).slice(1);
};

const buttonStyle = computed(() => ({
    backgroundColor: departmentColor.value,
    '--hover-bg': darkenColor(departmentColor.value, 10),
    '--active-bg': darkenColor(departmentColor.value, 20),
}));
</script>

<template>
    <button
        :style="buttonStyle"
        class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 hover:opacity-90 active:opacity-80"
        style="--tw-ring-color: var(--hover-bg)"
    >
        <slot />
    </button>
</template>
