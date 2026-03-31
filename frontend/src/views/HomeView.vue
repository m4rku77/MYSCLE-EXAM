<script setup>
import { useRouter } from "vue-router"
import { ref, onMounted } from "vue"
import axios from "axios"

const router = useRouter()

const goLogin = () => router.push("/login")
const goRegister = () => router.push("/login?register=true")

const scrolled = ref(false)
const visible = ref(false)
const loading = ref(true)

const stats = ref({
  workouts: 0,
  users: 0,
  consistency: 0
})

onMounted(async () => {
  window.addEventListener("scroll", () => {
    scrolled.value = window.scrollY > 50
  })

  setTimeout(() => {
    visible.value = true
  }, 100)

  try {
    const res = await axios.get("http://127.0.0.1:8000/api/stats")
    stats.value = res.data
  } catch (e) {
    console.log("API error:", e)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="bg-[#0b0b0b] text-white overflow-x-hidden">

    <header
      :class="[
        'fixed w-full z-50 px-6 py-4 flex justify-between items-center transition-all duration-500',
        scrolled ? 'bg-white/5 backdrop-blur-xl border-b border-white/10' : 'bg-transparent'
      ]"
    >
      <div class="flex items-center gap-3">
        <img src="/logo.png" class="h-9" />
        <span class="font-semibold text-lg tracking-wide">MYSCLE</span>
      </div>

      <div class="flex gap-3">
        <button @click="goLogin"
          class="px-4 py-2 text-sm text-gray-300 hover:text-white transition">
          Login
        </button>

        <button @click="goRegister"
          class="px-5 py-2 rounded-xl font-semibold text-black bg-gradient-to-r from-[#7ED957] to-[#5fcf47] hover:scale-105 transition-all duration-300 shadow-lg">
          Get Started
        </button>
      </div>
    </header>

    <section class="h-screen flex items-center justify-center text-center px-6 relative">

      <div class="absolute inset-0 bg-gradient-to-b from-[#111] via-[#0b0b0b] to-black"></div>

      <div class="absolute w-[700px] h-[700px] bg-[#7ED957]/20 blur-[160px] rounded-full"></div>

      <div
        class="relative z-10 max-w-4xl transition-all duration-700"
        :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
      >
        <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">
          Train Smarter.<br />
          <span class="bg-gradient-to-r from-[#7ED957] to-[#b6ff9e] bg-clip-text text-transparent">
            Get Stronger.
          </span>
        </h1>

        <p class="text-gray-400 text-lg mb-10 max-w-xl mx-auto">
          Track workouts, build plans, and stay consistent —
          <span class="text-white font-medium">all in one powerful platform.</span>
        </p>

        <div class="flex justify-center gap-4">
          <button @click="goRegister"
            class="bg-gradient-to-r from-[#7ED957] to-[#5fcf47] text-black px-8 py-4 rounded-2xl font-semibold hover:scale-105 transition-all shadow-xl">
            Start Free
          </button>

          <button @click="goLogin"
            class="border border-white/10 bg-white/5 backdrop-blur px-8 py-4 rounded-2xl hover:bg-white/10 transition-all">
            Login
          </button>
        </div>
      </div>
    </section>

    <section class="py-24 px-6 max-w-6xl mx-auto grid md:grid-cols-3 gap-8">

      <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl hover:scale-[1.03] transition-all">
        <h3 class="text-xl font-semibold mb-3">📊 Track Progress</h3>
        <p class="text-gray-400">Monitor strength and visualize improvements.</p>
      </div>

      <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl hover:scale-[1.03] transition-all">
        <h3 class="text-xl font-semibold mb-3">🏋️ Build Plans</h3>
        <p class="text-gray-400">Create personalized training systems.</p>
      </div>

      <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl hover:scale-[1.03] transition-all">
        <h3 class="text-xl font-semibold mb-3">⚡ Stay Consistent</h3>
        <p class="text-gray-400">Build discipline with data-driven habits.</p>
      </div>

    </section>
    <section class="py-24 text-center bg-gradient-to-r from-black to-[#0f0f0f]">
      <h2 class="text-4xl font-bold mb-12">Trusted by athletes</h2>

      <div v-if="loading">Loading...</div>

      <div v-else class="grid md:grid-cols-3 gap-10 max-w-4xl mx-auto">

        <div class="bg-white/5 p-6 rounded-xl border border-white/10">
          <h2 class="text-5xl font-bold text-[#7ED957]">
            {{ stats.workouts }}+
          </h2>
          <p class="text-gray-400">Workouts</p>
        </div>

        <div class="bg-white/5 p-6 rounded-xl border border-white/10">
          <h2 class="text-5xl font-bold text-[#7ED957]">
            {{ stats.users }}+
          </h2>
          <p class="text-gray-400">Users</p>
        </div>

        <div class="bg-white/5 p-6 rounded-xl border border-white/10">
          <h2 class="text-5xl font-bold text-[#7ED957]">
            {{ stats.consistency }}%
          </h2>
          <p class="text-gray-400">Consistency</p>
        </div>

      </div>
    </section>
<section class="py-24 px-6 max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">

  <div>
    <h2 class="text-4xl font-bold mb-4">Measure progress</h2>
    <p class="text-gray-400 mb-6">
      Staying motivated is easier when you can see how far you've come.
    </p>

    <ul class="space-y-3">
      <li v-for="item in [
        'Advanced Exercise Charts',
        'Personal Records',
        'Calculate One Rep Max',
        'High Quality Exercise Videos',
        'Custom Exercises',
        'Exercise History'
      ]" :key="item" class="flex items-center gap-3">
        <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs">✓</span>
        <span class="text-gray-300">{{ item }}</span>
      </li>
    </ul>
  </div>

  <div class="flex justify-center">
    <img src="/gym1.webp" class="rounded-2xl shadow-2xl max-h-[500px]" />
  </div>

</section>


<section class="py-24 px-6 max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">

  <div class="flex justify-center order-2 md:order-1">
    <img src="/gym2.jpg" class="rounded-2xl shadow-2xl max-h-[500px]" />
  </div>

  <div class="order-1 md:order-2">
    <h2 class="text-4xl font-bold mb-4">Build powerful workouts</h2>
    <p class="text-gray-400 mb-6">
      Create structured training plans that actually get results.
    </p>

    <ul class="space-y-3">
      <li v-for="item in [
        'Custom Workout Builder',
        'Exercise Library',
        'Smart Suggestions',
        'Supersets & Dropsets',
        'Rest Timer',
        'Track Every Set'
      ]" :key="item" class="flex items-center gap-3">
        <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs">✓</span>
        <span class="text-gray-300">{{ item }}</span>
      </li>
    </ul>
  </div>

</section>


<section class="py-24 px-6 max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">

  <div>
    <h2 class="text-4xl font-bold mb-4">Stay consistent</h2>
    <p class="text-gray-400 mb-6">
      Build habits and never miss a workout again.
    </p>

    <ul class="space-y-3">
      <li v-for="item in [
        'Daily Tracking',
        'Progress Reminders',
        'Goal System',
        'Streak Tracking',
        'Performance Insights',
        'Motivation Tools'
      ]" :key="item" class="flex items-center gap-3">
        <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs">✓</span>
        <span class="text-gray-300">{{ item }}</span>
      </li>
    </ul>
  </div>

  <div class="flex justify-center">
    <img src="/gym3.avif" class="rounded-2xl shadow-2xl max-h-[500px]" />
  </div>

</section>



    <section class="py-28 text-center px-6">
      <h2 class="text-4xl font-bold mb-6">
        Start your transformation today
      </h2>

      <p class="text-gray-400 mb-8">
        Join MYSCLE and take control of your training journey.
      </p>

      <button @click="goRegister"
        class="bg-gradient-to-r from-[#7ED957] to-[#5fcf47] text-black px-10 py-4 rounded-2xl font-semibold hover:scale-110 transition-all shadow-xl">
        Create Account
      </button>
    </section>

    <footer class="text-center py-10 text-gray-500 text-sm border-t border-white/10">
      © 2026 MYSCLE. Built for athletes.
    </footer>

  </div>
</template>