<script setup>
import { computed } from 'vue';

const props = defineProps({
    numbers: {
        type: Object,
        default: () => ({
            B: [], I: [], N: [], G: [], O: []
        })
    },
    cardSize: {
        type: String,
        default: 'w-16'
    }
});

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

</script>

<template>
    <div class="flex justify-between py-3 rounded-md max-w-sm w-full">
        <div v-for="(column, index) in formattedBingoData" :key="index" class=" text-center">
            <h3 class="bg-gray-800 font-semibold text-white rounded py-2">{{ columnLabels[index] }}</h3>
            <ul>
                <li v-for="(num, ind) in column" :key="ind" :class="cardSize">
                    <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-blue-600 font-semibold text-white rounded my-2.5 py-3 px-2">FREE</div>
                    <div v-else class="bg-white w-full rounded font-medium text-lg my-2 py-3 px-2">
                        {{ num }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>


<style scoped>

</style>
