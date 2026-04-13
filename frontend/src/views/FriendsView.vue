<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import BottomNav from "../components/BottomNav.vue"
import { useRouter } from "vue-router"

const router = useRouter()
const friends = ref([])
const users = ref([])
const search = ref("")
const loading = ref(true)

const fetchFriends = async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      "http://localhost:8000/api/friends",
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json"
        }
      }
    )

    let data = Array.isArray(res.data) ? res.data : res.data.data || []

    friends.value = data.map(u => ({
      id: u.id,
      name: u.name,
      workouts: u.workouts_count ?? 0,
      profile_photo: u.profile_photo
    }))

  } catch (err) {
    console.error(err)
  }
}

const searchUsers = async () => {
  if (!search.value.trim()) {
    users.value = []
    return
  }

  try {
    const token = localStorage.getItem("token")

    const res = await axios.get(
      `http://localhost:8000/api/users?search=${encodeURIComponent(search.value)}`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    users.value = res.data.map(u => ({
      id: u.id,
      name: u.name,
      profile_photo: u.profile_photo
    }))

  } catch (err) {
    console.error(err)
  }
}

const addFriend = async (id) => {
  try {
    const token = localStorage.getItem("token")

    await axios.post(
      "http://localhost:8000/api/friends/add",
      { friend_id: id },
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    await fetchFriends()
    users.value = users.value.filter(u => u.id !== id)

  } catch (err) {
    console.error(err.response?.data || err.message)
  }
}

const goToUser = (id) => {
  router.push(`/user/${id}`) 
}

onMounted(async () => {
  await fetchFriends()
  loading.value = false
})

const filteredFriends = () => {
  return friends.value.filter(f =>
    f.name.toLowerCase().includes(search.value.toLowerCase())
  )
}
</script>

<template>
  <div class="h-screen bg-[#0f0f0f] text-white flex flex-col">

    <div class="p-6 border-b border-gray-800">
      <h1 class="text-xl font-semibold">Friends</h1>
      <p class="text-sm text-gray-400 mt-1">
        Track your friends' progress
      </p>
    </div>

    <div class="p-4">
      <input
        v-model="search"
        @input="searchUsers"
        placeholder="Search users to add..."
        class="w-full px-4 py-3 rounded-lg bg-[#1f1f1f] border border-gray-700 focus:ring-2 focus:ring-[#7ED957] outline-none"
      />
    </div>

    <div v-if="search && users.length > 0" class="px-4 space-y-2 mb-2">
      <p class="text-sm text-gray-400">Add new friends</p>

      <div
        v-for="user in users"
        :key="user.id"
        class="bg-[#1f1f1f] border border-gray-800 rounded-xl p-3 flex justify-between items-center"
      >
        <div class="flex items-center gap-3">
          <img
            :src="user.profile_photo 
              ? user.profile_photo 
              : `https://ui-avatars.com/api/?name=${user.name}`"
            class="w-8 h-8 rounded-full object-cover"
          />
          <span>{{ user.name }}</span>
        </div>

        <button
          @click="addFriend(user.id)"
          class="px-3 py-1 bg-green-500 text-black rounded-lg text-sm font-semibold hover:scale-105 transition"
        >
          + Add
        </button>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 pb-4 space-y-3">

      <div v-if="loading" class="text-gray-400 text-center mt-6">
        Loading friends...
      </div>

      <div
        v-else-if="!search && filteredFriends().length > 0"
        v-for="friend in filteredFriends()"
        :key="friend.id"
        class="bg-[#1f1f1f] border border-gray-800 rounded-xl p-4 flex justify-between items-center hover:scale-[1.02] transition"
      >
        <div class="flex items-center gap-3">
          <img
            :src="friend.profile_photo 
              ? friend.profile_photo 
              : `https://ui-avatars.com/api/?name=${friend.name}`"
            class="w-10 h-10 rounded-full object-cover"
          />

          <div>
            <p class="font-semibold">{{ friend.name }}</p>
            <p class="text-sm text-gray-400">
              {{ friend.workouts }} workouts
            </p>
          </div>
        </div>

 <button 
  @click="goToUser(friend.id)"
  class="px-4 py-2 bg-[#7ED957] text-black rounded-lg text-sm font-semibold hover:scale-105 hover:bg-[#6fd44a] transition"
>
  View
</button>
      </div>

      <div v-else-if="!loading && !search" class="text-gray-500 text-center mt-6">
        No friends found.
      </div>

      <div v-else-if="search && users.length === 0" class="text-gray-500 text-center mt-6">
        No users found.
      </div>

    </div>

    <BottomNav />
  </div>
</template>