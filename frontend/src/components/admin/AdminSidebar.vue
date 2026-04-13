<script setup>
import { useRouter, useRoute } from "vue-router"

const router = useRouter()
const route = useRoute()

const links = [
  { name: "Dashboard", path: "/admin", icon: "fas fa-chart-line" },
  { name: "Users", path: "/admin/users", icon: "fas fa-users" },
  { name: "Workouts", path: "/admin/workouts", icon: "fas fa-dumbbell" },
]

const go = (path) => {
  router.push(path)
}
</script>

<template>
  <div class="h-screen w-64 bg-[#111] border-r border-gray-800 flex flex-col">

    <div class="p-6 border-b border-gray-800">
      <h1 class="text-xl font-bold text-[#7ED957]">ADMIN</h1>
      <p class="text-xs text-gray-400">Dashboard</p>
    </div>

    <div class="flex-1 p-4 space-y-2">

      <button
        v-for="link in links"
        :key="link.path"
        @click="go(link.path)"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition"
        :class="route.path === link.path
            ? 'bg-[#7ED957] text-black font-semibold'
            : 'text-gray-300 hover:bg-[#1f1f1f]'"
        >
        <i :class="link.icon"></i>
        <span>{{ link.name }}</span>
    </button>

    </div>

    <div class="p-4 border-t border-gray-800">
      <button
        @click="router.push('/dashboard')"
        class="w-full text-sm text-gray-400 hover:text-white transition"
      >
        ← Back to App
      </button>
    </div>

  </div>
</template>