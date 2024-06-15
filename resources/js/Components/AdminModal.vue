<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:visible']);

const closeModal = () => {
    emit('update:visible', false);
};
</script>

<template>
    <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-700">{{ title }}</h3>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <slot></slot>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>


<style scoped>
/* Add any additional styles here */
</style>
