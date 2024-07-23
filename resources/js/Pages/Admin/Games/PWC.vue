<script setup>
import {computed, ref} from "vue";
import {RefreshCcw} from "lucide-vue-next";
import Header from "@/Components/Header.vue";
import {router} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {XCircle} from "lucide-vue-next";

const props = defineProps({
    games: {
        type: Object,
        required: true
    }
})


function winnerAmount(game) {
    const amount = Number(game.game_category.amount);
    const playersCount = game.players_count;
    const percentage = Number(game.cashier.branch.percent) / 100;
    return Math.round(amount * playersCount * (1 - percentage));
}
const isLoading = ref(false);
function refreshData() {
    isLoading.value = true;
    router.visit('/admin/pwc',{
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

const showModal = ref(false);
const closeModal = () => {
    showModal.value = false;
};

const name = ref('');
const rows = ref(1);
const gameId = ref(null);
function setup(value) {
    showModal.value = true;
    gameId.value = value;
}

const errorMessages = ref([]);
function routeToGame(game) {
    isLoading.value = true;
    router.post('/admin/pwc',{
        game_id: gameId.value,
        name: name.value,
        rows: rows.value
    }, {
        preserveState: true,
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        },
        onError:(e) => {
            errorMessages.value = Object.values(e).flat();
        },
        onSuccess: () => {
            closeModal();
        }
    });
}


</script>

<template>
    <div>
        <Loading v-if="isLoading" type="brand" is-full-screen/>
        <div class="flex justify-between pb-4 pt-2">
            <Header class="font-semibold" value="Pending Games"/>
            <div @click="refreshData" class="flex items-center space-x-2 text-xs bg-brand-secondary rounded-md shadow-md text-white px-2">
                <RefreshCcw class="w-3" />
                <span>Refresh</span>
            </div>
        </div>
        <div v-if="games.length" class="flex flex-col space-y-3">
            <div @click="setup(item.id)" class=" bg-gray-50 flex justify-between px-2 py-3 font-medium text-sm rounded-sm" v-for="item in games">
                <span>#{{ item.pwc ? item.pwc.cartela.name : '-' }}</span>
                <span>{{item.cashier.branch.name}}</span>
                <span>{{item.game_category.amount}}Br</span>
                <span>{{ winnerAmount(item) }} Br</span>
                <span>{{ item.players_count }} Ply</span>
            </div>
        </div>
        <div v-else class="font-semibold text-center py-5">
            No Games Found!
        </div>


        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <div class="bg-red-600 text-white text-xs text-center  flex flex-col space-y-1 p-2 rounded-md" v-if="errorMessages.length">
                    <div class="flex space-x-1 justify-center items-center py-1">
                        <XCircle class="text-white" size="18"/>
                        <span class="font-semibold text-base text-center">Error</span>
                    </div>
                    <span v-for="(message, index) in errorMessages" :key="index">* {{ message }}</span>
                </div>
                <div class="">
                    <InputLabel for="number" value="Name" class="text-black" />

                    <TextInput
                        v-model="name"
                        id="number"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Name"
                    />
                </div>

                <div class="">
                    <InputLabel for="rows" value="Rows" class="text-black" />

                    <TextInput
                        v-model="rows"
                        id="rows"
                        type="number"
                        class="mt-1 block w-full"
                        placeholder="Row"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        @click="routeToGame"
                    >
                      Submit
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>

</style>
