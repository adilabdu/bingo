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
import TransferMoney from "@/Views/Wallet/TransferMoney.vue";
import YourBalance from "@/Views/Wallet/YourBalance.vue";
import WalletPageHeader from "@/Views/Wallet/WalletPageHeader.vue";

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
</script>

<template>
    <AuthenticatedLayout>
        <WalletPageHeader />
        <YourBalance :balance="balance" />
        <TransferMoney />
        <TransactionTable :transactions="transactions" />
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
