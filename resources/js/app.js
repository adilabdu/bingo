import './bootstrap';
import '../css/app.css';

import {createApp, h, provide, ref} from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const notificationData = ref(null);

const showNotification = (data) => {
    notificationData.value = data;
};

// TODO: Migrate the two providers
provide("showNotification", showNotification);
provide("notificationData", notificationData);

function getLayout(name) {
    switch (true) {
        case name.startsWith("Admin/"):
            return AdminLayout;
        case name.startsWith(name === "Welcome"):
            return GuestLayout;
        default:
            return AuthenticatedLayout;
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
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
