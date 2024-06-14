import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function formatToPrice(value) {
    return Number(value.toFixed(2)).toLocaleString("en-US");
}
