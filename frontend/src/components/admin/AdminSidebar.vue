<script setup>
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const links = [
    { name: "Dashboard", path: "/admin", icon: "fas fa-chart-line" },
    { name: "Users", path: "/admin/users", icon: "fas fa-users" },
    {
        name: "Subscriptions",
        path: "/admin/subscriptions",
        icon: "fas fa-credit-card",
    },
    {
        name: "Activity Log",
        path: "/admin/activitylog",
        icon: "fas fa-history",
    },
];
</script>

<template>
    <aside
        class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full z-40"
    >
        <div class="flex items-center gap-3 mb-4">
            <img src="/logo.png" class="h-8" />
            <span class="font-black text-lg tracking-widest uppercase"
                >Myscle</span
            >
        </div>
        <p
            class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-8"
        >
            Admin Panel
        </p>

        <nav class="space-y-1 flex-1">
            <div
                v-for="link in links"
                :key="link.path"
                @click="router.push(link.path)"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    route.path === link.path
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i :class="link.icon" class="w-4"></i>
                <span>{{ link.name }}</span>
            </div>
        </nav>

        <div class="border-t border-white/5 pt-4">
            <div
                @click="router.push('/dashboard')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-gray-500 hover:text-white hover:bg-white/5 font-semibold text-sm cursor-pointer transition-all"
            >
                <i class="fas fa-arrow-left w-4"></i> Back to App
            </div>
        </div>
    </aside>
</template>
