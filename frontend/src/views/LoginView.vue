<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"

const router = useRouter()

const isRegister = ref(false)

const username = ref("")
const firstName = ref("")
const lastName = ref("")
const email = ref("")
const password = ref("")
const error = ref("")

const visible = ref(false)

onMounted(() => {
  setTimeout(() => {
    visible.value = true
  }, 100)
})

const login = async () => {
  try {
    const response = await axios.post(
      "http://127.0.0.1:8000/api/login",
      {
        email: email.value,
        password: password.value
      }
    )

    localStorage.setItem("token", response.data.token)
    router.push("/dashboard")
      
  } catch (e) {
    error.value = "Invalid credentials"
  }
}

const register = async () => {
  try {
    const response = await axios.post(
      "http://127.0.0.1:8000/api/register",
      {
        username: username.value,
        first_name: firstName.value,
        last_name: lastName.value,
        email: email.value,
        password: password.value
      }
    )

    localStorage.setItem("token", response.data.token)
    router.push("/dashboard")


  } catch (e) {
    error.value = "Registration failed"
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f0f0f] relative overflow-hidden px-4 sm:px-0">

    <div class="absolute inset-0 bg-gradient-to-b from-[#1a1a1a] via-[#0f0f0f] to-black"></div>

    <div class="absolute w-[500px] h-[500px] bg-[#7ED957]/20 blur-[120px] rounded-full"></div>

    <div
      class="relative w-full max-w-md bg-[#1f1f1f] text-white rounded-2xl shadow-2xl p-10
             transition-all duration-700 ease-out"
      :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
    >

      <div class="flex justify-center mb-6">
        <img src="/logo.png" class="h-14 object-contain" />
      </div>

      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold tracking-wide">MYSCLE</h1>
        <p class="text-gray-400 text-sm mt-2">
          {{ isRegister ? "Create your account" : "Login to your account" }}
        </p>
      </div>

      <form
        @submit.prevent="isRegister ? register() : login()"
        class="space-y-4"
      >

        <div v-if="isRegister">
          <label class="text-sm text-gray-400">Username</label>
          <input v-model="username" type="text"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2a2a2a] border border-gray-700
                   focus:ring-2 focus:ring-[#7ED957] outline-none transition" />
        </div>

        <div v-if="isRegister">
          <label class="text-sm text-gray-400">First Name</label>
          <input v-model="firstName" type="text"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2a2a2a] border border-gray-700
                   focus:ring-2 focus:ring-[#7ED957] outline-none transition" />
        </div>

        <div v-if="isRegister">
          <label class="text-sm text-gray-400">Last Name</label>
          <input v-model="lastName" type="text"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2a2a2a] border border-gray-700
                   focus:ring-2 focus:ring-[#7ED957] outline-none transition" />
        </div>

        <div>
          <label class="text-sm text-gray-400">Email</label>
          <input v-model="email" type="email"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2a2a2a] border border-gray-700
                   focus:ring-2 focus:ring-[#7ED957] outline-none transition" />
        </div>

        <div>
          <label class="text-sm text-gray-400">Password</label>
          <input v-model="password" type="password"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2a2a2a] border border-gray-700
                   focus:ring-2 focus:ring-[#7ED957] outline-none transition" />
        </div>

        <button
          type="submit"
          class="w-full py-3 rounded-lg font-semibold text-black bg-[#7ED957]
                 transition-all duration-300 ease-in-out
                 hover:scale-105 hover:shadow-lg hover:bg-[#6ecc4b]"
        >
          {{ isRegister ? "Register" : "Login" }}
        </button>

        <p v-if="error" class="text-red-400 text-sm text-center">
          {{ error }}
        </p>

      </form>

      <div class="text-center mt-6 text-sm text-gray-400">

        <span v-if="!isRegister">
          Don't have an account?
          <button
            @click="isRegister = true"
            class="text-[#7ED957] ml-1 font-semibold hover:underline"
          >
            Register
          </button>
        </span>

        <span v-else>
          Already have an account?
          <button
            @click="isRegister = false"
            class="text-[#7ED957] ml-1 font-semibold hover:underline"
          >
            Login
          </button>
        </span>

      </div>

    </div>
  </div>
</template>