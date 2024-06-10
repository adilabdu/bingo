<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import InfoCard from "@/Views/Admin/Dashboard/InfoCard.vue";
import ListSection from "@/Views/Admin/Dashboard/ListSection.vue";
import {computed, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";

const authUser = computed(() => usePage().props.authUser);

const totalPlayers = computed(() => usePage().props.totalPlayers);

const upcomingGames = ref([
    { id: 1, name: 'Game 1', startTime: '12:00 PM', players: 5 },
    { id: 2, name: 'Game 2', startTime: '2:00 PM', players: 8 },
    { id: 3, name: 'Game 3', startTime: '4:00 PM', players: 10 },
]);

const recentWinners = ref([
    { username: 'User1', gameName: 'Game 1', winningPattern: 'Pattern A' },
    { username: 'User2', gameName: 'Game 2', winningPattern: 'Pattern B' },
    { username: 'User3', gameName: 'Game 3', winningPattern: 'Pattern C' },
]);

const activePlayers = ref(120);

const notifications = ref([
    { id: 1, message: 'Server maintenance at 3 PM', time: '10 minutes ago' },
    { id: 2, message: 'New user registered', time: '1 hour ago' },
    { id: 3, message: 'Game 2 has started', time: '2 hours ago' },
]);

const serverStatus = ref("Online");
const newMessages = ref(5);

const totalGames = ref(250);
const totalRevenue = ref("$15,300");

</script>

<template>
    <AdminLayout>
        <div class="space-y-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
                <h2 class="text-4xl font-bold mb-4">Welcome to the Admin Dashboard</h2>
                <p class="text-lg">Manage your games, and view recent activities with ease.</p>
            </div>

            <!-- Statistics Section -->
            <div class="flex flex-col lg:flex-row lg:space-x-8 space-y-8 lg:space-y-0">
                <InfoCard title="Total Games" :value="totalGames" />
                <InfoCard title="Total Players" :value="totalPlayers" />
                <InfoCard title="Total Revenue" :value="totalRevenue" />
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

                    <!-- Recent Winners -->
                    <ListSection
                        title="Recent Winners"
                        :items="recentWinners"
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-green-500"
                    />

                    <!-- Notifications -->
                    <ListSection
                        title="Notifications"
                        :items="notifications"
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-yellow-500"
                    />

                    <!-- Recent Activities -->
                    <ListSection
                        title="Recent Activities"
                        :items="notifications"
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-purple-500"
                    />
                </div>

                <!-- Right Column -->
                <div class="w-full lg:w-64 space-y-8">
                    <!-- Active Players -->
                    <InfoCard
                        title="Active Players"
                        :value="activePlayers"
                        icon
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-red-500"
                    />

                    <!-- Server Status -->
                    <InfoCard
                        title="Server Status"
                        :value="serverStatus"
                        icon
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-green-500"
                    />

                    <!-- New Messages -->
                    <InfoCard
                        title="New Messages"
                        :value="newMessages"
                        icon
                        iconPath="M12 4a8 8 0 100 16 8 8 0 000-16zm4 9H8v-2h8v2z"
                        iconColor="text-blue-500"
                    />
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<style scoped>
</style>

