<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const subscriptions = ref([]);
const loading = ref(true);
const search = ref("");
const filterStatus = ref("all");

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `https://myscle-exam-production.up.railway.app/storage/${path.replace("storage/", "")}`;
};

const formatDate = (date) => {
    if (!date) return "--";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

onMounted(async () => {
    try {
        const res = await axios.get(
            "https://myscle-exam-production.up.railway.app/api/admin/subscriptions",
            { headers },
        );
        subscriptions.value = res.data.data ?? res.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const filtered = computed(() => {
    return subscriptions.value.filter((s) => {
        const matchSearch =
            s.user?.name?.toLowerCase().includes(search.value.toLowerCase()) ||
            s.user?.email?.toLowerCase().includes(search.value.toLowerCase());
        const matchStatus =
            filterStatus.value === "all" || s.status === filterStatus.value;
        return matchSearch && matchStatus;
    });
});

const totalActive = computed(
    () => subscriptions.value.filter((s) => s.status === "active").length,
);
const totalTrial = computed(
    () => subscriptions.value.filter((s) => s.status === "trialing").length,
);
const totalCanceled = computed(
    () => subscriptions.value.filter((s) => s.status === "canceled").length,
);

const statusClass = (status) => {
    if (status === "active")
        return "bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20";
    if (status === "trialing")
        return "bg-yellow-500/10 text-yellow-400 border border-yellow-500/20";
    if (status === "canceled")
        return "bg-red-500/10 text-red-400 border border-red-500/20";
    return "bg-white/5 text-gray-400 border border-white/10";
};

const statusLabel = (status) => {
    if (status === "active") return "Active";
    if (status === "trialing") return "Trial";
    if (status === "canceled") return "Canceled";
    return status;
};
</script>

<template>
    <div class="h-full bg-[#080808] text-white flex flex-col overflow-hidden">
        <div class="bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
            <p
                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
            >
                Admin
            </p>
            <h1 class="text-3xl font-bold">Subscriptions</h1>
        </div>

        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Active
                    </p>
                    <p class="text-3xl font-bold text-[#7ED957]">
                        {{ totalActive }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">paying members</p>
                </div>
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Trial
                    </p>
                    <p class="text-3xl font-bold text-yellow-400">
                        {{ totalTrial }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">free period</p>
                </div>
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Canceled
                    </p>
                    <p class="text-3xl font-bold text-red-400">
                        {{ totalCanceled }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">churned</p>
                </div>
            </div>

            <div class="flex gap-3">
                <div class="relative flex-1">
                    <i
                        class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"
                    ></i>
                    <input
                        v-model="search"
                        placeholder="Search by name or email..."
                        class="w-full pl-10 pr-4 py-3 bg-[#111] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                    />
                </div>
                <div
                    class="flex bg-[#111] border border-white/5 rounded-2xl p-1"
                >
                    <button
                        v-for="s in ['all', 'active', 'trialing', 'canceled']"
                        :key="s"
                        @click="filterStatus = s"
                        class="px-4 py-2 rounded-xl text-xs font-semibold capitalize transition-all"
                        :class="
                            filterStatus === s
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-500 hover:text-white'
                        "
                    >
                        {{
                            s === "trialing"
                                ? "Trial"
                                : s.charAt(0).toUpperCase() + s.slice(1)
                        }}
                    </button>
                </div>
            </div>

            <div
                class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden"
            >
                <div
                    class="grid grid-cols-12 gap-4 px-6 py-3 border-b border-white/5 text-xs text-gray-500 uppercase tracking-wider"
                >
                    <span class="col-span-4">User</span>
                    <span class="col-span-2">Status</span>
                    <span class="col-span-2">Trial Ends</span>
                    <span class="col-span-2">Renews</span>
                    <span class="col-span-2">Stripe ID</span>
                </div>

                <div v-if="loading" class="text-center text-gray-600 py-16">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading
                    subscriptions...
                </div>

                <div
                    v-else-if="filtered.length === 0"
                    class="text-center text-gray-600 py-16"
                >
                    <i class="fas fa-credit-card text-3xl mb-3 opacity-20"></i>
                    <p>No subscriptions found</p>
                </div>

                <div v-else>
                    <div
                        v-for="sub in filtered"
                        :key="sub.id"
                        class="grid grid-cols-12 gap-4 px-6 py-4 border-b border-white/5 last:border-0 hover:bg-white/3 transition-all items-center"
                    >
                        <div class="col-span-4 flex items-center gap-3">
                            <img
                                :src="
                                    getImage(
                                        sub.user?.profile_photo,
                                        sub.user?.name,
                                    )
                                "
                                class="w-9 h-9 rounded-full object-cover shrink-0"
                            />
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate">
                                    {{ sub.user?.name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ sub.user?.email }}
                                </p>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <span
                                class="px-3 py-1 rounded-xl text-xs font-bold"
                                :class="statusClass(sub.status)"
                            >
                                {{ statusLabel(sub.status) }}
                            </span>
                        </div>
                        <div class="col-span-2 text-sm text-gray-400">
                            {{ formatDate(sub.trial_ends_at) }}
                        </div>
                        <div class="col-span-2 text-sm text-gray-400">
                            {{ formatDate(sub.ends_at) }}
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-600 font-mono truncate">
                                {{ sub.stripe_subscription_id ?? "--" }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
