import {defineStore} from "pinia";
import {Ref, ref} from "vue";
import {RemovableRef, useStorage} from "@vueuse/core";

interface GameStore {
    clickedNumbers: Array<(number|string)>;
    recentNumbers: Array<number>;
    drawNumbers: Array<number>;
    revealIndex: number;
}

export const useGameDataStore = defineStore('gameDataStore', () => {

    const persistedGameData: RemovableRef<GameStore> = useStorage('gameData', {
        clickedNumbers: ['FREE'],
        recentNumbers: [],
        drawNumbers: [],
        revealIndex: 0,
    })


    const clickedNumbers: Ref<Set<(number|string)>> = ref(new Set(persistedGameData.value.clickedNumbers));
    const recentNumbers: Ref<Array<number>> = ref(persistedGameData.value.recentNumbers);
    const drawNumbers: Ref<Set<number>> = ref(new Set(persistedGameData.value.drawNumbers))
    const revealIndex: Ref<number> = ref(persistedGameData.value.revealIndex);

    function addToDrawNumbers(numbers: Array<number>) {
        numbers.forEach((number: number) => {
            drawNumbers.value.add(number)
        })
        persistedGameData.value.drawNumbers = Array.from(drawNumbers.value)
    }

    function addToClickedNumbers(number: number) {
        clickedNumbers.value.add(number)
        persistedGameData.value.clickedNumbers = Array.from(clickedNumbers.value)
    }

    function setRecentNumbers(numbers: Array<number>) {
        recentNumbers.value = numbers
        persistedGameData.value.recentNumbers = recentNumbers.value
    }

    function incrementRevealIndex() {
        revealIndex.value = revealIndex.value + 1;
        persistedGameData.value.revealIndex = revealIndex.value
    }

    function resetRevealIndex() {
        revealIndex.value = 0;
        persistedGameData.value.revealIndex = revealIndex.value
    }

    function clearGameData() {
        clickedNumbers.value = new Set(['FREE'])
        recentNumbers.value = []
        revealIndex.value = 0
        persistedGameData.value.clickedNumbers = ['FREE']
        persistedGameData.value.recentNumbers = []
        persistedGameData.value.revealIndex = 0
    }

    return {
        clearGameData,
        addToClickedNumbers,
        setRecentNumbers,
        incrementRevealIndex,
        addToDrawNumbers,
        resetRevealIndex,
        drawNumbers,
        clickedNumbers,
        recentNumbers,
        revealIndex,
    }
})
