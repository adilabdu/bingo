<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import {Input} from "@/Components/shadcn/ui/input/index.js";
import GradientBorder from "@/Components/GradientBorder.vue";
import {computed, ref, watch} from "vue";
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
    selectedCartelas: {
        type: Object,
        required: true
    },
    percentage: {
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
        addCartela(cartelaName.value)
    }
})

const winnerAmount = computed(() => {
    const amount = Number(props.gameCategory.amount);
    const playersCount = Number(selectedCartelas.value.length);
    const percentage = Number(props.percentage) / 100;
    return Math.round(amount * playersCount * (1 - percentage));
});

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

function addCartela(value) {
    isLoading.value = true;
    router.post('/cashier/game/add', {
        cartelaName: String(value),
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

function removeCartela(name) {
    isLoading.value = true;
    if (!name)
        return;

    router.delete(`/cashier/game/remove/${String(name)}/${props.game.id}`, {
        preserveState: true,
        onError: (e) => {
            errorMessages.value = Object.values(e).flat();
        },
        onFinish: () => {
            isLoading.value = false;
        }
    })
}

function isCartelaAdded(cartelaName) {
    return selectedCartelas.value.some(cartela => cartela.name === String(cartelaName));
}

const selectedCartelas = ref(props.selectedCartelas);
watch(() => props.selectedCartelas, () => {
    selectedCartelas.value = props.selectedCartelas;
}, {immediate: true});

Echo.private('cashier-players')
    .listen(`.cashier-players.${props.game.id}`, (e) => {
        if (e.startGame)
            startGame();
        selectedCartelas.value = e.selectedCartelas
    });
</script>

<template>
    <Loading is-full-screen v-if="isLoading" type="brand"/>

    <div class="flex flex-col space-y-10 min-h-screen lg:h-screen lg:pb-0">
        <div class="flex flex-col lg:flex-row w-full justify-between">
            <div class=" w-full lg:w-7/12 flex items-center justify-between flex-wrap bg-white pt-3 md:pt-0 px-5 rounded-lg lg:shadow-md">
                <div v-for="i in 50"
                     @click="isCartelaAdded(i) ? removeCartela(i) : addCartela(i)"
                     :class="['min-w-10 w-14 h-14 lg:min-w-16 lg:w-12 lg:h-16 mr-2 mb-2 lg:mr-5 flex items-center justify-center  border-2 border-black px-2 rounded-lg font-bold text-2xl lg:text-3xl cursor-pointer hover:bg-brand-tertiary hover:text-white',
                      isCartelaAdded(i) ? 'bg-brand-secondary text-white' : '']">
                    {{ i }}
                </div>
            </div>
            <GradientBorder class="hidden md:inline-block !h-fit !w-4/12">
                <template #default>
                    <div class="flex flex-wrap justify-between bg-white rounded-xl w-full h-full px-10">

                        <div class="w-full flex flex-col justify-evenly space-y-10 py-10 px-5">
                            <div class="flex flex-col space-y-4">
                                <InputLabel class="!text-2xl">Enter Cartela Number (1-50)</InputLabel>
                                <Input v-model="cartelaName" type="number"
                                       class="h-24 font-semibold text-5xl border-black"
                                       placeholder="Ex: 20, 16, 50, 44..."/>
                                <span class="bg-red-600 text-white p-3 rounded-lg w-fit"
                                      v-for="(message, index) in errorMessages"
                                      :key="index">* {{ message }}</span>

                            </div>

                            <div
                                class="flex flex-col justify-center text-center items-center space-y-10  text-xl w-full px-10 py-6 rounded-lg font-bold"
                                :class="selectedCartelas.length <= 1 ?'bg-red-600 text-white': 'bg-brand-secondary text-white'">
                                <span class="text-6xl">{{ selectedCartelas.length }} Players Joined</span>
                            </div>
                        </div>

                    </div>
                </template>
            </GradientBorder>
        </div>
        <div class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row justify-between min-w-full items-center h-1/5">
            <div
                class="flex lg:hidden flex-col justify-center text-center items-center space-y-2 text-xs w-full px-4 py-2 rounded-lg font-bold"
                :class="selectedCartelas.length <= 1 ?'bg-red-600 text-white': 'bg-brand-secondary text-white'">
                <span class="text-sm font-normal">Players Joined</span>
                <span class="text-5xl font-bold">{{ selectedCartelas.length }} </span>
            </div>

            <div
                @click="startGame"
                :class="selectedCartelas.length <= 1 ?'bg-gray-800 opacity-40 cursor-not-allowed': 'bg-brand-secondary hover:scale-105 cursor-pointer opacity-100'"
                class=" border-8 border-black hover:shadow-2xl shadow-md rounded-xl text-white w-full lg:w-7/12 font-bold py-3 text-3xl lg:text-5xl text-center h-3/5 flex items-center justify-center">
                START BINGO
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
