<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BingoBoard from "@/Views/Game/BingoBoard.vue";
import CompleteGameDrawer from "@/Views/Game/CompleteGameDrawer.vue";
import { computed, onMounted, ref, onUnmounted } from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {Button} from "@/Components/shadcn/ui/button/index.js";

const pageProps = usePage().props;
const cartela = computed(() => pageProps.playerCartela);
const game = computed(() => pageProps.game);
const drawNumbers = ref(pageProps.drawnNumbers ?? []);
const currentNumber = ref(null);
const recentNumbers = ref([]);

let index = 0;
let pollInterval = null;
const batchIndex = computed(() => usePage().props.nextBatchIndex);
const fetchGameUpdates = () => {
    if (batchIndex.value > 8)
        return;
    router.get(`/game/play/`, {
        'game_id': game.value.id,
        'batch_index':  batchIndex.value,
    }, {
        preserveState: true,
        onSuccess: (page) => {
            // Add the new drawn numbers to the existing list
            drawNumbers.value = [...drawNumbers.value, ...page.props.drawnNumbers];
            if (index === 0 && drawNumbers.value.length > 0) {
                revealNumbers();
            }

            if (drawNumbers.value.length >= 75) {
                clearInterval(pollInterval);
            }
        }
    });
};

const revealNumbers = () => {
    if (index < drawNumbers.value.length) {
        currentNumber.value = drawNumbers.value[index];
        recentNumbers.value = drawNumbers.value.slice(Math.max(0, index - 7), index + 1).reverse();
        index++;
        if (index < drawNumbers.value.length) {
            setTimeout(revealNumbers, 2000); // Continue revealing if there are numbers left
        }
    }
};

const canCallBingo = ref(false);
const selectedNumbers = ref([]);
const enableBingoButton = (numbers) => {
    canCallBingo.value = true;
    selectedNumbers.value = numbers;
}

const callBingo = () => {
    router.post(`/game/play/bingo/${cartela.value.id}/${game.value.id}`, {
        draw_numbers_cut_off_index: index,
        selected_numbers: selectedNumbers.value
    }, {
        preserveState: true,
        onProgress: () => {},
        onSuccess: () => {},
    })
}

onMounted(() => {
    pollInterval = setInterval(fetchGameUpdates, 15000);
    if (drawNumbers.value.length > 0 && index === 0) {
        setTimeout(revealNumbers, 1000);
    }
});

onUnmounted(() => {
    clearInterval(pollInterval);
});
</script>



<template>
    <AuthenticatedLayout>

        <div class="flex w-full items-center space-x-2">
            <span class="bg-white rounded-full w-12 h-12 flex items-center justify-center font-bold text-2xl">{{index}}</span>
            <Button @click="callBingo" :disabled="!canCallBingo" class="disabled:opacity-25 bg-gradient-to-l from-blue-600 to-sky-600 text-white text-xl font-semibold uppercase w-full">
                Bingo
            </Button>
        </div>
        <div class="flex justify-between space-x-4 rounded-lg w-full h-full items-center">
            <div class="w-3/12 text-center py-3 text-white font-bold text-5xl rounded-lg bg-gradient-to-l from-blue-600 to-sky-600">
                {{ currentNumber }}
            </div>
            <div class="w-9/12 flex flex-wrap">
                <div v-for="number in recentNumbers" :key="number" class="w-12 py-2 font-semibold text-center rounded-lg border-2 border-black bg-white m-1">
                    {{ number }}
                </div>
            </div>
        </div>

        <BingoBoard @bingo="enableBingoButton" :numbers="cartela?.numbers" :currentDrawnNumber="currentNumber" :drawnNumbers="drawNumbers" />


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
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
