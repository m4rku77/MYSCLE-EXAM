<script setup>
import { ref } from "vue"

const active = ref("dashboard")

const menuItems = [
  { name: "dashboard", label: "Dashboard" },
  { name: "workouts", label: "Workouts" },
  { name: "plans", label: "Plans" },
  { name: "progress", label: "Progress" },
  { name: "profile", label: "Profile" }
]
</script>

<template>
  <aside class="w-20 bg-[#0f0f0f] border-r border-white/5 flex flex-col items-center py-6">
    <img src="/logo.png" class="h-10 mb-10" />

    <div class="flex flex-col gap-4">
      <button
        v-for="item in menuItems"
        :key="item.name"
        class="w-12 h-12 rounded-xl flex items-center justify-center transition-all"
        :class="active === item.name
          ? 'bg-[#7ED957] text-black scale-110'
          : 'bg-white/5 hover:bg-white/10 text-gray-400'"
        @click="active = item.name"
      >
        <div class="w-2 h-2 rounded-full bg-current"></div>
      </button>
    </div>
  </aside>
</template>