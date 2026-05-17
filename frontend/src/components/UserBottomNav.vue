<script setup>
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const go = (path) => {
    router.push(path);
};

const isActive = (type) => {
    if (type === "dashboard") return route.path === "/dashboard";
    if (type === "statistics") return route.path.startsWith("/statistics");
    if (type === "friends") return route.path.startsWith("/friends");
    if (type === "messages") return route.path.startsWith("/messages");
    if (type === "profile") return route.path.startsWith("/profile");
    return false;
};
</script>

<template>
    <div
        class="fixed bottom-0 left-0 w-full h-20 bg-[#0f0f0f] border-t border-gray-800 flex justify-around items-center text-xs z-50"
    >
        <button
            @click="go('/dashboard')"
            class="flex flex-col items-center"
            :class="isActive('dashboard') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-dumbbell text-lg"></i>
            <span>Workouts</span>
        </button>

        <button
            @click="go('/statistics')"
            class="flex flex-col items-center"
            :class="isActive('statistics') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-chart-line text-lg"></i>
            <span>Statistics</span>
        </button>

        <button
            @click="go('/friends')"
            class="flex flex-col items-center"
            :class="isActive('friends') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-user-group text-lg"></i>
            <span>Friends</span>
        </button>

        <button
            @click="go('/messages')"
            class="flex flex-col items-center"
            :class="isActive('messages') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-comment-dots text-lg"></i>
            <span>Messages</span>
        </button>

        <button
            @click="go('/profile')"
            class="flex flex-col items-center"
            :class="isActive('profile') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-user text-lg"></i>
            <span>Profile</span>
        </button>
    </div>
</template>
