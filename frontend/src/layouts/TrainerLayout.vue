<script setup>
import { computed } from "vue";

import { useRoute } from "vue-router";
import TrainerBottomNav from "../components/TrainerBottomNav.vue";
import TrainerSidebar from "../components/TrainerSidebar.vue";

const route = useRoute();

const shouldHideNav = computed(() =>
    /^\/trainer\/messages\/\d+/.test(route.path),
);
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
