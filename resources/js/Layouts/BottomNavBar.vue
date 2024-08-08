<script setup>
import BottomNavigationItem from "@/Components/BottomNavigationItem.vue";
import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {LayoutDashboard, User, MapPin, Gamepad, Wallet, Users, Home, WalletCards} from "lucide-vue-next";
import Notification from "@/Components/Notification.vue";

const user = computed(() => {
    return usePage().props.auth.user;
});
const showDropdown = ref(false);

const toggleDropdown = (state) => {
    showDropdown.value = state;
};

</script>

<template>

    <div class="min-h-screen bg-white">
        <main class="px-3 md:px-6 w-11/12 mx-auto md:w-11/12 bg-white pb-20 md:py-6">
            <div class="flex items-center py-6 w-full">
                <div class="flex items-center">
                <img class="w-8 object-cover" src="../../../public/assets/images/logo.png">
                <span class="font-bold text-2xl text-brand-secondary">
                    <span class="text-brand-primary">Kiwi</span>
                      Bingo
                  </span>

                </div>
                <div v-if="user" class="relative ml-auto" @mouseover="toggleDropdown(true)" @mouseleave="toggleDropdown(false)">
                    <div class="flex justify-end w-full space-x-2">
                        <div class="text-xs font-medium items-end justify-center flex flex-col">
                            <span>{{user.name}}</span>
                            <span class="capitalize">{{ user.type }}</span>
                        </div>
                    <img
                        :src="'https://api.dicebear.com/9.x/fun-emoji/svg/seed=' + usePage().props.auth.user.name"
                        alt="avatar"
                        class="w-11 h-11 rounded-md"
                    />
                    </div>
                    <transition name="fade">
                        <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white shadow-xl rounded-md py-2 z-10">
                            <a @click="router.post('/logout')" class="block cursor-pointer px-4 py-2 text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out">
                                Logout
                            </a>
                        </div>
                    </transition>
                </div>
            </div>
            <Notification />
            <slot />
        </main>
    <div class="fixed right-0 left-0 bottom-0 shadow-lg z-50">
        <div v-if="user?.type === 'agent'" class="flex w-full justify-around py-1.5 bg-white border-t border-gray-100 ">
            <BottomNavigationItem :icon="LayoutDashboard" label="Dashboard" to="/agent" :active="route().current('agent.home')" />
            <BottomNavigationItem v-if="user" :icon="MapPin" label="Branches" to="/agent/branches" :active="route().current('agent.branches')" />
            <BottomNavigationItem :icon="User" label="Profile" to="/profile" :active="route().current('profile.edit')" />
        </div>
        <div v-else-if="user?.type === 'cashier'" class="flex w-full justify-around py-1.5 bg-white border-t border-gray-100 ">
            <BottomNavigationItem :icon="Gamepad" label="Play" to="/cashier/game/initiate" :active="route().current('cashier.game.initiate') || route().current('cashier.game.create') || route().current('cashier.game.start')" />
            <BottomNavigationItem :icon="Wallet" label="Wallet" to="/cashier/finance" :active="route().current('cashier.finance')" />
            <BottomNavigationItem :icon="User" label="Profile" to="/profile" :active="route().current('profile.edit')" />
        </div>
        <div v-else-if="user?.type === 'admin'" class="flex w-full justify-around py-1.5 bg-white border-t border-gray-100 ">
            <BottomNavigationItem :icon="LayoutDashboard" label="Dashboard" to="/admin" :active="route().current('admin.index')" />
            <BottomNavigationItem :icon="Users" label="Agents" to="/admin/agents" :active="route().current('admin.agents')" />
            <BottomNavigationItem :icon="User" label="Profile" to="/profile" :active="route().current('profile.edit')" />
        </div>
        <div v-else class="flex w-full justify-around py-1.5 bg-white border-t border-gray-100 ">
            <BottomNavigationItem :icon="WalletCards" label="Cartelas" to="/cartela" :active="route().current('cartela') || route().current('play.cartela')" />
            <BottomNavigationItem :icon="User" label="Account" to="/coming-soon" :active="route().current('coming-soon')" />
        </div>
        </div>
    </div>
</template>

<style scoped>

</style>
