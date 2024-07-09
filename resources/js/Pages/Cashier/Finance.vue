<script setup>
import { ref, computed, onMounted } from 'vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { Button } from '@/Components/shadcn/ui/button/index.js';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from '@inertiajs/vue3';

// Fake transactions data
const transactions = ref([
    {
        id: 1,
        created_at: '2021-10-01 12:00:00',
        type: 'Deposit',
        amount: 1000,
        status: 'Success',
    },
    {
        id: 2,
        created_at: '2021-10-02 12:00:00',
        type: 'Withdrawal',
        amount: 500,
        status: 'Success',
    },
    {
        id: 3,
        created_at: '2021-10-03 12:00:00',
        type: 'Deposit',
        amount: 2000,
        status: 'Success',
    },
    {
        id: 4,
        created_at: '2021-10-04 12:00:00',
        type: 'Withdrawal',
        amount: 1000,
        status: 'Success',
    },
    {
        id: 5,
        created_at: '2021-10-05 12:00:00',
        type: 'Deposit',
        amount: 3000,
        status: 'Success',
    },
    {
        id: 6,
        created_at: '2021-10-06 12:00:00',
        type: 'Withdrawal',
        amount: 2000,
        status: 'Success',
    },
    {
        id: 7,
        created_at: '2021-10-07 12:00:00',
        type: 'Deposit',
        amount: 4000,
        status: 'Success',
    },
    {
        id: 8,
        created_at: '2021-10-08 12:00:00',
        type: 'Withdrawal',
        amount: 3000,
        status: 'Success',
    },
    {
        id: 9,
        created_at: '2021-10-09 12:00:00',
        type: 'Deposit',
        amount: 5000,
        status: 'Success',
    },
    {
        id: 10,
        created_at: '2021-10-10 12:00:00',
        type: 'Withdrawal',
        amount: 4000,
        status: 'Success',
    },
]);

const balance = ref(10000); // Fake balance

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const formatDate = (value) => {
    return new Date(value).toLocaleDateString();
};

// Initialize form
const form = useForm({
    amount: '',
    phone_number: '',
});

onMounted(() => {
    // Fetch balance and transactions if necessary
});

const submitDeposit = () => {
    // Handle deposit logic here
    console.log('Deposit submitted', form.amount, form.phone_number);
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

        <div class="flex w-full justify-between gap-4">
            <div class="flex w-full flex-col">
                <div class="font-semibold text-xl pb-1">Deposit</div>

                <div class="w-full flex">
                    <form @submit.prevent="submitDeposit" class="w-full">
                        <div>
                            <InputLabel for="amount" value="Amount in ETB" />
                            <TextInput
                                id="amount"
                                type="number"
                                class="mt-1 block w-full border"
                                v-model="form.amount"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.amount" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="phone_number" value="Phone Number" />
                            <TextInput
                                id="phone_number"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.phone_number"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.phone_number" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Deposit
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
           <div class="flex w-full flex-col">
               <div class="font-semibold text-xl pb-1">Withdraw</div>

               <div class="flex w-full">
                   <form @submit.prevent="submitDeposit" class="w-full">
                       <div>
                           <InputLabel for="amount" value="Amount in ETB" />
                           <TextInput
                               id="amount"
                               type="number"
                               class="mt-1 block w-full border"
                               v-model="form.amount"
                               required
                           />
                           <InputError class="mt-2" :message="form.errors.amount" />
                       </div>
                       <div class="mt-4">
                           <InputLabel for="phone_number" value="Phone Number" />
                           <TextInput
                               id="phone_number"
                               type="text"
                               class="mt-1 block w-full border"
                               v-model="form.phone_number"
                               required
                           />
                           <InputError class="mt-2" :message="form.errors.phone_number" />
                       </div>
                       <div class="flex items-center justify-end mt-4">
                           <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                               Withdraw
                           </PrimaryButton>
                       </div>
                   </form>
               </div>
           </div>
        </div>

        <!-- Transactions Table -->
        <Table class="min-w-full bg-white shadow rounded-lg">
            <TableBody class="bg-gray-300 font-semibold">
                <TableRow>
                    <TableCell class="p-4">Date</TableCell>
                    <TableCell class="p-4">Type</TableCell>
                    <TableCell class="p-4">Amount</TableCell>
                    <TableCell class="p-4">Status</TableCell>
                </TableRow>
            </TableBody>
            <TableBody>
                <TableRow v-for="transaction in transactions" :key="transaction.id" class="border-b">
                    <TableCell class="p-4">{{ formatDate(transaction.created_at) }}</TableCell>
                    <TableCell class="p-4">{{ transaction.type }}</TableCell>
                    <TableCell class="p-4">{{ formatCurrency(transaction.amount) }}</TableCell>
                    <TableCell class="p-4">{{ transaction.status }}</TableCell>
                </TableRow>
                <TableRow v-if="transactions.length === 0">
                    <TableCell class="p-4 text-center" colspan="4">No transactions found.</TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <!-- Pagination Controls -->
<!--        <div class="flex justify-between items-center mt-4">-->
<!--            <Button @click="/* add previous page logic */" :disabled="/* disable condition */">-->
<!--                &laquo; Previous-->
<!--            </Button>-->
<!--            <span>Page 1 of 1</span>-->
<!--            <Button @click="/* add next page logic */" :disabled="/* disable condition */">-->
<!--                Next &raquo;-->
<!--            </Button>-->
<!--        </div>-->
    </div>
</template>

<style scoped>
</style>
