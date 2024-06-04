<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Header from "@/Components/Header.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import {ref, watch} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import ConfirmCartelaDrawer from "@/Views/Game/ConfirmCartelaDrawer.vue";
import {router, usePage} from "@inertiajs/vue3";
import {debounce} from "lodash";
import Loading from "@/Components/Loading.vue";

const numberOfCards = ref(1);

const updateNumberOfCards = (action) => {
    if (action === 'increment' && numberOfCards.value < 3) {
        numberOfCards.value++;
    } else if (action === 'decrement' && numberOfCards.value > 1) {
        numberOfCards.value--;
    }
}

const gameCategory = usePage().props.gameCategory;
const cartelaName = ref('');
const isLoading = ref(false);

watch(cartelaName, () => {
    getCartela();
}, {});

const getCartela = debounce(() => {
    isLoading.value = true;
    router.visit(cartelaName.value,{
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
    <AuthenticatedLayout>
        <div class="flex flex-col space-y-2 bg-gray-800 text-white rounded-lg p-3 capitalize">
            <div class="font-bold text-3xl"> {{gameCategory.amount}} Br</div>
            <div class="font-light text-sm">{{gameCategory.name}} X {{gameCategory.category}}</div>
        </div>
        <Header>
            <template #default>
                <div class="font-semibold text-xl pb-1">Customize your Cartela</div>
                <div class="text-xs font-light">Cartela is a set of playing cards. You can play with up to 3 cards!</div>
            </template>
        </Header>

        <div class="flex flex-col space-y-2 py-3">
            <InputLabel value="Please select number of cartela's"/>
            <div class="flex justify-between">
                <div  @click="updateNumberOfCards('decrement')"
                      class="bg-gray-800  text-white font-semibold font w-12 text-center flex items-center justify-center rounded-md">
                    -
                </div>
                <Input type="text" class=" w-8/12" v-model="numberOfCards"/>
                <div
                    @click="updateNumberOfCards('increment')"
                    class="bg-gray-800 text-white font-semibold font w-12 text-center flex items-center justify-center rounded-md">
                    +
                </div>
            </div>
        </div>


        <div class="flex flex-col space-y-2 py-3">
            <InputLabel value="Enter your lucky cartela number"/>
            <div class="flex">
                <div  @click="updateNumberOfCards('decrement')"
                      class="bg-blue-600 text-white font-semibold w-14 text-center flex items-center justify-center rounded-l-md">
                    <Loading v-if="isLoading" type="spinner" class="p-2" color="primary"/>
                    <span v-else>#</span>
                </div>
                <Input type="number" class=" !rounded-none !rounded-r-md w-full" v-model="cartelaName"/>
            </div>
            <div class="text-xs font-light">You can select from # 1 - 3000</div>
        </div>


        <ConfirmCartelaDrawer />
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
