<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-900 focus:border-gray-800 !focus:ring-gray-800 rounded-lg shadow-sm p-3"
        v-model="model"
        ref="input"
    />
</template>
