<script setup>
import {ref, onMounted, onUnmounted, computed, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import moment from 'moment';
import {Input} from "@/Components/shadcn/ui/input/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import ConfirmCartelaDrawer from "@/Views/Game/ConfirmCartelaDrawer.vue";
import {debounce} from "lodash";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Frown} from "lucide-vue-next";

const game  = computed(() => usePage().props.game);
const gameCategory = usePage().props.gameCategory;
const cartela = computed(() => usePage().props.cartela);
const remainingSeconds = ref(usePage().props.remainingSeconds);

const updateRemainingSeconds = () => {
    if (remainingSeconds.value > 0) {
        remainingSeconds.value--;
    }
};

let intervalId;

onMounted(() => {
    updateRemainingSeconds();  // Initialize the current time
    intervalId = setInterval(updateRemainingSeconds, 1000);  // Update every second
});

onUnmounted(() => {
    clearInterval(intervalId);  // Clear the interval when the component unmounts
});

const isGameValidToStart = ref(true);
const gameMessage = ref('');

Echo.private('start-game')
    .listen(`.start-game.${game.value.id}`, (e) => {
        if (e.isGameValidToStart){
            return router.get('/game/play',{
                'game_id': game.value.id,
                'batch_index': 0,
            });
        }
        isGameValidToStart.value = false;
        gameMessage.value = "No other players joined the game. The game will be cancelled. Please join another game.";
    });

function routeToGameMenu() {
    router.visit('/game/initiate');
}

const cartelaName = ref(cartela.value.name);
function routeToRepeatGame() {
    router.get('/game/join',{
        'game_category_id': gameCategory.id,
        'cartela_id': cartela.value.id,
    },);
}

const isLoading = ref(false);
watch(cartelaName, () => {
    getCartela();
}, {immediate: false});

const getCartela = debounce(() => {
    isLoading.value = true;
    router.get('/game/initiate/'+gameCategory.id+'/' +cartelaName.value,{
        page:"Game/Initiate/Join"
    },{
        preserveState: true,
        replace: true,
        only: ['cartela'],
        onFinish: () => {
            isLoading.value = false;
        }
    });
}, 500);

const totalPlayers = ref(usePage().props.totalPlayers);
Echo.private('game-players')
    .listen(`.game-players.${game.value.id}`, (e) => {
        totalPlayers.value = e.totalPlayers;
    });
</script>


<template>
    <Loading v-if="isLoading" is-full-screen/>
    <div class="flex flex-col divide-y space-y-14 h-screen w-full md:max-w-md  mx-auto">
        <div class="flex flex-col items-center justify-center font-bold text-[7rem] px-4 pt-2 space-y-6 h-2/3 w-full">
            <span v-if="remainingSeconds > 0 " class="bg-brand-secondary text-white text-lg flex items-center justify-center font-medium rounded-lg px-4 py-1 space-x-3">
                <span class="text-5xl font-bold">{{ totalPlayers }}</span> <span>PLAYERS JOINED</span>
            </span>
            <span v-if="isGameValidToStart">{{ remainingSeconds }}<span class="uppercase font-light text-xl">sec</span></span>
            <div v-if="remainingSeconds > 0 && isGameValidToStart" class="text-lg font-light text-center">
                Waiting for other players to join, the game will start when the timer ends.
            </div>
            <div v-else-if="remainingSeconds < 1 && isGameValidToStart" class="text-lg font-light text-center">
                The game is starting...
            </div>

            <div v-else-if="!isGameValidToStart" class="text-xl font-normal text-center flex flex-col space-y-10 py-5">
                <Frown class="text-black mx-auto" size="70"/>
                <span class="font-medium">{{ gameMessage}}</span>
                <div class="flex flex-col space-y-4 p-4 border rounded-lg border-brand-primary">
                    <InputLabel value="Enter Cartela Number" class="text-start"/>
                    <Input type="text" class="w-full !border !border-black" placeholder="Enter Cartela Number" v-model="cartelaName"/>
                    <div class="flex justify-between w-full">
                        <div class="w-fit">
                            <ConfirmCartelaDrawer @start-bingo="isGameValidToStart = true" @click="getCartela" trigger-button-text="View Cartela" :is-trigger-disabled="!cartelaName || !cartela" />
                        </div>

                        <PrimaryButton @click="routeToRepeatGame"  class="bg-brand-secondary capitalize w-fit">
                            Play Again
                        </PrimaryButton>
                    </div>

                </div>
             </div>
            <div class="py-3 rounded-lg flex justify-between items-center divide-white divide-x text-white bg-brand-primary w-full">
                        <span class="w-6/12 text-center flex flex-col items-center space-y-2">
                            <span class="font-bold text-3xl">#{{ cartelaName }}</span>
                            <span class="text-sm">
                                Cartela No
                            </span>
                        </span>
                <span class="w-6/12 text-center flex flex-col items-center">
                            <span class="font-bold text-3xl">
                            {{gameCategory.amount}} <span class="font-light text-xl">Br</span>
                            </span>
                            <span class="font-light text-sm">Category</span>
                        </span>
            </div>

            <PrimaryButton v-if="!isGameValidToStart" @click="routeToGameMenu" class="bg-brand-tertiary !text-black text-lg font-medium capitalize w-full">Go To Game Menu</PrimaryButton>
        </div>
    </div>
</template>



<style scoped>

</style>
