<script setup>

import GameCategoryCard from "@/Views/Game/GameCategoryCard.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    gameCategories:{
        type: Array,
        required: true
    },
})

function redirectToGame(categoryId) {
    router.visit(`/cashier/game/initiate/${categoryId}`)
}

const gameCategories = props.gameCategories.filter(cat => cat.amount <= 500);
</script>

<template>
<div class="flex justify-center">
    <div class="w-5/12  flex flex-col space-y-4">
        <GameCategoryCard view="cashier" @click="redirectToGame(item.id, item.amount)" :players="item?.games?.length" :name="item.name" :amount="item.amount" v-for="(item, index) in gameCategories" :key="index" />
    </div>
    <div class="hidden lg:flex flex-col space-y-2 w-5/12 text-center rounded-lg ">
        <div class="flex w-full justify-center">
            <img class="w-7/12 object-cover" src="../../../../../public/assets/images/logo.png">
        </div>
        <div class="text-7xl font-bold text-brand-100">
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
