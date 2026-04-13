<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"

const user = ref(null)
const loading = ref(true)
const name = ref("")
const email = ref("")
const file = ref(null)
const preview = ref(null)

const requests = ref([])
const showRequests = ref(false)

const success = ref("")
const showPassword = ref(false)
const currentPassword = ref("")
const newPassword = ref("")
const confirmPassword = ref("")
const passwordError = ref("")

const fetchProfile = async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get("http://localhost:8000/api/me", {
      headers: { Authorization: `Bearer ${token}` }
    })

    user.value = res.data
    name.value = res.data.name
    email.value = res.data.email

  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const fetchRequests = async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      "http://localhost:8000/api/friends/requests",
      { headers: { Authorization: `Bearer ${token}` } }
    )

    requests.value = res.data

  } catch (err) {
    console.error(err)
  }
}

const saveProfile = async () => {
  try {
    const token = localStorage.getItem("token")

    await axios.put(
      "http://localhost:8000/api/me",
      { name: name.value },
      { headers: { Authorization: `Bearer ${token}` } }
    )

    if (file.value) {
      const formData = new FormData()
      formData.append("photo", file.value)

      const res = await axios.post(
        "http://localhost:8000/api/me/photo",
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      )

      user.value.profile_photo = res.data.photo
      preview.value = null
    }

    success.value = "Profile updated ✅"
    setTimeout(() => success.value = "", 3000)

  } catch (err) {
    console.log(err.response?.data || err.message)
  }
}

const updatePassword = async () => {
  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = "Passwords do not match ❌"
    return
  }

  try {
    const token = localStorage.getItem("token")

    await axios.put(
      "http://localhost:8000/api/me/password",
      {
        current_password: currentPassword.value,
        new_password: newPassword.value
      },
      { headers: { Authorization: `Bearer ${token}` }
    })

    success.value = "Password updated ✅"
    passwordError.value = ""
    showPassword.value = false

  } catch (err) {
    passwordError.value = err.response?.data?.message || "Error"
  }
}

const handleFile = (e) => {
  file.value = e.target.files[0]
  preview.value = URL.createObjectURL(file.value)
}

const accept = async (id) => {
  const token = localStorage.getItem("token")
  await axios.post(`http://localhost:8000/api/friends/accept/${id}`, {}, {
    headers: { Authorization: `Bearer ${token}` }
  })
  fetchRequests()
}

const decline = async (id) => {
  const token = localStorage.getItem("token")
  await axios.delete(`http://localhost:8000/api/friends/decline/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  })
  fetchRequests()
}

const logout = () => {
  localStorage.removeItem("token")
  window.location.href = "/login"
}

onMounted(() => {
  fetchProfile()
  fetchRequests()
})
</script>

<template>
  <div class="min-h-screen bg-[#0f0f0f] text-white flex flex-col">

    <div class="flex-1 overflow-y-auto px-4 pt-6 pb-40 md:pb-12 space-y-6 w-full max-w-2xl md:max-w-6xl mx-auto">

      <div v-if="success"
        class="bg-green-500 text-black text-sm py-3 rounded-xl text-center font-semibold">
        {{ success }}
      </div>

      <div class="bg-[#1a1a1a] rounded-2xl p-6 md:p-8 flex flex-col items-center gap-5 shadow border border-white/5">

        <img
          :src="preview 
            ? preview 
            : (user?.profile_photo 
              ? 'http://localhost:8000/storage/' + user.profile_photo 
              : `https://ui-avatars.com/api/?name=${user?.name}`)"
          class="w-24 h-24 rounded-full object-cover border border-gray-700"
        />

        <div class="text-center space-y-1">
          <p class="text-lg font-semibold">{{ user?.name }}</p>
          <p class="text-xs text-gray-400">{{ email }}</p>
        </div>

        <label class="text-xs bg-[#2a2a2a] px-4 py-2 rounded-lg cursor-pointer hover:bg-[#333] transition">
          Change Photo
          <input type="file" @change="handleFile" class="hidden" />
        </label>

      </div>

      <div class="bg-[#1a1a1a] rounded-2xl p-5 md:p-6 space-y-6 border border-white/5">

        <h3 class="text-xs text-gray-400 uppercase tracking-wide">
          Account
        </h3>

        <div class="space-y-1">
          <label class="text-xs text-gray-500">Name</label>
          <input v-model="name"
            class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm"/>
        </div>

        <div class="space-y-1">
          <label class="text-xs text-gray-500">Email</label>
          <input v-model="email" disabled
            class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm opacity-60"/>
        </div>

        <button @click="showPassword = !showPassword"
          class="w-full py-3 bg-[#2a2a2a] rounded-xl text-sm hover:bg-[#333] transition">
          {{ showPassword ? "Cancel" : "Change Password" }}
        </button>

        <div v-if="showPassword" class="space-y-3 pt-2">

          <input v-model="currentPassword" type="password" placeholder="Current Password"
            class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm"/>

          <input v-model="newPassword" type="password" placeholder="New Password"
            class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm"/>

          <input v-model="confirmPassword" type="password" placeholder="Confirm Password"
            class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm"/>

          <p v-if="passwordError" class="text-red-500 text-xs">
            {{ passwordError }}
          </p>

          <button @click="updatePassword"
            class="w-full py-3 bg-[#7ED957] text-black rounded-xl text-sm font-medium">
            Update Password
          </button>

        </div>

      </div>

      <div class="bg-[#1a1a1a] rounded-2xl overflow-hidden border border-white/5">

        <div @click="showRequests = !showRequests"
          class="flex justify-between items-center p-5 cursor-pointer">

          <span class="text-sm">Friend Requests</span>

          <div class="flex items-center gap-2">
            <span v-if="requests.length"
              class="bg-red-500 text-xs px-2 rounded-full">
              {{ requests.length }}
            </span>

            <span class="text-gray-500 text-xs">
              {{ showRequests ? "▲" : "▼" }}
            </span>
          </div>
        </div>

        <div v-if="showRequests"
          class="border-t border-gray-800 px-4 py-4 space-y-4">

          <div v-if="requests.length === 0"
            class="text-gray-500 text-sm text-center">
            No requests
          </div>

          <div v-for="req in requests" :key="req.id"
            class="flex justify-between items-center">

            <div class="flex items-center gap-3">
              <img
                :src="req.profile_photo 
                  ? 'http://localhost:8000/storage/' + req.profile_photo 
                  : `https://ui-avatars.com/api/?name=${req.name}`"
                class="w-10 h-10 rounded-full object-cover"
              />
              <span class="text-sm">{{ req.name }}</span>
            </div>

            <div class="flex gap-2">
              <button @click="accept(req.id)"
                class="w-9 h-9 bg-green-500 text-black rounded-lg flex items-center justify-center">
                ✓
              </button>

              <button @click="decline(req.id)"
                class="w-9 h-9 bg-red-500 rounded-lg flex items-center justify-center">
                ✕
              </button>
            </div>

          </div>

        </div>
      </div>


<div class="flex-1 overflow-y-auto px-4 pt-6 pb-40 md:pb-12 space-y-6 w-full max-w-2xl md:max-w-6xl mx-auto">
  <div class="fixed bottom-16 left-0 w-full p-4 bg-[#0f0f0f] border-t border-gray-800 z-50
              md:static md:p-0 md:mt-8 md:bg-transparent md:border-0">
    <div class="flex flex-col gap-3 md:flex-row md:justify-center">
      <button @click="saveProfile"
        class="w-full md:w-auto px-10 py-3 bg-[#7ED957] text-black rounded-xl text-sm font-semibold">
        Save Changes
      </button>
      <button @click="logout"
        class="w-full md:w-auto px-8 py-3 bg-red-500 rounded-xl text-sm font-semibold">
        Logout
      </button>

    </div>

  </div>

</div>
</div>

  </div>
</template>