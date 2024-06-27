<script setup>
import {Banknote, GiftIcon, PhoneCallIcon, LockKeyholeIcon, Percent, SendIcon, InstagramIcon, Twitter, FacebookIcon} from "lucide-vue-next";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import boa from "../../../public/assets/images/banks/boa.webp";
import awash from "../../../public/assets/images/banks/awash_bank.webp";
import cbe from "../../../public/assets/images/banks/cbe.webp";
import dashen from "../../../public/assets/images/banks/dashen.webp";
import zemen from "../../../public/assets/images/banks/zemen.webp";
import telebirr from "../../../public/assets/images/banks/telebirr.webp";
import coop from "../../../public/assets/images/banks/coop.webp";
import {Button} from "@/Components/shadcn/ui/button/index.js";
import {router} from "@inertiajs/vue3";

const benefits = [
    {
        icon: Banknote,
        title: 'Easy Deposits and Withdrawals',
        sub_title: 'Withdraw and deposit using any bank'
    },
    {
        icon: Percent,
        title: 'We maximize your winnings',
        sub_title: 'We offer the best commission rates'
    },
    {
        icon: PhoneCallIcon,
        title: '24/7 Customer Support',
        sub_title: 'We are always here to help you',
    },
    {
        icon: LockKeyholeIcon,
        title: 'Secure and Reliable',
        sub_title: 'We are secure and reliable'
    }


]
</script>

<template>
    <div class="flex flex-col w-full min-h-screen">
        <div class="flex space-x-2 justify-between w-11/12 md:w-full items-center mx-auto py-5">
            <div class="flex space-x-2 text-2xl md:text-4xl font-bold justify-center items-center">
                <img class="w-12 md:w-20 object-contain" src="../../../public/assets/images/logo.png">
                <span class="text-brand-primary">Kiwi</span> <span class="text-brand-secondary">Bingo</span>
            </div>
            <div class="flex space-x-6">
                <PrimaryButton @click="router.visit('/register')" class="!bg-brand-primary">Register</PrimaryButton>
                <PrimaryButton @click="router.visit('/login')"  class="!bg-brand-secondary hidden md:inline-block">Login</PrimaryButton>
            </div>
        </div>
    <div class="w-full flex flex-col lg:flex-row md:space-y-7 px-5 items-center font-poppins md:justify-between">
        <div class="flex flex-col space-y-4 md:space-y-10 py-5 md:w-6/12">
            <div class="text-5xl md:text-6xl font-semibold text-center md:text-start leading-tight md:leading-snug py-4 md:py-5">
                Join the fun <br/> Play Bingo, Win Big!
            </div>

            <div class="flex flex-col px-3 w-full justify-center items-center divide-y divide-gray-200">
                <div class="flex justify-evenly w-full items-center py-4 md:py-6" v-for="item in benefits">
                    <div class="w-2/12">
                        <component :is="item.icon" />
                    </div>
                    <div class="w-10/12 flex flex-col space-y-2">
                        <span class="font-semibold">{{ item.title }}</span>
                        <span class="font-light text-sm">{{ item.sub_title }}</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex flex-col space-y-10 sm:space-y-16 py-5 min-h-full h-full items-center md:w-4/12">
            <div class="w-full flex flex-wrap space-x-2 pb-5 justify-evenly">
                <img class="w-16 md:w-24 object-contain my-2" :src="cbe">
                <img class="w-16 md:w-24 object-contain my-2" :src="telebirr">
                <img class="w-16 md:w-24 object-contain my-2" :src="boa">
                <img class="w-16 md:w-24 object-contain my-2" :src="awash">
                <img class="w-12 object-contain my-2" :src="dashen">
                <img class="w-16 md:w-24 object-contain my-2" :src="zemen">
                <img class="w-16 md:w-24 object-contain my-2" :src="coop">
            </div>

            <div class="bg-brand-secondary text-white w-full rounded-lg p-3 text-center flex justify-center space-x-4">
                <GiftIcon/>
                <span>Register and get free 20Br</span>
            </div>

            <div class="w-full flex flex-wrap space-x-2 justify-evenly">
                <SendIcon/>
                <InstagramIcon/>
                <FacebookIcon/>
                <Twitter/>
            </div>

            <div class="flex justify-evenly w-full">
               <div class="flex space-x-2 items-center w-5/12">
                   <PhoneCallIcon class="w-4 md:w-8"/>
                <a href="" class="hover:underline underline-offset-4">+251943104396</a>
               </div>
                <div class="flex space-x-2 justify-center w-5/12">
                    <PhoneCallIcon class="w-4 md:w-8"/>
                    <a href="" class="hover:underline underline-offset-4">+251943104396</a>
                </div> </div>
        </div>
    </div>

    </div>
</template>
