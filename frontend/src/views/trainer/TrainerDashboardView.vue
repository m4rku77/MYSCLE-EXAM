<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import TrainerBottomNav from "../../components/TrainerBottomNav.vue";

const router = useRouter();

const users = ref([]);
const clients = ref([]);
const loading = ref(true);

const showAddModal = ref(false);
const clientSearch = ref("");
const modalSearch = ref("");

const getImage = (path, name) => {
    if (!path) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;
    }
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");

        const usersRes = await axios.get(
            "http://localhost:8000/api/trainer/users",
            { headers: { Authorization: `Bearer ${token}` } },
        );
        users.value = usersRes.data.data ?? usersRes.data;

        const clientsRes = await axios.get(
            "http://localhost:8000/api/trainer/clients-all",
            { headers: { Authorization: `Bearer ${token}` } },
        );
        clients.value = clientsRes.data.data ?? clientsRes.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const filteredClients = computed(() => {
    const query = clientSearch.value.toLowerCase().trim();
    if (!query) return clients.value;
    return clients.value.filter((c) => c.name.toLowerCase().includes(query));
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

        await axios.post(
            `http://localhost:8000/api/trainer/add-client/${id}`,
            {},
            { headers: { Authorization: `Bearer ${token}` } },
        );

        const addedUser = users.value.find((u) => u.id === id);
        if (addedUser) clients.value.push({ ...addedUser, status: "pending" });

        showAddModal.value = false;
        modalSearch.value = "";
    } catch (err) {
        console.error(err);
    }
};

const openUser = (user) => {
    if (user.status === "accepted") {
        router.push(`/trainer/client/${user.id}`);
    }
};

const openModal = () => {
    modalSearch.value = "";
    showAddModal.value = true;
};
</script>

<template>
    <div class="h-screen bg-[#080808] text-white flex">
        <aside
            class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full"
        >
            <div class="flex items-center gap-3 mb-12">
                <img src="/logo.png" class="h-8" />
                <span class="font-black text-lg tracking-widest uppercase"
                    >Myscle</span
                >
            </div>
            <nav class="space-y-1 flex-1">
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm"
                >
                    <i class="fas fa-users w-4"></i> Clients
                </div>
                <div
                    @click="router.push('/trainer/messages')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-comment w-4"></i> Messages
                </div>
                <div
                    @click="router.push('/trainer/profile')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-user w-4"></i> Profile
                </div>
            </nav>
            <button
                @click="openModal"
                class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2"
            >
                <i class="fas fa-plus text-xs"></i> Add Client
            </button>
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p
                            class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                        >
                            Trainer
                        </p>
                        <h1 class="text-3xl font-black">Clients</h1>
                    </div>
                    <button
                        @click="openModal"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#000000] text-[#6bc947] rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                    >
                        <i class="fas fa-plus text-xs"></i> Add Client
                    </button>
                </div>
                <input
                    v-model="clientSearch"
                    placeholder="Search clients..."
                    class="w-full bg-black/20 rounded-2xl px-4 py-3 outline-none placeholder-black/50 text-black text-sm"
                />
            </div>

            <div class="hidden md:block bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1">Trainer</p>
                        <h1 class="text-3xl font-bold">Clients</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <input
                            v-model="clientSearch"
                            placeholder="Search clients..."
                            class="bg-[#111] border border-white/5 rounded-2xl px-4 py-2.5 outline-none focus:border-[#7ED957] text-sm w-64 transition-all"
                        />
                        <button
                            @click="openModal"
                            class="flex items-center gap-2 px-5 py-2.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                        >
                            <i class="fas fa-plus text-xs"></i> Add Client
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-8 pt-6 pb-32 md:pb-10 space-y-3"
            >
                <div v-if="loading" class="text-center text-gray-600 mt-20">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading
                    clients...
                </div>

                <div
                    v-else-if="clients.length === 0"
                    class="flex flex-col items-center justify-center text-gray-600 mt-20"
                >
                    <i class="fas fa-users text-4xl mb-4 opacity-20"></i>
                    <p class="text-lg font-semibold">No clients yet</p>
                    <p class="text-sm mt-1">
                        Tap "+ Add" to add your first client
                    </p>
                </div>

                <div
                    v-else-if="filteredClients.length === 0"
                    class="text-center text-gray-600 mt-20"
                >
                    <p>No clients match "{{ clientSearch }}"</p>
                </div>

                <template v-else>
                    <div
                        v-for="user in filteredClients"
                        :key="user.id"
                        @click="openUser(user)"
                        class="flex items-center gap-4 bg-[#111] border border-white/5 rounded-2xl p-4 hover:border-white/10 transition-all group"
                        :class="
                            user.status === 'accepted'
                                ? 'cursor-pointer'
                                : 'opacity-60'
                        "
                    >
                        <img
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-12 h-12 rounded-full object-cover shrink-0 ring-2 ring-white/5 group-hover:ring-[#7ED957]/20 transition-all"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm truncate">
                                {{ user.name }}
                            </p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">
                                {{ user.email }}
                            </p>
                        </div>
                        <span
                            class="text-xs font-bold shrink-0 px-3 py-1.5 rounded-xl"
                            :class="
                                user.status === 'accepted'
                                    ? 'bg-[#7ED957]/10 text-[#7ED957]'
                                    : 'bg-yellow-500/10 text-yellow-500'
                            "
                        >
                            {{
                                user.status === "accepted"
                                    ? "Client"
                                    : "Pending"
                            }}
                        </span>
                    </div>
                </template>
            </div>

            <div
                v-if="showAddModal"
                class="fixed inset-0 bg-[#080808] z-50 flex flex-col md:bg-black/70 md:items-center md:justify-center"
            >
                <div
                    class="md:bg-[#111] md:border md:border-white/10 md:rounded-3xl md:w-full md:max-w-lg flex flex-col h-full md:h-auto md:max-h-[80vh]"
                >
                    <div class="p-6 pb-4 shrink-0">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-2xl font-black">Add Client</h2>
                            <button
                                @click="showAddModal = false"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 text-xl transition-all"
                            >
                                ×
                            </button>
                        </div>
                        <input
                            v-model="modalSearch"
                            placeholder="Search name or surname..."
                            class="w-full bg-[#0a0a0a] border border-white/5 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957] text-sm placeholder-gray-600 transition-all"
                            autofocus
                        />
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 pb-8 space-y-3">
                        <div
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="flex items-center gap-4 bg-[#0a0a0a] border border-white/5 rounded-2xl p-4"
                        >
                            <img
                                :src="getImage(user.profile_photo, user.name)"
                                class="w-10 h-10 rounded-full object-cover shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-sm truncate">
                                    {{ user.name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ user.email }}
                                </p>
                            </div>
                            <button
                                @click="addClient(user.id)"
                                class="bg-[#7ED957] text-black px-4 py-2 rounded-xl font-bold text-sm shrink-0 hover:bg-[#6bc947] transition-all"
                            >
                                Add
                            </button>
                        </div>
                        <div
                            v-if="modalSearch && filteredUsers.length === 0"
                            class="text-center text-gray-600 mt-10 text-sm"
                        >
                            No users match "{{ modalSearch }}"
                        </div>
                        <div
                            v-if="!modalSearch"
                            class="text-center text-gray-700 mt-10 text-sm"
                        >
                            Start typing to search users
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
