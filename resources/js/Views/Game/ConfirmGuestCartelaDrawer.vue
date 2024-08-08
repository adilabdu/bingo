<script setup>
import {
    DrawerContent,
    DrawerOverlay,
    DrawerPortal,
    DrawerRoot,
    DrawerTrigger,
} from "vaul-vue";
import {computed, ref} from "vue";
import {Button} from "@/Components/shadcn/ui/button/index.js";
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import {router, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CashierBingoBoard from "@/Views/Game/CashierBingoBoard.vue";

const props = defineProps({
    isTriggerDisabled: {
        type: Boolean,
        default: false,
    },
    triggerButtonText: {
        type: String,
        default: "Confirm & Play Bingo",
    },
    isDrawerOpen: {
        type: Boolean,
        default: false
    },
});

const isDrawerOpen = ref(props.isDrawerOpen);
function setDrawerOpen(val) {
    isDrawerOpen.value = val;
}

const cartela = computed(() => {
    return usePage().props?.cartela || [];
});

const cartelaNumbers = computed(() => {
    return usePage().props?.cartela?.numbers || [];
});

const gameCategory = ref(usePage().props.gameCategory);
function startBingo() {
    router.visit(`/cartela/play/${cartela.value.id}`,{
    });
}
</script>

<template>
    <DrawerRoot
        :open="isDrawerOpen"
        should-scale-background
        @update:open="setDrawerOpen"
    >
        <DrawerTrigger>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-50 flex flex-col rounded-t-xl !h-full max-h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60]"
            >
                <div class="overflow-y-auto flex flex-col space-y-5 w-full px-5 py-6 sm:max-w-md !mx-auto">

                    <div
                        class=" mx-auto w-14 h-1.5 flex-shrink-0 rounded-full bg-gray-300"
                    />

                    <div class="text-center font-semibold text-2xl">
                        Confirm Your Cartela
                    </div>

                        <div v-if="cartela" class="py-2">
                            <div  class="font-bold text-2xl text-center">Cartela # {{cartela?.name}}</div>
                            <CashierBingoBoard :numbers="cartela?.numbers"/>

                        </div>

                    <PrimaryButton @click="startBingo" class="w-full">
                        Use Cartela
                    </PrimaryButton>
                </div>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
