<script setup>
import {ref, watch} from 'vue'
import { CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/Components/shadcn/ui/calendar/index.js'
import { Button } from '@/Components/shadcn/ui/button/index.js'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/shadcn/ui/popover/index.js'
import moment from "moment";

const emit = defineEmits(['update:date', 'update:modelValue']);
const props = defineProps({
    label: {
        type: String,
        default: "Pick a date"
    },
});
const value = ref();

watch(value, (newValue) => {
    emit('update:date', moment(newValue).toDate());
    emit('update:modelValue', new Date(newValue));
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                class="min-w-32 w-6/12"
                variant="outline"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ value ? moment(value).format('DD/MM/YYYY') : label }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="value" initial-focus />
        </PopoverContent>
    </Popover>
</template>
