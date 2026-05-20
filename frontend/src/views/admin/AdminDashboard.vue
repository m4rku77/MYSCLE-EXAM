<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AdminSidebar from "../../components/admin/AdminSidebar.vue";

const users = ref([]);
const loading = ref(true);

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

const deleteUser = async (id) => {
    if (!confirm("Delete this user?")) return;

    try {
        const token = localStorage.getItem("token");

        await axios.delete(`http://localhost:8000/api/admin/users/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        users.value = users.value.filter((u) => u.id !== id);
    } catch (err) {
        console.error(err);
    }
};

const getUsersByMonth = (monthsAgo) => {
    const now = new Date();

    return users.value.filter((u) => {
        const d = new Date(u.created_at);

        const targetMonth = (now.getMonth() - monthsAgo + 12) % 12;
        const targetYear =
            now.getMonth() - monthsAgo < 0
                ? now.getFullYear() - 1
                : now.getFullYear();

        return d.getMonth() === targetMonth && d.getFullYear() === targetYear;
    }).length;
};

const getMonthName = (monthsAgo) => {
    const now = new Date();
    const date = new Date(now.getFullYear(), now.getMonth() - monthsAgo);

    return date.toLocaleString("default", { month: "short" });
};

const getMaxUsers = () => {
    const values = [5, 4, 3, 2, 1, 0].map((m) => getUsersByMonth(m));
    return Math.max(...values, 3);
};
onMounted(fetchUsers);
</script>

<template>
    <div class="flex min-h-screen bg-[#0f0f0f] text-white">
        <div class="flex-1 p-6">
            <div class="max-w-6xl mx-auto space-y-6">
                <div>
                    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                    <p class="text-gray-400 text-sm">Overview & analytics</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        class="bg-[#1a1a1a] p-5 rounded-xl border border-white/5"
                    >
                        <p class="text-gray-400 text-xs">Total Users</p>
                        <p class="text-2xl font-bold text-[#7ED957] mt-1">
                            {{ users.length }}
                        </p>
                    </div>

                    <div
                        class="bg-[#1a1a1a] p-5 rounded-xl border border-white/5"
                    >
                        <p class="text-gray-400 text-xs">Admins</p>
                        <p class="text-2xl font-bold text-blue-400 mt-1">
                            {{ users.filter((u) => u.role === "admin").length }}
                        </p>
                    </div>

                    <div
                        class="bg-[#1a1a1a] p-5 rounded-xl border border-white/5"
                    >
                        <p class="text-gray-400 text-xs">New This Month</p>
                        <p class="text-2xl font-bold text-yellow-400 mt-1">
                            {{
                                users.filter((u) => {
                                    const d = new Date(u.created_at);
                                    const now = new Date();
                                    return (
                                        d.getMonth() === now.getMonth() &&
                                        d.getFullYear() === now.getFullYear()
                                    );
                                }).length
                            }}
                        </p>
                    </div>
                </div>

                <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-white/5">
                    <h2 class="text-sm text-gray-400 uppercase mb-4">
                        User Growth (last 6 months)
                    </h2>

                    <div class="flex gap-4 h-52">
                        <div
                            class="flex flex-col justify-between text-xs text-gray-500 pr-2"
                        >
                            <span v-for="n in 5" :key="n">
                                {{ Math.round((getMaxUsers() * (5 - n)) / 4) }}
                            </span>
                        </div>

                        <div class="flex items-end gap-4 flex-1">
                            <div
                                v-for="month in [5, 4, 3, 2, 1, 0]"
                                :key="month"
                                class="flex-1 flex flex-col items-center justify-end"
                            >
                                <span class="text-xs text-gray-400 mb-1">
                                    {{ getUsersByMonth(month) }}
                                </span>

                                <div
                                    class="w-full rounded-md transition-all flex items-end justify-center"
                                    :class="
                                        getUsersByMonth(month) === 0
                                            ? 'bg-gray-700 opacity-40'
                                            : 'bg-[#7ED957]'
                                    "
                                    :style="{
                                        height:
                                            Math.max(
                                                (getUsersByMonth(month) /
                                                    getMaxUsers()) *
                                                    180,
                                                8,
                                            ) + 'px',
                                    }"
                                ></div>

                                <span class="text-xs text-gray-500 mt-2">
                                    {{ getMonthName(month) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-white/5">
                    <h2 class="text-sm text-gray-400 uppercase mb-4">
                        Recent Users
                    </h2>

                    <div class="space-y-3">
                        <div
                            v-for="user in users.slice(0, 5)"
                            :key="user.id"
                            class="flex items-center justify-between bg-[#151515] p-3 rounded-lg"
                        >
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
                                    class="w-8 h-8 rounded-full object-cover"
                                />
                                <span class="text-sm">{{ user.name }}</span>
                            </div>

                            <span class="text-xs text-gray-500">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString()
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
