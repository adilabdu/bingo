<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import {Button} from "@/Components/shadcn/ui/button/index.js";

const bingoNumbers = [
    {B: [12, 11, 23, 34, 45]},
    {I: [45, 88, 43, 12, 54]},
    {N: [33, 65, 23, 12]},
    {G: [45, 88, 43, 12, 54]},
    {O: [33, 65, 23, 78, 12]}
];
</script>

<template>
<AuthenticatedLayout>
    <div class="flex justify-between space-x-4 rounded-lg w-full h-full items-center">
        <div class="w-3/12 text-center py-3 text-white font-bold text-5xl rounded-lg bg-gradient-to-l from-emerald-500 to-green-500">
            41
        </div>
        <div class="w-9/12 flex flex-wrap ">
            <div v-for="item in 8" class="w-12 py-2 font-semibold text-center rounded-lg  border-2 border-black bg-white  m-1">
                {{item * item * 2}}
            </div>
        </div>
    </div>
    <BingoBoard :numbers="bingoNumbers" />

    <div class="flex justify-between divide-x divide-black bg-white p-3 rounded-lg">
        <div class="flex flex-col items-center w-6/12 space-y-2">
            <div class="text-xs font-light">Total Payout</div>
            <div class="text-xl font-semibold">50 Br</div>
        </div>
        <div class="flex flex-col items-center w-6/12 space-y-2">
            <div class="text-xs font-light">Cartel Number</div>
            <div class="text-xl font-semibold">#123</div>
        </div>
    </div>

    <Button class="bg-gradient-to-l from-emerald-500 to-green-500 text-white text-xl font-semibold uppercase w-full">
         Bingo
    </Button>
</AuthenticatedLayout>
</template>

<style scoped>

</style>
