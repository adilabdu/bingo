<script setup>
import Header from "@/Components/Header.vue";
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/shadcn/ui/tabs/index.js';
import {computed} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import GameCategoryCard from "@/Views/Game/GameCategoryCard.vue";

const props = usePage().props;
const basicCategories = computed(() => props.gameCategories.filter(cat => cat.category === 'basic'));
const vipCategories = computed(() => props.gameCategories.filter(cat => cat.category === 'vip'));

const balance = usePage().props.auth.user?.player?.balance;
function redirectToGame(categoryId, amount) {
    if (balance < amount) {
        // Todo: Show a dialog to show the user that they don't have enough balance
        return;
    }
    router.visit(`/game/initiate/${categoryId}`)
}
</script>


<template>
    <div class="flex justify-evenly w-full py-8">

        <div class="flex flex-col space-y-3 w-full md:w-5/12 px-3">
            <Header>
                <template #default>
                    <div class="font-semibold text-xl md:text-4xl pb-1">Choose Bet Category</div>
                    <div class="text-xs md:text-sm font-light">Click on the category you want to play</div>
                </template>
            </Header>
        <Tabs default-value="basic" class="!w-full max-w-lg">
            <TabsList class="bg-white w-full">
                <TabsTrigger value="basic" class="w-full">Basic</TabsTrigger>
                <TabsTrigger value="vip" class="w-full">VIP</TabsTrigger>
            </TabsList>
            <TabsContent value="basic">
                <GameCategoryCard @click="redirectToGame(item.id, item.amount)" :players="item?.games?.length" :name="item.name" :amount="item.amount" v-for="(item, index) in basicCategories" :key="index" />
            </TabsContent>
            <TabsContent value="vip">
                <GameCategoryCard @click="redirectToGame(item.id, item.amount)" :players="item?.games?.length"  :name="item.name" :amount="item.amount" v-for="(item, index) in vipCategories" :key="index" />
            </TabsContent>
        </Tabs>
        </div>
        <div class="hidden lg:flex flex-col space-y-2 w-4/12 text-center rounded-lg justify-center items-center">
            <div class="flex w-full justify-center">
                <img class="w-7/12 object-cover" src="../../../../../public/assets/images/logo.png">
            </div>
            <div class="text-4xl font-bold text-brand-100">
                <span class="text-brand-150">Kiwi</span>
                Bingo
            </div>
            <div class="text-sm">
                Embrace the fun of playing bingo online!
            </div>
        </div>
    </div>
    </template>


<style scoped>

</style>
