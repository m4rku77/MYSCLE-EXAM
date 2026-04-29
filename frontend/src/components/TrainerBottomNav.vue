<script setup>
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const go = (path) => {
    router.push(path);
};

const isActive = (type) => {
    if (type === "clients") {
        return route.path === "/trainer";
    }

    if (type === "plans") {
        return route.path.startsWith("/trainer/plans");
    }

    if (type === "messages") {
        return route.path.startsWith("/trainer/messages");
    }

    if (type === "profile") {
        return route.path.startsWith("/trainer/profile");
    }

    return false;
};
</script>

<template>
    <div
        class="h-20 bg-[#0f0f0f] border-t border-gray-800 flex justify-around items-center text-xs"
    >
        <button
            @click="go('/trainer')"
            class="flex flex-col items-center"
            :class="isActive('clients') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-users text-lg"></i>
            <span>Clients</span>
        </button>

        <button
            @click="go('/trainer/plans')"
            class="flex flex-col items-center"
            :class="isActive('plans') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-dumbbell text-lg"></i>
            <span>Plans</span>
        </button>

        <button
            @click="go('/trainer/messages')"
            class="flex flex-col items-center"
            :class="isActive('messages') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-comment-dots text-lg"></i>
            <span>Messages</span>
        </button>

        <button
            @click="go('/trainer/profile')"
            class="flex flex-col items-center"
            :class="isActive('profile') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-user text-lg"></i>
            <span>Profile</span>
        </button>
    </div>
</template>
