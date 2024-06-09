<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import {router, usePage} from "@inertiajs/vue3";
import moment from 'moment';

const { game } = usePage().props;

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

Echo.private('start-game')
    .listen(`.start-game.${game.id}`, (e) => {
        router.get('/game/play',{
            'game_id': game.id,
        });
    });
</script>


<template>
    <AuthenticatedLayout>
        <div class="flex flex-col divide-y space-y-14 h-screen">
            <div class="flex flex-col items-center justify-center font-bold text-[7rem] px-4 space-y-4 h-2/3">
                <span>{{ remainingSeconds }}<span class="uppercase font-light text-xl">sec</span></span>
                <div v-if="remainingSeconds > 0" class="text-lg font-light text-center">
                    Waiting for other players to join, the game will start when the timer ends.
                </div>
                <div v-else class="text-lg font-light text-center">
                    The game is starting...
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>



<style scoped>

</style>
