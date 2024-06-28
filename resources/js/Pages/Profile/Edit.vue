<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from "@/Layouts/AdminLayout.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 md:flex md:justify-between items-center">
                <div class="p-4 sm:p-8 bg-white max-w-xl rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
</template>
