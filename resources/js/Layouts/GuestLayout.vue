<!--<script setup>-->
<!--import ApplicationLogo from '@/Components/ApplicationLogo.vue';-->
<!--import { Link } from '@inertiajs/vue3';-->
<!--</script>-->

<!--<template>-->
<!--    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">-->
<!--        <div>-->
<!--            <Link href="/">-->
<!--                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />-->
<!--            </Link>-->
<!--        </div>-->

<!--        <div-->
<!--            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"-->
<!--        >-->
<!--            <slot />-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->




<script setup lang="ts">
import { computed, ref } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Head, Link, useForm } from '@inertiajs/vue3';

import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/Components/shadcn/ui/select/index.js';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    type: ''
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation')
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-100">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-semibold text-gray-900">
                    Welcome to Registration
                </h1>
                <nav class="flex items-center space-x-6">
                    <ul class="flex space-x-6">
                        <li>
                            <Link :href="route('dashboard')" class="relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out">
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('login')" class="relative text-gray-600 hover:text-gray-900 transition duration-200 ease-in-out">
                                Login
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="container mx-auto py-6 sm:px-6 lg:px-8">
                <Head title="Register" />

                <form @submit.prevent="submit" class="w-10/12 flex flex-col space-y-5 mx-auto p-6 bg-white shadow-lg rounded-lg">
                    <h1 class="text-2xl font-bold mb-6 text-center">
                        Register New User
                    </h1>

                    <div>
                        <InputLabel for="type" value="User Type" />
                        <Select v-model="form.type">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select user type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>User Type</SelectLabel>
                                    <SelectItem value="admin">Admin</SelectItem>
                                    <SelectItem value="player">Player</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.type" />
                    </div>

                    <div v-if="form.type">
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full border"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div v-if="form.type === 'admin'" class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full border"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="form.type === 'player'" class="mt-4">
                        <InputLabel for="phone" value="Phone Number" />
                        <TextInput
                            id="phone"
                            type="tel"
                            class="mt-1 block w-full border"
                            v-model="form.phone_number"
                            required
                            autocomplete="tel"
                        />
                        <InputError class="mt-2" :message="form.errors.phone_number" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full border"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full border"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <Link
                            :href="route('login')"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Already registered?
                        </Link>
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Register
                        </PrimaryButton>
                    </div>
                </form>
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
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
