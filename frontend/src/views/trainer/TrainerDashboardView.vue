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
    <div class="h-[100dvh] bg-[#0f0f0f] text-white flex flex-col">
        <header
            class="hidden md:flex h-16 bg-[#151515] border-b border-gray-800 items-center justify-between px-6"
        >
            <div class="flex items-center gap-3">
                <img src="/logo.png" class="h-10" />
                <span class="font-semibold text-lg">MYSCLE Trainer</span>
            </div>
        </header>

        <div class="flex-1 flex flex-col relative overflow-hidden">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black p-6 pb-8 rounded-b-3xl"
            >
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-4xl font-bold">Clients</h1>

                    <button
                        @click="openModal"
                        class="bg-black text-white px-4 py-2 rounded-xl font-semibold"
                    >
                        + Add
                    </button>
                </div>

                <input
                    v-model="clientSearch"
                    placeholder="Search clients..."
                    class="w-full bg-black/20 rounded-xl px-4 py-3 outline-none placeholder-black/50 text-black"
                />
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-10 pt-6 pb-28 md:pb-10 space-y-3 max-w-4xl mx-auto w-full"
            >
                <div v-if="loading" class="text-center text-gray-400 mt-20">
                    Loading clients...
                </div>

                <div
                    v-else-if="clients.length === 0"
                    class="flex flex-col items-center justify-center text-gray-500 mt-20"
                >
                    <p class="text-lg">No clients yet</p>
                    <p class="text-sm mt-1">
                        Tap "+ Add" to add your first client
                    </p>
                </div>

                <div
                    v-else-if="filteredClients.length === 0"
                    class="flex flex-col items-center justify-center text-gray-500 mt-20"
                >
                    <p>No clients match "{{ clientSearch }}"</p>
                </div>

                <template v-else>
                    <div
                        v-for="user in filteredClients"
                        :key="user.id"
                        @click="openUser(user)"
                        class="flex items-center gap-4 border border-white/5 bg-white/5 rounded-2xl p-4 transition-transform"
                        :class="
                            user.status === 'accepted'
                                ? 'cursor-pointer active:scale-[0.98]'
                                : 'opacity-70'
                        "
                    >
                        <img
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                        />

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">
                                {{ user.name }}
                            </p>
                            <p class="text-sm text-gray-400 truncate">
                                {{ user.email }}
                            </p>
                        </div>

                        <span
                            class="text-sm flex-shrink-0 font-semibold"
                            :class="
                                user.status === 'accepted'
                                    ? 'text-[#7ED957]'
                                    : 'text-yellow-500'
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
                class="fixed inset-0 bg-[#0f0f0f] z-50 flex flex-col"
            >
                <div class="p-6 pb-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-4xl font-bold text-white">
                            Add Client
                        </h2>

                        <button
                            @click="showAddModal = false"
                            class="text-gray-400 w-9 h-9 flex items-center justify-center rounded-xl text-2xl hover:bg-white/10"
                        >
                            ×
                        </button>
                    </div>

                    <input
                        v-model="modalSearch"
                        placeholder="Search name or surname..."
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 outline-none placeholder-gray-600 text-white"
                        autofocus
                    />
                </div>

                <div class="flex-1 overflow-y-auto px-5 pt-6 pb-10 space-y-3">
                    <div
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="flex items-center gap-4 bg-white/5 rounded-2xl p-4 border border-white/5"
                    >
                        <img
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                        />

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">
                                {{ user.name }}
                            </p>
                            <p class="text-sm text-gray-400 truncate">
                                {{ user.email }}
                            </p>
                        </div>

                        <button
                            @click="addClient(user.id)"
                            class="bg-[#7ED957] text-black px-4 py-2 rounded-xl font-semibold text-sm flex-shrink-0"
                        >
                            Add
                        </button>
                    </div>

                    <div
                        v-if="modalSearch && filteredUsers.length === 0"
                        class="text-center text-gray-500 mt-20 text-sm"
                    >
                        No users match "{{ modalSearch }}"
                    </div>

                    <div
                        v-if="!modalSearch"
                        class="text-center text-gray-600 mt-20 text-sm"
                    >
                        Start typing to search users
                    </div>
                </div>
            </div>

            <TrainerBottomNav class="fixed bottom-0 left-0 w-full z-40" />
        </div>
    </div>
</template>
