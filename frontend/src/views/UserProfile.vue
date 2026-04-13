<script setup>
import { ref, onMounted } from "vue"
import { useRoute } from "vue-router"
import axios from "axios"

const route = useRoute()
const user = ref(null)

const fetchUser = async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      `http://localhost:8000/api/users/${route.params.id}`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    user.value = res.data
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchUser)

const formatDate = (date) => {
  if (!date) return ""

  const d = new Date(date)

  return d.toLocaleDateString("en-US", {
    month: "long",
    year: "numeric"
  })
}
</script>

<template>
  <div class="min-h-screen bg-[#0f0f0f] text-white px-4 py-10 flex justify-center">

    <div v-if="user" class="w-full max-w-2xl space-y-8">

      <div class="bg-[#151515] rounded-2xl p-6 flex items-center gap-5 border border-white/10">
        
        <img
          :src="user.profile_photo || `https://ui-avatars.com/api/?name=${user.name}`"
          class="w-20 h-20 rounded-full object-cover"
        />

        <div class="space-y-1">
          <h1 class="text-2xl font-bold">
            {{ user.name }}
          </h1>

          <p class="text-xs text-gray-500">
            Joined {{ formatDate(user.created_at) }}
          </p>
        </div>
      </div>

      <div class="bg-[#151515] rounded-2xl p-5 border border-white/10 space-y-4">

        <h2 class="text-sm text-gray-400 uppercase tracking-wide">
          Info
        </h2>

        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2 text-gray-400">
            <i class="fas fa-globe"></i>
            <span>Country</span>
          </div>
          <span :class="user.country ? 'text-white' : 'text-gray-500 italic'">
            {{ user.country || "Not set" }}
          </span>
        </div>

        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2 text-gray-400">
            <i class="fas fa-dumbbell"></i>
            <span>Gym</span>
          </div>
          <span :class="user.gym ? 'text-white' : 'text-gray-500 italic'">
            {{ user.gym || "Not set" }}
          </span>
        </div>

      </div>

      <div class="bg-[#151515] rounded-2xl p-5 border border-white/10">
        <p :class="user.bio ? 'text-gray-300' : 'text-gray-500 italic'" class="text-sm">
          {{ user.bio || "No bio yet" }}
        </p>
      </div>

      <div class="grid grid-cols-3 gap-4">

        <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
          <p class="text-xl font-bold text-[#7ED957]">
            {{ user.stats.workouts }}
          </p>
          <p class="text-xs text-gray-400 mt-1">Workouts</p>
        </div>

        <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
          <p class="text-xl font-bold text-[#7ED957]">
            {{ user.stats.sets }}
          </p>
          <p class="text-xs text-gray-400 mt-1">Sets</p>
        </div>

        <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
          <p class="text-xl font-bold text-[#7ED957]">
            {{ user.stats.reps }}
          </p>
          <p class="text-xs text-gray-400 mt-1">Reps</p>
        </div>

      </div>

      <div class="bg-[#151515] rounded-2xl p-5 border border-white/10 space-y-4">

        <h2 class="text-sm text-gray-400 uppercase tracking-wide">
          Activity
        </h2>

        <div class="flex justify-between text-sm">
          <span class="text-gray-400">Total Volume</span>
          <span class="text-[#7ED957] font-semibold">
            {{ user.stats.reps * user.stats.sets }}
          </span>
        </div>

        <div class="flex justify-between text-sm">
          <span class="text-gray-400">Avg Reps / Workout</span>
          <span>
            {{
              user.stats.workouts
                ? Math.round(user.stats.reps / user.stats.workouts)
                : 0
            }}
          </span>
        </div>

        <div class="flex justify-between text-sm">
          <span class="text-gray-400">Consistency</span>
          <span class="text-green-400">
            {{
              user.stats.workouts > 10
                ? "High"
                : user.stats.workouts > 3
                ? "Medium"
                : "Low"
            }}
          </span>
        </div>

      </div>

    </div>

  </div>
</template>