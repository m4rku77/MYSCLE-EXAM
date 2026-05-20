<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";

const activities = ref([]);
const loading = ref(true);
const search = ref("");
const filterType = ref("all");
const lastFetch = ref(null);
const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

const formatDate = (date) => {
    if (!date) return "--";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const typeIcon = (type) => {
    if (type === "user_registered") return "fas fa-user-plus";
    if (type === "workout_completed") return "fas fa-dumbbell";
    if (type === "workout_created") return "fas fa-plus-circle";
    if (type === "workout_updated") return "fas fa-pen";
    if (type === "profile_updated") return "fas fa-user-edit";
    if (type === "subscription_started") return "fas fa-credit-card";
    if (type === "subscription_canceled") return "fas fa-times-circle";
    if (type === "trainer_client_added") return "fas fa-user-check";
    if (type === "friend_added") return "fas fa-user-friends"; // icon
    return "fas fa-circle";
};

const typeColor = (type) => {
    if (type === "user_registered") return "text-[#7ED957] bg-[#7ED957]/10";
    if (type === "workout_completed") return "text-blue-400 bg-blue-400/10";
    if (type === "workout_created") return "text-blue-400 bg-blue-400/10";
    if (type === "workout_updated") return "text-orange-400 bg-orange-400/10";
    if (type === "profile_updated") return "text-pink-400 bg-pink-400/10";
    if (type === "subscription_started")
        return "text-yellow-400 bg-yellow-400/10";
    if (type === "subscription_canceled") return "text-red-400 bg-red-400/10";
    if (type === "trainer_client_added")
        return "text-purple-400 bg-purple-400/10";
    if (type === "friend_added") return "text-cyan-400 bg-cyan-400/10"; // color
    return "text-gray-400 bg-gray-400/10";
};

const typeLabel = (type) => {
    if (type === "user_registered") return "New User";
    if (type === "workout_completed") return "Workout";
    if (type === "workout_created") return "New Workout";
    if (type === "workout_updated") return "Edited";
    if (type === "profile_updated") return "Profile";
    if (type === "subscription_started") return "Subscription";
    if (type === "subscription_canceled") return "Canceled";
    if (type === "trainer_client_added") return "Client";
    if (type === "friend_added") return "Friend"; // label
    return type;
};

const fetchActivity = async () => {
    try {
        const [
            usersRes,
            workoutsRes,
            subscriptionsRes,
            plansRes,
            clientsRes,
            friendsRes,
        ] = await Promise.all([
            axios.get("http://localhost:8000/api/admin/users", { headers }),
            axios.get("http://localhost:8000/api/admin/workout-logs", {
                headers,
            }),
            axios.get("http://localhost:8000/api/admin/subscriptions", {
                headers,
            }),
            axios.get("http://localhost:8000/api/admin/training-plans", {
                headers,
            }),
            axios.get("http://localhost:8000/api/admin/trainer-clients", {
                headers,
            }),
            axios.get("http://localhost:8000/api/admin/friends", {
                headers,
            }),
        ]);

        const users = (usersRes.data.data ?? usersRes.data).map((u) => ({
            id: `user-${u.id}`,
            type: "user_registered",
            user: {
                name: u.name,
                email: u.email,
                profile_photo: u.profile_photo,
            },
            description: `${u.name} registered as ${u.role}`,
            created_at: u.created_at,
        }));

        const workouts = (workoutsRes.data.data ?? workoutsRes.data).map(
            (w) => ({
                id: `workout-${w.id}`,
                type: "workout_completed",
                user: {
                    name: w.user?.name,
                    email: w.user?.email,
                    profile_photo: w.user?.profile_photo,
                },
                description: `${w.user?.name} completed a workout`,
                created_at: w.created_at,
            }),
        );

        const subs = (subscriptionsRes.data.data ?? subscriptionsRes.data).map(
            (s) => ({
                id: `sub-${s.id}`,
                type:
                    s.status === "canceled"
                        ? "subscription_canceled"
                        : "subscription_started",
                user: {
                    name: s.user?.name,
                    email: s.user?.email,
                    profile_photo: s.user?.profile_photo,
                },
                description:
                    s.status === "canceled"
                        ? `${s.user?.name} canceled their subscription`
                        : `${s.user?.name} started a ${s.status === "trialing" ? "trial" : "subscription"}`,
                created_at: s.created_at,
            }),
        );

        const plans = (plansRes.data.data ?? plansRes.data).map((p) => ({
            id: `plan-${p.id}`,
            type: "workout_created",
            user: {
                name: p.user?.name,
                email: p.user?.email,
                profile_photo: p.user?.profile_photo,
            },
            description: `${p.user?.name} created workout "${p.name}"`,
            created_at: p.created_at,
        }));

        const editedWorkouts = (plansRes.data.data ?? plansRes.data)
            .filter((p) => p.updated_at !== p.created_at)
            .map((p) => ({
                id: `plan-edit-${p.id}`,
                type: "workout_updated",
                user: {
                    name: p.user?.name,
                    email: p.user?.email,
                    profile_photo: p.user?.profile_photo,
                },
                description: `${p.user?.name} edited workout "${p.name}"`,
                created_at: p.updated_at,
            }));

        const editedProfiles = (usersRes.data.data ?? usersRes.data)
            .filter((u) => u.updated_at !== u.created_at)
            .map((u) => ({
                id: `profile-edit-${u.id}`,
                type: "profile_updated",
                user: {
                    name: u.name,
                    email: u.email,
                    profile_photo: u.profile_photo,
                },
                description: `${u.name} updated their profile`,
                created_at: u.updated_at,
            }));

        const clients = (clientsRes.data.data ?? clientsRes.data).map((c) => ({
            id: `client-${c.id}`,
            type: "trainer_client_added",
            user: {
                name: c.trainer?.name,
                email: c.trainer?.email,
                profile_photo: c.trainer?.profile_photo,
            },
            description: `${c.trainer?.name} added ${c.client?.name} as a client`,
            created_at: c.created_at,
        }));
        const friends = (friendsRes.data.data ?? friendsRes.data).map((f) => ({
            id: `friend-${f.id}`,
            type: "friend_added",
            user: {
                name: f.user_name,
                email: f.user_email,
                profile_photo: f.user_photo,
            },
            description: `${f.user_name} added ${f.friend_name} as a friend`,
            created_at: f.created_at,
        }));
        activities.value = [
            ...users,
            ...workouts,
            ...subs,
            ...plans,
            ...editedWorkouts,
            ...editedProfiles,
            ...clients,
            ...friends,
        ].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        lastFetch.value = new Date();
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

let interval = null;

onMounted(async () => {
    await fetchActivity();
    interval = setInterval(fetchActivity, 15000);
});

onUnmounted(() => {
    clearInterval(interval);
});

const filtered = computed(() => {
    return activities.value.filter((a) => {
        const matchSearch =
            a.user?.name?.toLowerCase().includes(search.value.toLowerCase()) ||
            a.description?.toLowerCase().includes(search.value.toLowerCase());
        const matchType =
            filterType.value === "all" || a.type === filterType.value;
        return matchSearch && matchType;
    });
});

const types = [
    { value: "all", label: "All" },
    { value: "user_registered", label: "Users" },
    { value: "workout_completed", label: "Workouts" },
    { value: "workout_created", label: "New Workouts" },
    { value: "workout_updated", label: "Edited" },
    { value: "profile_updated", label: "Profile" },
    { value: "subscription_started", label: "Subscriptions" },
    { value: "subscription_canceled", label: "Canceled" },
    { value: "trainer_client_added", label: "Clients" },
    { value: "friend_added", label: "Friends" },
];
</script>

<template>
    <div class="h-full bg-[#080808] text-white flex flex-col overflow-hidden">
        <div class="bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
            <p
                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
            >
                Admin
            </p>
            <h1 class="text-3xl font-bold">Activity Log</h1>
        </div>

        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-5">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <i
                        class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"
                    ></i>
                    <input
                        v-model="search"
                        placeholder="Search activity..."
                        class="w-full pl-10 pr-4 py-3 bg-[#111] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                    />
                </div>
                <div
                    class="flex bg-[#111] border border-white/5 rounded-2xl p-1 gap-0.5 flex-wrap"
                >
                    <button
                        v-for="t in types"
                        :key="t.value"
                        @click="filterType = t.value"
                        class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                        :class="
                            filterType === t.value
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-500 hover:text-white'
                        "
                    >
                        {{ t.label }}
                    </button>
                </div>
            </div>

            <div v-if="loading" class="text-center text-gray-600 py-16">
                <i class="fas fa-spinner fa-spin mr-2"></i> Loading activity...
            </div>

            <div
                v-else-if="filtered.length === 0"
                class="text-center text-gray-600 py-16"
            >
                <i class="fas fa-history text-3xl mb-3 opacity-20"></i>
                <p>No activity found</p>
            </div>

            <div
                v-else
                class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden"
            >
                <div
                    v-for="activity in filtered"
                    :key="activity.id"
                    class="flex items-center gap-4 px-6 py-4 border-b border-white/5 last:border-0 transition-all"
                    :class="
                        lastFetch.value &&
                        new Date(activity.created_at) >
                            new Date(lastFetch.value - 16000)
                            ? 'bg-[#7ED957]/10 animate-pulse-once'
                            : 'hover:bg-white/3'
                    "
                >
                    <div
                        class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
                        :class="typeColor(activity.type)"
                    >
                        <i :class="typeIcon(activity.type)" class="text-sm"></i>
                    </div>

                    <img
                        :src="
                            getImage(
                                activity.user?.profile_photo,
                                activity.user?.name,
                            )
                        "
                        class="w-9 h-9 rounded-full object-cover shrink-0"
                    />

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">
                            {{ activity.description }}
                        </p>
                        <p class="text-xs text-gray-500 truncate mt-0.5">
                            {{ activity.user?.email }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <span
                            class="text-xs px-2.5 py-1 rounded-xl font-semibold"
                            :class="typeColor(activity.type)"
                        >
                            {{ typeLabel(activity.type) }}
                        </span>
                        <span class="text-xs text-gray-600">{{
                            formatDate(activity.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
