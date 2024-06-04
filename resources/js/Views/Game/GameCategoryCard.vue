<script setup>

import {CircleDollarSign, SignalLow} from "lucide-vue-next";
defineProps({
    amount:{
        type: Number,
        required: true
    },
    name:{
        type: String,
        required: true
    },
    players:{
        type: Number,
        default: 0
    }
});
</script>

<template>
    <div  class="flex justify-between items-center bg-white p-4 mb-2 rounded-md">
        <div class="flex items-center space-x-5">
            <CircleDollarSign />
            <div>
                <div class="font-semibold text-lg">{{ amount }} Birr</div>
                <div class="text-xs">{{ name }}</div>
            </div>
        </div>
        <div class="flex items-end justify-center text-xs text-gray-700 font-medium rounded-sm text-center ">
            <SignalLow class="!my-auto h-fit"/>
            <span >
                 {{ players }} Players
             </span>
        </div>
    </div>
</template>

<style scoped>

</style>
