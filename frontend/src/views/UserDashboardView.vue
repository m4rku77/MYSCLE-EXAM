<script setup>
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"
import BottomNav from "../components/BottomNav.vue"

const router = useRouter()

const isEditing = ref(false)
const activeTab = ref("workouts")
const workouts = ref([])

const createWorkout = () => {
  router.push('/create-workout')
}

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    if (!token) {
      return
    }

    const res = await axios.get("http://localhost:8000/api/workouts", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json"
      }
    })

    const data = res.data.data ?? res.data

    workouts.value = data.map(w => ({
      id: w.id,
      name: w.name,
      exercises: w.exercises?.length || 0,
      sets: w.exercises?.reduce((sum, ex) =>
        sum + (ex.sets?.length || 0), 0
      ) || 0,
      reps: w.exercises?.reduce((sum, ex) =>
        sum + (ex.sets?.reduce((s, set) => s + (set.reps || 0), 0) || 0),
      0) || 0
    }))

  } catch (err) {
    workouts.value = []
  }
})

const toggleEdit = () => {
  isEditing.value = !isEditing.value
}

const openWorkout = (w) => {
  router.push(`/workout/${w.id}`)
}

const goToTab = (tab) => {
  activeTab.value = tab
  if (tab === 'statistics') router.push('/statistics')
  if (tab === 'workouts') router.push('/dashboard')
  if (tab === 'logs') router.push('/logs')
}
</script>

<template>
  <div class="h-screen bg-[#0f0f0f] text-white flex flex-col">
    <header class="hidden md:flex h-16 bg-[#151515] border-b border-gray-800 items-center justify-between px-6">
      <div class="flex items-center gap-3">
        <img src="/logo.png" class="h-10" />
        <span class="font-semibold text-lg">MYSCLE</span>
      </div>

      <button
        @click="toggleEdit"
        class="px-4 py-2 bg-[#7ED957] text-black rounded-lg text-sm"
      >
        {{ isEditing ? 'Save' : 'Edit' }}
      </button>
    </header>

    <div class="flex-1 flex">
      <div class="flex-1 flex flex-col relative">
        <div class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black p-6 pb-8 rounded-b-3xl">
          <h1 class="text-4xl font-bold mb-4">Workouts</h1>

          <input
            placeholder="Search Workouts"
            class="w-full bg-black/20 rounded-xl px-4 py-3 outline-none placeholder-black/50"
          />
        </div>

        <div class="flex-1 overflow-y-auto px-5 md:px-10 pt-6 pb-24 md:pb-10 space-y-6 max-w-4xl mx-auto w-full">
          <div v-if="workouts.length > 0">
            <div
              v-for="w in workouts"
              :key="w.id"
              @click="openWorkout(w)"
              class="group border border-white/5 bg-white/5 backdrop-blur-xl rounded-2xl p-5 cursor-pointer transition-all duration-300 hover:bg-white/10 hover:scale-[1.02]"
            >
              <input
                v-model="w.name"
                :disabled="!isEditing"
                @click.stop
                class="text-lg font-semibold bg-transparent outline-none w-full text-white group-hover:text-[#7ED957] transition"
              />

              <p class="text-gray-500 text-sm mt-2">
                Last Completed: Never
              </p>

              <div class="flex items-center gap-3 mt-3 text-sm text-gray-400">
                <div class="flex items-center gap-1">
                  <span class="text-[#7ED957] font-semibold">{{ w.exercises }}</span>
                  <span>ex</span>
                </div>

                <div class="w-1 h-1 bg-gray-600 rounded-full"></div>

                <div class="flex items-center gap-1">
                  <span class="text-[#7ED957] font-semibold">{{ w.sets }}</span>
                  <span>sets</span>
                </div>

                <div class="w-1 h-1 bg-gray-600 rounded-full"></div>

                <div class="flex items-center gap-1">
                  <span class="text-[#7ED957] font-semibold">{{ w.reps }}</span>
                  <span>reps</span>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="flex flex-col items-center justify-center text-gray-500 mt-20">
            <i class="fas fa-dumbbell text-3xl mb-3 opacity-50"></i>
            <p>No workouts yet</p>
          </div>
        </div>

        <BottomNav class="md:hidden fixed bottom-0 left-0 w-full z-40" />
      </div>

      <button
        @click="createWorkout"
        class="fixed bottom-24 md:bottom-6 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
      >
        <i class="fas fa-plus text-xl"></i>
      </button>
    </div>
  </div>
</template>