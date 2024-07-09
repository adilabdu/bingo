<script setup>
import {defineProps} from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: String,
        required: false,
    },
    amount: {
        type: String,
        required: false,
        default: "",
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
    },
    currency: {
        type: String,
        required: false,
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
        <p class="flex items-center space-x-2">
            <span class="text-lg" >{{ value }}</span>
            <span v-if="amount !== ''" class="text-lg">{{ Number(amount).toFixed(2) }}</span>
            <span class="text-sm">{{ currency }}</span>
        </p>
    </div>
</template>

<style scoped>
/* Add any additional styles here */
</style>
