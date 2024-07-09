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
        <div class="flex items-center justify-center h-fit">
            <img class="w-24 h-20 object-cover " src="../../../../../public/assets/images/logo.png">
            <div class="text-center text-brand-primary font-bold text-6xl">Kiwi <span
                class="text-brand-secondary">Bingo</span>
            </div>
        </div>

        <GradientBorder class="h-3/6">
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
                            <span>{{ winnerAmount }} Birr</span>
                        </div>
                    </div>
                    <div class="w-7/12 flex flex-col justify-evenly space-y-10 py-10 px-5">
                        <div class="flex flex-col space-y-4">
                            <InputLabel class="!text-2xl">Enter Cartela Number (1-50)</InputLabel>
                            <Input v-model="cartelaName" type="number" class="h-24 font-semibold text-5xl border-black"
                                   placeholder="Ex:20,16,50,44..."/>
                            <span class="text-red-600" v-for="(message, index) in errorMessages"
                                  :key="index">* {{ message }}</span>

                        </div>

                        <div
                            class="flex flex-col items-center space-y-10 bg-gray-800 text-white text-xl w-full px-10 py-6 rounded-lg font-bold">
                            <span class="text-6xl">{{gamePlayersCount}} Players Joined</span>
                        </div>
                    </div>

                </div>
            </template>
        </GradientBorder>

        <div class="flex items-center h-1/5">
            <div
                @click="startGame"
                class="bg-gray-800 border-8 border-black cursor-pointer hover:scale-105 hover:shadow-2xl shadow-md rounded-xl text-white w-fit font-bold text-7xl px-5 py-4 mx-auto">
                START BINGO
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
