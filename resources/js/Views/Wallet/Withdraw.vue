<template>
    <Loading v-if="isLoading" is-full-screen/>
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <div class="font-semibold text-xl pb-1">Withdraw Funds</div>
        <div class="text-xs font-light pb-2">Enter the details for the withdrawal.</div>

        <div v-if="flash.error" class="w-full rounded-md bg-red-100 p-4 mb-4">
            <h4 class="text-sm text-red-700 font-medium">
                {{ flash.error }}
            </h4>
        </div>

        <div v-if="flash.success" class="w-full rounded-md bg-green-100 p-4 mb-4">
            <h4 class="text-sm text-green-700 font-medium">
                {{ flash.success }}
            </h4>
        </div>

        <form @submit.prevent="submitWithdraw">
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
                <InputLabel for="recipient_bank" value="Recipient Bank" />
                <select v-model="form.recipient_bank" class="mt-1 block w-full border py-3 border border-gray-900 focus:border-gray-800 !focus:ring-gray-800 rounded-lg shadow-sm">
                    <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                        {{ bank.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.recipient_bank" />
            </div>

            <div class="mt-4">
                <InputLabel for="recipient_account" value="Recipient Account" />
                <TextInput
                    id="recipient_account"
                    type="text"
                    class="mt-1 block w-full border"
                    v-model="form.recipient_account"
                    required
                />
                <InputError class="mt-2" :message="form.errors.recipient_account" />
            </div>

            <div class="mt-4">
                <InputLabel for="recipient_name" value="Recipient Name" />
                <TextInput
                    id="recipient_name"
                    type="text"
                    class="mt-1 block w-full border"
                    v-model="form.recipient_name"
                    required
                />
                <InputError class="mt-2" :message="form.errors.recipient_name" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Withdraw
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Loading from "@/Components/Loading.vue";

const props = defineProps({
    flash: {
        type: Object,
        required: false,
        default: () => ({error: null, success: null, info: null})
    },
    banks: {
        type: Array,
        required: false,
        default: () => []
    },
    error: {
        type: String,
        required: false,
        default: ''
    }
});

const form = useForm({
    amount: '',
    recipient_bank: '',
    recipient_account: '',
    recipient_name: '',
});

const isLoading = ref(false);
const banks = ref(props.banks);
const error = ref(props.error);

const submitWithdraw = () => {
    isLoading.value = true;
    form.post('/chapa/withdraw/initiate', {
        preserveState: true,
        onSuccess: () => {
            form.reset();
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<style scoped>
</style>
