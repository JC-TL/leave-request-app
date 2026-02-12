<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const page = usePage();
const departmentColor = computed(() => page.props.auth.departmentColor);

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out',
);

const activeStyle = computed(() => 
    props.active ? { 
        borderLeftColor: departmentColor.value,
        color: departmentColor.value,
        backgroundColor: departmentColor.value + '10'
    } : {}
);
</script>

<template>
    <Link :href="href" :class="classes" :style="activeStyle">
        <slot />
    </Link>
</template>
