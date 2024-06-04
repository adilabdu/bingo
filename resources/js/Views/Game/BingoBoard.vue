<script setup>
import { computed } from 'vue';

const props = defineProps({
    numbers: {
        type: Array,
        default: () => []
    },
    cardSize: {
        type: String,
        default: 'w-16'
    }
});

const columnLabels = ['B', 'I', 'N', 'G', 'O'];

// Formatting the data for the template
const formattedBingoData = computed(() => {
    return props.numbers.map(row => {
        const key = Object.keys(row)[0];
        // Create a shallow copy of the values array to prevent modifying the original array
        const values = [...row[key]];
        if (key === 'N' && !values.includes('FREE')) {
            // Insert 'FREE' space in the middle for the 'N' column, ensuring it's added only once
            values.splice(2, 0, 'FREE');
        }
        return values;
    });
});

</script>


<template>
    <div class="flex justify-between py-3 rounded-md w-full">
        <div v-for="(column, index) in formattedBingoData" :key="index" class=" text-center">
            <h3 class="bg-gray-800 font-semibold text-white rounded py-2">{{ columnLabels[index] }}</h3>
            <ul>
                <li v-for="(num, ind) in column" :key="ind" :class="cardSize">
                    <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-gradient-to-l from-emerald-500 to-green-500 font-semibold text-white rounded my-2.5 py-3 px-2">FREE</div>
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
