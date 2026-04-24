<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import AdminSidebar from "../../components/admin/AdminSidebar.vue";

const users = ref([]);
const loading = ref(true);

const showModal = ref(false);
const selectedUserId = ref(null);

const showEditModal = ref(false);
const selectedFile = ref(null);

const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");

const search = ref("");
const roleFilter = ref("all");

const editUser = ref({
    id: null,
    name: "",
    email: "",
    role: "",
});

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

const openEditModal = (user) => {
    editUser.value = { ...user };
    showEditModal.value = true;
};

const handleFileChange = (e) => {
    selectedFile.value = e.target.files[0];
};

const updateUser = async () => {
    try {
        const token = localStorage.getItem("token");

        const formData = new FormData();
        formData.append("name", editUser.value.name);
        formData.append("email", editUser.value.email);
        formData.append("role", editUser.value.role);

        if (selectedFile.value) {
            formData.append("profile_photo", selectedFile.value);
        }

        formData.append("_method", "PUT");

        const res = await axios.post(
            `http://localhost:8000/api/admin/users/${editUser.value.id}`,
            formData,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
        );

        const index = users.value.findIndex((u) => u.id === editUser.value.id);
        if (index !== -1) {
            users.value[index] = res.data;
        }

        showEditModal.value = false;

        toastMessage.value = "User updated successfully";
        toastType.value = "success";
        showToast.value = true;
    } catch (err) {
        console.error(err);

        toastMessage.value = "Failed to update user";
        toastType.value = "error";
        showToast.value = true;
    }

    setTimeout(() => (showToast.value = false), 2500);
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

    setTimeout(() => (showToast.value = false), 2500);
};

const cancelDelete = () => {
    showModal.value = false;
    selectedUserId.value = null;
};

const filteredUsers = computed(() => {
    const searchValue = search.value.toLowerCase();

    return users.value.filter((user) => {
        const matchesSearch =
            user.name.toLowerCase().includes(searchValue) ||
            user.email.toLowerCase().includes(searchValue);

        const matchesRole =
            roleFilter.value === "all" || user.role === roleFilter.value;

        return matchesSearch && matchesRole;
    });
});

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
                    <div
                        class="p-4 flex flex-col md:flex-row gap-3 justify-between"
                    >
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search users..."
                            class="px-4 py-2 bg-[#111] border border-gray-700 rounded-lg text-white w-full md:w-1/3"
                        />

                        <select
                            v-model="roleFilter"
                            class="px-4 py-2 bg-[#111] border border-gray-700 rounded-lg text-white w-full md:w-40"
                        >
                            <option value="all">All</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
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
                                v-for="user in filteredUsers"
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
                                            @click="openEditModal(user)"
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
    <div
        v-if="showEditModal"
        class="fixed inset-0 flex items-center justify-center bg-black/60 z-50"
    >
        <div
            class="bg-[#1a1a1a] rounded-2xl p-6 w-[400px] border border-gray-800 shadow-xl"
        >
            <div class="flex flex-col items-center mb-4">
                <img
                    :src="
                        editUser.profile_photo
                            ? editUser.profile_photo.startsWith('http')
                                ? editUser.profile_photo
                                : 'http://localhost:8000/storage/' +
                                  editUser.profile_photo
                            : `https://ui-avatars.com/api/?name=${editUser.name}`
                    "
                    class="w-20 h-20 rounded-full object-cover mb-3 border border-gray-700"
                />

                <h3 class="text-lg font-semibold text-white">Edit User</h3>
                <div class="flex flex-col items-center gap-2">
                    <label
                        class="cursor-pointer bg-[#7ED957] text-black px-4 py-2 rounded-lg font-semibold hover:bg-green-400 transition"
                    >
                        Upload Photo
                        <input
                            type="file"
                            @change="handleFileChange"
                            class="hidden"
                        />
                    </label>

                    <span class="text-xs text-gray-500">
                        JPG or PNG, max 2MB
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <input
                    v-model="editUser.name"
                    type="text"
                    placeholder="Name"
                    class="w-full px-4 py-2 bg-[#111] border border-gray-700 rounded-lg text-white"
                />

                <input
                    v-model="editUser.email"
                    type="email"
                    placeholder="Email"
                    class="w-full px-4 py-2 bg-[#111] border border-gray-700 rounded-lg text-white"
                />

                <select
                    v-model="editUser.role"
                    class="w-full px-4 py-2 bg-[#111] border border-gray-700 rounded-lg text-white"
                >
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button
                    @click="showEditModal = false"
                    class="px-4 py-2 bg-gray-700 rounded-lg hover:bg-gray-600"
                >
                    Cancel
                </button>

                <button
                    @click="updateUser"
                    class="px-4 py-2 bg-green-500 text-black rounded-lg hover:bg-green-600"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>
