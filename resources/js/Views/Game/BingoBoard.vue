<script setup>
import { computed } from 'vue';

const props = defineProps({
    numbers: {
        type: Array,
        default: () => []
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
    <div class="flex justify-center border-2 py-3 shadow-md rounded-md border-gray-800">
        <div v-for="(column, index) in formattedBingoData" :key="index" class="mx-2 text-center">
            <h3 class="bg-gradient-to-l from-purple-500 to-violet-500 font-semibold text-white rounded py-2">{{ columnLabels[index] }}</h3>
            <ul>
                <li v-for="(num, ind) in column" :key="ind" class="w-12">
                    <div v-if="columnLabels[index] === 'N' && ind === 2" class="bg-gray-800 font-semibold text-white rounded p-2">FREE</div>
                    <div v-else class="bg-white w-full rounded font-medium my-2 p-2">
                        {{ num }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>


<style scoped>

</style>
