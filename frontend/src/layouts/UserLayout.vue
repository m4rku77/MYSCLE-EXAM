<script setup>
import Sidebar from "../components/Sidebar.vue"
import Topbar from "../components/Topbar.vue"

defineProps({
  title: String
})
</script>

<template>
  <div class="h-screen flex bg-[#0b0b0b] text-white">

    <Sidebar />

    <div class="flex-1 flex flex-col">
      <Topbar :title="title" />

      <main class="flex-1 p-8">
        <slot />
      </main>
    </div>

  </div>
</template>