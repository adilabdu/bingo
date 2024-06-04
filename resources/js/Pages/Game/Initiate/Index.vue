<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import { Gift, CircleDollarSign } from "lucide-vue-next";
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/shadcn/ui/tabs/index.js';
import { computed } from "vue";
import {router, usePage} from "@inertiajs/vue3";
import GameCategoryCard from "@/Views/Game/GameCategoryCard.vue";

const props = usePage().props;
const basicCategories = computed(() => props.gameCategories.filter(cat => cat.category === 'basic'));
const vipCategories = computed(() => props.gameCategories.filter(cat => cat.category === 'vip'));

function redirectToGame(categoryId) {
    router.visit(`/game/initiate/${categoryId}`)
}
</script>


<template>
    <AuthenticatedLayout>
        <div class="flex space-x-2 mb-2 min-w-full bg-gradient-to-r from-blue-600 via-blue-500 to-sky-500  p-3 rounded-md text-white">
            <Gift class="text-white" />
            <div class="font-medium text-sm">Unlock Your Wins: Bingo Awaits!</div>
        </div>
        <Header>
            <template #default>
                <div class="font-semibold text-xl pb-1">Choose Bet Category</div>
                <div class="text-xs font-light">Click on the category you want to play</div>
            </template>
        </Header>

        <Tabs default-value="basic" class="!w-full mx-auto">
            <TabsList class="bg-white w-full">
                <TabsTrigger value="basic" class="w-full">Basic</TabsTrigger>
                <TabsTrigger value="vip" class="w-full">VIP</TabsTrigger>
            </TabsList>
            <TabsContent value="basic">
                <GameCategoryCard @click="redirectToGame(item.id)" :name="item.name" :amount="item.amount" v-for="(item, index) in basicCategories" :key="index" />
            </TabsContent>
            <TabsContent value="vip">
                <GameCategoryCard @click="redirectToGame(item.id)"  :name="item.name" :amount="item.amount" v-for="(item, index) in vipCategories" :key="index" />
            </TabsContent>
        </Tabs>
    </AuthenticatedLayout>
</template>


<style scoped>

</style>
