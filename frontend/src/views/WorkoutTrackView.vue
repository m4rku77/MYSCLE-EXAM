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

const isEditing = ref(false)

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      `http://localhost:8000/api/workouts/${workoutId}`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    workout.value = res.data
    exercises.value = res.data.exercises ?? []
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

/* ---------- TOGGLE EDIT ---------- */
const toggleEdit = async () => {
  if (isEditing.value) {
    await saveChanges()
  }
  isEditing.value = !isEditing.value
}

/* ---------- SAVE ---------- */
const saveChanges = async () => {
  try {
    const token = localStorage.getItem("token")

    for (const ex of exercises.value) {
      await axios.put(
        `http://localhost:8000/api/exercises/${ex.id}`,
        {
          name: ex.name,
          sets_data: ex.sets
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
    console.error(err)
  }
}
</script>

<template>
  <div class="h-screen flex flex-col bg-[#0f0f0f] text-white">

    <!-- HEADER -->
    <div class="p-6 border-b border-gray-800 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="text-[#7ED957] text-xl">←</button>

        <h1 class="text-xl font-semibold">
          {{ workout?.name || "Loading..." }}
        </h1>
      </div>

      <button
        @click="toggleEdit"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition"
        :class="isEditing
          ? 'bg-yellow-400 text-black'
          : 'bg-[#7ED957] text-black'"
      >
        <i class="fas fa-pen"></i>
        {{ isEditing ? "Save" : "Edit" }}
      </button>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 overflow-y-auto p-6">

      <div v-if="loading" class="text-gray-400">
        Loading workout...
      </div>

      <div v-else class="space-y-6">

        <!-- OVERVIEW -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
          <h2 class="text-lg font-semibold mb-3">Workout Overview</h2>

          <div class="flex flex-wrap gap-6 text-sm text-gray-300">
            <div>
              <span class="text-[#7ED957] font-semibold">
                {{ exercises.length }}
              </span>
              exercises
            </div>

            <div>
              <span class="text-[#7ED957] font-semibold">
                {{ exercises.reduce((sum, ex) => sum + (ex.sets?.length || 0), 0) }}
              </span>
              total sets
            </div>

            <div>
              <span class="text-[#7ED957] font-semibold">
                {{
                  exercises.reduce((sum, ex) =>
                    sum + (ex.sets?.reduce((s, set) => s + set.reps, 0) || 0),
                  0)
                }}
              </span>
              total reps
            </div>
          </div>
        </div>

        <!-- EXERCISES -->
        <div
          v-for="ex in exercises"
          :key="ex.id"
          class="bg-white/5 border border-white/10 rounded-xl p-4"
        >
          <!-- NAME -->
          <div class="mb-4">
            <input
              v-if="isEditing"
              v-model="ex.name"
              class="bg-black/5 px-3 py-2 rounded w-full"
            />
            <h2 v-else class="text-lg font-semibold text-[#7ED957]">
              {{ ex.name }}
            </h2>
          </div>

          <!-- SETS -->
          <div class="overflow-hidden rounded-lg border border-white/10">

            <div class="grid grid-cols-3 bg-white/10 px-4 py-3 text-sm font-medium text-gray-300">
              <span>Set</span>
              <span>Reps</span>
              <span>Weight</span>
            </div>

            <div
              v-for="(set, index) in ex.sets"
              :key="set.id"
              class="grid grid-cols-3 px-4 py-3 text-sm border-t border-white/5 items-center"
            >
              <span>{{ index + 1 }}</span>

              <!-- REPS -->
              <input
                v-if="isEditing"
                v-model="set.reps"
                type="number"
                class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
              />
              <span v-else>{{ set.reps }}</span>

              <!-- WEIGHT -->
              <input
                v-if="isEditing"
                v-model="set.weight"
                type="number"
                class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
              />
              <span v-else>{{ set.weight }} kg</span>
            </div>

          </div>
        </div>

        <div v-if="exercises.length === 0" class="text-gray-500">
          No exercises found.
        </div>

      </div>
    </div>

    <BottomNav />
  </div>
</template>