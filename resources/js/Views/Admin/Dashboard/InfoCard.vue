<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: String,
        required: true
    },
    icon: {
        type: Boolean,
        default: false
    },
    iconPath: {
        type: String,
        default: ''
    },
    iconColor: {
        type: String,
        default: 'text-gray-500'
    }
});
</script>

<template>
    <div class="bg-white shadow rounded-lg p-6 flex-1">
        <div v-if="icon" class="mb-2">
            <svg :class="`w-8 h-8 ${iconColor}`" fill="currentColor" viewBox="0 0 24 24">
                <path :d="iconPath"></path>
            </svg>
        </div>
        <h2 class="text-md font-semibold text-gray-900">{{ title }}</h2>
        <p class="text-gray-500">{{ value }}</p>
    </div>
</template>

<style scoped>
/* Add any additional styles here */
</style>
