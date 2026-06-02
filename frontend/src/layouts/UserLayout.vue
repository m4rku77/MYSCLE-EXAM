<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import UserBottomNav from "../components/UserBottomNav.vue";
import UserSidebar from "../components/UserSidebar.vue";

const route = useRoute();

const shouldHideNav = computed(() => route.path.startsWith("/workout/"));
</script>

<template>
    <div class="min-h-screen flex bg-[#080808] text-white">
        <UserSidebar />
        <div class="flex-1 flex flex-col">
            <RouterView />
        </div>
        <UserBottomNav v-if="!shouldHideNav" class="md:hidden" />
    </div>
</template>
