<script setup>
import {formatToPrice} from "@/lib/utils.js";

defineProps({
    balance: {
        type: Number,
        required: true
    }
})
</script>

<template>
    <div class="flex flex-col space-y-2 bg-gray-800 text-white rounded-lg p-3 capitalize max-w-md">
        <div class="font-bold text-3xl"> {{ formatToPrice(balance) }} Br</div>
        <div class="font-light text-sm">Your Balance</div>
    </div>
</template>

<style scoped>

</style>
