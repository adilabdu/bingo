<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {ref} from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Table, TableBody, TableRow, TableCell} from '@/Components/shadcn/ui/table/index.js';
import {useForm} from '@inertiajs/vue3';
import OverViewItem from "@/Views/Agent/Dashboard/OverViewItem.vue";
import Header from "@/Components/Header.vue";
import moment from "moment";
import {RefreshCcw} from "lucide-vue-next";
import Loading from "@/Components/Loading.vue";

const page = usePage();
const agent = page.props.auth.user;
const branches = page.props.branches;
const recentActivities = page.props.recentActivities;
const totalRevenue = page.props.totalRevenue;
const todayRevenue = page.props.todayRevenue;
const thisMonthRevenue = page.props.thisMonthRevenue;
const thisWeekRevenue = page.props.thisWeekRevenue;
const activeGames = page.props.activeGames;
const totalGames = page.props.totalGames;

const isCreateBranchModalOpen = ref(false);

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

const isLoading = ref(false);
function refreshData() {
    isLoading.value = true;
    router.visit('/agent', {
        replace: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}

</script>

<template>
    <Loading v-if="isLoading" type="brand" is-full-screen/>
    <div class="flex flex-col space-y-4">
        <div class="mb-6 hidden md:inline-block">
            <h2 class="text-2xl md:text-3xl font-semibold mb-2">Welcome, {{ agent.name }}</h2>
            <p class="text-gray-600 hidden md:inline-flex">Manage your branches and cashiers below.</p>
        </div>

        <div>
            <div class="flex justify-between pb-2">
                <Header class="font-semibold" value="Revenue Numbers"/>
                <div @click="refreshData"
                     class="flex items-center space-x-2 text-xs bg-brand-secondary rounded-md shadow-md text-white px-2">
                    <RefreshCcw class="w-3"/>
                    <span>Refresh</span>
                </div>
            </div>

            <div class="flex justify-around divide-x p-2 mt-2 mb-4 rounded-lg items-center bg-indigo-600 text-white h-20">
                <div class="flex flex-col items-center font-light text-sm w-6/12"><span
                    class="text-2xl font-bold">{{ agent.agent.balance }}Br</span>
                    Balance
                </div>
                <div class="flex flex-col justify-center items-center text-sm font-light w-6/12"><span
                    class="text-2xl font-bold">{{ 100 - agent.agent.profit_percentage }}%</span>Profit
                </div>
            </div>

            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-lime-100" label="Today" :value="todayRevenue + ' Br'"/>
                <OverViewItem base-class="bg-emerald-100" label="This Week's Revenue" :value="thisWeekRevenue + ' Br'"/>
                <OverViewItem base-class="bg-purple-100" label="This Month Revenue" :value="thisMonthRevenue + ' Br'"/>
                <OverViewItem base-class="bg-zinc-200" label="Total Revenue" :value="totalRevenue + ' Br'"/>
            </div>
        </div>

        <div>
            <Header class="font-semibold" value="Your Stats"/>
            <div class="flex flex-wrap justify-between">
                <OverViewItem base-class="bg-orange-100" label="Active Games" :value="activeGames"/>
                <OverViewItem base-class="bg-cyan-100" label="Total Games" :value="totalGames"/>

                <OverViewItem base-class="bg-blue-100" label="Total Cashiers"
                              :value="branches.reduce((acc, branch) => acc + branch.cashiers.length, 0)"/>
                <OverViewItem base-class="bg-pink-100" label="Total Branches" :value="branches?.length ?? 0"/>
            </div>
        </div>


        <!-- Recent Activities -->
        <div class="flex flex-col space-y-4">
            <Header class="font-semibold" value="Recent Activities"/>
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
                            <!--                            Todo: Populate data's with properties but not events -->
                            <TableCell class="p-4 !text-xs md:!text-sm capitalize" v-if="!activity.event">
                                {{ activity.description }}
                            </TableCell>
                            <TableCell class="p-4 !text-xs md:text-sm capitalize" v-if="!activity.event">
                                {{ moment(activity.created_at).fromNow() }}
                            </TableCell>
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
                            <InputLabel for="name" value="Branch Name"/>
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError :message="form.errors.name" class="mt-2"/>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="location" value="Location"/>
                            <TextInput
                                id="location"
                                type="text"
                                class="mt-1 block w-full border"
                                v-model="form.location"
                                required
                            />
                            <InputError :message="form.errors.location" class="mt-2"/>
                        </div>

                        <div class="flex justify-end mt-6">
                            <PrimaryButton :disabled="form.processing">Create</PrimaryButton>
                            <button @click="isCreateBranchModalOpen = false" type="button"
                                    class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>
    </div>
</template>
