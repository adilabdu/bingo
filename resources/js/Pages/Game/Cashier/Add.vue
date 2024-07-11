<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import GradientBorder from "@/Components/GradientBorder.vue";
import {computed, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";

const props = defineProps({
    gameCategory: {
        type: Object,
        required: true
    },
    game: {
        type: Object,
        required: true
    },
    gamePlayersCount: {
        type: Number,
        required: true
    }
})

const cartelaName = ref(null);
const errorMessages = ref([]);
const isLoading = ref(false);

window.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        errorMessages.value = [];
        isLoading.value = true;
        router.post('/cashier/game/add', {
            cartelaName: String(cartelaName.value),
            gameId: props.game.id
        }, {
            preserveState: true,
            onError: (e) => {
                errorMessages.value = Object.values(e).flat();
            },
            onFinish: () => {
                isLoading.value = false;
            },
            onSuccess: () => {
                cartelaName.value = null;
            }
        })
    }
})

const winnerAmount = computed(() => Math.round(Number(props.gameCategory.amount) * Number(props.gamePlayersCount) - Number(props.gameCategory.amount) * Number(props.gamePlayersCount) * 0.1));

function startGame() {
    isLoading.value = true;
    router.get('/cashier/game/start', {
        game_id: props.game.id,
        game_category_id: props.gameCategory.id
    }, {
        preserveState: true,
        onError: (e) => {
            errorMessages.value = Object.values(e).flat();
        },
        onFinish: () => {
            isLoading.value = false;
        }
    })
}
</script>

<template>
    <Loading is-full-screen v-if="isLoading"/>
    <div class="flex flex-col space-y-6 min-h-screen h-screen">
        <GradientBorder class="h-3/5">
            <template #default>
                <div class="flex justify-between bg-white rounded-xl w-full h-full px-10">
                    <div class="min-h-full flex flex-col justify-evenly space-y-10 py-10 px-5">
                        <div
                            class="flex w-full flex-col h-32 space-y-2 items-center justify-center bg-gray-800 text-white px-4 rounded-lg shadow-2xl font-bold text-7xl">
                            <span class="font-normal text-2xl">Game</span>
                            <span>{{ gameCategory.amount }} Birr</span>
                        </div>
                        <div
                            class="flex w-full flex-col h-32 space-y-2 items-center justify-center bg-gray-800 text-white px-4 rounded-lg shadow-2xl font-bold text-7xl">
                            <span class="font-normal text-2xl"> Winner Amount</span>
                            <span v-if="gamePlayersCount > 1">{{ winnerAmount }} Birr</span>
                            <span v-else>-</span>
                        </div>
                    </div>
                    <div class="w-7/12 flex flex-col justify-evenly space-y-10 py-10 px-5">
                        <div class="flex flex-col space-y-4">
                            <InputLabel class="!text-2xl">Enter Cartela Number (1-50)</InputLabel>
                            <Input v-model="cartelaName" type="number" class="h-24 font-semibold text-5xl border-black"
                                   placeholder="Ex: 20, 16, 50, 44..."/>
                            <span class="bg-red-600 text-white p-3 rounded-lg w-fit" v-for="(message, index) in errorMessages"
                                  :key="index">* {{ message }}</span>

                        </div>

                        <div
                            class="flex flex-col items-center space-y-10  text-xl w-full px-10 py-6 rounded-lg font-bold" :class="gamePlayersCount <= 1 ?'bg-red-600 text-white': 'bg-brand-secondary text-white'">
                            <span class="text-6xl">{{gamePlayersCount}} Players Joined</span>
                        </div>
                    </div>

                </div>
            </template>
        </GradientBorder>

        <div class="flex items-center h-1/5">
            <div
                @click="startGame"
                :class="gamePlayersCount <= 1 ?'bg-gray-800 opacity-40 cursor-not-allowed': 'bg-brand-secondary hover:scale-105 cursor-pointer opacity-100'"
                class=" border-8 border-black hover:shadow-2xl shadow-md rounded-xl text-white w-fit font-bold text-7xl px-5 py-4 mx-auto">
                START BINGO
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
