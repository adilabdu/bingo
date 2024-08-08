<script setup>
import {ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import {RefreshCcw, Smile, Frown} from "lucide-vue-next";
import OverViewItem from "@/Views/Agent/Dashboard/OverViewItem.vue";
import Header from "@/Components/Header.vue";
import DateFilter from "@/Views/Admin/Dashboard/DateFilter.vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from "@/Components/shadcn/ui/select/index.js";
import {Switch} from "@/Components/shadcn/ui/switch/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TopUpBalanceDrawer from "@/Views/Game/TopUpBalanceDrawer.vue";
const props = defineProps([
    'selectedAgent'
])
const isLoading = ref(false);
const page = usePage();
const agents = page.props.agents;

const selectedAgentId = ref(page.props.selectedAgent.id);
const todayRevenue = ref(page.props.todayRevenue);
const thisWeekRevenue = ref(page.props.thisWeekRevenue);
const totalRevenue = ref(page.props.totalRevenue);
const activeGames = ref(page.props.activeGames);
const totalGames = ref(page.props.totalGames);
const branches = ref(page.props.branches);
const isActive = ref(props.selectedAgent.is_active);

const startDate = ref(page.props.startDate);
const endDate = ref(page.props.endDate);

function refreshData() {
    isLoading.value = true;
    router.get('/admin/agents',{
        agent_id: selectedAgentId.value
    }, {
        replace: true,
        preserveState: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

function applyDateFilter(start, end) {
    isLoading.value = true;
    router.get('/admin/agents', {
        agent_id: selectedAgentId.value,
        start_date: start,
        end_date: end,
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
    applyDateFilter(startDate.value, endDate.value);
}

watch([startDate, endDate], () => {
    applyDateFilter(startDate.value, endDate.value);
});

function handleAgentChange() {
    isLoading.value = true;
    router.visit(`/admin/agents?agent_id=${selectedAgentId.value}`, {
        replace: true,
        onSuccess: (page) => {
            const newProps = page.props;
            selectedAgentId.value = newProps.selectedAgent.id;
            todayRevenue.value = newProps.todayRevenue;
            thisWeekRevenue.value = newProps.thisWeekRevenue;
            totalRevenue.value = newProps.totalRevenue;
            activeGames.value = newProps.activeGames;
            totalGames.value = newProps.totalGames;
            branches.value = newProps.branches;
            startDate.value = newProps.startDate ? newProps.startDate.split('T')[0] : null;
            endDate.value = newProps.endDate ? newProps.endDate.split('T')[0] : null;
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        }
    });
}

watch(selectedAgentId, () => {
    handleAgentChange();
});

watch(isActive, () => {
    isLoading.value = true;
    router.post('/agent/toggle', {
        is_active: isActive.value,
        agent_id: selectedAgentId.value
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
});


</script>

<template>
    <Loading v-if="isLoading" type="brand" is-full-screen/>
    <div class="flex flex-col space-y-4 bg-white">
        <div class="mb-6 hidden md:inline-block">
            <p class="text-gray-600 hidden md:inline-flex">Manage your agents.</p>
        </div>

        <div class="w-full flex justify-between items-center gap-3 max-w-sm">
            <Select v-model="selectedAgentId" class="max-w-sm">
                <SelectTrigger>
                    <SelectValue placeholder="Select Agent"/>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="item in agents" :key="item.id" :value="item.id">
                            {{ item.user.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>

            <div @click="refreshData"
                 class="flex items-center space-x-2 text-xs bg-brand-secondary rounded-md shadow-md text-white w-fit h-8 px-2">
                <RefreshCcw class="w-3"/>
                <span>Refresh</span>
            </div>
        </div>

        <TopUpBalanceDrawer @success="refreshData" :agent="selectedAgent"/>

        <div class="flex justify-around p-2 mt-2 mb-4 rounded-lg items-center bg-indigo-600 text-white h-20">
            <div class="flex flex-col items-center font-light text-xs"><span
                class="text-2xl font-bold">{{ selectedAgent.balance }}Br</span>
                Balance
            </div>
            <div class="flex flex-col justify-center items-center text-xs font-light "><span
                class="text-2xl font-bold">{{ 100 - selectedAgent.profit_percentage }}%</span>Profit
            </div>
            <div class="flex flex-col justify-center items-center text-xs font-light"><span
                class="text-2xl font-bold">
                <Switch @update:checked="isActive = !isActive"
                        :checked="isActive"/></span>
                <span v-if="isActive">Active</span><span v-else>Blocked</span>
            </div>
        </div>


        <div>
            <Header class="font-semibold w-full " value="Revenue Numbers"/>

            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-lime-100" label="Today's Revenue"
                              :value="Number(todayRevenue).toFixed(2) + ' Br'"/>
                <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue"
                              :value="Number(thisWeekRevenue).toFixed(2) + ' Br'"/>
                <OverViewItem base-class="bg-purple-100" label="Total Revenue"
                              :value="Number(totalRevenue).toFixed(2) + ' Br'"/>
                <OverViewItem base-class="bg-zinc-200" label="Active Games" :value="activeGames"/>
                <OverViewItem base-class="bg-zinc-200" label="Total Games" :value="totalGames"/>
            </div>
        </div>

        <div>
            <Header class="font-semibold" value="Stats"/>
            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-orange-100" label="Active Games" :value="activeGames"/>
                <OverViewItem base-class="bg-cyan-100" label="Total Games" :value="totalGames"/>
                <OverViewItem base-class="bg-blue-100" label="Total Cashiers"
                              :value="branches.reduce((acc, branch) => acc + branch.cashiers.length, 0)"/>
                <OverViewItem base-class="bg-pink-100" label="Total Branches" :value="branches.length"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
