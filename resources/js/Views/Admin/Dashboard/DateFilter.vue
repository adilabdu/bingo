<script setup>
import {ref, watch} from 'vue';
import DatePicker from "@/Components/DatePicker.vue";
import moment from "moment";

const props = defineProps({
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

</script>

<template>
    <div class="flex w-full py-3 space-x-6 justify-between text-sm">
        <DatePicker @update:date="applyFilter" id="start_date" type="date" v-model="localStartDate"
                    class="border rounded p-2" :label="localStartDate ? moment(localStartDate).format('DD/MM/YYYY') : 'Start Date'"/>

        <DatePicker @update:date="applyFilter" id="end_date" type="date" v-model="localEndDate" :min="localStartDate"
                    class="border rounded p-2" :label="localEndDate ? moment(localEndDate).format('DD/MM/YYYY') : 'End Date'"/>

    </div>
</template>
