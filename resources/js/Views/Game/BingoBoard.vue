<script setup>
import {computed, ref, watch} from 'vue';
import {useIsBoardWinner} from "@/Composables/useIsBoardWinner";

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
    }
});

const emits = defineEmits(['bingo'])

const clickedNumbers = ref(new Set(['FREE']));

const handleClick = (number) => {
    if (props.drawnNumbers.includes(number) && !clickedNumbers.value.has(number)) {
        clickedNumbers.value.add(number);
    }
};

const getClassForNumber = (num) => {
    if (num === props.currentDrawnNumber && !clickedNumbers.value.has(num)) {
        return 'animate-pulse bg-blue-200';
    } else if (clickedNumbers.value.has(num)) {
        return 'bg-blue-600 text-white';
    } else {
        return 'bg-white';
    }
};

const columnLabels = ['B', 'I', 'N', 'G', 'O'];

// Formatting the data for the template
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
    console.log('watching... ')
    if (Array.from(clickedNumbers.value).length > 3) {
        if (useIsBoardWinner(Array.from(clickedNumbers.value), formattedBingoData.value)) {
            emits('bingo', Array.from(clickedNumbers.value));
        }
    }
})
</script>

<template>
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
