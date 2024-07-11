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




const getClassForNumber = (num) => {
     if (props.winnerNumbers.includes(num)) {
        return 'bg-brand-secondary text-white cursor-not-allowed';
    } else {
        return 'bg-white';
    }
};

</script>

<template>
    <div class="flex flex-col items-center w-full">
        <div class="flex justify-between py-3 rounded-md max-w-lg w-full">
            <div v-for="(column, index) in formattedBingoData" :key="index" class="text-center">
                <h3 class="bg-brand-primary font-bold text-white text-5xl rounded py-2 mb-4 min-w-20">{{ columnLabels[index] }}</h3>
                <ul>
                    <li v-for="(num, ind) in column" :key="ind" :class="cardSize">
                        <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-brand-secondary font-semibold text-white rounded my-2 flex justify-center text-3xl items-center py-3 px-2 min-h-20 min-w-20">FREE</div>
                        <div v-else :class="getClassForNumber(num)" class="rounded-md border-2 border-black font-bold text-4xl my-2 py-3 text-center px-2 cursor-pointer min-h-20 min-w-20 flex items-center justify-center">
                            {{ num }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
