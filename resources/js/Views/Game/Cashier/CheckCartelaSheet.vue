<script setup>
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTrigger,
} from '@/Components/shadcn/ui/sheet/index.js'
import {Input} from "@/Components/shadcn/ui/input/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import {computed, ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import CashierBingoBoard from "@/Views/Game/CashierBingoBoard.vue";
import {useGameDataStore} from "@/Stores/useGameDataStore.ts";

const emit = defineEmits(['togglePause'])
const props = defineProps({
    currentIndex: {
        type: Number,
        required: false
    },
    game: {
        type: Object,
        required: true
    },
})
const cartelaName = ref(null);
const errorMessages = ref([]);
const isLoading = ref(false);
const cartela = computed(() => usePage().props.cartela)
const gameStore = computed(()=>useGameDataStore());

window.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        getCartela();
    }
})

const drawNumbers = ref([]);
function getCartela(){
    errorMessages.value = [];
    isLoading.value = true;
    router.visit(cartelaName.value ? `/cashier/game/start/${cartelaName.value}/${props.game.id}/${props.game.game_category_id}` : '/cashier/game/start', {
    preserveState: true,
        only: ['cartela'],
        onError: (e) => {
            errorMessages.value = Object.values(e).flat();
        },
        onFinish: () => {
            isLoading.value = false;
        }
    })

    drawNumbers.value = [];
    drawNumbers.value.push(Array.from(gameStore.value.drawNumbers).slice(0, gameStore.value.revealIndex + 1))

}


</script>

<template>
    <Sheet
     @close="cartelaName = null">
        <SheetTrigger>
            <div
                @click="emit('togglePause')"
                class="text-5xl font-bold uppercase bg-brand-secondary text-white px-3 py-2 text-center rounded-lg cursor-pointer hover:scale-105 hover:shadow-xl">
                Check
            </div>
        </SheetTrigger>
        <SheetContent class="!overflow-y-auto">
            <SheetHeader >
                <SheetDescription>
                    <div class="flex flex-col items-center w-10/12 mx-auto text-black space-y-6">
                    <div class="text-7xl text-black font-bold py-5">Check Cartela</div>
                        <div class="flex w-full flex-col space-y-4">
                            <InputLabel class="!text-2xl">Add Cartela Number (1-50)</InputLabel>
                            <Input v-model="cartelaName" type="number" class="h-24 font-semibold text-5xl border-black"
                                   placeholder="Ex: 20, 16, 50, 44..."/>
                            <span class="text-red-600" v-for="(message, index) in errorMessages"
                                  :key="index">* {{ message }}</span>

                        </div>

                        <Loading v-if="isLoading" type="brand" class="pt-10"/>
                        <CashierBingoBoard v-else-if="cartela?.numbers && cartelaName" :cartela="cartela" :numbers="cartela?.numbers" :winner-numbers="drawNumbers[0]"/>
                        <div v-if="cartela === null && cartelaName && !isLoading" class="text-2xl font-bold bg-red-600 text-white px-5 py-2 rounded-lg">
                            Cartela Not found!
                        </div>
                        <div
                            v-if="cartela?.numbers && cartelaName"
                            class="flex w-full flex-col py-3 lg:h-36 space-y-2 items-center justify-center bg-gray-800 text-white px-4 rounded-lg shadow-2xl font-bold text-4xl lg:text-7xl">
                            <span class="font-normal text-sm lg:text-2xl"> Winner Amount</span>
                            <span>{{ Number(game.winner_net_amount).toFixed() }} Birr</span>
                        </div>
                    </div>
                </SheetDescription>
            </SheetHeader>
        </SheetContent>
    </Sheet>
</template>
