<script setup>
import {
    DrawerContent,
    DrawerOverlay,
    DrawerPortal,
    DrawerRoot,
} from "vaul-vue";
import { ref } from "vue";
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import {HeartCrack, X} from "lucide-vue-next";
import {router} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    isDrawerOpen: {
        type: Boolean,
        default: false
    },
    isWinner: {
        type: Boolean,
        default: false
    },
    winner: {
        type: Object,
        required: true
    },
    game:{
        type: Object,
        required: true
    },
    cartela:{
        type: Object,
        required: true
    },
    winnerCartela:{
        type:Object,
        required: true
    }
})
const isDrawerOpen = ref(props.isDrawerOpen);

function setDrawerOpen(val) {
    isDrawerOpen.value = val;
}

function routeToGameMenu() {
    router.visit('/game/initiate');
}

function routeToRepeatGame() {
    router.get('/game/join',{
        'game_category_id': props.game.game_category_id,
        'cartela_id': props.cartela.id,
    });
}

function routeToChangeCartelaPage() {
    router.visit(`/game/initiate/${props.game.game_category_id}`);
}
</script>

<template>
    <DrawerRoot
        :open="isDrawerOpen"
        should-scale-background
        @update:open="setDrawerOpen"
        :dismissible="false"
    >
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-100 flex flex-col space-y-6 rounded-t-xl !min-h-full h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60]"
            >
                <div class="overflow-y-auto flex flex-col space-y-8 w-full px-5 py-4 sm:max-w-lg mx-auto">
                    <div class="flex flex-col space-y-5 items-center">

                        <div @click="routeToChangeCartelaPage" class="w-10 h-10 md:w-12 md:h-12 cursor-pointer hover:scale-105 bg-brand-primary rounded-full flex items-center justify-center">
                            <X size="30" class="text-white"/>
                        </div>

                        <div v-if="isWinner" class="w-full bg-gradient-to-l from-green-500 to-emerald-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">
                            <div class="text-2xl uppercase font-bold">
                                You Won <span class="font-bold text-4xl">{{game.winner_net_amount}} </span> Br!
                            </div>
                        </div>

                        <div v-else class="w-full bg-gradient-to-l from-red-500 to-rose-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">
                            <HeartCrack size="25"/>
                            <div class="text-2xl font-medium">
                                You Lost!
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between divide-white divide-x text-white bg-brand-primary p-3 rounded-lg">
                        <div class="flex flex-col items-center w-6/12 space-y-2 text-center">
                            <div class="text-xs font-light">Winner</div>
                            <div class="text-xl px-2 font-medium capitalize">{{ winner.name }}</div>
                        </div>
                        <div class="flex flex-col items-center w-6/12 space-y-2 text-center">
                            <div class="text-xs font-light">Cartel Number</div>
                            <span class="font-medium text-xl px-2"># {{ winnerCartela.name }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2">
                        <div class="text-center font-bold text-4xl">Winner Board</div>
                        <BingoBoard :winner-numbers="game.winning_numbers" :numbers="winnerCartela.numbers" card-size="w-14" />
                    </div>

                    <div class="flex flex-col space-y-5">
                        <PrimaryButton @click="routeToChangeCartelaPage" class="!bg-brand-tertiary !text-black">Play Again With Another Cartela</PrimaryButton>
                        <PrimaryButton @click="routeToRepeatGame">Play Again</PrimaryButton>
                        <PrimaryButton @click="routeToGameMenu" class="!bg-brand-secondary  text-white text-lg font-medium capitalize w-full">Go To Game Menu</PrimaryButton>
                    </div>
                </div>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
