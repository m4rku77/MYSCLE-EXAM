<script setup>
import { useRouter, useRoute } from "vue-router";
import { ref, onMounted } from "vue";
import axios from "axios";

const router = useRouter();
const route = useRoute();

const user = ref(null);
const unreadMessages = ref(0);

const fetchUser = async () => {
    try {
        const token = localStorage.getItem("token");

        const res = await axios.get("http://localhost:8000/api/me", {
            headers: { Authorization: `Bearer ${token}` },
        });

        user.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

onMounted(fetchUser);

const go = (path) => {
    router.push(path);
};

const isActive = (type) => {
    if (type === "workouts") {
        return (
            route.path === "/" ||
            route.path === "/dashboard" ||
            route.path.startsWith("/workout/")
        );
    }

    if (type === "statistics") {
        return route.path === "/statistics";
    }

    if (type === "friends") {
        return route.path === "/friends";
    }

    if (type === "messages") {
        return route.path.startsWith("/messages");
    }

    if (type === "profile") {
        return route.path === "/profile";
    }

    return false;
};
</script>

<template>
    <div
        class="h-20 bg-[#0f0f0f] border-t border-gray-800 flex justify-around items-center text-xs"
    >
        <button
            @click="go('/dashboard')"
            class="flex flex-col items-center"
            :class="isActive('workouts') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-clipboard-list text-lg"></i>
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
            <i class="fas fa-user-friends text-lg"></i>
            <span>Friends</span>
        </button>

        <button
            @click="go('/messages')"
            class="flex flex-col items-center relative"
            :class="isActive('messages') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <i class="fas fa-comment-dots text-lg"></i>

            <span
                v-if="unreadMessages > 0"
                class="absolute -top-1 -right-2 bg-red-500 text-[10px] px-1.5 rounded-full"
            >
                {{ unreadMessages }}
            </span>

            <span>Messages</span>
        </button>

        <button
            @click="go('/profile')"
            class="flex flex-col items-center"
            :class="isActive('profile') ? 'text-[#7ED957]' : 'text-gray-500'"
        >
            <img
                :src="
                    user?.profile_photo
                        ? 'http://localhost:8000/storage/' + user.profile_photo
                        : `https://ui-avatars.com/api/?name=${user?.name || 'User'}`
                "
                class="w-6 h-6 rounded-full object-cover"
            />
            <span>Profile</span>
        </button>
    </div>
</template>
