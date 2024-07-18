<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import { useForm } from '@inertiajs/vue3';

const page = usePage();
const agent = page.props.auth.user;
const branches = page.props.branches;
const topBranches = page.props.topBranches;
const recentActivities = page.props.recentActivities;
const totalRevenue = page.props.totalRevenue;

const isCreateBranchModalOpen = ref(false);

const showCreateBranchModal = () => {
    isCreateBranchModalOpen.value = true;
};

const form = useForm({
    name: '',
    location: '',
});

const submit = () => {
    form.post(route('agent.branches.store'), {
        onSuccess: () => {
            isCreateBranchModalOpen.value = false;
            form.reset();
        }
    });
};
</script>

<template>
    <div class="bg-white rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-3xl font-bold mb-2">Welcome, {{ agent.name }}</h2>
            <p class="text-gray-600">Manage your branches and cashiers below.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg ">
                <h3 class="text-xl font-semibold mb-2">Total Branches</h3>
                <p v-if="branches?.length > 0" class="text-lg">{{ branches?.length }}</p>
                <p v-else class="text-lg">0</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg ">
                <h3 class="text-xl font-semibold mb-2">Total Cashiers</h3>
                <p class="text-lg">
                    {{ branches.reduce((acc, branch) => acc + branch.cashiers.length, 0) }}
                </p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg ">
                <h3 class="text-xl font-semibold mb-2">Total Revenue</h3>
                <p class="text-lg">
                    {{ totalRevenue }}
                </p>
            </div>
        </div>

        <div class="mb-6 flex justify-end">
            <PrimaryButton @click="showCreateBranchModal">
                Add Branch
            </PrimaryButton>
        </div>

        <!-- Top Performing Branches -->
        <div class="mb-6">
            <h3 class="text-2xl font-bold mb-4">Top Performing Branches</h3>
            <Table class="min-w-full bg-white shadow rounded-lg">
                <TableBody class="bg-gray-100 font-semibold">
                    <TableRow>
                        <TableCell class="p-4">Branch Name</TableCell>
                        <TableCell class="p-4">Location</TableCell>
                        <TableCell class="p-4">Total Transactions</TableCell>
                    </TableRow>
                </TableBody>
                <TableBody>
                    <TableRow v-for="branch in topBranches" :key="branch.id" class="border-b">
                        <TableCell class="p-4">{{ branch.name }}</TableCell>
                        <TableCell class="p-4">{{ branch.location }}</TableCell>
                        <TableCell class="p-4">{{ branch.transactions.length }}</TableCell>
                    </TableRow>
                    <TableRow v-if="topBranches.length === 0">
                        <TableCell class="p-4 text-center" colspan="3">No top-performing branches found.</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>


        <!-- Recent Activities -->
        <div class="mb-6">
            <h3 class="text-2xl font-bold mb-4">Recent Activities</h3>
            <div class="overflow-x-auto">
                <Table class="min-w-full bg-white shadow rounded-lg">
                    <TableBody class="bg-gray-100 font-semibold">
                        <TableRow>
                            <TableCell class="p-4">Description</TableCell>
                            <TableCell class="p-4">Date</TableCell>
                        </TableRow>
                    </TableBody>
                    <TableBody>
                        <TableRow v-for="activity in recentActivities" :key="activity.id" class="border-b">
                            <TableCell class="p-4">{{ activity.description }}</TableCell>
                            <TableCell class="p-4">{{ new Date(activity.created_at).toLocaleString() }}</TableCell>
                        </TableRow>
                        <TableRow v-if="recentActivities.length === 0">
                            <TableCell class="p-4 text-center" colspan="2">No recent activities available.</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>


        <!-- Create Branch Modal -->
        <Modal :show="isCreateBranchModalOpen" @close="isCreateBranchModalOpen = false">
            <template #default>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Create Branch</h3>
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <InputLabel for="name" value="Branch Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="location" value="Location" />
                            <TextInput
                                id="location"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.location"
                                required
                            />
                            <InputError :message="form.errors.location" class="mt-2" />
                        </div>

                        <div class="flex justify-end mt-6">
                            <PrimaryButton :disabled="form.processing">Create</PrimaryButton>
                            <button @click="isCreateBranchModalOpen = false" type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">Cancel</button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>
    </div>
</template>
