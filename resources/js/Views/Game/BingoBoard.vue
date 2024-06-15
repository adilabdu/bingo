<script setup>
import {computed, ref, watch} from 'vue';
import {useIsBoardWinner} from "@/Composables/useIsBoardWinner";
import {usePage} from "@inertiajs/vue3";
import CompleteGameDrawer from "@/Views/Game/CompleteGameDrawer.vue";

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
    winnerNumbers: {
        type: Array,
        default: () => []
    }
});

const emits = defineEmits(['bingo', 'finish'])

const clickedNumbers = ref(new Set(['FREE']));

const handleClick = (number) => {
    // Prevent interaction if the number is part of the winnerNumbers
    if (props.winnerNumbers.includes(number)) return;

    if (props.drawnNumbers.includes(number) && !clickedNumbers.value.has(number)) {
        clickedNumbers.value.add(number);
    }
};

const getClassForNumber = (num) => {
    if (props.winnerNumbers.includes(num)) {
        return 'bg-blue-600 text-white cursor-not-allowed'; // Added cursor-not-allowed for visual feedback
    } else if (num === props.currentDrawnNumber) {
        return 'animate-pulse bg-blue-200';
    } else if (clickedNumbers.value.has(num)) {
        return 'bg-blue-600 text-white';
    } else {
        return 'bg-white';
    }
};

const columnLabels = ['B', 'I', 'N', 'G', 'O'];

const formattedBingoData = computed(() => {
    return columnLabels.map(label => {
        const values = Array.isArray(props.numbers[label]) ? [...props.numbers[label]] : [];
        if (label === 'N' && values.length === 4 && !values.includes('FREE')) {
            values.splice(2, 0, 'FREE'); // Insert 'FREE' space in the middle for the 'N' column
        }
        return values;
    });
});

watch(() => Array.from(clickedNumbers.value), () => {
    if (Array.from(clickedNumbers.value).length > 3) {
        if (useIsBoardWinner(Array.from(clickedNumbers.value), formattedBingoData.value)) {
            emits('bingo', Array.from(clickedNumbers.value));
        }
    }
})

const user = ref(usePage().props.auth.user);
const game = ref(null)
const winner = ref(null)
const cartela = ref(null)
const winnerNumbers = ref(null)
const isWinner = ref(null);

Echo.private('game-result')
    .listen(`.game-result.${props.gameId}`, (e) => {
        game.value = e.game
        winner.value = e.winner
        cartela.value = e.cartela
        winnerNumbers.value = e.winner_numbers;

      isWinner.value = e.winner.id === user.value.id;
      emits('finish', isWinner.value)
    });
</script>

<template>
    <CompleteGameDrawer v-if="isWinner!== null" :game="game" :winner="winner" :cartela="cartela" :is-winner="isWinner" :is-drawer-open="true" />
    <div class="flex justify-between py-3 rounded-md max-w-sm w-full">
        <div v-for="(column, index) in formattedBingoData" :key="index" class="text-center">
            <h3 class="bg-gray-800 font-semibold text-white rounded py-2 min-w-10">{{ columnLabels[index] }}</h3>
            <ul>
                <li v-for="(num, ind) in column" :key="ind" :class="cardSize">
                    <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-blue-600 font-semibold text-white rounded my-2.5 py-3 px-2">FREE</div>
                    <div v-else :class="getClassForNumber(num)" class="w-full rounded font-medium text-lg my-2 py-3 px-2 cursor-pointer" @click="handleClick(num)">
                        {{ num }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
