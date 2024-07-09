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
    router.visit(`/cashier/game/create/${categoryId}`)
}

const gameCategoriesOne = props.gameCategories.filter(cat => cat.amount <= 50);
const gameCategoriesTwo = props.gameCategories.filter(cat => cat.amount > 100);
</script>

<template>
<div class="flex flex-col  h-screen">
    <div class="flex items-center py-5 justify-center h-fit">
        <img class="w-24 h-20 object-cover " src="../../../../../public/assets/images/logo.png">
        <div class="text-center text-brand-primary font-bold text-7xl">Kiwi <span
            class="text-brand-secondary">Bingo</span>
        </div>
    </div>
    <div class="flex justify-evenly h-5/6">

    <div class="w-5/12 h-4/5 flex flex-col justify-evenly">
        <GameCategoryCard view="cashier" @click="redirectToGame(item.id, item.amount)" :players="item?.games?.length" :name="item.name" :amount="item.amount" v-for="(item, index) in gameCategoriesOne" :key="index" />
    </div>
    <div class="w-5/12 h-4/5 flex flex-col justify-evenly">
        <GameCategoryCard view="cashier" @click="redirectToGame(item.id, item.amount)" :players="item?.games?.length" :name="item.name" :amount="item.amount" v-for="(item, index) in gameCategoriesTwo" :key="index" />
    </div>
</div>

</div>
</template>

<style scoped>

</style>
