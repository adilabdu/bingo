<script setup>
import {
    DrawerContent,
    DrawerOverlay,
    DrawerPortal,
    DrawerRoot, DrawerTrigger,
} from "vaul-vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Loading from "@/Components/Loading.vue";

const emit = defineEmits(['success']);
const props = defineProps({
    isDrawerOpen: {
        type: Boolean,
        default: false
    },
    agent: {
        type: Number,
        required: true
    },
});

const isDrawerOpen = ref(props.isDrawerOpen);

function setDrawerOpen(val) {
    isDrawerOpen.value = val;
}

const amount = ref();
const isLoading = ref(false);
function topUp(){
    isLoading.value = true;
    router.post('/agent/top-up', {
        amount: amount.value,
        agent_id: props.agent.id
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            amount.value = '';
            setDrawerOpen(false)
            emit('success')
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
}
</script>

<template>
    <Loading v-if="isLoading" is-full-screen type="brand"/>
    <DrawerRoot
        :open="isDrawerOpen"
        should-scale-background
        @update:open="setDrawerOpen"
    >
        <DrawerTrigger>
            <PrimaryButton class="!h-11 !bg-brand-secondary w-full">TopUp Agent Balance</PrimaryButton>
        </DrawerTrigger>
        <DrawerPortal>
            <DrawerOverlay class="z-50 fixed bg-black/40 inset-0"/>

            <DrawerContent
                class="bg-gray-100 flex flex-col space-y-6 rounded-t-xl !min-h-full h-[calc(100%-(env(safe-area-inset-top)+51px+1rem))] fixed bottom-0 left-0 right-0 z-[60]"
            >
                <div class="overflow-y-auto flex flex-col space-y-4 w-full px-5 py-4 sm:max-w-lg mx-auto text-black">
                    <div class="mt-4 mx-auto w-14 h-1.5 flex-shrink-0 rounded-full bg-gray-300 mb-5"/>

                    <div class="text-center font-semibold text-4xl pb-4">Top Up Balance</div>
                    <InputLabel value="Enter Amount" class="w-full"/>
                    <TextInput type="number" placeholder="Enter Amount" v-model="amount"/>
                    <PrimaryButton @click="topUp" class="!h-11 !bg-brand-secondary w-full">Top Up</PrimaryButton>
                </div>
            </DrawerContent>
        </DrawerPortal>
    </DrawerRoot>
</template>

<style scoped></style>
