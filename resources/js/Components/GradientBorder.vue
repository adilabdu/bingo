<template>
    <div :class="gradientStyle" class="rounded-xl w-full h-full p-1.5">
        <div
            class=" rounded-lg h-full w-full flex flex-col items-center justify-evenly"
        >
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    gradientStyle: {
        type: String,
        default: "bg-gradient-to-r from-gray-700 to-slate-600",
    },
});
</script>

<style scoped></style>
