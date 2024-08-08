<script setup>
import CashierBingoBoard from "@/Views/Game/CashierBingoBoard.vue";

const props = defineProps({
    cartela: Object
})

</script>

<template>
<div class="flex flex-col space-y-4">
    <div v-if="cartela" class="py-2">
        <div  class="font-bold text-2xl text-center">Cartela # {{cartela?.name}}</div>
        <CashierBingoBoard is-playable-cartela :numbers="cartela?.numbers"/>
    </div>
    <div v-else class="h-60 flex items-center justify-center text-red-600 font-semibold text-2xl text-center">
        No Cartela Found!
    </div>
    </div>
</template>

<style scoped>

</style>
