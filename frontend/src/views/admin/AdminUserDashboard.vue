<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import AdminSidebar from "../../components/admin/AdminSidebar.vue"

const users = ref([])
const loading = ref(true)

const fetchUsers = async () => {
  try {
    const token = localStorage.getItem("token")

    const res = await axios.get("http://localhost:8000/api/admin/users", {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    users.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const deleteUser = async (id) => {
  if (!confirm("Delete this user?")) return

  try {
    const token = localStorage.getItem("token")

    await axios.delete(`http://localhost:8000/api/admin/users/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    users.value = users.value.filter(u => u.id !== id)
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchUsers)
</script>

<template>
  <div class="flex min-h-screen bg-[#0f0f0f] text-white">

    <AdminSidebar />

    <div class="flex-1 p-6">

      <div class="bg-[#1a1a1a] rounded-2xl overflow-hidden border border-white/5">

        <div class="p-5 border-b border-gray-800 flex justify-between items-center">
          <div>
            <h2 class="text-lg font-semibold">Users</h2>
            <p class="text-xs text-gray-400">Manage all registered users</p>
          </div>
        </div>

        <div v-if="loading" class="p-6 text-center text-gray-500">
          Loading users...
        </div>

        <div v-else class="overflow-x-auto">

          <table class="w-full text-sm">

            <thead class="bg-[#151515] text-gray-400 text-xs uppercase">
              <tr>
                <th class="text-left px-4 py-3">User</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Role</th>
                <th class="text-left px-4 py-3">Joined</th>
                <th class="text-left px-4 py-3">Updated</th>
                <th class="text-right px-4 py-3">Actions</th>
              </tr>
            </thead>

            <tbody>

              <tr v-if="users.length === 0">
                <td colspan="6" class="text-center py-6 text-gray-500">
                  No users found
                </td>
              </tr>

              <tr
                v-for="user in users"
                :key="user.id"
                class="border-t border-gray-800 hover:bg-[#181818] transition align-middle"
              >

                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img
                      :src="
                        user.profile_photo
                          ? (user.profile_photo.startsWith('http')
                              ? user.profile_photo
                              : 'http://localhost:8000/storage/' + user.profile_photo)
                          : `https://ui-avatars.com/api/?name=${user.name}`
                      "
                      class="w-9 h-9 rounded-full object-cover"
                    />
                    <span class="font-medium">{{ user.name }}</span>
                  </div>
                </td>

                <td class="px-4 py-3 text-gray-400">
                  {{ user.email }}
                </td>

                <td class="px-4 py-3">
                  <span
                    class="text-xs px-2 py-1 rounded-lg font-semibold"
                    :class="user.role === 'admin'
                      ? 'bg-green-500 text-black'
                      : 'bg-gray-700 text-gray-300'"
                  >
                    {{ user.role }}
                  </span>
                </td>

                <td class="px-4 py-3 text-gray-400">
                  {{ new Date(user.created_at).toLocaleDateString() }}
                </td>

                <td class="px-4 py-3 text-gray-400">
                  {{
                    user.updated_at === user.created_at
                      ? "Never"
                      : new Date(user.updated_at).toLocaleDateString()
                  }}
                </td>

                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-2">

                    <button
                      @click="$router.push(`/admin/users/${user.id}`)"
                      class="w-9 h-9 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500 hover:text-white transition"
                    >
                      <i class="fas fa-pen"></i>
                    </button>

                    <button
                      @click="deleteUser(user.id)"
                      class="w-9 h-9 flex items-center justify-center bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition"
                    >
                      <i class="fas fa-trash"></i>
                    </button>

                  </div>
                </td>

              </tr>

            </tbody>

          </table>

        </div>

      </div>

    </div>

  </div>
</template>