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
import Deposit from "@/Views/Wallet/Deposit.vue";
import Withdraw from "@/Views/Wallet/Withdraw.vue";
import {computed} from "vue";

const { props } = usePage();
const banks = computed(() => props.banks);

defineProps({
    balance: {
        type: Number,
        required: true
    },
    transactions: {
        type: Object,
        required: true,
    },
    flash: {
        type: Object,
        required: false,
        default: () => ({})
    }
})
</script>

<template>
    <WalletPageHeader />
    <YourBalance :balance="balance" />
    <Deposit />
    <TransferMoney :flash="flash" />
    <Withdraw :banks="banks" :flash="flash" />
    <TransactionTable :transactions="transactions" />
</template>

<style scoped>
</style>
