<script setup>
import { computed, onMounted, ref } from "vue";
import ShuffleBall from "@/Views/Game/Cashier/ShuffleBall.vue";

const props = defineProps({
    duration: {
        type: Number,
        default: 5000,
    },
    cartelaName: Number,
    gameId: Number,
    gameCategoryId: Number,
})

const shuffleArea = ref(null);
const shuffleAreaWidth = computed(() => shuffleArea.value?.offsetWidth);
const shuffleAreaHeight = computed(() => shuffleArea.value?.offsetHeight);

function getStartPosition(i, total) {
    switch (true) {

        case i < total * 1 / 4:
            return { x: shuffleAreaWidth.value - 64, y: shuffleAreaHeight.value - 64 };

        case i < total * 2 / 4:
            return { x: 0, y: shuffleAreaHeight.value - 64 };

        case i < total * 3 / 4:
            return { x: shuffleAreaWidth.value - 64, y: 0 };

        default:
            return { x: 0, y: 0 };
    }
}

onMounted(() => {
    // TODO: Proceed to Start Game after `props.duration` timeout
})
</script>

<template>

    <div ref="shuffleArea" class="w-[80%] mx-auto h-[36rem] relative">
        <ShuffleBall
            v-if="shuffleAreaWidth"
            v-for="i in 75"
            :key="i"
            :name="i.toString()"
            :start="getStartPosition(i, 75)"
            :bounds="{ height: shuffleAreaHeight, width: shuffleAreaWidth }"
        />
    </div>

</template>

<style scoped>

</style>
