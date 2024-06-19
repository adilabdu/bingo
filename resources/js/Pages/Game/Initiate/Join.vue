<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import {router, usePage} from "@inertiajs/vue3";
import moment from 'moment';
import {Button} from "@/Components/shadcn/ui/button/index.js";

const { game } = usePage().props;
const gameCategory = usePage().props.gameCategory;
const cartela = usePage().props.cartela;

const currentTime = ref(moment());
const scheduledTime = ref(moment(game.scheduled_at));

const remainingSeconds = computed(() => {
    if (!scheduledTime.value.isValid()) {
        // Todo: Handle error
        console.error("Invalid scheduled time:", game.scheduled_at);
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
    .listen(`.start-game.${game.id}`, (e) => {
        if (e.isGameValidToStart){
           return router.get('/game/play',{
               'game_id': game.id,
               'is_valid_request': true,
            });
        }
        isGameValidToStart.value = false;
        gameMessage.value = "No other players joined the game. The game will be cancelled. Please join another game.";
    });

function routeToGameMenu() {
    router.visit('/game/initiate');
}

function routeToRepeatGame() {
    router.get('/game/join',{
        'game_category_id': gameCategory.id,
        'cartela_id': cartela.name,
    });
}
</script>


<template>
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
                    <Button @click="routeToRepeatGame"  class="bg-gray-800 text-white text-lg font-medium uppercase w-full">
                        Play Again
                    </Button>
                    <Button @click="routeToGameMenu" class="bg-gradient-to-l from-blue-600 to-sky-600 text-white text-lg font-medium capitalize w-full">
                        Go To Game Menu
                    </Button>
                </div>
                <div v-if="isGameValidToStart" class="py-3 rounded-lg flex justify-between items-center divide-white divide-x text-white bg-gradient-to-br from-blue-600 to-sky-600 w-full">
                        <span class="w-6/12 text-center flex flex-col items-center space-y-2">
                            <span class="font-bold text-3xl">#{{ cartela?.name }}</span>
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
