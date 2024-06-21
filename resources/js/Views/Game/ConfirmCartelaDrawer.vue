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

const isDrawerOpen = ref(false);
const emit = defineEmits(["start-bingo"]);
defineProps({
    isTriggerDisabled: {
        type: Boolean,
        default: false,
    },
    triggerButtonText: {
        type: String,
        default: "Confirm & Play Bingo",
    },
});

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
    router.get('/game/join', {
        "game_category_id": gameCategory.value.id,
        "cartela_id": cartela.value.id
    },{
        preserveState: true,
        replace: true,
        onFinish: () => {
            emit('start-bingo')
            setDrawerOpen(false);
        }
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
            <!--   Todo: Check disabled reactivity         -->
            <Button :disabled="isTriggerDisabled" class="bg-blue-600 text-white font-semibold w-full">
                {{ triggerButtonText }}
            </Button>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0" />
            <DrawerContent
                class="bg-gray-100 flex flex-col rounded-t-xl !h-full max-h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60]"
            >
                <div class="overflow-y-auto flex flex-col space-y-5 w-full px-5 py-6">

                    <div
                        class=" mx-auto w-14 h-1.5 flex-shrink-0 rounded-full bg-gray-300"
                    />

                    <div class="text-center font-semibold text-2xl">
                        Confirm Your Cartela
                    </div>

                    <div>
                        <div class="py-3 rounded-lg flex justify-between items-center divide-white divide-x text-white bg-gradient-to-br from-blue-600 to-sky-600">
                        <span class="w-6/12 text-center flex flex-col items-center space-y-2">
                            <span class="font-bold text-3xl">#{{ cartela?.name }}</span>
                            <span class="text-sm">
                                Cartela No
                            </span>
                        </span>
                            <span class="w-6/12 text-center flex flex-col items-center">
                            <span class="font-bold text-3xl">
                            {{gameCategory.amount}} <span class="font-light text-xl">Br</span>
                            </span>
                            <span class="font-light text-sm">Category</span>
                        </span>
                        </div>
                        <BingoBoard v-if="cartelaNumbers" :numbers="cartelaNumbers" card-size="w-14" />
                    </div>

                    <Button @click="startBingo" class="bg-blue-600 text-white font-semibold w-full">
                        Start Bingo
                    </Button>
                </div>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
