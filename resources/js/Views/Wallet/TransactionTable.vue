<script setup>
import {
    Table,
    TableBody,
    TableCell,
    TableRow,
} from '@/Components/shadcn/ui/table'
import {computed} from "vue";
import {formatToPrice, visit} from "@/lib/utils.js";
import dayjs from "dayjs";
import resolveTime from "dayjs/plugin/relativeTime"
import {usePage} from "@inertiajs/vue3";
import {Plus, Minus} from "lucide-vue-next";

dayjs.extend(resolveTime)

const props = defineProps({
    transactions: {
        type: Object,
    }
})

const page = usePage()
const auth = computed(() => page.props.auth)
const userPhoneNumber = computed(() => auth.value.user.phone_number)

const nextPage = computed(() => props.transactions.next_page_url)
const previousPage = computed(() => props.transactions.prev_page_url)

const data = computed(() => props.transactions.data)

const transferType = (record) => {
    if (record.type !== 'transfer') {
        return record.type
    }

    if (record.from.phone_number === userPhoneNumber.value) {
        return 'sent'
    }

    return 'received'
}

function goToNextPage() {
    visit(nextPage.value, {
        preserveScroll: true,
        preserveState: true,
        only: ['transactions'],
    })
}

function goToPreviousPage() {
    visit(previousPage.value, {
        preserveScroll: true,
        preserveState: true,
        only: ['transactions'],
    })
}
</script>

<template>

    <div class="max-w-md pt-6 w-full h-fit bg-white rounded-md shadow-md">
        <div class="font-semibold text-xl pb-1 px-6">Transactions</div>
        <div class="text-xs font-light pb-2 px-6">
            View your recent transaction records here
        </div>
        <Table>
            <TableBody>
                <TableRow :class="{ 'bg-slate-100': r % 2 }" v-for="(record, r) in data" :key="r">
                    <TableCell class="pr-2">
                        <div
                            :class="{
                                'bg-green-200': record.type === 'deposit' || transferType(record) === 'received',
                                'bg-red-200': record.type === 'withdraw' || transferType(record) === 'sent',
                            }"
                            class="w-8 h-8 grid place-items-center rounded-full"
                        >
                            <Plus
                                class="w-6 h-6 text-green-700"
                                v-if="record.type === 'deposit' || transferType(record) === 'received'"
                            />
                            <Minus
                                class="w-6 h-6 text-red-700"
                                v-if="record.type === 'withdraw' || transferType(record) === 'sent'"
                            />
                        </div>
                    </TableCell>
                    <TableCell class="flex flex-col gap-2 w-full pl-0">
                        <span>
                            <span class="capitalize">
                            {{ record.type }}&nbsp;
                        </span>
                            <span
                                :class="{
                                'text-red-600': record.type === 'withdraw' || transferType(record) === 'sent',
                                'text-green-600': record.type === 'deposit' || transferType(record) === 'received',
                            }"
                                class="pl-0 text-right whitespace-nowrap font-medium"
                            >
                                {{ formatToPrice(record.amount) }} Br
                            </span>
                            <span v-if="record.type === 'transfer'">
                                {{ transferType(record) === 'sent' ? 'to' : 'from' }}
                                {{ transferType(record) === 'sent' ? record.to.phone_number : record.from.phone_number }}
                            </span>
                            <span v-else-if="record.type === 'withdraw'">
                                to Bank
                            </span>
                            <span v-else-if="record.type === 'deposit'">
                                from Bank
                            </span>
                        </span>
                        <div class="text-xs text-slate-700">{{ dayjs(record.created_at).fromNow() }}</div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <div class="flex items-center justify-between text-xs w-full border-t">
            <div class="pl-4">
                {{ props.transactions.current_page }} of {{ props.transactions.last_page }}
            </div>
            <div>
                <button :disabled="!previousPage" @click="goToPreviousPage" class="p-4 disabled:opacity-50">Previous Page</button>
                <button :disabled="!nextPage" @click="goToNextPage" class="p-4 disabled:opacity-50">Next Page</button>
            </div>
        </div>
    </div>
</template>
