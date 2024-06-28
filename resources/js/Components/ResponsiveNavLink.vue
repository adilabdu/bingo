<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 md:border-l-0 md:border-b-4 border-brand-primary text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'flex md:justify-center w-full ps-3 pe-4 py-2 border-l-4 md:border-l-0 md:border-b-4 border-transparent text-start text-base font-medium text-brand-primary hover:text-brand-secondary hover:bg-gray-50 hover:border-brand-primary focus:outline-none focus:text-brand-secondary focus:bg-gray-50 focus:border-brand-secondary transition duration-150 ease-in-out md:hover:none'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
