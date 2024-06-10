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
import {HeartCrack, PartyPopper} from "lucide-vue-next";

const isDrawerOpen = ref(false);

function setDrawerOpen(val) {
    isDrawerOpen.value = val;
}
const bingoNumbers = [
    {B: [12, 11, 23, 34, 45]},
    {I: [45, 88, 43, 12, 54]},
    {N: [33, 65, 23, 12]},
    {G: [45, 88, 43, 12, 54]},
    {O: [33, 65, 23, 78, 12]}
];
</script>

<template>
    <DrawerRoot
        :open="isDrawerOpen"
        should-scale-background
        @update:open="setDrawerOpen"
    >
        <DrawerTrigger>
            <Button class="bg-gradient-to-l from-blue-600 to-sky-600 text-white text-xl font-semibold uppercase w-full">
                Bingo
            </Button>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-100 flex flex-col space-y-6 rounded-t-xl !min-h-full max-h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60] px-5 overflow-y-auto"
            >
                <div
                    class="mt-4 mx-auto w-14 h-1.5 flex-shrink-0 rounded-full bg-gray-300"
                />

                <div class="bg-gradient-to-l from-red-500 to-rose-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">
                    <HeartCrack size="50"/>
                    <div class="text-4xl  font-bold">
                        You Lost!
                    </div>
                </div>

                <div class="bg-gradient-to-l from-green-500 to-emerald-500 p-3 rounded-xl shadow-md text-white flex items-center justify-center space-x-2 text-center font-semibold text-2xl">
                    <PartyPopper size="50"/>
                    <div class="text-4xl  font-bold">
                        You Won!
                    </div>
                </div>


                <div class="flex justify-between divide-x divide-black bg-white p-3 rounded-lg">
                    <div class="flex flex-col items-center w-6/12 space-y-2">
                        <div class="text-xs font-light">Winner</div>
                        <div class="font-semibold bg-gray-800 text-xl text-white px-2">Teke</div>
                    </div>
                    <div class="flex flex-col items-center w-6/12 space-y-2">
                        <div class="text-xs font-light">Cartel Number</div>
                        <span class="font-semibold bg-gray-800 px-2  text-white"># 123</span>
                    </div>
                </div>
                <div class="flex flex-col space-y-2">
                    <div class="text-center font-bold text-4xl">Winner Board</div>

                    <BingoBoard :numbers="bingoNumbers" card-size="w-14" />
                </div>

                <Button class="bg-gradient-to-l from-emerald-500 to-green-500 text-white text-xl font-semibold uppercase w-full">
                    Play Again
                </Button>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
