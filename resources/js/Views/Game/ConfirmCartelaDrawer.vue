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

defineProps({
    project: {
        type: Object,
        required: true,
    },
});
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
            <Button class="bg-gradient-to-l from-purple-500 to-violet-500 text-white font-semibold w-full">
               Confirm & Play Bingo
            </Button>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-100 flex flex-col space-y-8 rounded-t-xl !h-full max-h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60] px-5"
            >
                <div
                    class="mt-4 mx-auto w-14 h-1.5 flex-shrink-0 rounded-full bg-gray-300"
                />

                    <div class="text-center font-semibold text-2xl">
                        Confirm Your Cartela
                    </div>

                <div>
                    <div class="pb-4">
                        Cartela No: <span class="font-bold bg-gray-800 px-2 italic text-white">#123</span>
                    </div>
                <BingoBoard :numbers="bingoNumbers" />
                </div>

                <Button class="bg-gradient-to-l from-purple-500 to-violet-500 text-white font-semibold w-full">
                    Start Bingo
                </Button>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
