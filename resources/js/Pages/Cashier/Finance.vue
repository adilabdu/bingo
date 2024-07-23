<script setup>
import { ref, computed, onMounted } from 'vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { Button } from '@/Components/shadcn/ui/button/index.js';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm, usePage} from '@inertiajs/vue3';
import moment from "moment";
import Pagination from "@/Components/Pagination.vue";

defineProps({
    transactions: {
        type: Array,
        required: true
    }
});

const formatDate = (value) => {
    return new Date(value).toLocaleDateString();
};

const user = computed(()=> usePage().props.auth.user)
</script>

<template>
    <div class="space-y-8">
        <!-- Display Balance -->
        <div class="mb-6">
            <div class="flex flex-col items-center space-y-1">
                <p class="text-5xl md:text-7xl font-bold bg-brand-secondary text-white  py-2 px-4 rounded-sm">{{ user.cashier.balance}} Br</p>
            </div>
        </div>

        <!-- Transactions Table -->
        <Table class="w-full max-w-screen-xl mx-auto bg-white shadow rounded-sm px-2">
            <TableBody class="bg-gray-300 font-semibold">
                <TableRow>
                    <TableCell class="p-4">Date</TableCell>
                    <TableCell class="p-4">Type</TableCell>
                    <TableCell class="p-4">Amount</TableCell>
                    <TableCell class="p-4">Description</TableCell>
                </TableRow>
            </TableBody>
            <TableBody>
                <TableRow v-for="item in transactions.data" :key="item.id" class="border-b !text-xs">
                    <TableCell>{{ moment(item.created_at).format('DD/MM/YY') }}</TableCell>
                    <TableCell>{{ item.event }}</TableCell>
                    <TableCell>{{ item.properties['amount']}}</TableCell>
                    <TableCell>{{ item.description }}</TableCell>
                </TableRow>
                <TableRow v-if="transactions.data.length === 0">
                    <TableCell class="p-4 text-center" colspan="4">No transactions found.</TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <Pagination :links="transactions.links"/>
    </div>
</template>

<style scoped>
</style>
