<script setup>
import {computed, provide, ref} from 'vue';
import {router, usePage} from "@inertiajs/vue3";
import Notification from "@/Components/Notification.vue";

// Determine the active route based on the current URL
const currentPath = window.location.pathname;

const user = computed(() => usePage().props.auth.user);

const isActiveRoute = (path) => {
    if (path === '/cashier') {
        return currentPath === path;
    }
    return currentPath.startsWith(path);
};

// State for managing dropdown visibility
const showDropdown = ref(false);

const toggleDropdown = (state) => {
    showDropdown.value = state;
};

const logout = () => {
    router.post("/logout", {}, {});
};

const notificationData = ref(null);
const showNotification = (data) => {
    notificationData.value = data;
};

provide("showNotification", showNotification);
provide("notificationData", notificationData);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-100">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
              <div class="flex items-center space-x-2">
                <img class="w-20 h-12 object-cover" src="../../../public/assets/images/logo.png">
                <div class="font-bold text-5xl text-brand-primary">Kiwi <span class="text-brand-secondary">bingo</span></div>
              </div>
                <nav class="flex items-center space-x-14">
                    <ul class="flex items-center space-x-20 ">
                        <li>
                            <a :href="'/cashier'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/cashier')
                 }">
                                Home
                            </a>
                        </li>
                        <li>
                            <a :href="'/cashier/game/initiate'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/cashier/game/initiate')
                 }">
                                Play
                            </a>
                        </li>
                        <li>
                            <a :href="'/cashier/finance'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/cashier/finance')
                 }">
                                Finance
                            </a>
                        </li>
                    </ul>
                    <div class="relative ml-auto" @mouseover="toggleDropdown(true)" @mouseleave="toggleDropdown(false)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600 cursor-pointer" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A11.955 11.955 0 0112 15c3.142 0 6.028 1.166 8.121 3.071A8.962 8.962 0 0021 12c0-4.962-4.038-9-9-9s-9 4.038-9 9a8.962 8.962 0 003.121 6.804zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <transition name="fade">
                            <div v-if="showDropdown"
                                 class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md py-2 z-10">
                                <a href="/admin/profile"
                                   class="block px-4 py-2 text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out">
                                    Profile
                                </a>
                                <a @click="logout"
                                   class="block cursor-pointer px-4 py-2 text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out">
                                    Logout
                                </a>
                            </div>
                        </transition>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="container md:max-w-[1650px] mx-auto py-6 sm:px-6 lg:px-8">
                <Notification class="min-w-full" />
                <slot></slot>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow mt-6">
            <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">© 2024 rigel. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
a {
    position: relative;
}

a::after {
    content: "";
    position: absolute;
    width: 100%;
    transform: scaleX(0);
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: #3b82f6;
    transform-origin: bottom right;
    transition: transform 0.25s ease-out;
}

a:hover::after,
a.active::after {
    transform: scaleX(1);
    transform-origin: bottom left;
}

a.active {
    color: #808080;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */
{
    opacity: 0;
}
</style>
