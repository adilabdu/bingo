<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { computed } from 'vue';
import { usePage, router } from "@inertiajs/vue3";

const player = computed(() => usePage().props.player);
const recentActivities = computed(() => usePage().props.recentActivities);
const gamesPlayed = computed(() => usePage().props.gamesPlayed);
const totalWonAmount = computed(() => usePage().props.totalWonAmount);
const totalLossAmount = computed(() => usePage().props.totalLossAmount);
const totalWageredAmount = computed(() => usePage().props.totalWageredAmount);
</script>

<template>
    <div class="space-y-8">
        <!-- Player Header -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg flex items-center justify-between">
            <div>
                <h2 class="text-4xl font-bold mb-2">{{ player.name }}</h2>
                <p class="text-lg">View detailed information about {{ player.name }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-semibold text-green-200">Balance: ${{ player.player.balance || '0.00' }}</p>
            </div>
        </div>

        <!-- Player Profile -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Player Profile</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong>Name:</strong> {{ player.name || 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ player.email || 'N/A' }}</p>
                    <p><strong>Phone Number:</strong> {{ player.phone_number || 'N/A' }}</p>
                </div>
                <div>
                    <p><strong>Joined:</strong> {{ player.created_at ? new Date(player.created_at).toLocaleString() : 'N/A' }}</p>
                    <p><strong>Status:</strong> <span :class="{'text-green-500': !player.is_blocked, 'text-red-500': player.is_blocked}">{{ player.is_blocked ? 'Blocked' : 'Active' }}</span></p>
                </div>
            </div>
        </div>

        <!-- Player Dashboard -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Player Dashboard</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h4 class="text-lg font-semibold mb-2">Games Played</h4>
                    <p class="text-lg">{{ gamesPlayed }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h4 class="text-lg font-semibold mb-2">Total Wagered Amount</h4>
                    <p class="text-lg">{{ totalWageredAmount || '0' }} <span class="text-sm">birr</span></p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h4 class="text-lg font-semibold mb-2">Total Won Amount</h4>
                    <p class="text-lg">{{ totalWonAmount || '0' }} <span class="text-sm">birr</span></p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h4 class="text-lg font-semibold mb-2">Total Loss Amount</h4>
                    <p class="text-lg">{{ totalLossAmount || '0' }} <span class="text-sm">birr</span></p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Recent Activities</h3>
            <ul class="space-y-2">
                <li v-if="recentActivities.length === 0" class="text-gray-500">No recent activities available.</li>
                <li v-for="activity in recentActivities" :key="activity.id" class="flex justify-between items-center p-4 bg-gray-100 rounded-lg shadow">
                    <span>{{ activity.description }}</span>
                    <span class="text-gray-500 text-sm">{{ new Date(activity.created_at).toLocaleString() }}</span>
                </li>
            </ul>
        </div>

        <!-- Back Button -->
        <button class="bg-gray-500 text-white py-2 px-4 rounded-lg" @click="router.visit('/admin/players')">
            Back to Players
        </button>
    </div>
</template>

<style scoped>
/* Add any additional styling here */
</style>
