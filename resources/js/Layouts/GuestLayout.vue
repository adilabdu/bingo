<script setup>
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
        <div
            class="w-full sm:max-w-md mt-6 px-2 py-4 overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
        <!-- Footer -->
        <footer class="absolute bottom-1 mt-6">
            <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">© 2024
                    <span class="font-semibold text-black">
                    <span class="text-[#8ee53f]">Kiwi</span> Bingo</span>. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
