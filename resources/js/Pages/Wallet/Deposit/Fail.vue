<template>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-red-600">
                    Payment Failed
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Unfortunately, your payment was not successful. Please try again.
                </p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Reason
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            Payment verification failed or was rejected by the payment gateway.
                        </dd>
                    </div>
                </dl>
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
