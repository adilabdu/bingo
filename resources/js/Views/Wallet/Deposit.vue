<template>
    <Loading v-if="isLoading" is-full-screen/>
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <div class="font-semibold text-xl pb-1">Deposit Funds</div>
        <div class="text-xs font-light pb-2">Enter the amount you wish to deposit into your account.</div>

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

        <form @submit.prevent="submitDeposit">
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

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Deposit
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Loading from "@/Components/Loading.vue";

const props = defineProps({
    flash: {
        type: Object,
        required: false,
        default: () => ({ error: null, success: null, info: null })
    }
});

const form = useForm({
    amount: ''
});

const isLoading = ref(false);

const submitDeposit = () => {
    isLoading.value = true;
    form.post('/chapa/payment/initiate', {
        preserveState: true,
        onSuccess: () => {
            form.reset();
            // Optionally, you can also update the UI to show a success message
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
}
</script>

<style scoped>
/* You can add any additional styles if needed */
</style>

