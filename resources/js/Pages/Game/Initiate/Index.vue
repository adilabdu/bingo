<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import {Gift, CircleDollarSign} from "lucide-vue-next";
</script>

<template>
<AuthenticatedLayout>
    <div class="flex space-x-2 mb-2 min-w-full bg-gradient-to-l from-purple-500 to-violet-500  p-3 rounded-md text-white">
        <Gift class="text-white" />
        <div class="font-medium text-sm">Unlock Your Wins: Bingo Awaits! </div>
    </div>
    <Header>
        <template #default>
            <div class="font-semibold text-xl pb-1">Choose Bet Category</div>
            <div class="text-xs font-light">Click on the category you want to play</div>
        </template>
    </Header>

    <div class="flex flex-col space-y-3 text-sm font-poppins">
        <div v-for="(item, index) in [10,15,20,30,50,100,200]" class="flex space-x-4 justify-between items-center bg-white p-4 rounded-md">
            <div class="flex space-x-4 items-center">
            <CircleDollarSign/>
            <div class="font-medium">{{ item }} Birr</div>
            </div>
            <div class="text-xs bg-green-600 rounded-sm p-1 w-3/12 text-center text-white">{{index + 2}} Players</div>
        </div>
    </div>
</AuthenticatedLayout>
</template>

<style scoped>

</style>
