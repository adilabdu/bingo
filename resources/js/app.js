import './bootstrap';
import '../css/app.css';

import {computed, createApp, h, provide, ref} from 'vue';
import {createInertiaApp, usePage} from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { createPinia } from 'pinia';
import CashierLayout from "@/Layouts/CashierLayout.vue";
import AgentLayout from "@/Layouts/AgentLayout.vue";
import BottomNavBar from "@/Layouts/BottomNavBar.vue";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const notificationData = ref(null);
const showNotification = (data) => {
    notificationData.value = data;
};

// TODO: Migrate the two providers
provide("showNotification", showNotification);
provide("notificationData", notificationData);

function getLayout(name) {
    if (name.startsWith("Auth/") || name === "Welcome")
        return GuestLayout;
    else if (window.innerWidth >= 1024) {
        switch (true) {
            case name.startsWith("Admin/"):
                return AdminLayout;
            case name.startsWith("Cashier/") || name.includes("Cashier"):
                return CashierLayout;
            case name.startsWith("Agent/"):
                return AgentLayout;
            default:
                return AuthenticatedLayout;
        }
    }
    else {
            return BottomNavBar;
        }
}


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];
        page.default.layout = getLayout(name, page);
        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(createPinia())
            .mount(el);
    },
    progress: {
        color: '#1E9F32',
    },
});
