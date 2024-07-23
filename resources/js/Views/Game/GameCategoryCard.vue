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
    },
    view:{
        type: String,
        default: 'player'
    }
});

const balance = usePage().props.auth.user?.player?.balance;

const isBalanceEnough = computed(() => balance >= props.amount);
</script>

<template>
    <div class="bg-gray-50 border md:border-0 border-gray-600 w-full hover:shadow-2xl hover:bg-brand-secondary hover:text-white cursor-pointer rounded-md flex flex-col space-y-4 mb-2" :class="view === 'cashier' ? 'py-5 lg:py-3 px-10' : isBalanceEnough ? 'bg-white opacity-100 p-4' : 'px-4 py-2'">

    <div  class="flex justify-between items-center" >
        <div class="flex items-center w-full" :class="view==='player' ? 'space-x-5':'space-x-5 lg:space-x-10'">
            <CircleDollarSign />
            <div :class="view === 'player' ? 'flex flex-col': 'flex  w-full items-center space-x-5 lg:justify-evenly'">
                <div class=" " :class="view === 'player' ? 'text-lg font-semibold' : 'font-bold text-xl lg:text-6xl lg:pb-2'">{{ amount }} Birr</div>
                <div class="text-xs">{{ name }}</div>
            </div>
        </div>
        <div v-if="view === 'player'" class="flex items-end justify-center text-xs text-gray-700 font-medium rounded-sm text-center ">
            <SignalLow v-if="players < 3" class="!my-auto h-fit"/>
            <SignalMedium v-else-if="players > 3 && players < 8" class="!my-auto h-fit"/>
            <SignalHigh v-else-if="players > 7" class="!my-auto h-fit"/>

            <span>{{ players }} Players</span>
        </div>
    </div>
        <div v-if="!isBalanceEnough && view ==='player'" class="flex justify-between items-center">
            <div class="text-xs text-red-600">Not Enough Balance</div>
            <PrimaryButton  class="w-5/12" size="xs">Deposit</PrimaryButton>
        </div>

    </div>
</template>

<style scoped>

</style>
