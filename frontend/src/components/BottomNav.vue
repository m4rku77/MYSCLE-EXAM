<script setup>
import { useRouter, useRoute } from "vue-router"

const router = useRouter()
const route = useRoute()

const go = (path) => {
  router.push(path)
}

const isActive = (type) => {
  if (type === "workouts") {
    return (
      route.path === "/" ||
      route.path === "/dashboard" ||
      route.path.startsWith("/workout/")
    )
  }

  if (type === "statistics") {
    return route.path === "/statistics"
  }

  return false
}
</script>

<template>
  <div class="h-20 bg-[#0f0f0f] border-t border-gray-800 flex justify-around items-center text-xs">
    <button
      @click="go('/dashboard')"
      class="flex flex-col items-center"
      :class="isActive('workouts') ? 'text-[#7ED957]' : 'text-gray-500'"
    >
      <i class="fas fa-clipboard-list text-lg"></i>
      <span>Workouts</span>
    </button>

    <button
      @click="go('/statistics')"
      class="flex flex-col items-center"
      :class="isActive('statistics') ? 'text-[#7ED957]' : 'text-gray-500'"
    >
      <i class="fas fa-chart-line text-lg"></i>
      <span>Statistics</span>
    </button>

    <button class="flex flex-col items-center text-gray-500">
      <i class="fas fa-cog text-lg"></i>
      <span>Settings</span>
    </button>
  </div>
</template>