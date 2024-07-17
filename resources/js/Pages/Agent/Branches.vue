<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { Table, TableBody, TableRow, TableCell } from '@/Components/shadcn/ui/table/index.js';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from "@/Components/SecondaryButton.vue";

const page = usePage();
const branches = ref(page.props.branches);

const isCreateBranchModalOpen = ref(false);
const isAddCashierModalOpen = ref(false);
const isCashiersModalOpen = ref(false);
const currentBranchId = ref(null);
const currentBranch = ref({ name: '', cashiers: [] });

const showCreateBranchModal = () => {
    isCreateBranchModalOpen.value = true;
};

const showAddCashierModal = (branchId) => {
    currentBranchId.value = branchId;
    formCashier.branch_id = branchId;
    isAddCashierModalOpen.value = true;
};

const showCashiersModal = (branch) => {
    currentBranch.value = branch;
    isCashiersModalOpen.value = true;
};

const formBranch = useForm({
    name: '',
    location: '',
});

const formCashier = useForm({
    name: '',
    phone_number: '',
    branch_id: '',
});

const fetchBranches = () => {
    router.get(route('agent.branches'), {}, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            branches.value = page.props.branches;
        }
    });
};

const submitBranch = () => {
    formBranch.post(route('agent.branches.store'), {
        onSuccess: () => {
            isCreateBranchModalOpen.value = false;
            formBranch.reset();
            fetchBranches();
        }
    });
};

const submitCashier = () => {
    formCashier.phone_number = `+251${formCashier.phone_number}`;
    formCashier.post(route('cashier.store'), {
        onSuccess: () => {
            isAddCashierModalOpen.value = false;
            formCashier.reset();
            fetchBranches();
        }
    });
};
</script>

<template>
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between mb-6">
            <div class="flex flex-col">
                <h2 class="text-3xl font-bold mb-2">Branches</h2>
                <p class="text-gray-600">Manage your branches below.</p>
            </div>
            <div>
                <PrimaryButton @click="showCreateBranchModal">
                    Add Branch
                </PrimaryButton>
            </div>
        </div>

        <div class="overflow-x-auto">
            <Table class="min-w-full bg-white shadow rounded-lg">
                <TableBody class="bg-gray-300 font-semibold">
                    <TableRow class="flex w-full">
                        <TableCell class="flex w-full p-4">Branch Name</TableCell>
                        <TableCell class="flex w-full p-4">Location</TableCell>
                        <TableCell class="flex w-full p-4">Actions</TableCell>
                    </TableRow>
                </TableBody>
                <TableBody>
                    <TableRow v-for="branch in branches" :key="branch.id" class="border-b flex w-full">
                        <TableCell class="flex w-full p-4">{{ branch.name }}</TableCell>
                        <TableCell class="flex w-full p-4">{{ branch.location }}</TableCell>
                        <TableCell class="flex w-full p-4 gap-4">
                            <SecondaryButton
                                class="hover:bg-gray-300"
                                @click="showCashiersModal(branch)"
                            >
                                View Cashiers
                            </SecondaryButton>
                            <SecondaryButton
                                class="hover:bg-gray-300"
                                @click="showAddCashierModal(branch.id)"
                            >
                                Add Cashier
                            </SecondaryButton>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="branches?.length === 0">
                        <TableCell class="p-4 text-center" colspan="3">No branches found.</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Create Branch Modal -->
        <Modal :show="isCreateBranchModalOpen" @close="isCreateBranchModalOpen = false">
            <template #default>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Create Branch</h3>
                    <form @submit.prevent="submitBranch">
                        <div class="mb-4">
                            <InputLabel for="name" value="Branch Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="formBranch.name"
                                required
                                autofocus
                            />
                            <InputError :message="formBranch.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="location" value="Location" />
                            <TextInput
                                id="location"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="formBranch.location"
                                required
                            />
                            <InputError :message="formBranch.errors.location" class="mt-2" />
                        </div>

                        <div class="flex justify-end mt-6">
                            <PrimaryButton :disabled="formBranch.processing">Create</PrimaryButton>
                            <button @click="isCreateBranchModalOpen = false" type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">Cancel</button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>

        <!-- Add Cashier Modal -->
        <Modal :show="isAddCashierModalOpen" @close="isAddCashierModalOpen = false">
            <template #default>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Add Cashier</h3>
                    <form @submit.prevent="submitCashier">
                        <div class="mb-4">
                            <InputLabel for="cashierName" value="Cashier Name" />
                            <TextInput
                                id="cashierName"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="formCashier.name"
                                required
                                autofocus
                            />
                            <InputError :message="formCashier.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="phone" value="Phone Number" />
                            <TextInput
                                id="phone"
                                type="number"
                                class="mt-1 block w-full border"
                                v-model="formCashier.phone_number"
                                required
                                autocomplete="tel"
                            />
                            <InputError class="mt-2" :message="formCashier.errors.phone_number" />
                        </div>

                        <div class="flex justify-end mt-6">
                            <PrimaryButton :disabled="formCashier.processing">Add</PrimaryButton>
                            <button @click="isAddCashierModalOpen = false" type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">Cancel</button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>

        <!-- View Cashiers Modal -->
        <Modal :show="isCashiersModalOpen" @close="isCashiersModalOpen = false">
            <template #default>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Cashiers for {{ currentBranch.name }}</h3>
                    <div v-if="currentBranch?.cashiers?.length > 0">
                        <Table class="min-w-full bg-white shadow rounded-lg">
                            <TableBody class="bg-gray-100 font-semibold">
                                <TableRow>
                                    <TableCell class="p-4">Name</TableCell>
                                    <TableCell class="p-4">Phone Number</TableCell>
                                    <TableCell class="p-4">Balance</TableCell>
                                </TableRow>
                            </TableBody>
                            <TableBody>
                                <TableRow v-for="cashier in currentBranch?.cashiers" :key="cashier.id" class="border-b">
                                    <TableCell class="p-4">{{ cashier.user.name }}</TableCell>
                                    <TableCell class="p-4">{{ cashier.user.phone_number }}</TableCell>
                                    <TableCell class="p-4">{{ cashier.balance }}</TableCell>
                                </TableRow>
                                <TableRow v-if="currentBranch?.cashiers?.length === 0">
                                    <TableCell class="p-4 text-center" colspan="4">No cashiers found.</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div v-else>
                        <p class="text-gray-500">No cashiers</p>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button @click="isCashiersModalOpen = false" type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">Close</button>
                    </div>
                </div>
            </template>
        </Modal>
    </div>
</template>
