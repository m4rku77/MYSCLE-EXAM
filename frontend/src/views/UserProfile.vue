<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const user = ref(null);
const friendStatus = ref(null);
const currentUserId = ref(null);

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const fetchUser = async () => {
    try {
        const res = await axios.get(
            `https://myscle-exam-production.up.railway.app/api/users/${route.params.id}`,
            { headers },
        );
        user.value = res.data.data ?? res.data;
    } catch (err) {
        console.error(err);
    }
};

const fetchFriendStatus = async () => {
    try {
        const meRes = await axios.get("https://myscle-exam-production.up.railway.app/api/me", { headers });
        currentUserId.value = meRes.data.id;

        const friendsRes = await axios.get("https://myscle-exam-production.up.railway.app/api/friends", { headers });
        const friends = friendsRes.data.data ?? friendsRes.data;

        if (friends.some((f) => f.id === Number(route.params.id))) {
            friendStatus.value = "friends";
        }
    } catch (err) {
        console.error(err);
    }
};

const addFriend = async () => {
    try {
        await axios.post(
            "https://myscle-exam-production.up.railway.app/api/friends/add",
            { friend_id: route.params.id },
            { headers },
        );
        friendStatus.value = "pending";
    } catch (err) {
        console.error(err);
    }
};

const formatDate = (date) => {
    if (!date) return "";
    return new Date(date).toLocaleDateString("en-US", { month: "long", year: "numeric" });
};

const goalLabel = (goal) => {
    const map = {
        lose_weight: "Lose Weight",
        build_muscle: "Build Muscle",
        increase_strength: "Increase Strength",
        improve_endurance: "Improve Endurance",
        stay_fit: "Stay Fit",
    };
    return map[goal] ?? goal;
};

onMounted(async () => {
    await fetchUser();
    await fetchFriendStatus();
});
</script>

<template>
    <div class="min-h-screen bg-[#0f0f0f] text-white px-4 py-10 flex justify-center">
        <div v-if="user" class="w-full max-w-2xl space-y-8">

            <div class="bg-[#151515] rounded-2xl p-6 flex items-center gap-5 border border-white/10">
                <img
                    :src="user.profile_photo
                        ? 'https://myscle-exam-production.up.railway.app/storage/' + user.profile_photo
                        : `https://ui-avatars.com/api/?name=${user.name}&background=1a1a1a&color=7ED957`"
                    class="w-20 h-20 rounded-full object-cover"
                />
                <div class="space-y-1 flex-1">
                    <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                    <p class="text-xs text-gray-500">Joined {{ formatDate(user.created_at) }}</p>
                </div>
                <div class="pt-2">
                    <button v-if="friendStatus === 'friends'"
                        class="flex items-center gap-2 px-4 py-2 bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] rounded-xl text-xs font-semibold">
                        <i class="fas fa-check"></i> Friends
                    </button>
                    <button v-else-if="friendStatus === 'pending'"
                        class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 text-gray-400 rounded-xl text-xs font-semibold">
                        <i class="fas fa-clock"></i> Request Sent
                    </button>
                    <button v-else-if="currentUserId !== Number(route.params.id)" @click="addFriend"
                        class="flex items-center gap-2 px-4 py-2 bg-[#7ED957] text-black rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all">
                        <i class="fas fa-user-plus"></i> Add Friend
                    </button>
                </div>
            </div>

            <div class="bg-[#151515] rounded-2xl p-5 border border-white/10 space-y-4">
                <h2 class="text-sm text-gray-400 uppercase tracking-wide">Info</h2>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-bullseye"></i>
                        <span>Goal</span>
                    </div>
                    <span :class="user.goal ? 'text-white' : 'text-gray-500 italic'">
                        {{ user.goal ? goalLabel(user.goal) : "Not set" }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-venus-mars"></i>
                        <span>Gender</span>
                    </div>
                    <span :class="user.gender ? 'text-white capitalize' : 'text-gray-500 italic'">
                        {{ user.gender || "Not set" }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-gray-400">
                        <i class="fas fa-ruler-vertical"></i>
                        <span>Height</span>
                    </div>
                    <span :class="user.height ? 'text-white' : 'text-gray-500 italic'">
                        {{ user.height ? user.height + " cm" : "Not set" }}
                    </span>
                </div>
            </div>
            <div class="bg-[#151515] rounded-2xl p-5 border border-white/10">
                <p :class="user.bio ? 'text-gray-300' : 'text-gray-500 italic'" class="text-sm">
                    {{ user.bio || "No bio yet" }}
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
                    <p class="text-xl font-bold text-[#7ED957]">{{ user.completed_workouts ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Workouts</p>
                </div>
                <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
                    <p class="text-xl font-bold text-[#7ED957]">{{ user.age ?? "—" }}</p>
                    <p class="text-xs text-gray-400 mt-1">Age</p>
                </div>
                <div class="bg-[#151515] p-5 rounded-2xl border border-white/10 text-center">
                    <p class="text-xl font-bold text-[#7ED957] capitalize">{{ user.role ?? "—" }}</p>
                    <p class="text-xs text-gray-400 mt-1">Role</p>
                </div>
            </div>

        </div>
    </div>
</template>