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

  if (type === "friends") {
    return route.path === "/friends"
  }

  if (type === "profile") {
    return route.path === "/profile"
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

    <button
      @click="go('/friends')"
      class="flex flex-col items-center"
      :class="isActive('friends') ? 'text-[#7ED957]' : 'text-gray-500'"
    >
      <i class="fas fa-user-friends text-lg"></i>
      <span>Friends</span>
    </button>

    <button
      @click="go('/profile')"
      class="flex flex-col items-center"
      :class="isActive('profile') ? 'text-[#7ED957]' : 'text-gray-500'"
    >
      <i class="fas fa-user text-lg"></i>
      <span>Profile</span>
    </button>

  </div>
</template>