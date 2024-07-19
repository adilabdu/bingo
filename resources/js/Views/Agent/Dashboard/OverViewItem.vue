<script setup>
defineProps({
    baseClass: {
        type: String,
        default:'bg-gray-100 text-black'
    },
    label: {
        type: String,
        required: true
    },
    value: {
        type: String,
        required: true
    }
})
</script>

<template>
    <div
        :class="baseClass"
        class="flex flex-col space-y-1 my-2 w-5/12 p-2 rounded-md items-center max-w-sm">
        <span class="text-[0.70rem] font-light">{{ label }}</span>
        <span class="font-semibold text-lg">{{ value }}</span>
    </div>
</template>

<style scoped>

</style>
