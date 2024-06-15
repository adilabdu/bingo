<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import {formatToPrice} from "@/lib/utils.js";
import {useForm, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import TransactionTable from "@/Views/Wallet/TransactionTable.vue";

defineProps({
    balance: {
        type: Number,
        required: true
    },
    transactions: {
        type: Object,
        required: true,
    }
})

const form = useForm({
    recipient: '',
    amount: '',
})

function submit() {
    form.post('/wallet/transfer', {
        preserveState: true,
        onSuccess: () => {
            form.reset()
        }
    })
}
</script>

<template>

    <AuthenticatedLayout>

        <Header>
            <template #default>
                <div class="font-semibold text-xl pb-1">Your Wallet</div>
                <div class="text-xs font-light">You can withdraw from or deposit to your wallet account. You can also transfer funds to another player.</div>
            </template>
        </Header>

        <div class="flex flex-col space-y-2 bg-gray-800 text-white rounded-lg p-3 capitalize">
            <div class="font-bold text-3xl"> {{ formatToPrice(balance) }} Br</div>
            <div class="font-light text-sm">Your Balance</div>
        </div>

        <div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <div class="font-semibold text-xl pb-1">Transfer Money</div>
            <div class="text-xs font-light pb-2">You can transfer money to another player using their phone number.</div>
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="recipient" value="Recipient Phone Number" />

                    <TextInput
                        id="recipient"
                        type="text"
                        class="mt-1 block w-full border"
                        v-model="form.recipient"
                        required
                        autofocus
                    />

                    <InputError class="mt-2" :message="form.errors.recipient" />
                </div>

                <div class="mt-4">
                    <InputLabel for="amount" value="Amount in Birr" />

                    <TextInput
                        id="amount"
                        type="text"
                        class="mt-1 block w-full border"
                        v-model="form.amount"
                        required
                    />

                    <InputError class="mt-2" :message="form.errors.amount" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Transfer Money
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <TransactionTable :transactions="transactions" />

    </AuthenticatedLayout>

</template>

<style scoped>

</style>
