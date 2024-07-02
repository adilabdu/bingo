<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { Button } from '@/Components/shadcn/ui/button/index.js';
import { router, usePage } from "@inertiajs/vue3";

// Initialize paginated games with usePage props
const pageData = usePage().props;
const paginatedGames = ref(pageData.games);

const totalGames = computed(() => usePage().props.totalGames);
const totalGamesToday = computed(() => usePage().props.totalGamesToday);
const activeGames = computed(() => usePage().props.activeGames);

const statuses = ['pending', 'active', 'completed', 'cancelled'];
const selectedStatus = ref('');

const fetchGames = (url = '/admin/games') => {
    const params = { status: selectedStatus.value };
    router.get(url, params, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            paginatedGames.value = page.props.games;
        }
    });
};

watch([selectedStatus], () => {
    fetchGames();
});

onMounted(() => {
    fetchGames();
});
</script>

<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex justify-between bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
            <div class="flex w-full flex-col">
                <h2 class="text-4xl font-bold mb-4">Games Management</h2>
                <p class="text-lg">Browse and manage games with ease.</p>
            </div>
            <div class="flex w-full flex-col items-end">
                <span class="text-lg">Total Games: {{ totalGames }}</span>
                <span class="text-lg">Total Games Today: {{ totalGamesToday }}</span>
                <span class="text-lg">Active Games: {{ activeGames }}</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex items-center mb-4">
            <div class="relative w-1/4">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Select Status</label>
                <select id="status" v-model="selectedStatus" class="w-full p-2 border rounded">
                    <option value="">All</option>
                    <option v-for="status in statuses" :key="status" :value="status">
                        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Games Table -->
        <Table class="min-w-full bg-white shadow rounded-lg">
            <TableBody class="bg-gray-300 font-semibold">
                <TableRow>
                    <TableCell class="p-4">Game ID</TableCell>
                    <TableCell class="p-4">Category</TableCell>
                    <TableCell class="p-4">Status</TableCell>
                    <TableCell class="p-4">Scheduled At</TableCell>
                    <TableCell class="p-4">Players</TableCell>
                    <TableCell class="p-4">Actions</TableCell>
                </TableRow>
            </TableBody>
            <TableBody>
                <TableRow v-if="paginatedGames.data.length === 0">
                    <TableCell colspan="6" class="text-center p-4">No games available.</TableCell>
                </TableRow>
                <TableRow v-for="game in paginatedGames.data" :key="game.id" class="border-b">
                    <TableCell class="p-4">{{ game.id }}</TableCell>
                    <TableCell class="p-4">{{ game.game_category.name }}</TableCell>
                    <TableCell class="p-4">{{ game.status }}</TableCell>
                    <TableCell class="p-4">{{ game.scheduled_at }}</TableCell>
                    <TableCell class="p-4">{{ game.players.length }}</TableCell>
                    <TableCell class="p-4">
                        <Button class="bg-blue-500 text-white px-4 py-2 rounded" @click="router.visit(route('game', game.id))">
                            View
                        </Button>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <Button @click="fetchGames(paginatedGames.prev_page_url)" :disabled="!paginatedGames.prev_page_url">
                &laquo; Previous
            </Button>
            <span>Page {{ paginatedGames.current_page }} of {{ paginatedGames.last_page }}</span>
            <Button @click="fetchGames(paginatedGames.next_page_url)" :disabled="!paginatedGames.next_page_url">
                Next &raquo;
            </Button>
        </div>
    </div>
</template>

<style scoped>
</style>
