<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { useGameDataStore } from "@/Stores/useGameDataStore.ts";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
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
const isLoading = ref(false);

// Define bingo numbers grouped correctly by their columns
const bingoNumbers = computed(() => {
    return {
        B: Array.from({ length: 15 }, (_, i) => i + 1),
        I: Array.from({ length: 15 }, (_, i) => i + 16),
        N: Array.from({ length: 15 }, (_, i) => i + 31),
        G: Array.from({ length: 15 }, (_, i) => i + 46),
        O: Array.from({ length: 15 }, (_, i) => i + 61),
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
    currentNumber.value = Array.from(gameStore.drawNumbers)[gameStore.revealIndex];
    revealedNumbers.value.push(currentNumber.value);
    gameStore.addToRecentNumbers(currentNumber.value);
    gameStore.incrementRevealIndex();
}

watch(() => gameStore.revealIndex, () => {
    if (gameStore.revealIndex > 74) {
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
</script>

    <template>
        <div class="flex w-full space-x-2 justify-evenly">
           <div class="flex flex-col w-2/12">
            <div class="w-full flex items-center justify-center text-center  font-bold text-[13rem] rounded-full">
                {{ currentNumber }}45
            </div>
            <div class="flex flex-col space-y-6">
                <div class="text-5xl font-bold uppercase bg-brand-tertiary px-3 py-2 text-center rounded-lg">Pause</div>
                <div class="text-5xl font-bold uppercase bg-brand-primary text-white px-3 py-2 text-center rounded-lg">
                    Play
                </div>
                <CheckCartelaSheet/>
                <div class="text-5xl font-bold uppercase bg-rose-600 text-white px-3 py-2 text-center rounded-lg">
                    Finish
                </div>
            </div>

           </div>
            <div class="flex flex-col justify-center w-8/12">
                <div v-for="letter in ['B', 'I', 'N', 'G', 'O']" :key="letter" class="text-center font-bold text-5xl">
                    <div class="flex space-x-4 my-6 h-full pr-4 items-center">
                        <div class="bg-brand-tertiary min-w-16 h-16 flex items-center justify-center rounded-sm mx-5 text-black">
                            {{ letter }}
                        </div>
                        <div
                            v-for="number in bingoNumbers[letter]"
                            :key="number"
                            :class="['min-w-14 w-full h-16 flex items-center justify-center  border-2 border-black px-2 rounded-lg font-bold text-4xl', revealedNumbers.includes(number) ? 'bg-brand-primary text-white' : 'bg-white text-black']"
                        >
                            {{ number }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

<style scoped>

</style>
