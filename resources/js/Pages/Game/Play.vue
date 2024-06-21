<script setup>
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import { computed, onMounted, ref, onUnmounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Button } from "@/Components/shadcn/ui/button/index.js";
import Loading from "@/Components/Loading.vue";
import { useGameDataStore } from "@/Stores/useGameDataStore.ts";

const pageProps = usePage().props;
const gameStore = useGameDataStore();

const cartela = computed(() => pageProps.playerCartela);
const game = computed(() => pageProps.game);
const currentNumber = ref(null);
const revealingNumbers = ref(false);

const isLoading = ref(false);

let pollInterval = null;
const batchIndex = computed(() => usePage().props.nextBatchIndex);

const fetchGameUpdates = () => {

    console.log('(fetchGameUpdates) fetching game updates... ')

    if (batchIndex.value > 8)
        return;
    router.get(`/game/play/`, {
        'game_id': game.value.id,
        'batch_index':  batchIndex.value,
    }, {
        preserveState: true,
        onSuccess: (page) => {
            // Add the new drawn numbers to the existing list
            // drawNumbers.value = [...drawNumbers.value, ...page.props.drawnNumbers];
            gameStore.addToDrawNumbers(page.props.drawnNumbers);
            if (!revealingNumbers.value || (gameStore.revealIndex === 0 && Array.from(gameStore.drawNumbers).length > 0)) {
                revealNumbers();
            }

            if (Array.from(gameStore.drawNumbers).length >= 75) {
                clearInterval(pollInterval);
            }
        }
    });
};

const revealNumbers = () => {
    revealingNumbers.value = true;
    if (gameStore.revealIndex < Array.from(gameStore.drawNumbers).length) {
        currentNumber.value = Array.from(gameStore.drawNumbers)[gameStore.revealIndex];
        gameStore.setRecentNumbers(Array.from(gameStore.drawNumbers).slice(Math.max(0, gameStore.revealIndex - 7), gameStore.revealIndex + 1).reverse());
        gameStore.incrementRevealIndex();
        if (gameStore.revealIndex < Array.from(gameStore.drawNumbers).length) {
            setTimeout(revealNumbers, 2000);
        }
    }
};

const canCallBingo = ref(false);
const selectedNumbers = ref([]);

const enableBingoButton = (numbers) => {
    canCallBingo.value = true;
    selectedNumbers.value = numbers;
}

function handleFinish() {
    isLoading.value = false;
    gameStore.clearGameData();
}

const callBingo = () => {
    isLoading.value = true;
    router.post(`/game/play/bingo/${cartela.value.id}/${game.value.id}`, {
        draw_numbers_cut_off_index: gameStore.revealIndex,
        selected_numbers: selectedNumbers.value
    }, {
        preserveState: true,
        onProgress: () => {},
        onSuccess: () => {
            gameStore.clearGameData();
        },
    })
}

onMounted(() => {
    if (game.value.status === 'completed') {
        router.get('/game/initiate');
    }
    pollInterval = setInterval(fetchGameUpdates, 15000);
    if (!revealingNumbers.value || (Array.from(gameStore.drawNumbers).length > 0 && gameStore.revealIndex === 0)) {
        setTimeout(revealNumbers, 2000);
    }
});

onUnmounted(() => {
    clearInterval(pollInterval);
});
</script>

<template>
        <div class="flex w-full items-center space-x-2">
            <span class="bg-white rounded-full min-w-12 min-h-12 flex items-center justify-center font-bold text-2xl">{{gameStore.revealIndex}}</span>
            <Button @click="callBingo" :disabled="!canCallBingo" class="disabled:opacity-25 bg-gradient-to-l h-12 from-blue-600 to-sky-600 text-white text-xl font-semibold uppercase w-full">
                Bingo
            </Button>
        </div>
        <div class="flex justify-between space-x-4 rounded-lg w-full h-full items-center">
            <div class="w-[82px] h-[75px] grid place-items-center text-center py-3 text-white font-bold text-5xl rounded-lg bg-gradient-to-l from-blue-600 to-sky-600">
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
        />

        <div class="flex justify-between divide-x divide-black bg-white p-3 rounded-lg">
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
</template>

<style scoped>

</style>
