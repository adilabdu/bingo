<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {useGameDataStore} from "@/Stores/useGameDataStore.ts";
import {computed, onMounted, onUnmounted, ref, watch} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CheckCartelaSheet from "@/Views/Game/Cashier/CheckCartelaSheet.vue";

const props = defineProps({
    game: {
        type: Object,
        required: true
    },
    gamePlayersCount: {
        type: Number,
        required: true
    },
    drawnNumbers: {
        type: Array
    }
});

const pageProps = usePage().props;
const gameStore = useGameDataStore();

let pollRevealNumbers = null;

const game = computed(() => pageProps.game);
const batchIndex = computed(() => usePage().props.nextBatchIndex);

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
            batch_index: batchIndex.value
        }, {
            preserveState: true,
            onSuccess() {
                gameStore.addToDrawNumbers(props.drawnNumbers);
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
    }
}

watch(() => gameStore.revealIndex, () => {
    if (gameStore.revealIndex > 75) {
        clearInterval(pollRevealNumbers);
    } else if (gameStore.revealIndex === (Array.from(gameStore.drawNumbers).length - 1)) {
        fetchGameUpdates();
    }
});

onMounted(() => {
    if (game.value.status === 'completed') {
        router.get('/game/initiate');
    }

    gameStore.addToDrawNumbers(props.drawnNumbers);
    pollRevealNumbers = setInterval(revealNumbers, 2000);
});

onUnmounted(() => {
    clearInterval(pollRevealNumbers);
});

const isPaused = ref(false);

function togglePause() {
    isPaused.value = !isPaused.value;
}
function isRevealed(number) {
    return revealedNumbers.value.some(arr => arr.includes(number));
}
</script>

<template>
    <div class="flex w-full space-x-2 justify-evenly mx-auto">
        <div class="flex flex-col w-2/12">
            <div class="w-full h-64 flex items-center justify-center text-center  font-bold text-[13rem]">
                {{ currentNumber ?? '-' }}
            </div>
            <div class="text-5xl font-bold flex items-center justify-center text-gray-600 h-36  text-center">{{currentIndex}}/75</div>
            <div class="flex flex-col space-y-6">
                <div
                    class="text-5xl font-bold uppercase bg-brand-tertiary px-3 py-2 text-center rounded-lg cursor-pointer"
                    :class="{ 'opacity-50': isPaused }"
                    @click="togglePause"
                >
                    Pause
                </div>
                <div
                    class="text-5xl font-bold uppercase bg-brand-primary text-white px-3 py-2 text-center rounded-lg cursor-pointer"
                    :class="{ 'opacity-50': !isPaused }"
                    @click="togglePause"
                >
                    Play
                </div>
                <CheckCartelaSheet :game="game" :current-index="currentIndex"/>
                <div class="text-5xl font-bold uppercase bg-rose-600 text-white px-3 py-2 text-center rounded-lg">
                    Finish
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
                <div class="flex flex-col space-y-2 items-center w-1/3 ">
                    <div class="font-normal text-xl">Bet Amount</div>
                    <div class="text-6xl   font-bold">{{ game.game_category.amount }} Br</div>
                </div>
                <div class="flex flex-col space-y-2 items-center w-1/3">
                    <div class="font-normal text-xl">Winning Amount</div>
                    <div class="text-6xl   font-bold">{{ game.winner_net_amount }} Br</div>
                </div>
                <div class="flex flex-col space-y-2 w-1/3 items-center ">
                    <div class="font-normal text-xl">Total Players</div>
                    <div class="text-6xl   font-bold">{{ gamePlayersCount }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
