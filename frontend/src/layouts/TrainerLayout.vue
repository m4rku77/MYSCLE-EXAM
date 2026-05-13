<script setup>
import TrainerBottomNav from "../components/TrainerBottomNav.vue";
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#0f0f0f] text-white">
        <div class="flex-1 overflow-y-auto">
            <RouterView />
        </div>

        <TrainerBottomNav />
    </div>
</template>
