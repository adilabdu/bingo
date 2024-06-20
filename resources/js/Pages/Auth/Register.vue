<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    type: 'player',
});

const submit = () => {
    form.phone_number = `+251${form.phone_number}`;
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <div class="px-4 rounded-lg">
        <Head title="Register" />

        <div class="text-4xl pb-10">
            Sign up and start winning!
        </div>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full !bg-gray-50 !border-gray-900 !border"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Abebe Kebede"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone_number" value="Phone Number" />

                <div class="flex items-center font-semibold min-h-full">
                    <div class="bg-black text-white px-3 py-3.5 h-full  rounded-l-lg min-h-full ">+251</div>

                    <TextInput
                        id="phone_number"
                        type="number"
                        class=" block w-full !bg-gray-50 !border-gray-900 !border !rounded-none !rounded-r-lg"
                        v-model="form.phone_number"
                        required
                        autocomplete="number"
                        placeholder="(9/7)11223344"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.phone_number" />
            </div>


            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full !bg-gray-50 !border-gray-900 !border"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="New Password "
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full !bg-gray-50 !border-gray-900 !border"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm Password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4 px-6" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
