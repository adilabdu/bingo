<script setup>
import {computed, nextTick, ref} from "vue";

const props = defineProps({
    start: {
        type: Object,
        default: { x: 0, y: 0 }
    },
    bounds: {
        type: Object,
        required: true
    },
    name: {
        type: String,
        required: true
    }
})

const MAX_DURATION = 1000;
const SIDES = ['top', 'bottom', 'left', 'right'];

const ball = ref()
const X = ref(props.start.x);
const Y = ref(props.start.y);

const previousX = ref(props.bounds.width / 2);
const previousY = ref(props.bounds.height / 2);

const hitSide = ref(SIDES[Math.floor(Math.random() * 4)]);
const duration = ref(MAX_DURATION);

const maxLength = computed(() => getHypotenuse(props.bounds.width, props.bounds.height));

function getHypotenuse(x, y) {
    return Math.floor(Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2)));
}

function randomMove(maxHeight, maxWidth, hitSide) {
    let x = Math.random() * maxWidth
    if (hitSide === 'left') {
        x = 0;
    }
    if (hitSide === 'right') {
        x = maxWidth;
    }
    if (x >= maxWidth - 64) {
        x = x - 64;
    }
    x = Math.floor(x)

    let y = Math.random() * maxHeight
    if (hitSide === 'top') {
        y = 0;
    }
    if (hitSide === 'bottom') {
        y = maxHeight;
    }
    if (y >= maxHeight - 64) {
        y = y - 64;
    }
    y = Math.floor(y)

    return { x, y }
}

function shuffle(first=false) {
    if (!first) {
        const random = randomMove(props.bounds.height, props.bounds.width, hitSide.value);
        X.value = random.x;
        Y.value = random.y;
    }

    hitSide.value = SIDES.filter(s => s !== hitSide.value)[Math.floor(Math.random() * 3)];

    const moveLength = getHypotenuse(previousX.value - X.value, previousY.value - Y.value);
    duration.value = Math.floor((moveLength / maxLength.value) * MAX_DURATION);

    previousX.value = X.value;
    previousY.value = Y.value;



    setTimeout(shuffle, first ? 1 : duration.value)
}

shuffle(true);
</script>

<template>
    <div
        ref="ball"
        :style="{ transform: `translate(${X}px, ${Y}px) rotate(${Math.floor(360*Math.random())}deg)`, transitionDuration: `${duration}ms` }"
        class="w-16 h-16 sphere transition-transform text-3xl absolute ease-linear duration-1000 delay-0 font-medium rounded-full text-white grid place-items-center"
    >
        {{ name }}
    </div>
</template>

<style scoped>
.sphere {
    background:
        radial-gradient(
            circle at 30% 30%,
            rgb(30 159 50),
            rgb(18 62 34)
        );
}
</style>
