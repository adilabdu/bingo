<script setup>
defineProps({
    disabled: {
        type: Boolean,
        default: false
    }
})
</script>
<template>
    <button
        :disabled="disabled"
        :class="disabled ? 'opacity-60 cursor-not-allowed ' : 'opacity-100'"
        class="inline-flex items-center justify-center px-3 md:px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-secondary focus:bg-brand-primary active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-tertiary focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
