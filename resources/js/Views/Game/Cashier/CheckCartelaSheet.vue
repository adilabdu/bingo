<script setup>
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTrigger,
} from '@/Components/shadcn/ui/sheet/index.js'
import {Input} from "@/Components/shadcn/ui/input/index.js";
import InputLabel from "@/Components/InputLabel.vue";
import {ref} from "vue";

const cartelaName = ref(null);
const errorMessages = ref([]);
</script>

<template>
    <Sheet>
        <SheetTrigger>
            <div
                class="text-5xl font-bold uppercase bg-brand-secondary text-white px-3 py-2 text-center rounded-lg">
                Check
            </div>
        </SheetTrigger>
        <SheetContent class="!overflow-y-auto">
            <SheetHeader >
                <SheetDescription>
                    <div class="flex flex-col items-center w-10/12 mx-auto text-black space-y-6">
                    <div class="text-7xl text-black font-bold py-5">Check Cartela</div>
                        <div class="flex w-full flex-col space-y-4">
                            <InputLabel class="!text-2xl">Add Cartela Number (1-50)</InputLabel>
                            <Input v-model="cartelaName" type="number" class="h-24 font-semibold text-5xl border-black"
                                   placeholder="Ex:20,16,50,44..."/>
                            <span class="text-red-600" v-for="(message, index) in errorMessages"
                                  :key="index">* {{ message }}</span>

                        </div>
                    </div>
                </SheetDescription>
            </SheetHeader>
        </SheetContent>
    </Sheet>
</template>
