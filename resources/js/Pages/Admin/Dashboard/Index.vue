<script setup>
import { router } from "@inertiajs/vue3";
import { RefreshCcw } from "lucide-vue-next";
import Header from "@/Components/Header.vue";
import OverViewItem from "@/Views/Agent/Dashboard/OverViewItem.vue";
import DateFilter from "@/Views/Admin/Dashboard/DateFilter.vue";
import { ref, computed, onMounted } from "vue";
import Loading from "@/Components/Loading.vue";
import moment from "moment";

const props = defineProps({
    todayRevenue: Number,
    thisWeekRevenue: Number,
    thisMonthRevenue: Number,
    totalRevenue: Number,
    todayCashierRevenue: Number,
    thisWeekCashierRevenue: Number,
    thisMonthCashierRevenue: Number,
    totalCashierRevenue: Number,
    todayPlayerRevenue: Number,
    thisWeekPlayerRevenue: Number,
    thisMonthPlayerRevenue: Number,
    totalPlayerRevenue: Number,
    totalAgents: Number,
    totalBranches: Number,
    todayCashierGames: Number,
    totalCashierGames: Number,
    todayPlayerGames: Number,
    totalPlayerGames: Number,
    todayRegisteredPlayers: Number,
    totalRegisteredPlayers: Number,
    startDateQuery: String,
    endDateQuery: String,
    dateFilterQuery: String,
});

const isLoading = ref(false);
const startDate = ref(props.startDateQuery);
const endDate = ref(props.endDateQuery);
const appliedStartDate = ref(null);
const appliedEndDate = ref(null);

function refreshData() {
    isLoading.value = true;
    router.visit('/admin', {
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

function applyDateFilter(start, end) {
    startDate.value = start;
    endDate.value = end
    isLoading.value = true;
    router.get('/admin', {
            start_date: start,
            end_date: end
        }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

function clearDateFilter() {
    startDate.value = null;
    endDate.value = null;
    appliedStartDate.value = null;
    appliedEndDate.value = null;
    refreshData();
}

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('start_date')) {
        startDate.value = urlParams.get('start_date');
        appliedStartDate.value = urlParams.get('start_date');
    }
    if (urlParams.has('end_date')) {
        endDate.value = urlParams.get('end_date');
        appliedEndDate.value = urlParams.get('end_date');
    }
});
</script>

<template>
    <Loading v-if="isLoading" type="brand" is-full-screen />
    <div class="flex flex-col space-y-4 max-w-xl mx-auto">
        <div class="flex items-center pt-2 w-full justify-between">
            <Header class="font-semibold !w-fit" value="Revenue Numbers" />
            <div @click="refreshData" class="flex items-center space-x-2 text-xs py-1 bg-brand-secondary rounded-md shadow-md w-fit text-white px-2">
                 <RefreshCcw class="w-3" />
                <span>Refresh</span>
            </div>
        </div>

        <DateFilter
            :startDate="startDate"
            :endDate="endDate"
            :applyDateFilter="applyDateFilter"
            :clearDateFilter="clearDateFilter"
        />
        <div class="flex flex-wrap justify-between">
            <OverViewItem base-class="bg-indigo-600 text-white !w-full !max-w-full " :label="moment(startDate).format('ddd DD, MMM YYYY') + ' - ' + moment(endDate).format('ddd DD, MMM YYYY') " :value="dateFilterQuery + ' Br'" />
        </div>

        <div class="flex flex-wrap justify-between">
            <OverViewItem base-class="bg-lime-100" label="Today's revenue" :value="todayRevenue + ' Br'" />
            <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue" :value="thisWeekRevenue + ' Br'" />
            <OverViewItem base-class="bg-amber-100" label="This Month Revenue" :value="thisMonthRevenue + ' Br'" />
            <OverViewItem base-class="bg-zinc-100" label="Total Revenue" :value="totalRevenue + ' Br'" />
        </div>

        <div>
            <Header class="font-semibold" value="Cashier Stats" />
            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-gray-100" label="Today's Revenue" :value="todayCashierRevenue + ' Br'" />
                <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue" :value="thisWeekCashierRevenue + ' Br'" />
                <OverViewItem base-class="bg-purple-100" label="This Month Revenue" :value="thisMonthCashierRevenue + ' Br'" />
                <OverViewItem base-class="bg-amber-100" label="Total Revenue" :value="totalCashierRevenue + ' Br'" />
                <OverViewItem base-class="bg-sky-100" label="Total Agents" :value="totalAgents" />
                <OverViewItem base-class="bg-lime-100" label="Total Branches" :value="totalBranches" />
                <OverViewItem base-class="bg-zinc-100" label="Today's Games" :value="todayCashierGames" />
                <OverViewItem base-class="bg-emerald-100" label="Total Games" :value="totalCashierGames" />
            </div>
        </div>
        <div>
            <Header class="font-semibold" value="Online Players Stats" />
            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-gray-100" label="Today's Revenue" :value="todayPlayerRevenue + ' Br'" />
                <OverViewItem base-class="bg-lime-100" label="Total Revenue" :value="totalPlayerRevenue + ' Br'" />
                <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue" :value="thisWeekPlayerRevenue + ' Br'" />
                <OverViewItem base-class="bg-rose-100" label="This Month Revenue" :value="thisMonthPlayerRevenue + ' Br'" />
                <OverViewItem base-class="bg-sky-100" label="Registered Today" :value="todayRegisteredPlayers" />
                <OverViewItem base-class="bg-amber-100" label="Total Players" :value="totalRegisteredPlayers" />
                <OverViewItem base-class="bg-lime-100" label="Today's Games" :value="todayPlayerGames" />
                <OverViewItem base-class="bg-purple-100" label="Total Games" :value="totalPlayerGames" />
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
