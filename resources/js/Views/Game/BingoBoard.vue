<script setup>
import {computed, ref, watch} from 'vue';
import {useIsBoardWinner} from "@/Composables/useIsBoardWinner";
import {usePage} from "@inertiajs/vue3";
import CompleteGameDrawer from "@/Views/Game/CompleteGameDrawer.vue";
import { useGameDataStore } from "@/Stores/useGameDataStore.ts";

const props = defineProps({
    numbers: {
        type: Object,
        default: () => ({
            B: [], I: [], N: [], G: [], O: []
        })
    },
    currentDrawnNumber: {
        type: [Number, String],
        default: null
    },
    drawnNumbers: {
        type: Array,
        default: () => []
    },
    cardSize: {
        type: String,
        default: 'w-16'
    },
    gameId: {
        type: Number,
        default: null
    },
    cartela: {
        type: Object,
        default: () => ({})
    },
    winnerNumbers: {
        type: Array,
        default: () => []
    },
});

const emits = defineEmits(['bingo', 'finish']);

const gameStore = useGameDataStore();

const handleClick = (number) => {
    if (props.winnerNumbers.includes(number)) return;
    if (
        props.drawnNumbers.includes(number) &&
        props.drawnNumbers.indexOf(number) <= gameStore.revealIndex &&
        !gameStore.clickedNumbers.has(number)
    ) {
        gameStore.clickedNumbers.add(number);
        gameStore.addToClickedNumbers(number);
    }
};

const columnLabels = ['B', 'I', 'N', 'G', 'O'];

const formattedBingoData = computed(() => {
    return columnLabels.map(label => {
        const values = Array.isArray(props.numbers[label]) ? [...props.numbers[label]] : [];
        if (label === 'N' && values.length === 4 && !values.includes('FREE')) {
            values.splice(2, 0, 'FREE');
        }
        return values;
    });
});

watch(() => Array.from(gameStore.clickedNumbers), () => {
    if (Array.from(gameStore.clickedNumbers).length > 3) {
        if (useIsBoardWinner(Array.from(gameStore.clickedNumbers), formattedBingoData.value)) {
            emits('bingo', Array.from(gameStore.clickedNumbers));
        }
    }
});

const user = ref(usePage().props.auth.user);
const game = ref(null);
const winner = ref(null);
const isWinner = ref(null);
const winnerCartela = ref([]);

Echo.private('game-result')
    .listen(`.game-result.${props.gameId}`, (e) => {
        game.value = e.game;
        winner.value = e.winner;
        winnerCartela.value = e.cartela;
        isWinner.value = e.winner.id === user.value.id;
        emits('finish', isWinner.value);
    });


const getClassForNumber = (num) => {
    if (num === props.currentDrawnNumber && !gameStore.clickedNumbers.has(num)) {
        return 'animate-pulse bg-brand-tertiary text-white';
    } else if (gameStore.clickedNumbers.has(num)) {
        return 'bg-brand-secondary text-white';
    }else if (props.winnerNumbers.includes(num)) {
        return 'bg-brand-secondary text-white cursor-not-allowed';
    } else {
        return 'bg-white';
    }
};

</script>

<template>
    <div class="flex flex-col items-center w-full">
        <CompleteGameDrawer v-if="isWinner !== null" :game="game" :winner="winner" :is-winner="isWinner" :cartela="cartela" :winner-cartela="winnerCartela" :is-drawer-open="true" />
        <div class="flex justify-between py-3 rounded-md max-w-sm w-full">
            <div v-for="(column, index) in formattedBingoData" :key="index" class="text-center">
                <h3 class="bg-brand-primary font-semibold text-white rounded py-2 mb-4 min-w-10">{{ columnLabels[index] }}</h3>
                <ul>
                    <li v-for="(num, ind) in column" :key="ind" :class="cardSize">
                        <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-brand-secondary font-semibold text-white rounded my-2.5 py-3 px-2">FREE</div>
                        <div v-else :class="getClassForNumber(num)" class="rounded font-medium text-lg my-2 py-3 text-center px-2 cursor-pointer" @click="handleClick(num)">
                            {{ num }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
