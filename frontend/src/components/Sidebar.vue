<script setup>
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const menuItems = [
    {
        name: "dashboard",
        label: "Dashboard",
        path: "/dashboard",
        icon: "fa-solid fa-house",
    },
    {
        name: "workouts",
        label: "Workouts",
        path: "/workout",
        icon: "fa-solid fa-dumbbell",
    },
    {
        name: "statistics",
        label: "Statistics",
        path: "/statistics",
        icon: "fa-solid fa-chart-line",
    },
    {
        name: "friends",
        label: "Friends",
        path: "/friends",
        icon: "fa-solid fa-user-group",
    },
    {
        name: "messages",
        label: "Messages",
        path: "/messages",
        icon: "fa-solid fa-message",
    },
    {
        name: "profile",
        label: "Profile",
        path: "/profile",
        icon: "fa-solid fa-user",
    },
];

const isActive = (path) => route.path.startsWith(path);
</script>

<template>
    <aside
        class="w-20 bg-[#0f0f0f] border-r border-white/5 flex flex-col items-center py-6"
    >
        <img src="/logo.png" class="h-10 mb-10" />

        <div class="flex flex-col gap-3">
            <button
                v-for="item in menuItems"
                :key="item.name"
                @click="router.push(item.path)"
                :title="item.label"
                class="w-12 h-12 rounded-xl flex items-center justify-center transition-all"
                :class="
                    isActive(item.path)
                        ? 'bg-[#7ED957] text-black scale-110'
                        : 'bg-white/5 hover:bg-white/10 text-gray-400'
                "
            >
                <i :class="item.icon" class="text-base"></i>
            </button>
        </div>
    </aside>
</template>
