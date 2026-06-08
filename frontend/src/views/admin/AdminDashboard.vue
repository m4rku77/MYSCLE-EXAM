<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const users = ref([]);
const workoutLogs = ref([]);
const subscriptions = ref([]);
const loading = ref(true);

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const avatarUrl = (user) => {
    if (!user?.profile_photo) return `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name ?? "U")}&background=1a1a1a&color=7ED957`;
    if (user.profile_photo.startsWith("http")) return user.profile_photo;
    return `https://myscle-exam-production.up.railway.app/storage/${user.profile_photo}`;
};

const fetchData = async () => {
    try {
        const [usersRes, logsRes, subsRes] = await Promise.all([
            axios.get("https://myscle-exam-production.up.railway.app/api/admin/users", { headers }),
            axios.get("https://myscle-exam-production.up.railway.app/api/admin/workout-logs", { headers }),
            axios.get("https://myscle-exam-production.up.railway.app/api/admin/subscriptions", { headers }),
        ]);
        users.value = usersRes.data.data ?? usersRes.data;
        workoutLogs.value = logsRes.data.data ?? logsRes.data;
        subscriptions.value = subsRes.data.data ?? subsRes.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const totalUsers = computed(() => users.value.length);
const totalTrainers = computed(() => users.value.filter(u => u.role === "trainer").length);
const totalAdmins = computed(() => users.value.filter(u => u.role === "admin").length);
const newThisMonth = computed(() => {
    const now = new Date();
    return users.value.filter(u => {
        const d = new Date(u.created_at);
        return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    }).length;
});
const activeSubscriptions = computed(() => subscriptions.value.filter(s => s.status === "active" || s.status === "trialing").length);
const totalWorkouts = computed(() => workoutLogs.value.length);

const recentUsers = computed(() => [...users.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 6));

const getUsersByMonth = (monthsAgo) => {
    const now = new Date();
    return users.value.filter((u) => {
        const d = new Date(u.created_at);
        const targetMonth = (now.getMonth() - monthsAgo + 12) % 12;
        const targetYear = now.getMonth() - monthsAgo < 0 ? now.getFullYear() - 1 : now.getFullYear();
        return d.getMonth() === targetMonth && d.getFullYear() === targetYear;
    }).length;
};

const getMonthName = (monthsAgo) => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth() - monthsAgo).toLocaleString("default", { month: "short" });
};

const chartData = computed(() => {
    const months = [5, 4, 3, 2, 1, 0].map(m => ({ label: getMonthName(m), value: getUsersByMonth(m) }));
    const max = Math.max(...months.map(m => m.value), 1);
    return months.map(m => ({ ...m, height: Math.max((m.value / max) * 180, 6) }));
});

const roleDistribution = computed(() => {
    const total = users.value.length || 1;
    return [
        { label: "Users", count: users.value.filter(u => u.role === "user").length, color: "bg-[#7ED957]" },
        { label: "Trainers", count: users.value.filter(u => u.role === "trainer").length, color: "bg-blue-400" },
        { label: "Admins", count: users.value.filter(u => u.role === "admin").length, color: "bg-purple-400" },
    ].map(r => ({ ...r, pct: Math.round((r.count / total) * 100) }));
});

onMounted(fetchData);
</script>

<template>
    <div class="min-h-screen bg-[#080808] text-white">
        <div class="flex-1 p-6 md:p-8">

            <div class="mb-8">
                <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1">Admin</p>
                <h1 class="text-3xl font-bold">Dashboard</h1>
            </div>

            <div v-if="loading" class="text-center text-gray-600 py-20">
                <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
            </div>

            <div v-else class="space-y-6">

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Total Users</p>
                        <p class="text-3xl font-black text-[#7ED957]">{{ totalUsers }}</p>
                        <p class="text-xs text-gray-600 mt-1">registered</p>
                    </div>
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Trainers</p>
                        <p class="text-3xl font-black text-blue-400">{{ totalTrainers }}</p>
                        <p class="text-xs text-gray-600 mt-1">active</p>
                    </div>
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Admins</p>
                        <p class="text-3xl font-black text-purple-400">{{ totalAdmins }}</p>
                        <p class="text-xs text-gray-600 mt-1">total</p>
                    </div>
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">New This Month</p>
                        <p class="text-3xl font-black text-yellow-400">{{ newThisMonth }}</p>
                        <p class="text-xs text-gray-600 mt-1">joined</p>
                    </div>
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Subscriptions</p>
                        <p class="text-3xl font-black text-[#7ED957]">{{ activeSubscriptions }}</p>
                        <p class="text-xs text-gray-600 mt-1">active</p>
                    </div>
                    <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Workouts</p>
                        <p class="text-3xl font-black text-orange-400">{{ totalWorkouts }}</p>
                        <p class="text-xs text-gray-600 mt-1">logged</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">

                    <div class="md:col-span-2 bg-[#111] border border-white/5 rounded-2xl p-6">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-6">User Growth — last 6 months</p>
                        <div class="flex items-end gap-3 h-48">
                            <div v-for="m in chartData" :key="m.label" class="flex-1 flex flex-col items-center justify-end gap-2">
                                <span class="text-xs text-gray-500 font-mono">{{ m.value }}</span>
                                <div class="w-full rounded-xl transition-all"
                                    :class="m.value === 0 ? 'bg-white/5' : 'bg-[#7ED957]'"
                                    :style="{ height: m.height + 'px' }"></div>
                                <span class="text-xs text-gray-600">{{ m.label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#111] border border-white/5 rounded-2xl p-6">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-6">Role Distribution</p>
                        <div class="space-y-4">
                            <div v-for="r in roleDistribution" :key="r.label">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-semibold">{{ r.label }}</span>
                                    <span class="text-sm text-gray-400">{{ r.count }} <span class="text-xs text-gray-600">({{ r.pct }}%)</span></span>
                                </div>
                                <div class="w-full bg-white/5 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all" :class="r.color" :style="{ width: r.pct + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#111] border border-white/5 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Recent Users</p>
                        <button @click="router.push('/admin/users')" class="text-xs text-[#7ED957] hover:text-[#6bc947] transition-all">View all →</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="user in recentUsers" :key="user.id"
                            class="flex items-center gap-3 bg-[#0a0a0a] border border-white/5 rounded-2xl p-3 hover:border-white/10 transition-all">
                            <img :src="avatarUrl(user)" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-white/5" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate">{{ user.name }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ user.email }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="text-xs px-2 py-0.5 rounded-lg font-semibold"
                                    :class="user.role === 'admin' ? 'bg-[#7ED957]/15 text-[#7ED957]' : user.role === 'trainer' ? 'bg-blue-500/15 text-blue-400' : 'bg-white/10 text-gray-400'">
                                    {{ user.role }}
                                </span>
                                <span class="text-xs text-gray-600">{{ new Date(user.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>