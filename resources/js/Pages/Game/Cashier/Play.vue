<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {useGameDataStore} from "@/Stores/useGameDataStore.ts";
import {computed, onMounted, onUnmounted, ref, watch} from "vue";
import CheckCartelaSheet from "@/Views/Game/Cashier/CheckCartelaSheet.vue";
import {Volume2, VolumeX} from "lucide-vue-next";
import Loading from "@/Components/Loading.vue";
import {useDeviceSize} from "@/Composables/useSize.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    game: {
        type: Object,
        required: true
    },
    gamePlayersCount: {
        type: Number,
        required: true
    },
});

const pageProps = usePage().props;
const gameStore = useGameDataStore();

let pollRevealNumbers = null;

const game = computed(() => pageProps.game);
const batchIndex = computed(() => usePage().props.nextBatchIndex);
const drawnNumbers = computed(() => usePage().props.drawnNumbers);

const currentNumber = ref(null);
const currentIndex = ref(0)

const bingoNumbers = computed(() => {
    return {
        B: Array.from({length: 15}, (_, i) => i + 1),
        I: Array.from({length: 15}, (_, i) => i + 16),
        N: Array.from({length: 15}, (_, i) => i + 31),
        G: Array.from({length: 15}, (_, i) => i + 46),
        O: Array.from({length: 15}, (_, i) => i + 61),
    };
});

const revealedNumbers = ref([]);

async function fetchGameUpdates() {
    return new Promise((resolve, reject) => {
        router.get('/cashier/game/start', {
            game_id: game.value.id,
            game_category_id: game.value.game_category_id,
            batch_index: batchIndex.value
        }, {
            preserveState: true,
            onSuccess() {
                gameStore.addToDrawNumbers(drawnNumbers.value);
                resolve();
            },
            onError(error) {
                reject(error);
            }
        });
    });
}

function revealNumbers() {
    if (!isPaused.value) {
        const newNumber = Array.from(gameStore.drawNumbers)[gameStore.revealIndex];
        const newNumbers = Array.from(gameStore.drawNumbers).slice(0, gameStore.revealIndex + 1);
        newNumbers.forEach(number => {
            revealedNumbers.value.push([number]);
            currentNumber.value = newNumber;
        });
        currentIndex.value = gameStore.revealIndex;
        gameStore.addToRecentNumbers(currentNumber.value);
        gameStore.incrementRevealIndex();
        playSound();
    }
}

watch(() => gameStore.revealIndex, () => {
    if (gameStore.revealIndex > 75) {
        clearInterval(pollRevealNumbers);
    } else if (gameStore.revealIndex === (Array.from(gameStore.drawNumbers).length - 1)) {
        fetchGameUpdates();
    }
});

const deviceSize = useDeviceSize().deviceSize;

onMounted(() => {
    gameStore.clearGameData()
    if (game.value.status === 'completed') {
        router.get('/cashier/game/initiate');
    }
    if (drawnNumbers.value !== null)
        gameStore.addToDrawNumbers(drawnNumbers.value);
    else
        fetchGameUpdates()
    pollRevealNumbers = setInterval(revealNumbers, 5500);

    // Load all audio files
    Array.from(gameStore.drawNumbers).forEach(number => {
        new Audio(`/assets/sounds/numbers/${number}.aac`);
    });
});

onUnmounted(() => {
    clearInterval(pollRevealNumbers);
    gameStore.clearGameData()
});

const isPaused = ref(false);

function togglePause(isCheck = false) {
    if (isCheck)
        return isPaused.value = true;
    isPaused.value = !isPaused.value;
}

function isRevealed(number) {
    return revealedNumbers.value.some(arr => arr.includes(number));
}

const isLoading = ref(false);

function finishGame() {
    isLoading.value = true;
    router.post('/cashier/game/finish', {
        game_id: game.value.id
    }, {
        preserveState: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

const soundEnabled = ref(true);

function toggleSound() {
    soundEnabled.value = !soundEnabled.value;
}
function playSound() {
    const audio = new Audio(`/assets/sounds/numbers/${currentNumber.value}.aac`);
    const isLargeDevice = deviceSize.value === 'lg' || deviceSize.value === 'xl' || deviceSize.value === '2xl';

    if (soundEnabled.value && isLargeDevice) {
        audio.currentTime = 0;
        audio.play().catch(error => {
            console.log('Sound play failed:', error);
        });
    }
}
// Computed property to get the current number letter
const currentNumberLetter = computed(() => {
    if (currentNumber.value === null) return null;
    if (currentNumber.value <= 15) return 'B';
    if (currentNumber.value <= 30) return 'I';
    if (currentNumber.value <= 45) return 'N';
    if (currentNumber.value <= 60) return 'G';
    return 'O';
});

function repeatGame(){
    router.get(`/cashier/game/create/${props.game.game_category_id}`,{
        repeatGame: true,
        gameToRepeatId: props.game.id
    })
}

</script>

<template>
    <Loading is-full-screen v-if="isLoading"/>
    <div v-if="deviceSize ==='lg' || deviceSize ==='xl' || deviceSize ==='2xl'" class="flex w-full space-x-2 justify-evenly mx-auto">
        <div class="flex flex-col w-4/12 px-1">
            <div class="w-full h-64 flex items-center justify-center font-bold text-[13rem]" v-if="currentNumber">
                <span class="text-brand-secondary">{{ currentNumberLetter }}</span>
                <span class="text-6xl">-</span>
                <span>{{ currentNumber }}</span>
            </div>
            <div v-else class="text-7xl text-center font-bold max-w-sm">
                -
            </div>
            <div class="text-5xl font-bold flex items-center justify-center text-gray-600 h-36 max-w-sm">
                {{ currentIndex + 1 > 75 ? 75 : currentIndex }}/{{ drawnNumbers?.length }}
            </div>
            <div class="flex flex-col space-y-6 max-w-sm">
                <div
                    class="text-5xl font-bold uppercase bg-brand-primary text-white px-3 py-2 text-center rounded-lg cursor-pointer hover:scale-105 hover:shadow-xl"
                    :class="{ 'bg-brand-tertiary !text-black': !isPaused }"
                    @click="togglePause(false)"
                >
                    {{ isPaused ? 'Play' : 'Pause' }}
                </div>
                <CheckCartelaSheet @toggle-pause="togglePause(true)" :game="game" :current-index="currentIndex"/>
                <div @click="finishGame"
                     class="text-5xl font-bold uppercase bg-rose-600 text-white px-3 py-2 text-center rounded-lg cursor-pointer hover:scale-105 hover:shadow-xl">
                    Finish
                </div>
                <div @click="repeatGame"
                     class="text-5xl font-bold uppercase bg-blue-500 text-white px-3 py-2 text-center rounded-lg cursor-pointer hover:scale-105 hover:shadow-xl">
                    Repeat
                </div>
            </div>

        </div>
        <div class="flex flex-col justify-center w-8/12">
            <div v-for="letter in ['B', 'I', 'N', 'G', 'O']" :key="letter" class="text-center font-bold text-5xl">
                <div class="flex space-x-4 my-6 h-full pr-4 items-center">
                    <div
                        class="bg-brand-tertiary min-w-16 h-16 flex items-center justify-center rounded-sm mx-5 text-black">
                        {{ letter }}
                    </div>
                    <div
                        v-for="number in bingoNumbers[letter]"
                        :key="number"
                        :class="['min-w-12 w-full h-14 flex items-center justify-center  border-2 border-black px-2 rounded-lg font-bold text-3xl', isRevealed(number) ? 'bg-brand-primary text-white' : 'bg-white text-black']"
                    >
                        {{ number }}
                    </div>
                </div>
            </div>
            <div
                class="flex w-full divide-x divide-white items-center justify-evenly py-6 mt-10 border-4 bg-gray-800 text-white rounded-lg mx-auto">
                <div class="flex flex-col space-y-2 items-center w-2/5 ">
                    <div class="font-normal text-xl">Bet Amount</div>
                    <div class="text-6xl   font-bold">{{ game.game_category.amount }} Br</div>
                </div>
                <div class="flex flex-col space-y-2 items-center w-2/5">
                    <div class="font-normal text-xl">Winning Amount</div>
                    <div class="text-6xl   font-bold">{{ game.winner_net_amount }} Br</div>
                </div>
                <div class="flex flex-col cursor-pointer space-y-2 w-1/5 h-full justify-center items-center ">
                    <div @click="toggleSound()">
                        <Volume2 size="50" class="hover:scale-110 hover:text-brand-secondary" v-if="soundEnabled"/>
                        <VolumeX size="50" class="hover:scale-110 hover:text-brand-secondary" v-else/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="h-96 w-full flex flex-col justify-center items-center space-y-6 text-white">
        <div class="p-3 rounded-lg text-red-600 font-semibold text-center">
            Page Not Allowed on Mobile Devices, Try opening on Desktop or TV
        </div>

        <PrimaryButton @click="router.get('/cashier/game/initiate')" class="bg-brand-secondary capitalize !w-full">
            Go Back
        </PrimaryButton>
    </div>
</template>

<style scoped>

</style>
