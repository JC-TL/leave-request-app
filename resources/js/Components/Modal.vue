<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

watch(() => props.show, (visible) => {
    document.body.style.overflow = visible ? 'hidden' : '';
}, { immediate: true });

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();
        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <Teleport to="body">
        <div
            v-show="show"
            class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0"
        >
            <!-- Backdrop - click to close -->
            <div
                class="fixed inset-0 bg-gray-500/75"
                aria-hidden="true"
                @click="close"
            />
            <!-- Content - stop propagation so backdrop doesn't receive click -->
            <div
                v-show="show"
                class="relative z-10 w-full transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full"
                :class="maxWidthClass"
                role="dialog"
                aria-modal="true"
                @click.stop
            >
                <slot />
            </div>
        </div>
    </Teleport>
</template>
