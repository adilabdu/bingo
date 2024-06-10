<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref } from 'vue';
import { Table, TableHead, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { Pagination } from '@/Components/shadcn/ui/pagination/index.js';
import { Button } from '@/Components/shadcn/ui/button/index.js';
import { Input } from '@/Components/shadcn/ui/input/index.js';
import { Select } from '@/Components/shadcn/ui/select/index.js';
import Modal from '@/Components/AdminModal.vue';
import {router} from "@inertiajs/vue3";

const users = ref([
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'User' },
    { id: 3, name: 'Jim Brown', email: 'jim@example.com', role: 'User' },
    // Add more user data here
]);

const roles = ['Admin', 'User'];

const isModalOpen = ref(false);
const selectedUser = ref({ name: '', email: '', role: '' });

const openModal = (user) => {
    selectedUser.value = user ? { ...user } : { name: '', email: '', role: '' };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedUser.value = { name: '', email: '', role: '' };
};

const saveUser = () => {
    // Save user logic here
    closeModal();
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-8">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">
                <h2 class="text-4xl font-bold mb-4">User Management</h2>
                <p class="text-lg">Manage your users, roles, and permissions with ease.</p>
            </div>

            <!-- Search and Filters -->
            <div class="flex justify-between items-center mb-4">
                <Input placeholder="Search users..." class="w-1/3" />
                <Select v-model="selectedUser.role" class="w-1/4">
                    <option value=""
                            class="p-2 border-b border-gray-200 hover:bg-blue-200 rounded-lg hover:cursor-pointer"
                            :class="{ 'bg-blue-200': selectedUser.role === '' }"
                    >All Roles</option>
                    <option v-for="role in roles" :key="role" :value="role"
                            class="p-2 border-b border-gray-200 hover:bg-blue-200 rounded-lg hover:cursor-pointer"
                            :class="{ 'bg-blue-500 text-white': selectedUser.role === role }"
                    >{{ role }}</option>
                </Select>
                <Button class="bg-blue-500 text-white py-2 px-4 rounded-lg" @click="router.visit('/register')">
                    Add New User
                </Button>
            </div>

            <!-- Users Table -->
            <Table class="min-w-full bg-white shadow rounded-lg">
                <TableBody class="bg-gray-300 font-semibold">
                    <TableRow>
                        <TableCell class="p-4">Name</TableCell>
                        <TableCell class="p-4">Email</TableCell>
                        <TableCell class="p-4">Role</TableCell>
                        <TableCell class="p-4">Actions</TableCell>
                    </TableRow>
                </TableBody>
                <TableBody>
                    <TableRow v-for="user in users" :key="user.id" class="border-b">
                        <TableCell class="p-4 ">{{ user.name }}</TableCell>
                        <TableCell class="p-4">{{ user.email }}</TableCell>
                        <TableCell class="p-4">{{ user.role }}</TableCell>
                        <TableCell class="p-4">
                            <Button class="bg-yellow-500 text-white py-1 px-2 rounded-lg mr-2" @click="openModal(user)">
                                Edit
                            </Button>
                            <Button class="bg-red-500 text-white py-1 px-2 rounded-lg">
                                Block
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <Pagination :total="100" :current="1" :pageSize="10" class="mt-4" />

            <!-- User Modal -->
            <Modal :visible="isModalOpen" title="User Information" @update:visible="closeModal">
                <div class="space-y-4">
                    <Input v-model="selectedUser.name" placeholder="Name" />
                    <Input v-model="selectedUser.email" placeholder="Email" />
                    <Select v-model="selectedUser.role">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </Select>
                </div>
                <template #footer>
                    <Button class="bg-gray-500 text-white py-2 px-4 rounded-lg mr-2" @click="closeModal">
                        Cancel
                    </Button>
                    <Button class="bg-blue-500 text-white py-2 px-4 rounded-lg" @click="saveUser">
                        Save
                    </Button>
                </template>
            </Modal>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Add any additional styles here */
</style>
