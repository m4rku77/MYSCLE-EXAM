<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import TrainerBottomNav from "../../components/TrainerBottomNav.vue";

const router = useRouter();

const users = ref([]);
const clients = ref([]);
const loading = ref(true);
const sidebarOpen = ref(true);
const showAddModal = ref(false);
const clientSearch = ref("");
const modalSearch = ref("");
const filterTab = ref("all");

const getImage = (path, name) => {
    if (!path) return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");
        const usersRes = await axios.get("http://localhost:8000/api/trainer/users", { headers: { Authorization: `Bearer ${token}` } });
        users.value = usersRes.data.data ?? usersRes.data;
        const clientsRes = await axios.get("http://localhost:8000/api/trainer/clients-all", { headers: { Authorization: `Bearer ${token}` } });
        clients.value = clientsRes.data.data ?? clientsRes.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const acceptedClients = computed(() => clients.value.filter(c => c.status === 'accepted'));
const pendingClients = computed(() => clients.value.filter(c => c.status !== 'accepted'));

const filteredClients = computed(() => {
    const query = clientSearch.value.toLowerCase().trim();
    let list = filterTab.value === 'pending' ? pendingClients.value : filterTab.value === 'active' ? acceptedClients.value : clients.value;
    if (query) list = list.filter((c) => c.name.toLowerCase().includes(query));
    return [...list].sort((a, b) => {
        if (a.status === 'accepted' && b.status !== 'accepted') return -1;
        if (a.status !== 'accepted' && b.status === 'accepted') return 1;
        return 0;
    });
});

const filteredUsers = computed(() => {
    const query = modalSearch.value.toLowerCase().trim();
    if (!query) return [];
    return users.value.filter((user) => {
        const nameMatch = user.name.toLowerCase().includes(query);
        const notClient = !clients.value.some((c) => c.id === user.id);
        return nameMatch && notClient;
    });
});

const addClient = async (id) => {
    try {
        const token = localStorage.getItem("token");
        await axios.post(`http://localhost:8000/api/trainer/add-client/${id}`, {}, { headers: { Authorization: `Bearer ${token}` } });
        const addedUser = users.value.find((u) => u.id === id);
        if (addedUser) clients.value.push({ ...addedUser, status: "pending" });
        showAddModal.value = false;
        modalSearch.value = "";
    } catch (err) {
        console.error(err);
    }
};

const openUser = (user) => {
    if (user.status === "accepted") router.push(`/trainer/client/${user.id}`);
};

const openModal = () => {
    modalSearch.value = "";
    showAddModal.value = true;
};
</script>

<template>
    <div class="h-screen bg-[#080808] text-white flex">

        <aside class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full">
            <div class="flex items-center gap-3 mb-12">
                <img src="/logo.png" class="h-8" />
                <span class="font-black text-lg tracking-widest uppercase">Myscle</span>
            </div>
            <nav class="space-y-1 flex-1">
                <div class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm">
                    <i class="fas fa-users w-4"></i> Clients
                </div>
                <div @click="router.push('/trainer/messages')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-comment w-4"></i> Messages
                </div>
                <div @click="router.push('/trainer/profile')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                    <i class="fas fa-user w-4"></i> Profile
                </div>
            </nav>
            <button @click="openModal" class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2">
                <i class="fas fa-plus text-xs"></i> Add Client
            </button>
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">

            <div class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1">Trainer</p>
                        <h1 class="text-3xl font-black">Clients</h1>
                    </div>
                    <button @click="openModal" class="flex items-center gap-2 px-5 py-2.5 bg-black text-[#7ED957] rounded-2xl font-bold text-sm">
                        <i class="fas fa-plus text-xs"></i> Add
                    </button>
                </div>
                <input v-model="clientSearch" placeholder="Search clients..." class="w-full bg-black/20 rounded-2xl px-4 py-3 outline-none placeholder-black/50 text-black text-sm" />
            </div>

            <div class="hidden md:block bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1">Trainer</p>
                        <h1 class="text-3xl font-bold">Clients</h1>
                    </div>
                    <button @click="openModal" class="flex items-center gap-2 px-5 py-2.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20">
                        <i class="fas fa-plus text-xs"></i> Add Client
                    </button>
                </div>
            </div>

            <div class="md:hidden flex-1 overflow-y-auto px-5 pt-5 pb-32 space-y-3">
                <div v-if="loading" class="text-center text-gray-600 py-20"><i class="fas fa-spinner fa-spin mr-2"></i></div>
                <template v-else>
                    <div v-for="user in filteredClients" :key="user.id" @click="openUser(user)"
                        class="flex items-center gap-4 bg-[#111] border border-white/5 rounded-2xl p-4 transition-all"
                        :class="user.status === 'accepted' ? 'cursor-pointer hover:border-[#7ED957]/20' : 'opacity-60'">
                        <img :src="getImage(user.profile_photo, user.name)" class="w-12 h-12 rounded-full object-cover shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ user.email }}</p>
                        </div>
                        <span class="text-xs font-bold shrink-0 px-3 py-1.5 rounded-xl"
                            :class="user.status === 'accepted' ? 'bg-[#7ED957]/10 text-[#7ED957]' : 'bg-yellow-500/10 text-yellow-500'">
                            {{ user.status === "accepted" ? "Active" : "Pending" }}
                        </span>
                    </div>
                </template>
            </div>

            <div class="hidden md:flex flex-1 overflow-hidden">

                <div class="flex flex-col overflow-hidden transition-all duration-300 border-white/5"
                    :class="sidebarOpen ? 'w-72 border-r' : 'w-0 opacity-0'">

                    <div class="h-[69px] flex items-center px-5 border-b border-white/5">
                        <div class="relative w-full">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"></i>
                            <input v-model="clientSearch" placeholder="Search clients..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="px-5 py-3 border-b border-white/5">
                        <div class="flex bg-white/5 rounded-2xl p-1 gap-1">
                            <button @click="filterTab = 'all'" :class="filterTab === 'all' ? 'bg-[#7ED957] text-black' : 'text-gray-400 hover:text-white'" class="flex-1 py-2 rounded-xl text-xs font-bold transition-all">
                                All <span class="ml-1 opacity-60">{{ clients.length }}</span>
                            </button>
                            <button @click="filterTab = 'active'" :class="filterTab === 'active' ? 'bg-[#7ED957] text-black' : 'text-gray-400 hover:text-white'" class="flex-1 py-2 rounded-xl text-xs font-bold transition-all">
                                Active <span class="ml-1 opacity-60">{{ acceptedClients.length }}</span>
                            </button>
                            <button @click="filterTab = 'pending'" :class="filterTab === 'pending' ? 'bg-yellow-500 text-black' : 'text-gray-400 hover:text-white'" class="flex-1 py-2 rounded-xl text-xs font-bold transition-all">
                                Pending <span class="ml-1 opacity-60">{{ pendingClients.length }}</span>
                            </button>
                        </div>
                    </div>

                   

                    <div class="p-5">
                        <button @click="openModal" class="w-full py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#7ED957]/10">
                            <i class="fas fa-plus text-xs"></i> Add New Client
                        </button>
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <div class="h-[69px] flex items-center px-6 border-b border-white/5 gap-3">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all shrink-0">
                            <i class="fas text-xs" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                        </button>
                        <p class="text-sm text-gray-500">
                            Showing <span class="text-white font-bold">{{ filteredClients.length }}</span> clients
                            <span v-if="filterTab !== 'all'" class="ml-2 text-xs">· <span class="text-[#7ED957]">{{ filterTab }}</span></span>
                        </p>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6">
                        <div v-if="loading" class="text-center text-gray-600 py-20">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading clients...
                        </div>
                        <div v-else-if="filteredClients.length === 0" class="text-center text-gray-600 py-20">
                            <i class="fas fa-users text-4xl mb-4 opacity-20 block"></i>
                            <p class="text-sm">No clients found.</p>
                            <p class="text-xs mt-1 text-gray-700">Try a different filter or add a new client.</p>
                        </div>
                        <div v-else class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                            <div v-for="user in filteredClients" :key="user.id" @click="openUser(user)"
                                class="bg-[#111] border border-white/5 rounded-2xl p-4 flex items-center gap-4 transition-all group"
                                :class="user.status === 'accepted' ? 'cursor-pointer hover:border-[#7ED957]/25 hover:-translate-y-0.5' : 'opacity-50'">
                                <div class="relative shrink-0">
                                    <img :src="getImage(user.profile_photo, user.name)" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/5 group-hover:ring-[#7ED957]/20 transition-all" />
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#111]"
                                        :class="user.status === 'accepted' ? 'bg-[#7ED957]' : 'bg-yellow-500'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm truncate group-hover:text-[#7ED957] transition-colors">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ user.email }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-xl"
                                        :class="user.status === 'accepted' ? 'bg-[#7ED957]/10 text-[#7ED957]' : 'bg-yellow-500/10 text-yellow-500'">
                                        {{ user.status === "accepted" ? "Active" : "Pending" }}
                                    </span>
                                    <i v-if="user.status === 'accepted'" class="fas fa-chevron-right text-gray-700 group-hover:text-[#7ED957] transition-colors text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showAddModal" class="fixed inset-0 bg-[#080808] z-50 flex flex-col md:bg-black/70 md:items-center md:justify-center">
            <div class="md:bg-[#111] md:border md:border-white/10 md:rounded-3xl md:w-full md:max-w-lg flex flex-col h-full md:h-auto md:max-h-[80vh]">
                <div class="p-6 pb-4 shrink-0">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h2 class="text-2xl font-black">Add Client</h2>
                            <p class="text-gray-500 text-sm mt-0.5">Search and send a request</p>
                        </div>
                        <button @click="showAddModal = false" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 text-xl transition-all">×</button>
                    </div>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-sm"></i>
                        <input v-model="modalSearch" placeholder="Search by name..." class="w-full pl-10 pr-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl outline-none focus:border-[#7ED957] text-sm placeholder-gray-600 transition-all" autofocus />
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto px-6 pb-8 space-y-2">
                    <div v-for="user in filteredUsers" :key="user.id" class="flex items-center gap-4 bg-[#0a0a0a] border border-white/5 rounded-2xl p-4 hover:border-white/10 transition-all">
                        <img :src="getImage(user.profile_photo, user.name)" class="w-10 h-10 rounded-full object-cover shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                        </div>
                        <button @click="addClient(user.id)" class="bg-[#7ED957] text-black px-4 py-2 rounded-xl font-bold text-sm shrink-0 hover:bg-[#6bc947] transition-all">Add</button>
                    </div>
                    <div v-if="modalSearch && filteredUsers.length === 0" class="text-center text-gray-600 py-10 text-sm">
                        <i class="fas fa-search text-2xl mb-3 opacity-20 block"></i>
                        No users match "{{ modalSearch }}"
                    </div>
                    <div v-if="!modalSearch" class="text-center text-gray-700 py-10 text-sm">
                        <i class="fas fa-user-plus text-2xl mb-3 opacity-20 block"></i>
                        Start typing to search users
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>