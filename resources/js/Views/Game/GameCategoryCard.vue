<script setup>

import {CircleDollarSign, SignalLow, SignalMedium, SignalHigh} from "lucide-vue-next";
import {usePage} from "@inertiajs/vue3";
import {Button} from "@/Components/shadcn/ui/button/index.js";
import {computed} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
const props = defineProps({
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

const balance = usePage().props.auth.user?.player?.balance;

const isBalanceEnough = computed(() => balance >= props.amount);
</script>

<template>
    <div class="bg-white rounded-md flex flex-col space-y-4 mb-2" :class="isBalanceEnough ? 'bg-white opacity-100 p-4' : 'px-4 py-2'">

    <div  class="flex justify-between items-center" >
        <div class="flex items-center space-x-5">
            <CircleDollarSign />
            <div>
                <div class="font-semibold text-lg">{{ amount }} Birr</div>
                <div class="text-xs">{{ name }}</div>
            </div>
        </div>
        <div class="flex items-end justify-center text-xs text-gray-700 font-medium rounded-sm text-center ">
            <SignalLow v-if="players < 3" class="!my-auto h-fit"/>
            <SignalMedium v-else-if="players > 3 && players < 8" class="!my-auto h-fit"/>
            <SignalHigh v-else-if="players > 7" class="!my-auto h-fit"/>

            <span>{{ players }} Players</span>
        </div>
    </div>
        <div v-if="!isBalanceEnough" class="flex justify-between items-center">
            <div class="text-xs text-red-600">Not Enough Balance</div>
        <PrimaryButton  class="w-5/12" size="xs">Deposit</PrimaryButton>
        </div>

    </div>
</template>

<style scoped>

</style>
