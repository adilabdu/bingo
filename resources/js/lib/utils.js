import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import {router} from "@inertiajs/vue3";

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function formatToPrice(value) {
    return Number(value.toFixed(2)).toLocaleString("en-US");
}

export function visit(url, options) {
    router.visit(url, options);
}
