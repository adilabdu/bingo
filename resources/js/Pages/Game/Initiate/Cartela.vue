<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import {computed, ref, watch} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import ConfirmCartelaDrawer from "@/Views/Game/ConfirmCartelaDrawer.vue";
import {router, usePage} from "@inertiajs/vue3";
import {debounce} from "lodash";
import Loading from "@/Components/Loading.vue";

const gameCategory = usePage().props.gameCategory;
// Todo: Add query params from url on refresh
const cartelaName = ref('');
const isLoading = ref(false);
const cartela = computed(() => usePage().props.cartela);
watch(cartelaName, () => {
    getCartela();
}, {});
const getCartela = debounce(() => {
    isLoading.value = true;
    router.visit('/game/initiate/'+gameCategory.id+'/' +cartelaName.value,{
        preserveState: true,
        replace: true,
        only: ['cartela'],
        onFinish: () => {
            isLoading.value = false;
        }
    });
}, 500);
</script>

<template>
        <div class="flex flex-col space-y-2 bg-brand-primary text-white rounded-lg p-3 capitalize">
            <div class="font-bold text-3xl"> {{gameCategory.amount}} Br</div>
            <div class="font-light text-sm">{{gameCategory.name}} X {{gameCategory.category}}</div>
        </div>

        <div class="flex flex-col space-y-3 py-3">
            <InputLabel value="Enter your lucky cartela number"/>
            <div class="flex">
                <div class="bg-brand-secondary text-white font-semibold w-14 text-center flex items-center justify-center rounded-l-md">
                    <Loading v-if="isLoading" type="spinner" class="p-2" color="primary"/>
                    <span v-else>#</span>
                </div>
                <Input type="number" class=" !rounded-none !rounded-r-md w-full" v-model="cartelaName"/>
            </div>
            <div class="text-xs font-light">You can select from # 1 - 3000</div>
        </div>

        <ConfirmCartelaDrawer :is-trigger-disabled="!cartelaName || !cartela" />
</template>

<style scoped>

</style>
