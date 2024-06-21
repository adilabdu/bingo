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

const game  = computed(() => usePage().props.game);
const gameCategory = usePage().props.gameCategory;
const cartela = computed(() => usePage().props.cartela);

const currentTime = ref(moment());
const scheduledTime = computed(() => moment(game.value.scheduled_at));

const remainingSeconds = computed(() => {
    if (!scheduledTime.value.isValid()) {
        // Todo: Handle error
    }
    const duration = moment.duration(scheduledTime.value.diff(currentTime.value));
    return duration.asSeconds() > 0 ? Math.floor(duration.asSeconds()) : 0;
});

const updateCurrentTime = () => {
    currentTime.value = moment();
};

let intervalId;

onMounted(() => {
    updateCurrentTime();  // Initialize the current time
    intervalId = setInterval(updateCurrentTime, 1000);  // Update every second
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
                'is_valid_request': true,
            });
        }
        console.log("Game is not valid to start", e);
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
        'cartela_id': cartelaName.value,
    },{
        onError: (error) => {
            console.error(error);
        }
    });
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
</script>


<template>
    <Loading v-if="isLoading" is-full-screen/>
    <div class="flex flex-col divide-y space-y-14 h-screen">
        <div class="flex flex-col items-center justify-center font-bold text-[7rem] px-4 space-y-4 h-2/3">
            <span v-if="isGameValidToStart">{{ remainingSeconds }}<span class="uppercase font-light text-xl">sec</span></span>
            <div v-if="remainingSeconds > 0 && isGameValidToStart" class="text-lg font-light text-center">
                Waiting for other players to join, the game will start when the timer ends.
            </div>
            <div v-else-if="remainingSeconds < 1 && isGameValidToStart" class="text-lg font-light text-center">
                The game is starting...
            </div>

            <div v-else-if="!isGameValidToStart" class="text-xl font-normal text-center flex flex-col space-y-10">
                    <span>
                        {{ gameMessage}}
                    </span>
                <div class="flex flex-col space-y-4 border-2 border-black rounded-lg p-4">
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
                <PrimaryButton @click="routeToGameMenu" class="bg-brand-tertiary !text-black text-lg font-medium capitalize w-full">Go To Game Menu</PrimaryButton>
            </div>
            <div v-if="isGameValidToStart" class="py-3 rounded-lg flex justify-between items-center divide-white divide-x text-white bg-brand-primary w-full">
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
        </div>
    </div>
</template>



<style scoped>

</style>
