<script setup>
import { useRoute } from "vue-router";
import TrainerBottomNav from "../components/TrainerBottomNav.vue";
import TrainerSidebar from "../components/TrainerSidebar.vue";

const route = useRoute();

const shouldHideNav = route.path.startsWith("/trainer/messages");
</script>

<template>
    <div class="min-h-screen flex bg-[#080808] text-white">
        <TrainerSidebar />
        <div class="flex-1 flex flex-col">
            <RouterView />
        </div>
        <TrainerBottomNav v-if="!shouldHideNav" class="md:hidden" />
    </div>
</template>
