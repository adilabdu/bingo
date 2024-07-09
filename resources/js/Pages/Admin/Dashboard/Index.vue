<script setup>
import InfoCard from "@/Views/Admin/Dashboard/InfoCard.vue";
import ListSection from "@/Views/Admin/Dashboard/ListSection.vue";
import { computed, ref, watch } from 'vue';
import { usePage, router } from "@inertiajs/vue3";

const authUser = computed(() => usePage().props.authUser);

const totalPlayers = computed(() => usePage().props.totalPlayers || 0);
const upcomingGames = computed(() => usePage().props.pendingGames || []);
const recentWinners = computed(() => usePage().props.recentWinners || []);
const totalGames = computed(() => usePage().props.totalGames || 0);
const activePlayers = computed(() => usePage().props.activePlayers || 0);

const totalRevenue = computed(() => usePage().props.totalRevenue || "0");
const totalProfit = computed(() => usePage().props.totalProfit || "0");

const startDate = ref(usePage().props.startDate || '');
const endDate = ref(usePage().props.endDate || '');

// Watch startDate and update endDate to ensure it's selectable only >= startDate
watch(startDate, (newStartDate) => {
    if (endDate.value && newStartDate > endDate.value) {
        endDate.value = '';
    }
});

const applyDateFilter = () => {
    router.get(route('index'), { start_date: startDate.value, end_date: endDate.value }, { preserveState: true });
};

const clearDateFilter = () => {
    startDate.value = '';
    endDate.value = '';
    router.get(route('index'), {}, { preserveState: false });
};
</script>

<template>
    <div class="container mx-auto space-y-8 px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
            <h2 class="text-4xl font-bold mb-4">Welcome to the Admin Dashboard</h2>
            <p class="text-lg">Manage your games, and view recent activities with ease.</p>
        </div>

        <!-- Revenue and Profit Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg space-y-4">
            <h3 class="text-2xl font-bold">Revenue and Profit</h3>
            <div class="flex flex-col lg:flex-row lg:space-x-4 space-y-4 lg:space-y-0">
                <InfoCard title="Total Revenue" :amount="totalRevenue" currency="birr" />
                <InfoCard title="Total Profit" :amount="totalProfit" currency="birr"/>
            </div>
            <!-- Date Filter Section -->
            <div class="flex justify-end space-x-4 text-sm">
                <div>
                    <label for="start_date" class="block">Start Date:</label>
                    <input id="start_date" type="date" v-model="startDate" class="border rounded p-2" />
                </div>
                <div>
                    <label for="end_date" class="block">End Date:</label>
                    <input id="end_date" type="date" v-model="endDate" :min="startDate" class="border rounded p-2" />
                </div>
                <div class="flex items-end space-x-2">
                    <button @click="applyDateFilter" class="bg-blue-500 text-white rounded p-2">Apply</button>
                    <button @click="clearDateFilter" class="bg-gray-500 text-white rounded p-2">Clear</button>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="flex flex-col lg:flex-row lg:space-x-8 space-y-8 lg:space-y-0">
            <InfoCard title="Total Games" :value="totalGames" />
            <InfoCard title="Total Players" :value="totalPlayers" />
        </div>

        <!-- Main Content Section -->
        <div class="flex flex-col lg:flex-row lg:space-x-8 space-y-8 lg:space-y-0">
            <!-- Left Column -->
            <div class="flex-1 space-y-8">
                <!-- Upcoming Games -->
                <ListSection
                    title="Upcoming Games"
                    :items="upcomingGames"
                    iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                    iconColor="text-blue-500"
                />
                <div v-if="upcomingGames.length === 0" class="text-gray-500 text-center">
                    No upcoming games available.
                </div>

                <!-- Recent Winners -->
                <ListSection
                    title="Recent Winners"
                    :items="recentWinners"
                    iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                    iconColor="text-green-500"
                />
                <div v-if="recentWinners.length === 0" class="text-gray-500 text-center">
                    No recent winners available.
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-full lg:w-64 space-y-8">
                <!-- Active Players -->
                <InfoCard
                    title="Active Players"
                    :value="activePlayers"
                    icon
                    iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                    iconColor="text-green-500"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    width: 100%;
    max-width: 1440px;
}
@media (max-width: 1024px) {
    .flex {
        flex-direction: column;
    }
    .lg\:space-x-8 {
        --tw-space-x-reverse: 0;
        margin-right: 0;
        margin-left: 0;
    }
    .lg\:space-y-8 {
        --tw-space-y-reverse: 0;
        margin-top: 2rem;
    }
}
</style>
