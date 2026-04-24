<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AdminSidebar from "../../components/admin/AdminSidebar.vue";

const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const selectedUserId = ref(null);
const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");
const fetchUsers = async () => {
    try {
        const token = localStorage.getItem("token");

        const res = await axios.get("http://localhost:8000/api/admin/users", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        users.value = res.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openDeleteModal = (id) => {
    selectedUserId.value = id;
    showModal.value = true;
};

const confirmDelete = async () => {
    try {
        const token = localStorage.getItem("token");

        await axios.delete(
            `http://localhost:8000/api/admin/users/${selectedUserId.value}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
        );

        users.value = users.value.filter((u) => u.id !== selectedUserId.value);

        showModal.value = false;
        selectedUserId.value = null;

        toastMessage.value = "User deleted successfully";
        toastType.value = "success";
        showToast.value = true;
    } catch (err) {
        console.error(err);

        toastMessage.value = "Failed to delete user";
        toastType.value = "error";
        showToast.value = true;
    }

    setTimeout(() => {
        showToast.value = false;
    }, 2500);
};

const cancelDelete = () => {
    showModal.value = false;
    selectedUserId.value = null;
};

onMounted(fetchUsers);
</script>

<template>
    <div class="flex min-h-screen bg-[#0f0f0f] text-white">
        <AdminSidebar />

        <div class="flex-1 p-6">
            <div
                class="bg-[#1a1a1a] rounded-2xl overflow-hidden border border-white/5"
            >
                <div
                    class="p-5 border-b border-gray-800 flex justify-between items-center"
                >
                    <div>
                        <h2 class="text-lg font-semibold">Users</h2>
                        <p class="text-xs text-gray-400">
                            Manage all registered users
                        </p>
                    </div>
                </div>

                <div v-if="loading" class="p-6 text-center text-gray-500">
                    Loading users...
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead
                            class="bg-[#151515] text-gray-400 text-xs uppercase"
                        >
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
                                <td
                                    colspan="6"
                                    class="text-center py-6 text-gray-500"
                                >
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
                                                    ? user.profile_photo.startsWith(
                                                          'http',
                                                      )
                                                        ? user.profile_photo
                                                        : 'http://localhost:8000/storage/' +
                                                          user.profile_photo
                                                    : `https://ui-avatars.com/api/?name=${user.name}`
                                            "
                                            class="w-9 h-9 rounded-full object-cover"
                                        />
                                        <span class="font-medium">{{
                                            user.name
                                        }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-gray-400">
                                    {{ user.email }}
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="text-xs px-2 py-1 rounded-lg font-semibold"
                                        :class="
                                            user.role === 'admin'
                                                ? 'bg-green-500 text-black'
                                                : 'bg-gray-700 text-gray-300'
                                        "
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-400">
                                    {{
                                        new Date(
                                            user.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </td>

                                <td class="px-4 py-3 text-gray-400">
                                    {{
                                        user.updated_at === user.created_at
                                            ? "Never"
                                            : new Date(
                                                  user.updated_at,
                                              ).toLocaleDateString()
                                    }}
                                </td>

                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <button
                                            @click="
                                                $router.push(
                                                    `/admin/users/${user.id}`,
                                                )
                                            "
                                            class="w-9 h-9 flex items-center justify-center bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500 hover:text-white transition"
                                        >
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <button
                                            @click="openDeleteModal(user.id)"
                                            class="w-9 h-9 flex items-center justify-center bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div
                        v-if="showModal"
                        class="fixed inset-0 flex items-center justify-center bg-black/60 z-50"
                    >
                        <div
                            class="bg-[#1a1a1a] rounded-2xl p-6 w-[400px] border border-gray-800 shadow-xl"
                        >
                            <h3 class="text-lg font-semibold mb-4">
                                Delete User
                            </h3>

                            <p class="text-gray-400 mb-6">
                                Are you sure you want to delete this user? This
                                action cannot be undone.
                            </p>

                            <div class="flex justify-end gap-3">
                                <button
                                    @click="cancelDelete"
                                    class="px-4 py-2 bg-gray-700 rounded-lg hover:bg-gray-600 transition"
                                >
                                    Cancel
                                </button>

                                <button
                                    @click="confirmDelete"
                                    class="px-4 py-2 bg-red-500 rounded-lg text-white hover:bg-red-600 transition"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        v-if="showToast"
        class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none"
    >
        <div
            class="px-6 py-4 rounded-xl shadow-lg text-white font-semibold transition"
            :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
        >
            {{ toastMessage }}
        </div>
    </div>
</template>
