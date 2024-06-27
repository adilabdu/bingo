<script setup>
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import {computed, onMounted, ref, onUnmounted, watch} from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Button } from "@/Components/shadcn/ui/button/index.js";
import Loading from "@/Components/Loading.vue";
import { useGameDataStore } from "@/Stores/useGameDataStore.ts";

const props = defineProps({
    drawnNumbers: {
        type: Array
    }
})

const pageProps = usePage().props;
const gameStore = useGameDataStore();

let pollRevealNumbers = null;

const cartela = computed(() => pageProps.playerCartela);
const game = computed(() => pageProps.game);
const batchIndex = computed(() => usePage().props.nextBatchIndex);

const currentNumber = ref(null);
const isLoading = ref(false);
const canCallBingo = ref(false);
const selectedNumbers = ref([]);

function enableBingoButton(numbers) {
    canCallBingo.value = true;
    selectedNumbers.value = numbers;
}

function handleFinish() {
    isLoading.value = false;
    gameStore.clearGameData();
}

function callBingo() {
    isLoading.value = true;
    router.post(`/game/play/bingo/${cartela.value.id}/${game.value.id}`, {
        draw_numbers_cut_off_index: gameStore.revealIndex,
        selected_numbers: selectedNumbers.value
    }, {
        preserveState: true,
        onSuccess() {
            gameStore.clearGameData();
        },
    })
}

async function fetchGameUpdates() {
    new Promise((resolve, reject) => {
        router.get('/game/play', {
            game_id: game.value.id,
            batch_index: batchIndex.value
        }, {
            preserveState: true,
            onSuccess() {
                gameStore.addToDrawNumbers(props.drawnNumbers)
                resolve()
            },
            onError(error) {
                reject(error)
            }
        })
    })
}

function revealNumbers() {
    currentNumber.value = Array.from(gameStore.drawNumbers)[gameStore.revealIndex];

    gameStore.addToRecentNumbers(currentNumber.value)
    gameStore.incrementRevealIndex();
}

watch(() => gameStore.revealIndex, () => {
    if (gameStore.revealIndex > 74) {
        clearInterval(pollRevealNumbers);
    } else if (
        gameStore.revealIndex === (Array.from(gameStore.drawNumbers).length - 1)
    ) {
        fetchGameUpdates()
    }
})

onMounted(() => {
    if (game.value.status === 'completed') {
        router.get('/game/initiate');
    }

    gameStore.addToDrawNumbers(props.drawnNumbers);
    pollRevealNumbers = setInterval(revealNumbers, 2000);
});

onUnmounted(() => {
    clearInterval(pollRevealNumbers);
})
</script>

<template>
    <div class="sm:max-w-lg w-full flex flex-col items-center mx-auto space-y-4">
        <div class="flex w-full items-center justify-between space-x-2">
            <div class="w-3/12 flex items-center justify-center">
                <span class="bg-white rounded-full min-w-12 min-h-12 flex items-center justify-center font-bold text-2xl">{{gameStore.revealIndex}}</span>
            </div>
            <Button @click="callBingo" :disabled="!canCallBingo" class="disabled:opacity-25 bg-brand-primary text-white text-xl font-semibold uppercase w-9/12">
                Bingo
            </Button>
        </div>
        <div class="flex justify-between space-x-4 rounded-lg w-full h-full items-center">
            <div class="w-[82px] h-[75px] grid place-items-center text-center py-3 text-white font-bold text-5xl rounded-lg bg-brand-secondary">
                {{ currentNumber }}
            </div>
            <div class="w-9/12 grid grid-cols-4 grid-rows-2 place-items-center min-h-[104px]">
                <div v-for="number in gameStore.recentNumbers" :key="number" class="w-12 py-2 font-semibold text-center rounded-lg border-2 border-black bg-white m-1">
                    {{ number }}
                </div>
            </div>
        </div>

        <BingoBoard
            @finish="handleFinish()"
            @bingo="enableBingoButton"
            :numbers="cartela?.numbers"
            :currentDrawnNumber="currentNumber"
            :drawnNumbers="Array.from(gameStore.drawNumbers)"
            :game-id="game.id"
            :cartela="cartela"
        />

        <div class=" w-full flex justify-between divide-x divide-white bg-brand-primary text-white p-3 rounded-lg">
            <div class="flex flex-col items-center w-6/12 space-y-2">
                <div class="text-xs font-light">Total Payout</div>
                <div class="text-xl font-semibold">{{ game?.winner_net_amount }} Br</div>
            </div>
            <div class="flex flex-col items-center w-6/12 space-y-2">
                <div class="text-xs font-light">Cartel Number</div>
                <div class="text-xl font-semibold">#{{cartela?.name}}</div>
            </div>
        </div>
    <Loading v-if="isLoading" is-full-screen />
    </div>
</template>

<style scoped>

</style>
