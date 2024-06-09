<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    items: {
        type: Array,
        required: true
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
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ title }}</h2>
            <svg :class="`w-6 h-6 ${iconColor}`" fill="currentColor" viewBox="0 0 24 24">
                <path :d="iconPath"></path>
            </svg>
        </div>
        <ul class="space-y-4">
            <li v-for="item in items" :key="item.id || item.username || item.message" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg shadow-sm">
                <div>
                    <p class="text-xl font-semibold text-gray-700">{{ item.title || item.name || item.username }}</p>
                    <p class="text-gray-500">{{ item.subtitle || item.startTime || item.gameName || item.message }}</p>
                </div>
                <p class="text-gray-500">{{ item.extraInfo || item.players || item.winningPattern || item.time }}</p>
            </li>
        </ul>
    </div>
</template>

<style scoped>
/* Add any additional styles here */
</style>
