<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { computed } from 'vue';
import { usePage, router } from "@inertiajs/vue3";

const game = computed(() => usePage().props.game);
const winner = computed(() => game.value.players.find(player => player.player.id === game.value.winner_player_id));
</script>

<template>
    <div class="space-y-8">
        <!-- Game Header -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
            <h2 class="text-4xl font-bold mb-4">Game Details: {{ game.id }}</h2>
            <p class="text-lg">View detailed information about this game.</p>
        </div>

        <!-- Winner Information -->
        <div v-if="winner" class="bg-green-100 p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Winner Information</h3>
            <p><strong>Winner:</strong> {{ winner.player.user.name }}</p>
            <p><strong>Balance:</strong> {{ winner.player.balance }}</p>
            <p><strong>Cartela Name:</strong> {{ winner.cartela.name }}</p>
        </div>

        <!-- Game Information -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Game Information</h3>
            <p><strong>Game Category:</strong> {{ game.game_category.name }}</p>
            <p><strong>Status:</strong> {{ game.status }}</p>
            <p><strong>Scheduled At:</strong> {{ new Date(game.scheduled_at).toLocaleString() }}</p>
            <p><strong>Winner Net Amount:</strong> {{ game.winner_net_amount }}</p>
        </div>

        <!-- Players List -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-4">Players</h3>
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-gray-300 font-semibold">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Balance</th>
                    <th class="px-4 py-2 text-left">Cartela Name</th>
                    <th class="px-4 py-2 text-left">Is Winner</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="player in game.players" :key="player.id" class="border-b">
                    <td class="px-4 py-2">{{ player.player.user.name }}</td>
                    <td class="px-4 py-2">{{ player.player.balance }}</td>
                    <td class="px-4 py-2">{{ player.cartela.name }}</td>
                    <td class="px-4 py-2">{{ game.winner_player_id === player.player.id ? 'Yes' : 'No' }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <button class="bg-gray-500 text-white py-2 px-4 rounded-lg" @click="router.visit('/admin/games')">
            Back to Games
        </button>
    </div>
</template>

<style scoped>
/* Add any additional styling here */
</style>
