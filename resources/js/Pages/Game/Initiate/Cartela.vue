<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Button} from "@/Components/shadcn/ui/button/index.js";

const numberOfCards = ref(1);

// function to increment/decrement the number of cards, the value must be between 1 and 3
const updateNumberOfCards = (action) => {
    if (action === 'increment' && numberOfCards.value < 3) {
        numberOfCards.value++;
    } else if (action === 'decrement' && numberOfCards.value > 1) {
        numberOfCards.value--;
    }
}
</script>

<template>
<AuthenticatedLayout>
    <Header>
        <template #default>
            <div class="font-semibold text-lg pb-1">Customize your Cartela</div>
            <div class="text-xs font-light">Cartela is a set of playing cards. You can play with up to 3 cards!</div>
        </template>
    </Header>

    <div class="flex flex-col space-y-2 py-3">
        <InputLabel value="Please select number of cartela's"/>
        <div class="flex justify-between">
            <div  @click="updateNumberOfCards('decrement')"
                  class="bg-gradient-to-l from-purple-500 to-violet-500  text-white font-semibold font w-12 text-center flex items-center justify-center rounded-md">
                -
            </div>
            <Input type="text" class=" w-7/12" v-model="numberOfCards"/>
            <div
                @click="updateNumberOfCards('increment')"
                class="bg-gradient-to-l from-purple-500 to-violet-500  text-white font-semibold font w-12 text-center flex items-center justify-center rounded-md">
                +
            </div>
        </div>
    </div>


    <div class="flex flex-col space-y-2 py-3">
        <InputLabel value="Enter your lucky cartela number"/>
        <div class="flex">
            <div  @click="updateNumberOfCards('decrement')"
                  class="bg-gradient-to-l from-purple-500 to-violet-500 text-white font-semibold  w-12 text-center flex items-center justify-center rounded-l-md">
                #
            </div>
            <Input type="text" class=" !rounded-none !rounded-r-md w-full" v-model="numberOfCards"/>
        </div>
            <div class="text-xs font-light">You can select from # 1 - 3000</div>
    </div>

        <Button class="bg-gradient-to-l from-purple-500 to-violet-500 text-white font-semibold w-full">
            Play Bingo
        </Button>
</AuthenticatedLayout>
</template>

<style scoped>

</style>
