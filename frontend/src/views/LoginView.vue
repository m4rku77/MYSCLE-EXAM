<script setup>
import { ref } from "vue"
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

<div class="min-h-screen flex items-center justify-center bg-[#f4f4f4]">

  <div class="w-full max-w-md bg-[#393939] text-white rounded-2xl shadow-2xl p-10">

    <!-- Logo -->
    <div class="flex justify-center mb-6">
      <img src="/logo.png" alt="MYSCLE logo" class="h-14 object-contain" />
    </div>

    <!-- Title -->
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold tracking-wide">MYSCLE</h1>
      <p class="text-gray-300 text-sm mt-2">
        {{ isRegister ? "Create your account" : "Login" }}
      </p>
    </div>

    <form
      @submit.prevent="isRegister ? register() : login()"
      class="space-y-4"
    >

      <!-- REGISTER FIELDS -->

      <div v-if="isRegister">
        <label class="text-sm text-gray-300">Username</label>
        <input v-model="username" type="text"
        class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2f2f2f] border border-gray-600 focus:ring-2 focus:ring-[#7ED957]" />
      </div>

      <div v-if="isRegister">
        <label class="text-sm text-gray-300">First Name</label>
        <input v-model="firstName" type="text"
        class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2f2f2f] border border-gray-600 focus:ring-2 focus:ring-[#7ED957]" />
      </div>

      <div v-if="isRegister">
        <label class="text-sm text-gray-300">Last Name</label>
        <input v-model="lastName" type="text"
        class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2f2f2f] border border-gray-600 focus:ring-2 focus:ring-[#7ED957]" />
      </div>

      <!-- EMAIL -->

      <div>
        <label class="text-sm text-gray-300">Email</label>
        <input v-model="email" type="email"
        class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2f2f2f] border border-gray-600 focus:ring-2 focus:ring-[#7ED957]" />
      </div>

      <!-- PASSWORD -->

      <div>
        <label class="text-sm text-gray-300">Password</label>
        <input v-model="password" type="password"
        class="mt-1 w-full px-4 py-3 rounded-lg bg-[#2f2f2f] border border-gray-600 focus:ring-2 focus:ring-[#7ED957]" />
      </div>

      <!-- BUTTON -->

      <button
        type="submit"
        class="w-full py-3 rounded-lg font-semibold text-[#393939] bg-[#7ED957] hover:bg-[#6ecc4b]"
      >
        {{ isRegister ? "Register" : "Login" }}
      </button>

      <p v-if="error" class="text-red-400 text-sm text-center">
        {{ error }}
      </p>

    </form>

    <!-- SWITCH -->

    <div class="text-center mt-6 text-sm text-gray-300">

      <span v-if="!isRegister">
        Don't have an account?
        <button
          @click="isRegister = true"
          class="text-[#7ED957] ml-1 font-semibold"
        >
          Register
        </button>
      </span>

      <span v-else>
        Already have an account?
        <button
          @click="isRegister = false"
          class="text-[#7ED957] ml-1 font-semibold"
        >
          Login
        </button>
      </span>

    </div>

  </div>

</div>

</template>