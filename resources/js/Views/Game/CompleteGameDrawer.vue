<script setup>
import {
    DrawerContent,
    DrawerOverlay,
    DrawerPortal,
    DrawerRoot,
    DrawerTrigger,
} from "vaul-vue";
import { ref } from "vue";
import {Button} from "@/Components/shadcn/ui/button/index.js";
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import {HeartCrack} from "lucide-vue-next";
import {router} from "@inertiajs/vue3";

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
})
const isDrawerOpen = ref(props.isDrawerOpen);

function setDrawerOpen(val) {
    isDrawerOpen.value = val;
}

function routeToGame() {
    router.visit('/game/initiate');
}
</script>

<template>
    <DrawerRoot
        :open="isDrawerOpen"
        should-scale-background
        @update:open="setDrawerOpen"
        @open="routeToGame"
        :dismissible="false"
    >
        <DrawerTrigger>
            <Button class="bg-gradient-to-l from-blue-600 to-sky-600 text-white text-xl font-semibold uppercase w-full">
                Bingo
            </Button>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-100 flex flex-col space-y-6 pt-10 rounded-t-xl !min-h-full h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60] px-5 overflow-y-auto"
            >

                <div v-if="isWinner" class="bg-gradient-to-l from-green-500 to-emerald-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">

                    <div class="text-2xl uppercase font-bold">
                        You Won <span class="font-semibold text-4xl">{{game.winner_net_amount}} </span> Br!
                    </div>
                </div>

                <div v-else class="bg-gradient-to-l from-red-500 to-rose-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">
                    <HeartCrack size="25"/>
                    <div class="text-2xl font-medium">
                        You Lost!
                    </div>
                </div>

                <div class="flex justify-between divide-white divide-x text-white bg-gradient-to-br from-blue-600 to-sky-600 p-3 rounded-lg">
                    <div class="flex flex-col items-center w-6/12 space-y-2">
                        <div class="text-xs font-light">Winner</div>
                        <div class="text-xl px-2 font-medium capitalize">{{ winner.name }}</div>
                    </div>
                    <div class="flex flex-col items-center w-6/12 space-y-2">
                        <div class="text-xs font-light">Cartel Number</div>
                        <span class="font-medium text-xl px-2"># {{ cartela.name }}</span>
                    </div>
                </div>
                <div class="flex flex-col space-y-2">
                    <div class="text-center font-bold text-4xl">Winner Board</div>
                    <BingoBoard :winner-numbers="game.winning_numbers" :numbers="cartela.numbers" card-size="w-14" />
                </div>

                <Button class="bg-gradient-to-l from-emerald-500 to-green-500 text-white text-xl font-semibold uppercase w-full">
                    Play Again
                </Button>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
