<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"

const router = useRouter()

const name = ref("")
const exercises = ref([])
const error = ref("")
const loading = ref(false)

const addExercise = () => {
  exercises.value.push({
    name: "",
    sets: []
  })
}

const removeExercise = (index) => {
  exercises.value.splice(index, 1)
}

const addSet = (exercise) => {
  exercise.sets.push({
    set_number: exercise.sets.length + 1,
    reps: "",
    weight: ""
  })
}

const removeSet = (exercise, index) => {
  exercise.sets.splice(index, 1)
}

const saveWorkout = async () => {
  if (!name.value) {
    error.value = "Workout name required"
    return
  }

  try {
    loading.value = true

    const token = localStorage.getItem("token")

    await axios.post("http://localhost:8000/api/workouts", {
      name: name.value,
      exercises: exercises.value
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json"
      }
    })

    router.push("/dashboard")

  } catch (err) {
    error.value = "Failed to create workout"
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="h-screen overflow-y-auto bg-[#0f0f0f] text-white px-4 py-10 md:py-16">

    <div class="w-full max-w-2xl mx-auto pb-72">

      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl md:text-3xl font-bold">
          Create Workout
        </h1>

        <button
          @click="router.push('/dashboard')"
          class="text-sm text-gray-400 hover:text-white"
        >
          Cancel
        </button>
      </div>

      <div class="mb-10">
        <label class="text-xs text-gray-500 mb-2 block">Workout Name</label>

        <input
          v-model="name"
          placeholder="Push Day"
          class="w-full bg-[#151515] border border-gray-700 rounded-xl px-4 py-3 outline-none focus:border-[#7ED957]"
        />
      </div>

      <div class="mb-8">

        <div
          v-for="(ex, exIndex) in exercises"
          :key="exIndex"
          class="bg-[#151515] rounded-xl p-5 mb-8"
        >

          <div class="flex justify-between items-center mb-4 mt-1">
            <span class="text-sm text-gray-400">
              Exercise {{ exIndex + 1 }}
            </span>

            <button
                @click="removeExercise(exIndex)"
                class="w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-md text-xs hover:bg-red-600 transition"
                >
                ✕
            </button>
          </div>

          <input
            v-model="ex.name"
            placeholder="Exercise name"
            class="w-full bg-[#0f0f0f] border border-gray-700 px-4 py-3 rounded-lg outline-none focus:border-[#7ED957] mb-5"
          />

          <div class="mb-4">

            <div
              v-for="(set, setIndex) in ex.sets"
              :key="setIndex"
              class="flex items-center gap-3 bg-[#0f0f0f] px-3 py-2 rounded-lg mb-3"
            >
              <span class="text-xs text-gray-400 w-10">
                {{ setIndex + 1 }}
              </span>

              <input
                v-model="set.reps"
                type="number"
                placeholder="Reps"
                class="w-20 bg-transparent outline-none"
              />

              <input
                v-model="set.weight"
                type="number"
                placeholder="kg"
                class="w-24 bg-transparent outline-none"
              />

              <button
                @click="removeSet(ex, setIndex)"
                class="text-red-400 text-xs ml-auto"
              >
                ✕
              </button>
            </div>

          </div>

          <button
            @click="addSet(ex)"
            class="text-[#7ED957] text-sm mt-1"
          >
            + Add Set
          </button>

        </div>

      </div>

      <p v-if="error" class="text-red-400 text-sm mb-3">
        {{ error }}
      </p>

    </div>

    <div class="fixed bottom-24 left-0 w-full flex justify-center px-4">

    <div class="bg-[#151515] border border-gray-800 rounded-2xl p-3 shadow-lg">

        <button
        @click="addExercise"
        class="w-full py-2 text-sm bg-[#0f0f0f] rounded-lg text-[#7ED957] hover:bg-[#1a1a1a] transition mb-2"
        >
        + Add Exercise
        </button>

        <button
        @click="saveWorkout"
        class="w-full py-2.5 p-3 text-sm bg-[#7ED957] text-black rounded-lg font-semibold hover:scale-[1.01] transition"
        >
        {{ loading ? "Saving..." : "Save Workout" }}
        </button>

    </div>


    </div>

  </div>
</template>