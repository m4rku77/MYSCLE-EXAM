<script setup>
import { useRoute, useRouter } from "vue-router"
import { ref, onMounted } from "vue"
import axios from "axios"
import BottomNav from "../components/BottomNav.vue"

const route = useRoute()
const router = useRouter()

const workoutId = route.params.id

const workout = ref(null)
const exercises = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      `http://localhost:8000/api/workouts/${workoutId}`,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json"
        }
      }
    )

    const data = res.data.data ?? res.data

    workout.value = data

    exercises.value = data.exercises ?? []

  } catch (err) {
    console.error("ERROR:", err.response?.data || err.message)
  } finally {
    loading.value = false
  }
})

const saveChanges = async () => {
  try {
    const token = localStorage.getItem("token")

    for (const ex of exercises.value) {
      await axios.put(
        `http://localhost:8000/api/exercises/${ex.id}`,
        {
          name: ex.name,
          sets_data: ex.sets // ✅ correct format
        },
        {
          headers: {
            Authorization: `Bearer ${token}`
          }
        }
      )
    }

    console.log("Saved ✅")
  } catch (err) {
    console.error("SAVE ERROR:", err.response?.data || err.message)
  }
}
</script>

<template>
  <div v-if="loading" class="h-screen flex items-center justify-center bg-[#0f0f0f] text-gray-400">
    Loading workout...
  </div>


  <div v-else-if="workout" class="h-screen flex flex-col bg-[#0f0f0f] text-white">

<div class="p-6 border-b border-gray-800 flex items-center justify-between">
  <div class="flex items-center gap-4">
    <button @click="router.back()" class="text-[#7ED957] text-xl">←</button>

    <input
      v-model="workout.name"
      class="bg-black/20 px-3 py-2 rounded text-xl font-semibold w-full max-w-xs"
    />
  </div>

  <button
    @click="saveChanges"
    class="bg-[#7ED957] text-black px-4 py-2 rounded-lg text-sm font-semibold"
  >
    Save
  </button>
</div>

<div class="flex-1 overflow-y-auto p-6 space-y-6">

  <div class="bg-white/5 border border-white/10 rounded-xl p-4">
    <h2 class="text-lg font-semibold mb-3">Workout Overview</h2>

    <div class="grid grid-cols-3 gap-4 text-center">

      <div class="bg-black/30 p-3 rounded-lg">
        <p class="text-xs text-gray-500">Exercises</p>
        <p class="text-lg text-[#7ED957] font-bold">
          {{ exercises.length }}
        </p>
      </div>

      <div class="bg-black/30 p-3 rounded-lg">
        <p class="text-xs text-gray-500">Sets</p>
        <p class="text-lg text-[#7ED957] font-bold">
          {{ exercises.reduce((sum, ex) => sum + (ex.sets?.length || 0), 0) }}
        </p>
      </div>

      <div class="bg-black/30 p-3 rounded-lg">
        <p class="text-xs text-gray-500">Reps</p>
        <p class="text-lg text-[#7ED957] font-bold">
          {{
            exercises.reduce((sum, ex) =>
              sum + ex.sets.reduce((s, set) => s + (set.reps || 0), 0),
            0)
          }}
        </p>
      </div>

    </div>
  </div>

  <div
    v-for="ex in exercises"
    :key="ex.id"
    class="bg-white/5 border border-white/10 rounded-xl p-5"
  >

    <div class="mb-3">
      <input
        v-model="ex.name"
        class="bg-black/20 px-3 py-2 rounded w-full"
      />
    </div>
    <div class="overflow-hidden rounded-lg border border-white/10">

      <div class="grid grid-cols-3 bg-white/10 px-4 py-2 text-sm font-medium text-gray-300">
        <span>Set</span>
        <span>Reps</span>
        <span>Weight</span>
      </div>

      <div
        v-for="(set, index) in ex.sets"
        :key="index"
        class="grid grid-cols-3 px-4 py-2 text-sm border-t border-white/5 items-center"
      >
        <span class="text-gray-400">{{ index + 1 }}</span>

        <input
          v-model.number="set.reps"
          type="number"
          class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
        />

        <input
          v-model.number="set.weight"
          type="number"
          class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
        />
      </div>

    </div>
  </div>

  <div v-if="exercises.length === 0" class="text-gray-500">
    No exercises found.
  </div>

</div>

<BottomNav />
  </div>


  <div v-else class="h-screen flex items-center justify-center bg-[#0f0f0f] text-gray-400">
    Workout not found.
  </div>
</template>
