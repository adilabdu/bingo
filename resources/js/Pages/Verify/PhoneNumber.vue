<script setup>
import PinInput from "@/Components/shadcn/ui/pin-input/PinInput.vue";
import PinInputGroup from "@/Components/shadcn/ui/pin-input/PinInputGroup.vue";
import PinInputInput from "@/Components/shadcn/ui/pin-input/PinInputInput.vue";
import {ref} from "vue";
import Header from "@/Components/Header.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    expires_in: {
        type: Number,
        required: true
    }
})

const value = ref([])
function handleSubmit(e) {
    router.post('/verify/phone', {
        otp: e.join('')
    })
}
</script>

<template>

    <Header class="max-w-md">
        <template #default>
            <div class="font-semibold text-xl pb-1">Verify your Phone</div>
            <div class="text-xs font-light">
                You need to verify your phone number to continue.
                Please enter the verification code that was sent to your phone number.
            </div>
        </template>
    </Header>

    <p>{{ expires_in }}</p>

    <div class="w-full flex justify-center">
        <PinInput
            id="pin-input"
            v-model="value"
            placeholder="○"
            @complete="handleSubmit"
        >
            <PinInputGroup>
                <PinInputInput
                    v-for="(id, index) in 6"
                    :key="id"
                    :index="index"
                    class="border-slate-600"
                />
            </PinInputGroup>
        </PinInput>
    </div>


</template>

<style scoped>

</style>
