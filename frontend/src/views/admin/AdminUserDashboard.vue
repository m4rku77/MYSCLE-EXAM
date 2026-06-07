<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

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
    weight: "",
    height: "",
    age: "",
    gender: "",
    bio: "",
    goal: "",
    profile_photo: null,
});

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const fetchUsers = async () => {
    try {
        const res = await axios.get("http://localhost:8000/api/admin/users", { headers });
        users.value = res.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openEditModal = (user) => {
    editUser.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        weight: user.weight ?? "",
        height: user.height ?? "",
        age: user.age ?? "",
        gender: user.gender ?? "",
        bio: user.bio ?? "",
        goal: user.goal ?? "",
        profile_photo: user.profile_photo,
    };
    showEditModal.value = true;
};

const handleFileChange = (e) => {
    selectedFile.value = e.target.files[0];
};

const updateUser = async () => {
    try {
        const formData = new FormData();
        formData.append("name", editUser.value.name);
        formData.append("email", editUser.value.email);
        formData.append("role", editUser.value.role);
        formData.append("weight", editUser.value.weight);
        formData.append("height", editUser.value.height);
        formData.append("age", editUser.value.age);
        formData.append("gender", editUser.value.gender);
        formData.append("bio", editUser.value.bio);
        formData.append("goal", editUser.value.goal);
        if (selectedFile.value) formData.append("profile_photo", selectedFile.value);
        formData.append("_method", "PUT");

        const res = await axios.post(`http://localhost:8000/api/admin/users/${editUser.value.id}`, formData, { headers });
        const index = users.value.findIndex((u) => u.id === editUser.value.id);
        if (index !== -1) users.value[index] = res.data;
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
        await axios.delete(`http://localhost:8000/api/admin/users/${selectedUserId.value}`, { headers });
        users.value = users.value.filter((u) => u.id !== selectedUserId.value);
        showModal.value = false;
        toastMessage.value = "User deleted";
        toastType.value = "success";
        showToast.value = true;
    } catch (err) {
        toastMessage.value = "Failed to delete user";
        toastType.value = "error";
        showToast.value = true;
    }
    setTimeout(() => (showToast.value = false), 2500);
};

const filteredUsers = computed(() => {
    const s = search.value.toLowerCase();
    return users.value.filter((user) => {
        const matchesSearch = user.name.toLowerCase().includes(s) || user.email.toLowerCase().includes(s);
        const matchesRole = roleFilter.value === "all" || user.role === roleFilter.value;
        return matchesSearch && matchesRole;
    });
});

const avatarUrl = (user) => {
    if (!user.profile_photo) return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=1a1a1a&color=7ED957`;
    if (user.profile_photo.startsWith("http")) return user.profile_photo;
    return `http://localhost:8000/storage/${user.profile_photo}`;
};

onMounted(fetchUsers);
</script>

<template>
    <div class="flex min-h-screen bg-[#080808] text-white">
        <div class="flex-1 p-6">
            <div class="bg-[#111] rounded-3xl overflow-hidden border border-white/5">

                <div class="p-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1">Admin</p>
                        <h2 class="text-2xl font-bold">Users</h2>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <input v-model="search" placeholder="Search users..." class="px-4 py-2.5 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all w-56" />
                        <select v-model="roleFilter" class="px-4 py-2.5 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all">
                            <option value="all">All roles</option>
                            <option value="admin">Admin</option>
                            <option value="trainer">Trainer</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>

                <div v-if="loading" class="p-10 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading users...
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#0a0a0a] text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="text-left px-5 py-3">User</th>
                                <th class="text-left px-5 py-3">Email</th>
                                <th class="text-left px-5 py-3">Role</th>
                                <th class="text-left px-5 py-3 hidden md:table-cell">Joined</th>
                                <th class="text-right px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="text-center py-10 text-gray-600">No users found</td>
                            </tr>
                            <tr v-for="user in filteredUsers" :key="user.id" class="border-t border-white/5 hover:bg-white/3 transition-all">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <img :src="avatarUrl(user)" class="w-9 h-9 rounded-full object-cover ring-2 ring-white/5" />
                                        <span class="font-semibold">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-400">{{ user.email }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs px-2.5 py-1 rounded-xl font-semibold"
                                        :class="user.role === 'admin' ? 'bg-[#7ED957]/15 text-[#7ED957]' : user.role === 'trainer' ? 'bg-blue-500/15 text-blue-400' : 'bg-white/10 text-gray-400'">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 hidden md:table-cell text-xs">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(user)" class="w-8 h-8 flex items-center justify-center bg-blue-500/10 text-blue-400 rounded-xl hover:bg-blue-500 hover:text-white transition-all">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        <button @click="openDeleteModal(user.id)" class="w-8 h-8 flex items-center justify-center bg-red-500/10 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-trash text-xs"></i>
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

    <div v-if="showModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-5">
        <div class="bg-[#111] border border-red-500/20 rounded-3xl p-8 w-full max-w-sm text-center">
            <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash text-red-400"></i>
            </div>
            <h3 class="text-xl font-black text-white mb-2">Delete User?</h3>
            <p class="text-gray-500 text-sm mb-6">This will permanently delete the user and all their data.</p>
            <div class="flex gap-3">
                <button @click="showModal = false" class="flex-1 py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all">Cancel</button>
                <button @click="confirmDelete" class="flex-1 py-3 bg-red-500 text-white rounded-2xl text-sm font-bold hover:bg-red-600 transition-all">Delete</button>
            </div>
        </div>
    </div>

    <div v-if="showEditModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-5">
        <div class="bg-[#111] border border-white/10 rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-white/5 flex items-center justify-between sticky top-0 bg-[#111] z-10">
                <div>
                    <h3 class="text-xl font-black">Edit User</h3>
                    <p class="text-gray-500 text-sm mt-0.5">{{ editUser.email }}</p>
                </div>
                <button @click="showEditModal = false" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 text-xl transition-all">×</button>
            </div>

            <div class="p-6 space-y-5">
                <div class="flex items-center gap-4">
                    <img :src="avatarUrl(editUser)" class="w-16 h-16 rounded-full object-cover ring-2 ring-white/10" />
                    <div>
                        <label class="cursor-pointer bg-white/5 border border-white/10 text-white px-4 py-2 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all inline-block">
                            Change Photo
                            <input type="file" @change="handleFileChange" class="hidden" accept="image/*" />
                        </label>
                        <p class="text-xs text-gray-600 mt-1">JPG or PNG, max 2MB</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Basic Info</p>
                    <input v-model="editUser.name" placeholder="Name" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                    <input v-model="editUser.email" placeholder="Email" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                    <select v-model="editUser.role" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all">
                        <option value="user">User</option>
                        <option value="trainer">Trainer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Physical Info</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-600 mb-1.5">Weight</p>
                            <input v-model="editUser.weight" placeholder="e.g. 82kg" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1.5">Height</p>
                            <input v-model="editUser.height" placeholder="e.g. 182cm" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1.5">Age</p>
                            <input v-model="editUser.age" type="number" placeholder="e.g. 23" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 mb-1.5">Gender</p>
                            <select v-model="editUser.gender" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1.5">Goal</p>
                        <input v-model="editUser.goal" placeholder="e.g. Muscle Gain" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1.5">Bio</p>
                        <textarea v-model="editUser.bio" placeholder="User bio..." rows="3" class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-white text-sm outline-none focus:border-[#7ED957] transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button @click="showEditModal = false" class="flex-1 py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all">Cancel</button>
                    <button @click="updateUser" class="flex-1 py-3 bg-[#7ED957] text-black rounded-2xl text-sm font-bold hover:bg-[#6bc947] transition-all">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showToast" class="fixed bottom-6 right-6 z-50">
        <div class="px-5 py-3.5 rounded-2xl shadow-xl text-sm font-semibold flex items-center gap-2"
            :class="toastType === 'success' ? 'bg-[#7ED957] text-black' : 'bg-red-500 text-white'">
            <i class="fas" :class="toastType === 'success' ? 'fa-check' : 'fa-times'"></i>
            {{ toastMessage }}
        </div>
    </div>
</template>