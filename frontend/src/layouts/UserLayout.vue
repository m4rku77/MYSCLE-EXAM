<script setup>
import { useRoute } from "vue-router";
import UserBottomNav from "../components/UserBottomNav.vue";

const route = useRoute();

const hideNav = ["/dashboard"];
const shouldHideNav =
    hideNav.includes(route.path) || route.path.startsWith("/workout/");
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#0f0f0f] text-white">
        <div class="flex-1 overflow-y-auto">
            <RouterView />
        </div>

        <UserBottomNav v-if="!shouldHideNav" />
    </div>
</template>
