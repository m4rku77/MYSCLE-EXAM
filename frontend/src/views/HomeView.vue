<script setup>
import { useRouter } from "vue-router"
import { ref, onMounted } from "vue"

const router = useRouter()

const goLogin = () => router.push("/login")
const goRegister = () => router.push("/login?register=true")

const scrolled = ref(false)
const visible = ref(false)

onMounted(() => {
  window.addEventListener("scroll", () => {
    scrolled.value = window.scrollY > 50
  })

  // simple fade-in animation
  setTimeout(() => {
    visible.value = true
  }, 100)
})
</script>

<template>
  <div class="bg-[#0f0f0f] text-white">

    <!-- HEADER -->
    <header
      :class="[
        'fixed top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center transition-all duration-300',
        scrolled ? 'bg-[#1f1f1f]/90 backdrop-blur shadow-lg' : 'bg-transparent'
      ]"
    >
      <div class="flex items-center gap-3">
        <img src="/logo.png" class="h-10" />
        <span class="font-bold text-lg">MYSCLE</span>
      </div>

      <div class="flex gap-3">
        <button
          @click="goLogin"
          class="px-4 py-2 text-sm rounded-lg
                 transition-all duration-300 ease-in-out
                 hover:text-white hover:bg-gray-800 hover:scale-105"
        >
          Login
        </button>

        <button
          @click="goRegister"
          class="px-4 py-2 bg-[#7ED957] text-black rounded-lg font-semibold
                 transition-all duration-300 ease-in-out
                 hover:scale-105 hover:shadow-lg"
        >
          Get Started
        </button>
      </div>
    </header>

    <!-- HERO -->
    <section class="h-screen flex flex-col justify-center items-center text-center px-6 relative">

      <!-- background -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#1f1f1f] via-[#151515] to-[#0f0f0f]"></div>

      <!-- glow -->
      <div class="absolute w-[600px] h-[600px] bg-[#7ED957]/20 blur-[120px] rounded-full"></div>

      <!-- content -->
      <div
        class="relative z-10 max-w-3xl transition-all duration-700 ease-out"
        :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
      >

        <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">
          Train Smarter.<br />
          <span class="text-[#7ED957]">Get Stronger.</span>
        </h1>

        <p class="text-gray-400 text-lg mb-10">
          MYSCLE helps you track workouts, build plans, and stay consistent — all in one place.
        </p>

        <div class="flex justify-center gap-4">
          <button
            @click="goRegister"
            class="bg-[#7ED957] text-black px-8 py-4 rounded-xl font-semibold
                   transition-all duration-300 ease-in-out
                   hover:scale-105 hover:shadow-xl"
          >
            Start Free
          </button>

          <button
            @click="goLogin"
            class="border border-gray-600 px-8 py-4 rounded-xl
                   transition-all duration-300 ease-in-out
                   hover:bg-gray-800 hover:scale-105"
          >
            Login
          </button>
        </div>

      </div>
    </section>

    <!-- TRANSITION -->
    <div class="h-32 bg-gradient-to-b from-[#0f0f0f] to-[#1a1a1a]"></div>

    <!-- FEATURES -->
    <section class="py-24 px-6 max-w-6xl mx-auto grid md:grid-cols-3 gap-10">

      <div
        class="bg-[#1f1f1f] p-8 rounded-2xl shadow-lg
               transition-all duration-300 ease-in-out
               hover:scale-105 hover:shadow-xl"
      >
        <h3 class="text-xl font-semibold mb-3">📊 Track Progress</h3>
        <p class="text-gray-400">
          Log workouts, monitor strength gains, and visualize your growth.
        </p>
      </div>

      <div
        class="bg-[#1f1f1f] p-8 rounded-2xl shadow-lg
               transition-all duration-300 ease-in-out
               hover:scale-105 hover:shadow-xl"
      >
        <h3 class="text-xl font-semibold mb-3">🏋️ Build Plans</h3>
        <p class="text-gray-400">
          Create structured training programs tailored to your goals.
        </p>
      </div>

      <div
        class="bg-[#1f1f1f] p-8 rounded-2xl shadow-lg
               transition-all duration-300 ease-in-out
               hover:scale-105 hover:shadow-xl"
      >
        <h3 class="text-xl font-semibold mb-3">⚡ Stay Consistent</h3>
        <p class="text-gray-400">
          Build discipline with a system designed for long-term progress.
        </p>
      </div>

    </section>

    <!-- STATS -->
    <section class="py-24 text-center bg-gradient-to-r from-[#1a1a1a] to-[#0f0f0f]">
      <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-10">

        <div>
          <h2 class="text-4xl font-bold text-[#7ED957]">0+</h2>
          <p class="text-gray-400">Workouts logged</p>
        </div>

        <div>
          <h2 class="text-4xl font-bold text-[#7ED957]">0+</h2>
          <p class="text-gray-400">Active users</p>
        </div>

        <div>
          <h2 class="text-4xl font-bold text-[#7ED957]">0%</h2>
          <p class="text-gray-400">Consistency rate</p>
        </div>

      </div>
    </section>

    <!-- CTA -->
    <section class="py-24 text-center px-6">
      <h2 class="text-4xl font-bold mb-6">
        Start your transformation today
      </h2>

      <p class="text-gray-400 mb-8">
        Join MYSCLE and take control of your training journey.
      </p>

      <button
        @click="goRegister"
        class="bg-[#7ED957] text-black px-10 py-4 rounded-xl font-semibold
               transition-all duration-300 ease-in-out
               hover:scale-110 hover:shadow-2xl"
      >
        Create Account
      </button>
    </section>

    <!-- FOOTER -->
    <footer class="text-center py-10 text-gray-500 text-sm border-t border-gray-800">
      © 2026 MYSCLE. Built for athletes.
    </footer>

  </div>
</template>