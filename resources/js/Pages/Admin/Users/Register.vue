<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/Components/shadcn/ui/select/index.js";
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from "vue";

const branches = computed(() => usePage().props.branches);

const form = useForm({
    name: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    type: '',
    branch_id: '',
});

const submit = () => {
    form.phone_number = `+251${form.phone_number}`;
    form.post(route('register'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="w-10/12 flex flex-col space-y-5 mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">
            Register New User
        </h1>

        <div>
            <InputLabel for="type" value="User Type" />
            <Select v-model="form.type">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select User Type" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>User Type</SelectLabel>
                        <SelectItem value="admin">Admin</SelectItem>
                        <SelectItem value="agent">Agent</SelectItem>
                        <SelectItem value="player">Player</SelectItem>
                        <SelectItem value="cashier">Cashier</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.type" />
        </div>

        <div v-if="form.type === 'cashier'">
            <InputLabel for="branch_id" value="Branch" />
            <Select v-model="form.branch_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select branch" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Branches</SelectLabel>
                        <SelectItem v-for="branch in branches" :key="branch.id" :value="branch.id" class="cursor-pointer">
                            {{ branch.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.branch_id" />
        </div>

        <div>
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

        <div class="mt-4">
            <InputLabel for="phone" value="Phone Number" />
            <TextInput
                id="phone"
                type="number"
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

        <div class="flex items-center justify-end mt-4">
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Register
            </PrimaryButton>
        </div>
    </form>
</template>

<style scoped>
</style>
