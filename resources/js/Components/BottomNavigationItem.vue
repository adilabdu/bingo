<script setup>
import {computed, defineProps} from 'vue';
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    icon: {
        type: Object,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    to: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    }
});
const classes = computed(() =>
    props.active
        ? 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-brand-secondary focus:outline-none transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 text-sm leading-5 text-black hover:text-brand-primary hover:border-brand-primary focus:outline-none focus:text-brand-primary focus:border-brand-primary transition duration-150 ease-in-out'
);
</script>
<template>
    <Link :href="to" class="flex flex-col space-y-1 items-center justify-center" :class="classes">
        <component :is="icon" class="w-5"/>
        <div class="text-xs">{{ label }}</div>
    </Link>
</template>

<style scoped>

</style>
