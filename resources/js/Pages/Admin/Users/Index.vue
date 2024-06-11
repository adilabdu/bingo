<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, watch, onMounted, computed } from 'vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { Button } from '@/Components/shadcn/ui/button/index.js';
import { Input } from '@/Components/shadcn/ui/input/index.js';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/shadcn/ui/select/index.js';
import Modal from '@/Components/AdminModal.vue';
import { router, usePage } from "@inertiajs/vue3";
import { debounce } from "lodash";

// Initialize paginated users with usePage props
const pageData = usePage().props;
const paginatedUsers = ref(pageData.users);

const roles = ['Admin', 'Player'];
const query = ref('');
const selectedRole = ref('');
const isModalOpen = ref(false);
const selectedUser = ref({ name: '', email: '', role: '' });
const isConfirmModalOpen = ref(false);
const userToToggle = ref(null);

const fetchUsers = (url = '/admin/users') => {
    const params = { search: query.value, role: selectedRole.value };
    router.get(url, params, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            paginatedUsers.value = page.props.users;
        }
    });
};

const search = debounce(() => {
    fetchUsers();
}, 300);

watch([query], () => {
    search();
});

watch([selectedRole], () => {
    fetchUsers();
});

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

// Filtered users based on selected role
const filteredUsers = computed(() => {
    return paginatedUsers.value.data.filter(user => {
        return (!selectedRole.value || user.type === selectedRole.value.toLowerCase());
    });
});

// Check if role should be disabled
const isDisabled = (role) => {
    return (selectedRole.value === role) || (selectedRole.value === '' && role === null);
};

// Toggle block/unblock user
const toggleBlockUser = (user) => {
    userToToggle.value = user;
    isConfirmModalOpen.value = true;
};

const confirmToggleBlockUser = () => {
    const user = userToToggle.value;
    router.post(route('users.block', user.id), {}, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            user.is_blocked = !user.is_blocked;
            isConfirmModalOpen.value = false;
        }
    });
};

const closeConfirmModal = () => {
    isConfirmModalOpen.value = false;
};

onMounted(() => {
    fetchUsers();
});
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
                <Input
                    v-model="query"
                    placeholder="Search users..."
                    class="w-1/3"
                />

                <div class="relative w-1/4">
                    <Select v-model="selectedRole">
                        <SelectTrigger class="w-full">
                            <SelectValue
                                placeholder="Select User Type"
                            >
                                {{ selectedRole ? selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1) : 'All' }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel value="">Select User Type</SelectLabel>
                                <SelectItem value="Admin" :disabled="isDisabled('Admin')" class="cursor-pointer">Admin</SelectItem>
                                <SelectItem value="Player" :disabled="isDisabled('Player')" class="cursor-pointer">Player</SelectItem>
                                <SelectItem :value="null" :disabled="isDisabled(null)" class="cursor-pointer">All</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <Button class="bg-blue-500 text-white py-2 px-4 rounded-lg" @click="router.visit('/register')">
                    Add New User
                </Button>
            </div>

            <!-- Users Table -->
            <Table class="min-w-full bg-white shadow rounded-lg">
                <TableBody class="bg-gray-300 font-semibold">
                    <TableRow>
                        <TableCell class="p-4">Name</TableCell>
                        <TableCell class="p-4">Email/Phone</TableCell>
                        <TableCell class="p-4">Role</TableCell>
                        <TableCell class="p-4">Actions</TableCell>
                    </TableRow>
                </TableBody>
                <TableBody>
                    <TableRow v-for="user in filteredUsers" :key="user.id" class="border-b">
                        <TableCell class="p-4">{{ user.name }}</TableCell>
                        <TableCell class="p-4">
                            <span v-if="user.type === 'admin'">{{ user.email }}</span>
                            <span v-else-if="user.type === 'player'">{{ user.phone_number }}</span>
                        </TableCell>
                        <TableCell class="p-4">{{ user.type.charAt(0).toUpperCase() + user.type.slice(1) }}</TableCell>
                        <TableCell class="p-4">
                            <Button
                                :class="user.is_blocked ? 'bg-green-500' : 'bg-red-500'"
                                class="text-white py-1 px-2 rounded-lg"
                                @click="toggleBlockUser(user)"
                            >
                                {{ user.is_blocked ? 'Unblock' : 'Block' }}
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <Button @click="fetchUsers(paginatedUsers.prev_page_url)" :disabled="!paginatedUsers.prev_page_url">
                    &laquo; Previous
                </Button>
                <span>Page {{ paginatedUsers.current_page }} of {{ paginatedUsers.last_page }}</span>
                <Button @click="fetchUsers(paginatedUsers.next_page_url)" :disabled="!paginatedUsers.next_page_url">
                    Next &raquo;
                </Button>
            </div>

            <!-- User Modal -->
            <Modal :visible="isModalOpen" title="User Information" @update:visible="closeModal">
                <div class="space-y-4">
                    <Input v-model="selectedUser.name" placeholder="Name"/>
                    <Input v-model="selectedUser.email" placeholder="Email"/>
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

            <!-- Confirmation Modal -->
            <Modal :visible="isConfirmModalOpen" title="Confirm Action" @update:visible="closeConfirmModal">
                <div class="space-y-4">
                    <p>Are you sure you want to {{ userToToggle?.is_blocked ? 'unblock' : 'block' }} this user?</p>
                </div>
                <template #footer>
                    <Button class="bg-gray-500 text-white py-2 px-4 rounded-lg mr-2" @click="closeConfirmModal">
                        Cancel
                    </Button>
                    <Button class="bg-red-500 text-white py-2 px-4 rounded-lg" @click="confirmToggleBlockUser">
                        Confirm
                    </Button>
                </template>
            </Modal>
        </div>
    </AdminLayout>
</template>

<style scoped>
</style>

<style scoped>
</style>



<!--<script setup>-->
<!--import AdminLayout from "@/Layouts/AdminLayout.vue";-->
<!--import { ref, watch, onMounted, computed } from 'vue';-->
<!--import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';-->
<!--import { Button } from '@/Components/shadcn/ui/button/index.js';-->
<!--import { Input } from '@/Components/shadcn/ui/input/index.js';-->
<!--import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/shadcn/ui/select/index.js';-->
<!--import Modal from '@/Components/AdminModal.vue';-->
<!--import { router, usePage } from "@inertiajs/vue3";-->
<!--import { debounce } from "lodash";-->

<!--// Initialize paginated users with usePage props-->
<!--const pageData = usePage().props;-->
<!--const paginatedUsers = ref(pageData.users);-->

<!--const roles = ['Admin', 'Player'];-->
<!--const query = ref('');-->
<!--const selectedRole = ref('');-->
<!--const isModalOpen = ref(false);-->
<!--const selectedUser = ref({ name: '', email: '', role: '' });-->

<!--const fetchUsers = (url = '/admin/users') => {-->
<!--    const params = { search: query.value, role: selectedRole.value };-->
<!--    router.get(url, params, {-->
<!--        preserveState: true,-->
<!--        replace: true,-->
<!--        onSuccess: (page) => {-->
<!--            paginatedUsers.value = page.props.users;-->
<!--        }-->
<!--    });-->
<!--};-->

<!--const search = debounce(() => {-->
<!--    fetchUsers();-->
<!--}, 300);-->

<!--watch([query], () => {-->
<!--    search();-->
<!--});-->

<!--watch([selectedRole], () => {-->
<!--    fetchUsers();-->
<!--});-->

<!--const openModal = (user) => {-->
<!--    selectedUser.value = user ? { ...user } : { name: '', email: '', role: '' };-->
<!--    isModalOpen.value = true;-->
<!--};-->

<!--const closeModal = () => {-->
<!--    isModalOpen.value = false;-->
<!--    selectedUser.value = { name: '', email: '', role: '' };-->
<!--};-->

<!--const saveUser = () => {-->
<!--    // Save user logic here-->
<!--    closeModal();-->
<!--};-->

<!--// Filtered users based on selected role-->
<!--const filteredUsers = computed(() => {-->
<!--    return paginatedUsers.value.data.filter(user => {-->
<!--        return (!selectedRole.value || user.type === selectedRole.value.toLowerCase());-->
<!--    });-->
<!--});-->

<!--// Check if role should be disabled-->
<!--const isDisabled = (role) => {-->
<!--    return (selectedRole.value === role) || (selectedRole.value === '' && role === null);-->
<!--};-->

<!--onMounted(() => {-->
<!--    fetchUsers();-->
<!--});-->
<!--</script>-->

<!--<template>-->
<!--    <AdminLayout>-->
<!--        <div class="space-y-8">-->
<!--            &lt;!&ndash; Header Section &ndash;&gt;-->
<!--            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-lg shadow-lg">-->
<!--                <h2 class="text-4xl font-bold mb-4">User Management</h2>-->
<!--                <p class="text-lg">Manage your users, roles, and permissions with ease.</p>-->
<!--            </div>-->

<!--            &lt;!&ndash; Search and Filters &ndash;&gt;-->
<!--            <div class="flex justify-between items-center mb-4">-->
<!--                <Input-->
<!--                    v-model="query"-->
<!--                    placeholder="Search users..."-->
<!--                    class="w-1/3"-->
<!--                />-->

<!--                <div class="relative w-1/4">-->
<!--                    <Select v-model="selectedRole">-->
<!--                        <SelectTrigger class="w-full">-->
<!--                            <SelectValue-->
<!--                                placeholder="Select User Type"-->
<!--                            >-->
<!--                                {{ selectedRole ? selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1) : 'All' }}-->
<!--                            </SelectValue>-->
<!--                        </SelectTrigger>-->
<!--                        <SelectContent>-->
<!--                            <SelectGroup>-->
<!--                                <SelectLabel value="">Select User Type</SelectLabel>-->
<!--                                <SelectItem value="Admin" :disabled="isDisabled('Admin')" class="cursor-pointer">Admin</SelectItem>-->
<!--                                <SelectItem value="Player" :disabled="isDisabled('Player')" class="cursor-pointer">Player</SelectItem>-->
<!--                                <SelectItem :value="null" :disabled="isDisabled(null)" class="cursor-pointer">All</SelectItem>-->
<!--                            </SelectGroup>-->
<!--                        </SelectContent>-->
<!--                    </Select>-->
<!--                </div>-->

<!--                <Button class="bg-blue-500 text-white py-2 px-4 rounded-lg" @click="router.visit('/register')">-->
<!--                    Add New User-->
<!--                </Button>-->
<!--            </div>-->

<!--            &lt;!&ndash; Users Table &ndash;&gt;-->
<!--            <Table class="min-w-full bg-white shadow rounded-lg">-->
<!--                <TableBody class="bg-gray-300 font-semibold">-->
<!--                    <TableRow>-->
<!--                        <TableCell class="p-4">Name</TableCell>-->
<!--                        <TableCell class="p-4">Email/Phone</TableCell>-->
<!--                        <TableCell class="p-4">Role</TableCell>-->
<!--                        <TableCell class="p-4">Actions</TableCell>-->
<!--                    </TableRow>-->
<!--                </TableBody>-->
<!--                <TableBody>-->
<!--                    <TableRow v-for="user in filteredUsers" :key="user.id" class="border-b">-->
<!--                        <TableCell class="p-4">{{ user.name }}</TableCell>-->
<!--                        <TableCell class="p-4">-->
<!--                            <span v-if="user.type === 'admin'">{{ user.email }}</span>-->
<!--                            <span v-else-if="user.type === 'player'">{{ user.phone_number }}</span>-->
<!--                        </TableCell>-->
<!--                        <TableCell class="p-4">{{ user.type.charAt(0).toUpperCase() + user.type.slice(1) }}</TableCell>-->
<!--                        <TableCell class="p-4">-->
<!--                            <Button class="bg-red-400 text-white py-1 px-2 rounded-lg">-->
<!--                                Block-->
<!--                            </Button>-->
<!--                        </TableCell>-->
<!--                    </TableRow>-->
<!--                </TableBody>-->
<!--            </Table>-->

<!--            &lt;!&ndash; Pagination &ndash;&gt;-->
<!--            <div class="flex justify-between items-center mt-4">-->
<!--                <Button @click="fetchUsers(paginatedUsers.prev_page_url)" :disabled="!paginatedUsers.prev_page_url">-->
<!--                    &laquo; Previous-->
<!--                </Button>-->
<!--                <span>Page {{ paginatedUsers.current_page }} of {{ paginatedUsers.last_page }}</span>-->
<!--                <Button @click="fetchUsers(paginatedUsers.next_page_url)" :disabled="!paginatedUsers.next_page_url">-->
<!--                    Next &raquo;-->
<!--                </Button>-->
<!--            </div>-->

<!--            &lt;!&ndash; User Modal &ndash;&gt;-->
<!--            <Modal :visible="isModalOpen" title="User Information" @update:visible="closeModal">-->
<!--                <div class="space-y-4">-->
<!--                    <Input v-model="selectedUser.name" placeholder="Name"/>-->
<!--                    <Input v-model="selectedUser.email" placeholder="Email"/>-->
<!--                    <Select v-model="selectedUser.role">-->
<!--                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>-->
<!--                    </Select>-->
<!--                </div>-->
<!--                <template #footer>-->
<!--                    <Button class="bg-gray-500 text-white py-2 px-4 rounded-lg mr-2" @click="closeModal">-->
<!--                        Cancel-->
<!--                    </Button>-->
<!--                    <Button class="bg-blue-500 text-white py-2 px-4 rounded-lg" @click="saveUser">-->
<!--                        Save-->
<!--                    </Button>-->
<!--                </template>-->
<!--            </Modal>-->
<!--        </div>-->
<!--    </AdminLayout>-->
<!--</template>-->

<!--<style scoped>-->
<!--</style>-->
