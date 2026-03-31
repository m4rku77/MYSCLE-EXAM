<script setup>
import { useRoute, useRouter } from "vue-router"
import { ref, onMounted } from "vue"
import axios from "axios"

const route = useRoute()
const router = useRouter()

const workoutId = route.params.id

const workout = ref(null)
const exercises = ref([])
const loading = ref(true)

// editable sets
const setData = ref({})

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      `http://localhost:8000/api/workouts/${workoutId}/track`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    workout.value = res.data
    exercises.value = res.data.exercises ?? []

    // ✅ initialize sets from DB
    exercises.value.forEach(ex => {
      setData.value[ex.id] = ex.sets?.map((set, index) => ({
        id: set.id,
        setNumber: index + 1,
        reps: set.reps,
        weight: set.weight
      })) || []
    })

  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})

// ➕ add new set
const addSet = (ex) => {
  setData.value[ex.id].push({
    setNumber: setData.value[ex.id].length + 1,
    reps: 0,
    weight: 0
  })
}

// ❌ remove set
const removeSet = (ex, index) => {
  setData.value[ex.id].splice(index, 1)

  // re-number
  setData.value[ex.id].forEach((set, i) => {
    set.setNumber = i + 1
  })
}

// 💾 save
const saveExercise = async (ex) => {
  try {
    const token = localStorage.getItem("token")

    await axios.put(
      `http://localhost:8000/api/exercises/${ex.id}`,
      {
        name: ex.name,
        sets_data: setData.value[ex.id]
      },
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    alert("Saved ✅")
  } catch (err) {
    console.error(err)
  }
}
</script>

<template>
  <div class="h-screen bg-[#0f0f0f] text-white flex flex-col">

    <div class="p-6 border-b border-gray-800 flex items-center gap-4">
      <button @click="router.back()" class="text-[#7ED957] text-xl">
        ←
      </button>

      <h1 class="text-xl font-semibold">
        {{ workout?.name || "Loading..." }}
      </h1>
    </div>

    <div class="flex-1 p-6 overflow-y-auto">

      <div v-if="loading" class="text-gray-400">
        Loading workout...
      </div>

      <div v-else class="space-y-6">

        <div
          v-for="ex in exercises"
          :key="ex.id"
          class="bg-white/5 border border-white/10 rounded-xl p-4"
        >

          <input
            v-model="ex.name"
            class="bg-black/30 p-2 rounded w-full outline-none mb-4"
            placeholder="Exercise name"
          />

          <div class="overflow-hidden rounded-lg border border-white/10">

            <div class="grid grid-cols-4 bg-white/10 px-4 py-3 text-sm font-medium text-gray-300">
              <span>Set</span>
              <span>Reps</span>
              <span>Weight</span>
              <span></span>
            </div>

            <div
              v-for="(row, index) in setData[ex.id]"
              :key="row.id || index"
              class="grid grid-cols-4 px-4 py-3 text-sm border-t border-white/5 items-center"
            >
              <span>{{ index + 1 }}</span>

              <input
                v-model="row.reps"
                type="number"
                class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
              />

              <input
                v-model="row.weight"
                type="number"
                class="bg-transparent border border-white/10 rounded px-2 py-1 text-center"
              />

              <button
                @click="removeSet(ex, index)"
                class="text-red-400 text-sm"
              >
                ✕
              </button>
            </div>

          </div>

          <div class="flex gap-3 mt-4">

            <button
              @click="addSet(ex)"
              class="bg-white/10 px-3 py-1 rounded text-sm"
            >
              + Add Set
            </button>

            <button
              @click="saveExercise(ex)"
              class="bg-[#7ED957] text-black px-4 py-2 rounded"
            >
              Save
            </button>

          </div>

        </div>

      </div>

    </div>
  </div>
</template>