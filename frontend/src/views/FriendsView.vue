<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import BottomNav from "../components/BottomNav.vue";
import { useRouter } from "vue-router";

const router = useRouter();
const friends = ref([]);
const users = ref([]);
const requests = ref([]);
const pendingSent = ref([]);
const search = ref("");
const loading = ref(true);

const token = localStorage.getItem("token");
const headers = {
    Authorization: `Bearer ${token}`,
    Accept: "application/json",
};

const getImage = (path, name) => {
    if (!path) return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path}`;
};

const fetchFriends = async () => {
    try {
        const res = await axios.get("http://localhost:8000/api/friends", { headers });
        let data = Array.isArray(res.data) ? res.data : res.data.data || [];
        friends.value = data.map((u) => ({
            id: u.id, name: u.name, workouts: u.workouts_count ?? 0, profile_photo: u.profile_photo,
        }));
    } catch (err) { console.error(err); }
};

const fetchRequests = async () => {
    try {
        const res = await axios.get("http://localhost:8000/api/friends/requests", { headers });
        requests.value = Array.isArray(res.data) ? res.data : res.data.data || [];
    } catch (err) { console.error(err); }
};

const searchUsers = async () => {
    if (!search.value.trim()) { users.value = []; return; }
    try {
        const res = await axios.get(`http://localhost:8000/api/users?search=${encodeURIComponent(search.value)}`, { headers });
        users.value = res.data.map((u) => ({ id: u.id, name: u.name, profile_photo: u.profile_photo }));
    } catch (err) { console.error(err); }
};

const addFriend = async (id) => {
    try {
        await axios.post("http://localhost:8000/api/friends/add", { friend_id: id }, { headers });
        pendingSent.value.push(id);
        users.value = users.value.filter((u) => u.id !== id);
    } catch (err) { console.error(err.response?.data || err.message); }
};

const acceptRequest = async (id) => {
    try {
        await axios.post(`http://localhost:8000/api/friends/accept/${id}`, {}, { headers });
        requests.value = requests.value.filter((r) => r.id !== id);
        await fetchFriends();
    } catch (err) { console.error(err); }
};

const declineRequest = async (id) => {
    try {
        await axios.delete(`http://localhost:8000/api/friends/decline/${id}`, { headers });
        requests.value = requests.value.filter((r) => r.id !== id);
    } catch (err) { console.error(err); }
};

const goToUser = (id) => router.push(`/user/${id}`);

onMounted(async () => {
    await Promise.all([fetchFriends(), fetchRequests()]);
    loading.value = false;
});

const filteredFriends = () =>
    friends.value.filter((f) => f.name.toLowerCase().includes(search.value.toLowerCase()));
</script>

<template>
    <div class="h-screen bg-[#080808] text-white flex">

        <!-- Sidebar -->
        <aside class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <img src="/logo.png" class="h-8" />
                <span class="font-black text-lg tracking-widest uppercase">Myscle</span>
            </div>
            <nav class="space-y-1 flex-1">
                <div @click="router.push('/dashboard')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-dumbbell w-4"></i> Workouts
                </div>
                <div @click="router.push('/statistics')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-chart-line w-4"></i> Statistics
                </div>
                <div class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm">
                    <i class="fas fa-users w-4"></i> Friends
                </div>
                <div @click="router.push('/messages')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-comment w-4"></i> Messages
                </div>
                <div @click="router.push('/profile')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-user w-4"></i> Profile
                </div>
            </nav>
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">

            <!-- Mobile header -->
            <div class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0">
                <p class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1">My Network</p>
                <h1 class="text-3xl font-black">Friends</h1>
            </div>

            <!-- Desktop page header -->
            <div class="hidden md:block bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
                <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1">My Network</p>
                <h1 class="text-3xl font-bold">Friends</h1>
            </div>

            <!-- MOBILE layout -->
            <div class="md:hidden flex-1 overflow-y-auto px-5 pt-5 pb-32 space-y-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"></i>
                    <input v-model="search" @input="searchUsers" placeholder="Search users to add..." class="w-full pl-10 pr-4 py-3 rounded-2xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all" />
                </div>

                <div v-if="search && users.length > 0" class="space-y-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wider px-1">Add new friends</p>
                    <div v-for="user in users" :key="user.id" class="bg-[#111] border border-white/5 rounded-2xl p-3 flex items-center justify-between hover:border-white/10 transition-all">
                        <div @click="goToUser(user.id)" class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer">
                            <img :src="getImage(user.profile_photo, user.name)" class="w-9 h-9 rounded-full object-cover shrink-0" />
                            <span class="font-semibold text-sm truncate">{{ user.name }}</span>
                        </div>
                        <button v-if="!pendingSent.includes(user.id)" @click="addFriend(user.id)" class="px-3 py-1.5 bg-[#7ED957] text-black rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all shrink-0 ml-2">+ Add</button>
                        <span v-else class="px-3 py-1.5 bg-white/5 text-gray-500 rounded-xl text-xs shrink-0 ml-2">Sent</span>
                    </div>
                </div>

                <div v-if="requests.length > 0" class="space-y-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wider px-1">
                        Friend Requests
                        <span class="ml-2 bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20 px-2 py-0.5 rounded-full text-xs font-bold">{{ requests.length }}</span>
                    </p>
                    <div v-for="req in requests" :key="req.id" class="bg-[#111] border border-[#7ED957]/20 rounded-2xl p-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <img :src="getImage(req.profile_photo, req.name)" class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="font-bold text-sm">{{ req.name }}</p>
                                <p class="text-xs text-gray-500">Wants to be your friend</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="acceptRequest(req.id)" class="px-3 py-2 bg-[#7ED957] text-black rounded-xl text-xs font-bold">Accept</button>
                            <button @click="declineRequest(req.id)" class="px-3 py-2 bg-white/5 border border-white/10 text-gray-400 rounded-xl text-xs font-semibold">Decline</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider px-1">{{ filteredFriends().length }} friends</p>
                    <div v-for="friend in filteredFriends()" :key="friend.id" @click="goToUser(friend.id)" class="bg-[#111] border border-white/5 rounded-2xl p-4 flex justify-between items-center hover:border-[#7ED957]/20 transition-all group cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img :src="getImage(friend.profile_photo, friend.name)" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/5" />
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-[#7ED957] rounded-full border-2 border-[#111]"></div>
                            </div>
                            <div>
                                <p class="font-bold text-sm">{{ friend.name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-dumbbell mr-1 text-[#7ED957]/50"></i>{{ friend.workouts }} workouts</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-700 group-hover:text-[#7ED957] transition-colors text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- DESKTOP two column layout -->
            <div class="hidden md:flex flex-1 overflow-hidden">

                <!-- Left column: search + requests -->
                <div class="w-80 shrink-0 border-r border-white/5 flex flex-col overflow-hidden">

                    <!-- Aligned header -->
                    <div class="h-[65px] flex items-center px-6 border-b border-white/5">
                        <div class="relative w-full">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"></i>
                            <input v-model="search" @input="searchUsers" placeholder="Search to add friends..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <div v-if="search && users.length > 0" class="space-y-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wider px-1 mb-2">Add people</p>
                           <div v-for="user in users" :key="user.id" class="bg-[#111] border border-white/5 rounded-2xl p-3 flex items-center justify-between hover:border-white/10 transition-all">
                            <div @click="goToUser(user.id)" class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer">
                                <img :src="getImage(user.profile_photo, user.name)" class="w-9 h-9 rounded-full object-cover shrink-0" />
                                <span class="font-semibold text-sm truncate">{{ user.name }}</span>
                            </div>
                            <button v-if="!pendingSent.includes(user.id)" @click="addFriend(user.id)" class="px-3 py-1.5 bg-[#7ED957] text-black rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all shrink-0 ml-2">+ Add</button>
                            <span v-else class="px-3 py-1.5 bg-white/5 text-gray-500 rounded-xl text-xs shrink-0 ml-2">Sent</span>
                        </div>
                        </div>

                        <div v-else-if="search && users.length === 0" class="text-center text-gray-600 py-8 text-sm">No users found</div>

                        <div v-if="requests.length > 0" class="space-y-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wider px-1 mb-2">
                                Requests
                                <span class="ml-2 bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20 px-2 py-0.5 rounded-full text-xs font-bold">{{ requests.length }}</span>
                            </p>
                            <div v-for="req in requests" :key="req.id" class="bg-[#111] border border-[#7ED957]/20 rounded-2xl p-3">
                                <div class="flex items-center gap-3 mb-3">
                                    <img :src="getImage(req.profile_photo, req.name)" class="w-9 h-9 rounded-full object-cover" />
                                    <div>
                                        <p class="font-bold text-sm">{{ req.name }}</p>
                                        <p class="text-xs text-gray-500">Friend request</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="acceptRequest(req.id)" class="flex-1 py-1.5 bg-[#7ED957] text-black rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all">Accept</button>
                                    <button @click="declineRequest(req.id)" class="flex-1 py-1.5 bg-white/5 border border-white/10 text-gray-400 rounded-xl text-xs font-semibold hover:bg-white/10 transition-all">Decline</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="!search && requests.length === 0" class="text-center text-gray-600 py-8 text-sm">
                            <i class="fas fa-search text-2xl mb-3 opacity-20 block"></i>
                            Search for people to add
                        </div>
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">

                    <div class="h-[65px] flex items-center px-8 border-b border-white/5">
                        <p class="text-sm text-gray-500 font-mono">
                            <span class="text-white font-bold text-lg">{{ filteredFriends().length }}</span> friends
                        </p>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6">
                        <div v-if="loading" class="text-gray-600 text-center py-20">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                        </div>

                        <div v-else-if="filteredFriends().length === 0" class="text-center text-gray-600 py-20">
                            <i class="fas fa-user-friends text-4xl mb-4 opacity-20 block"></i>
                            <p class="text-sm">No friends yet.</p>
                            <p class="text-xs mt-1">Search on the left to add people.</p>
                        </div>

                        <div v-else class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                            <div v-for="friend in filteredFriends()" :key="friend.id" @click="goToUser(friend.id)"
                                class="bg-[#111] border border-white/5 rounded-2xl p-4 flex items-center justify-between hover:border-[#7ED957]/20 hover:-translate-y-0.5 transition-all group cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img :src="getImage(friend.profile_photo, friend.name)" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/5 group-hover:ring-[#7ED957]/30 transition-all" />
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-[#7ED957] rounded-full border-2 border-[#111]"></div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">{{ friend.name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-dumbbell mr-1 text-[#7ED957]/50"></i>{{ friend.workouts }} workouts
                                        </p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-700 group-hover:text-[#7ED957] transition-colors text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <BottomNav class="md:hidden fixed bottom-0 left-0 w-full z-40" />
        </div>
    </div>
</template>