<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import Loading from "@/Components/Loading.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import {ref, watch} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {router} from "@inertiajs/vue3";
import CashierBingoBoard from "@/Views/Game/CashierBingoBoard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ConfirmGuestCartelaDrawer from "@/Views/Game/ConfirmGuestCartelaDrawer.vue";
import {debounce} from "lodash";

const props = defineProps({
    cartela: Object
})

const isLoading = ref(false);
const isDrawerOpen = ref(false);
const cartelaName = ref(props.cartela.name ?? '');
function getCartela() {
    isLoading.value = true;
    router.visit(`/cartela/${cartelaName.value}`,{
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

const search = debounce(() => {
    getCartela();
}, 1000);

watch(cartelaName, () => {
   search()
});

watch(() => props.cartela, () => {
    if (props.cartela) {
        isDrawerOpen.value = true;
    }
});
</script>

<template>
<div class="flex flex-col space-y-4">
    <Loading v-if="isLoading" type="brand" is-full-screen/>
    <div class="text-3xl font-semibold">Add Cartela</div>
    <div class="flex flex-col space-y-3 py-3">
        <InputLabel value="Enter your lucky cartela number"/>
        <div class="flex">
            <div class="bg-brand-secondary text-white font-semibold w-14 text-center flex items-center justify-center rounded-l-md">
                <span>#</span>
            </div>
            <Input type="number" class=" !rounded-none !rounded-r-md w-full" v-model="cartelaName"/>
        </div>
        <div class="text-xs font-light">You can select from # 1 - 50</div>
    </div>
    <ConfirmGuestCartelaDrawer v-if="cartela && !isLoading" :is-drawer-open="true" />

    </div>
</template>

<style scoped>

</style>
