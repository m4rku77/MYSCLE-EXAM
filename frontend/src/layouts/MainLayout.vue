<script setup>
import BottomNav from "../components/BottomNav.vue"
</script>

<template>
  <div class="h-screen flex flex-col bg-[#0f0f0f] text-white">

    <div class="flex-1 overflow-hidden">
      <RouterView />
    </div>

    <BottomNav />

  </div>
</template>