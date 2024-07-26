<script setup>
import { ref, watch } from 'vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    filteredDateRange: String,
    startDate: String,
    endDate: String,
    applyDateFilter: Function,
    clearDateFilter: Function
});

const localStartDate = ref(props.startDate);
const localEndDate = ref(props.endDate);

watch(() => props.startDate, (newVal) => {
    localStartDate.value = newVal;
});

watch(() => props.endDate, (newVal) => {
    localEndDate.value = newVal;
});

function applyFilter() {
    props.applyDateFilter(localStartDate.value, localEndDate.value);
}

function clearFilter() {
    props.clearDateFilter();
}
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-end md:justify-end space-y-4 md:space-y-0 md:space-x-4 text-sm">
        <div class="flex items-center space-x-2 text-xs py-1 bg-gray-200 rounded-md shadow-md w-fit text-black px-2">
            <span>{{ filteredDateRange }}</span>
        </div>
        <div class="flex flex-col space-y-2">
            <label for="start_date" class="block">Start Date:</label>
            <input id="start_date" type="date" v-model="localStartDate" class="border rounded p-2" />
        </div>
        <div class="flex flex-col space-y-2">
            <label for="end_date" class="block">End Date:</label>
            <input id="end_date" type="date" v-model="localEndDate" :min="localStartDate" class="border rounded p-2" />
        </div>
        <div class="flex items-end space-x-2">
            <SecondaryButton @click="applyFilter">Apply</SecondaryButton>
            <SecondaryButton @click="clearFilter">Clear</SecondaryButton>
        </div>
    </div>
</template>
