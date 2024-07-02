<template>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
                <div class="font-semibold text-xl pb-1">Withdrawal Failed</div>
                <div class="text-xs font-light pb-2">There was an issue processing your withdrawal. Please try again later or contact support.</div>
            </div>
            <div class="px-4 py-5 sm:px-6">
                <button @click="goToWallet" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Go to Wallet
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
function goToWallet() {
    window.location.href = '/wallet';
}
</script>
