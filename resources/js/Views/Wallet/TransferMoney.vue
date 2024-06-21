<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import {ref} from "vue";

const props = defineProps({
    flash: {
        type: Object,
        required: false,
        default: () => ({ error: null, success: null, info: null })
    }
})

const form = useForm({
    recipient: '',
    amount: '',
})

const isLoading = ref(false);
function submit() {
    isLoading.value = true;
    form.recipient = `+251${form.recipient}`;
    form.post('/wallet/transfer', {
        preserveState: true,
        onSuccess: () => {
            form.reset()
        },
        onFinish: () => {
            isLoading.value = false
        }
    })
}
</script>

<template>
    <Loading v-if="isLoading" is-full-screen/>
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <div class="font-semibold text-xl pb-1">Transfer Money</div>
        <div class="text-xs font-light pb-2">You can transfer money to another player using their phone number.</div>

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

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="recipient" value="Recipient Phone Number" />
                <div class="flex items-center font-semibold min-h-full">
                    <div class="bg-brand-primary text-white px-3 py-3.5 h-full  rounded-l-lg min-h-full ">+251</div>
                    <TextInput
                        id="recipient"
                        type="text" class="block w-full !bg-gray-50 !border-black !border !rounded-none !rounded-r-lg"
                        v-model="form.recipient"
                        required
                        autofocus
                    />
                </div>
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

</template>

<style scoped>

</style>
