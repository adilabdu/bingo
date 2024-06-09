<script setup>
import { ref } from 'vue';

// Determine the active route based on the current URL
const currentPath = window.location.pathname;

const isActiveRoute = (path) => {
    return currentPath === path;
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-100">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Hello Admin,</h1>
                <nav>
                    <ul class="flex space-x-6">
                        <li>
                            <a :href="'/admin'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/admin')
                 }">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a :href="'/admin/users'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/admin/users')
                 }">
                                Users
                            </a>
                        </li>
                        <li>
                            <a :href="'/admin/game'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/admin/game')
                 }">
                                Game
                            </a>
                        </li>
                        <li>
                            <a :href="'/admin/winners'"
                               :class="{
                   'relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out': true,
                   'active': isActiveRoute('/admin/winners')
                 }">
                                Winners
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Dynamic Content -->
                <slot></slot>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow mt-6">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
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
</style>
