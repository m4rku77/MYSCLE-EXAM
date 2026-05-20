<script setup>
import { useRoute } from "vue-router";
import UserBottomNav from "../components/UserBottomNav.vue";

const route = useRoute();

const shouldHideNav =
    route.path === "/dashboard" ||
    route.path.startsWith("/workout/") ||
    route.path === "/statistics" ||
    route.path === "/create-workout" ||
    route.path === "/friends";
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#0f0f0f] text-white">
        <div class="flex-1 overflow-y-auto">
            <RouterView />
        </div>
        <UserBottomNav v-if="!shouldHideNav" />
    </div>
</template>
