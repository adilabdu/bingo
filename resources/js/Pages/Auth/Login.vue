<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.login = `+251${form.login}`;
    form.post(route('login'), {
        onError: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="px-3 w-full flex justify-evenly mx-auto min-h-screen md:min-h-fit py-16 md:pt-0 ">
        <div class="w-full lg:w-7/12 max-w-sm md:max-w-md">
            <div class="flex justify-between items-center pb-10">
                <div class="text-5xl lg:text-6xl font-medium w-full">
                    Sign in to your account
                </div>
            </div>
            <form @submit.prevent="submit">
                <div class="flex flex-col space-y-2">
                    <InputLabel for="login" value="Phone Number" />
                    <div class="flex items-center font-semibold min-h-full">
                        <div class="bg-brand-primary text-white px-3 py-3.5 h-full  rounded-l-lg min-h-full ">+251</div>

                        <TextInput
                            id="login"
                            type="number"
                            class="block w-full !bg-gray-50 !border-black !border !rounded-none !rounded-r-lg"
                            v-model="form.login"
                            required
                            autofocus
                            autocomplete="number"
                            placeholder="(09/07)12345678"
                        />

                    </div>
                    <InputError class="mt-2" :message="form.errors.login" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full !bg-gray-50 !border-black !border"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex justify-between items-center py-2 mt-4">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Forgot your password?
                    </Link>
                </div>

                <div class="flex items-center text-center mt-4">


                    <PrimaryButton class="w-full flex justify-center !bg-brand-150" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Log in
                    </PrimaryButton>
                </div>

                <div class="flex justify-center py-3">
                    <Link
                        :href="route('register')"
                        class="text-center text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                    >
                        Don't have an account? <span class="underline">
                    Register
                </span>
                    </Link>
                </div>
            </form>

        </div>
        <div class="hidden lg:flex flex-col space-y-2 w-4/12 text-center rounded-lg justify-center items-center">
            <div class="flex w-full justify-center">
                <img class="w-7/12 object-cover" src="../../../../public/assets/images/logo.png">
            </div>
            <div class="text-4xl font-bold text-brand-100">
                <span class="text-brand-150">Kiwi</span>
                Bingo
            </div>
            <div class="text-sm">
                Embrace the fun of playing bingo online!
            </div>
        </div>
    </div>
</template>
