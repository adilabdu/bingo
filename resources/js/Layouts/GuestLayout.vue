<script setup>
import {Button} from "@/Components/shadcn/ui/button/index.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router} from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-white">
        <div
            class="w-full sm:max-w-md lg:max-w-7xl my-auto overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
        <!-- Footer -->
        <footer class="absolute bottom-1 mt-6">
            <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">© 2024
                    <span class="font-semibold text-brand-secondary">
                    <span class="text-brand-primary">Kiwi</span> Bingo</span>. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
