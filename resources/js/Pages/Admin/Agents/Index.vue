<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import { RefreshCcw } from "lucide-vue-next";
import OverViewItem from "@/Views/Agent/Dashboard/OverViewItem.vue";
import Header from "@/Components/Header.vue";
import DateFilter from "@/Views/Admin/Dashboard/DateFilter.vue";

const isLoading = ref(false);
const page = usePage();
const agents = page.props.agents;

const selectedAgent = ref(page.props.selectedAgent.id);
const todayRevenue = ref(page.props.todayRevenue);
const thisWeekRevenue = ref(page.props.thisWeekRevenue);
const totalRevenue = ref(page.props.totalRevenue);
const activeGames = ref(page.props.activeGames);
const totalGames = ref(page.props.totalGames);
const branches = ref(page.props.branches);

const startDate = ref(page.props.startDate ? page.props.startDate.split('T')[0] : null);
const endDate = ref(page.props.endDate ? page.props.endDate.split('T')[0] : null);

const selectedAgentName = computed(() => {
    const agent = agents.find(agent => agent.id === selectedAgent.value);
    return agent ? agent.user.name : 'Agent';
});

function refreshData() {
    isLoading.value = true;
    router.visit('/admin/agents', {
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

function applyDateFilter(start, end) {
    isLoading.value = true;
    router.visit('/admin/agents', {
        method: 'get',
        data: {
            agent_id: selectedAgent.value,
            start_date: start,
            end_date: end,
        },
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

function handleAgentChange(event) {
    const agentId = event.target.value;
    isLoading.value = true;
    router.visit(`/admin/agents?agent_id=${agentId}`, {
        replace: true,
        onSuccess: (page) => {
            const newProps = page.props;
            selectedAgent.value = newProps.selectedAgent.id;
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
</script>

<template>
    <Loading v-if="isLoading" type="brand" is-full-screen />
    <div class="flex flex-col space-y-4">
        <div class="mb-6 hidden md:inline-block">
            <p class="text-gray-600 hidden md:inline-flex">Manage your agents.</p>
        </div>

        <div>
            <div class="flex flex-col pb-2">
                <Header class="font-semibold" :value="`${selectedAgentName} Revenue Numbers`" />
            </div>

            <div class="mb-4 flex justify-between gap-2">
                <select id="agent" v-model="selectedAgent" @change="handleAgentChange" class="border rounded p-2 w-full">
                    <option v-for="agent in agents" :key="agent.id" :value="agent.id">{{ agent.user.name }}</option>
                </select>
                <div @click="refreshData" class="flex items-center space-x-2 text-xs bg-brand-secondary rounded-md shadow-md text-white px-2">
                    <RefreshCcw class="w-3" />
                    <span>Refresh</span>
                </div>
            </div>

            <DateFilter
                :filteredDateRange="filteredDateRange"
                :startDate="startDate"
                :endDate="endDate"
                :applyDateFilter="applyDateFilter"
                :clearDateFilter="clearDateFilter"
            />

            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-lime-100" label="Today's Revenue" :value="todayRevenue + ' Br'" />
                <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue" :value="thisWeekRevenue + ' Br'" />
                <OverViewItem base-class="bg-purple-100" label="Total Revenue" :value="totalRevenue + ' Br'" />
                <OverViewItem base-class="bg-zinc-200" label="Active Games" :value="activeGames" />
                <OverViewItem base-class="bg-zinc-200" label="Total Games" :value="totalGames" />
            </div>

            <div>
                <Header class="font-semibold" value="Stats"/>
                <div class="flex flex-wrap justify-between">
                    <OverViewItem base-class="bg-orange-100" label="Active Games" :value="activeGames"/>
                    <OverViewItem base-class="bg-cyan-100" label="Total Games" :value="totalGames"/>
                    <OverViewItem base-class="bg-blue-100" label="Total Cashiers" :value="branches.reduce((acc, branch) => acc + branch.cashiers.length, 0)"/>
                    <OverViewItem base-class="bg-pink-100" label="Total Branches" :value="branches.length"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
