<script setup>
import { ref } from "vue"

const active = ref("dashboard")

const menuItems = [
  { name: "dashboard", label: "Dashboard", icon: "📊" },
  { name: "workouts", label: "Workouts", icon: "🏋️" },
  { name: "plans", label: "Plans", icon: "📋" },
  { name: "progress", label: "Progress", icon: "📈" },
  { name: "profile", label: "Profile", icon: "👤" }
]

const setActive = (item) => {
  active.value = item
}
</script>

<template>
  <div class="h-screen flex bg-[#0f0f0f] text-white overflow-hidden">

    <aside class="w-64 bg-[#111] border-r border-gray-800 flex flex-col justify-between">

      <div class="p-6">

        <div class="flex items-center gap-3 mb-10">
          <img src="/logo.png" class="h-10" />
          <span class="font-semibold text-lg tracking-wide">MYSCLE</span>
        </div>

        <nav class="space-y-2">
          <button
            v-for="item in menuItems"
            :key="item.name"
            @click="setActive(item.name)"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm
                   transition-all duration-300 ease-in-out"
            :class="active === item.name
              ? 'bg-[#7ED957] text-black font-semibold'
              : 'text-gray-400 hover:bg-[#1a1a1a] hover:text-white'"
          >
            <span>{{ item.icon }}</span>
            <span>{{ item.label }}</span>
          </button>
        </nav>

      </div>

      <div class="p-6 text-xs text-gray-500">
        © 2026 MYSCLE
      </div>

    </aside>

    <div class="flex-1 flex flex-col">

      <header class="h-16 bg-[#151515] border-b border-gray-800 flex items-center justify-between px-6">

        <h1 class="text-lg font-semibold capitalize tracking-wide">
          {{ active }}
        </h1>

        <div class="flex items-center gap-4">

          <div class="relative">
            <div class="w-2 h-2 bg-red-500 rounded-full absolute top-0 right-0"></div>
            <span class="text-gray-400 text-lg">🔔</span>
          </div>

          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#7ED957] rounded-full"></div>
            <span class="text-sm text-gray-300">John</span>
          </div>

        </div>

      </header>

      <main class="flex-1 flex items-center justify-center text-gray-600 text-sm">
        Select a section from the sidebar
      </main>

    </div>

  </div>
</template>