<script setup>
import { ref, onMounted, watch, nextTick } from "vue"
import axios from "axios"
import Chart from "chart.js/auto"

const workouts = ref([])
const filteredWorkouts = ref([])

const totalSets = ref(0)
const totalReps = ref(0)
const totalWorkouts = ref(0)

const selectedMonth = ref(new Date().getMonth())
const viewMode = ref("year")

const monthlyChartRef = ref(null)
let chartInstance = null

onMounted(async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get("http://localhost:8000/api/workouts", {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    workouts.value = res.data

    updateStats()
    await nextTick()

    if (viewMode.value === "year") createChart()

  } catch (err) {
    console.error(err)
  }
})

const updateStats = () => {
  let filtered = []

  if (viewMode.value === "year") {
    filtered = workouts.value
  } else {
    filtered = workouts.value.filter(w => {
      if (!w.created_at) return false
      return new Date(w.created_at).getMonth() === selectedMonth.value
    })
  }

  filteredWorkouts.value = filtered
  totalWorkouts.value = filtered.length

  let sets = 0
  let reps = 0

  filtered.forEach(w => {
    sets += w.sets
    reps += w.reps
  })

  totalSets.value = sets
  totalReps.value = reps
}

const createChart = () => {
  if (!monthlyChartRef.value) return

  const ctx = monthlyChartRef.value.getContext("2d")

  if (chartInstance) chartInstance.destroy()

  if (viewMode.value === "year") {
    const months = Array(12).fill(0)

    workouts.value.forEach(w => {
      if (!w.created_at) return
      const date = new Date(w.created_at)
      months[date.getMonth()]++
    })

    chartInstance = new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        datasets: [{
          label: "Gym Visits",
          data: months,
          borderColor: "#7ED957",
          backgroundColor: "rgba(126,217,87,0.15)",
          fill: true,
          tension: 0.4,
          pointBackgroundColor: "#7ED957",
          pointRadius: 4
        }]
      },
      options: chartOptions()
    })

  } else {
    const daysInMonth = new Date(new Date().getFullYear(), selectedMonth.value + 1, 0).getDate()
    const days = Array(daysInMonth).fill(0)

    workouts.value.forEach(w => {
      if (!w.created_at) return
      const date = new Date(w.created_at)

      if (date.getMonth() === selectedMonth.value) {
        days[date.getDate() - 1]++
      }
    })

    chartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels: Array.from({ length: daysInMonth }, (_, i) => i + 1),
        datasets: [{
          label: "Daily Workouts",
          data: days,
          backgroundColor: "#7ED957"
        }]
      },
      options: chartOptions()
    })
  }
}

const chartOptions = () => ({
  responsive: true,
  plugins: {
    legend: {
      labels: { color: "#d1d5db" }
    }
  },
  scales: {
    x: {
      ticks: { color: "#6b7280" },
      grid: { color: "rgba(255,255,255,0.05)" }
    },
    y: {
      ticks: { color: "#6b7280" },
      grid: { color: "rgba(255,255,255,0.05)" }
    }
  }
})

const getCalendarDays = () => {
  const year = new Date().getFullYear()
  const firstDay = new Date(year, selectedMonth.value, 1)
  const lastDate = new Date(year, selectedMonth.value + 1, 0).getDate()

  const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay()

  const days = []

  for (let i = 1; i < startDay; i++) days.push(null)

  for (let d = 1; d <= lastDate; d++) {
    let reps = 0
    let sets = 0
    let workoutNames = []

    workouts.value.forEach(w => {
      if (!w.created_at) return
      const date = new Date(w.created_at)

      if (
        date.getMonth() === selectedMonth.value &&
        date.getDate() === d
      ) {
        reps += w.reps
        sets += w.sets
        workoutNames.push(w.name)
      }
    })

    days.push({
      day: d,
      reps,
      sets,
      workouts: workoutNames,
      intensity: getIntensity(reps)
    })
  }

  return days
}

const getIntensity = (reps) => {
  if (reps === 0) return 0
  if (reps < 50) return 1
  if (reps < 150) return 2
  return 3
}

const selectedDay = ref(null)
const showModal = ref(false)

const openDay = (day) => {
  if (!day) return
  selectedDay.value = day
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

watch([selectedMonth, viewMode], async () => {
  updateStats()
  await nextTick()

  if (viewMode.value === "year") createChart()
})
</script>

<template>
  <div class="h-screen flex flex-col bg-[#0b0b0b] text-white">

    <div class="flex-1 overflow-y-auto pb-28">

      <div class="max-w-6xl mx-auto px-4 md:px-12 py-6 md:py-10">

        <div class="mb-6 md:mb-10">
          <h1 class="text-2xl md:text-4xl font-bold mb-1">
            Statistics
          </h1>
          <p class="text-gray-500 text-sm">
            Track your training performance
          </p>
        </div>

        <div class="flex gap-2 mb-5 overflow-x-auto">
          <button @click="viewMode = 'year'"
            :class="viewMode === 'year' ? 'bg-[#7ED957] text-black' : 'bg-[#1a1a1a] text-gray-400'"
            class="px-4 py-2 rounded-lg text-sm font-semibold">
            Year
          </button>

          <button @click="viewMode = 'month'"
            :class="viewMode === 'month' ? 'bg-[#7ED957] text-black' : 'bg-[#1a1a1a] text-gray-400'"
            class="px-4 py-2 rounded-lg text-sm font-semibold">
            Month
          </button>
        </div>

        <div v-if="viewMode === 'month'" class="mb-6">
          <select v-model="selectedMonth"
            class="bg-[#1a1a1a] border border-white/10 rounded-lg px-3 py-2 text-sm w-full">
            <option v-for="(m, i) in ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']"
              :key="i" :value="i">
              {{ m }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-3 gap-3 md:gap-6 mb-8">
          <div class="bg-[#151515] p-4 rounded-xl text-center">
            <p class="text-xs text-gray-400">Workouts</p>
            <p class="text-xl text-[#7ED957] font-bold">{{ totalWorkouts }}</p>
          </div>

          <div class="bg-[#151515] p-4 rounded-xl text-center">
            <p class="text-xs text-gray-400">Sets</p>
            <p class="text-xl text-[#7ED957] font-bold">{{ totalSets }}</p>
          </div>

          <div class="bg-[#151515] p-4 rounded-xl text-center">
            <p class="text-xs text-gray-400">Reps</p>
            <p class="text-xl text-[#7ED957] font-bold">{{ totalReps }}</p>
          </div>
        </div>

        <div v-if="viewMode === 'year'" class="h-[260px] md:h-[420px] mb-8">
          <canvas ref="monthlyChartRef"></canvas>
        </div>

        <div v-else>

          <div class="grid grid-cols-7 text-center text-xs text-gray-500 mb-2">
            <span v-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']">{{ d }}</span>
          </div>

          <div class="grid grid-cols-7 gap-1">
            <div
                v-for="(d, i) in getCalendarDays()"
                :key="i"
                @click="openDay(d)"
                class="aspect-square rounded-md flex items-center justify-center text-[10px] cursor-pointer transition active:scale-95"
                :class="[
                    !d ? '' :
                    d.intensity === 0 ? 'bg-[#1a1a1a]' :
                    d.intensity === 1 ? 'bg-green-800' :
                    d.intensity === 2 ? 'bg-green-600 text-black' :
                    'bg-[#7ED957] text-black font-semibold'
                ]"
                >
                <span v-if="d">{{ d.day }}</span>
                </div>
          </div>

        </div>

      </div>
    </div>
  </div>
<div
  v-if="showModal"
  class="fixed inset-0 bg-black/70 flex items-end md:items-center justify-center z-50"
>
  <div class="bg-[#121212] w-full md:max-w-md rounded-t-2xl md:rounded-2xl p-6">

    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">
        Day {{ selectedDay?.day }}
      </h2>

      <button @click="closeModal" class="text-gray-400 text-xl">
        ✕
      </button>
    </div>

    <div class="mb-4 text-sm text-gray-400">
      <div>🏋️ Sets: {{ selectedDay?.sets }}</div>
      <div>🔁 Reps: {{ selectedDay?.reps }}</div>
    </div>

    <div v-if="selectedDay?.workouts?.length" class="space-y-2">

      <div
        v-for="(w, i) in selectedDay.workouts"
        :key="i"
        class="bg-[#1a1a1a] p-3 rounded-lg text-sm text-[#7ED957]"
      >
        {{ w }}
      </div>

    </div>

    <div v-else class="text-gray-500 text-sm">
      No workouts this day
    </div>

  </div>
</div>
  </template>