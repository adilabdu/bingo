<script setup>
import { ref } from 'vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const balance = ref(page.props.balance);
const recentActivities = ref(page.props.recentActivities);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'ETB',
    }).format(value);
};

const formatDate = (value) => {
    return new Date(value).toLocaleDateString();
};
</script>

<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
            <h2 class="text-4xl font-bold mb-4">Cashier Finance Management</h2>
            <p class="text-lg">Manage and view your financial transactions</p>
        </div>

        <!-- Display Balance -->
        <div class="mb-6">
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-700">Current Balance</h3>
                <p class="text-2xl font-bold text-green-600">{{ formatCurrency(balance) }}</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="p-4 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-700">Recent Activities</h3>
            <Table class="min-w-full bg-white shadow rounded-lg">
                <TableBody class="bg-gray-300 font-semibold">
                    <TableRow>
                        <TableCell class="p-4">Date</TableCell>
                        <TableCell class="p-4">Description</TableCell>
                    </TableRow>
                </TableBody>
                <TableBody>
                    <TableRow v-for="activity in recentActivities" :key="activity.id" class="border-b">
                        <TableCell class="p-4">{{ formatDate(activity.created_at) }}</TableCell>
                        <TableCell class="p-4">{{ activity.description }}</TableCell>
                    </TableRow>
                    <TableRow v-if="recentActivities.length === 0">
                        <TableCell class="p-4 text-center" colspan="2">No recent activities found.</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>

<style scoped>
</style>
