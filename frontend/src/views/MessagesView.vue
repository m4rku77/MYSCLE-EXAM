<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const chats = ref([]);
const loading = ref(true);
const activeTab = ref("chats");
const me = ref(null);

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

const formatTime = (date) => {
    if (!date) return "";
    const d = new Date(date);
    return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

const getLastMessage = async (userId) => {
    try {
        const res = await axios.get(
            `http://localhost:8000/api/messages/${userId}/last`,
            { headers },
        );
        if (!res.data)
            return {
                last_message: null,
                time: null,
                sender_id: null,
                read_at: null,
            };
        return {
            last_message: res.data.message,
            time: res.data.created_at,
            sender_id: res.data.sender_id,
            read_at: res.data.read_at,
        };
    } catch {
        return {
            last_message: null,
            time: null,
            sender_id: null,
            read_at: null,
        };
    }
};

onMounted(async () => {
    try {
        const meRes = await axios.get("http://localhost:8000/api/user", {
            headers,
        });
        me.value = meRes.data;

        let contacts = [];

        if (me.value.role === "trainer") {
            const clientsRes = await axios.get(
                "http://localhost:8000/api/trainer/clients",
                { headers },
            );
            contacts = clientsRes.data.data ?? clientsRes.data;
        } else {
            const friendsRes = await axios.get(
                "http://localhost:8000/api/friends",
                { headers },
            );
            contacts = friendsRes.data;

            try {
                const trainerRes = await axios.get(
                    "http://localhost:8000/api/my/trainer",
                    { headers },
                );
                if (trainerRes.data) {
                    const alreadyIn = contacts.some(
                        (c) => c.id === trainerRes.data.id,
                    );
                    if (!alreadyIn) {
                        contacts.push({ ...trainerRes.data, isTrainer: true });
                    }
                }
            } catch {
                // no trainer
            }
        }

        const chatData = await Promise.all(
            contacts.map(async (user) => {
                const msgData = await getLastMessage(user.id);
                return { ...user, ...msgData };
            }),
        );

        chats.value = chatData.sort((a, b) => {
            if (!a.time) return 1;
            if (!b.time) return -1;
            return new Date(b.time) - new Date(a.time);
        });
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const isUnread = (chat) => {
    return (
        chat.last_message && chat.sender_id !== me.value?.id && !chat.read_at
    );
};

const goToChat = (id) => {
    if (me.value?.role === "trainer") {
        router.push(`/trainer/messages/${id}`);
    } else {
        router.push(`/messages/${id}`);
    }
};

const activeChats = computed(() => chats.value.filter((c) => c.last_message));
const newChats = computed(() => chats.value.filter((c) => !c.last_message));
</script>

<template>
    <div class="h-screen bg-[#0f0f0f] text-white flex flex-col">
        <div class="p-6 border-b border-gray-800 space-y-4">
            <h1 class="text-xl font-semibold">Messages</h1>

            <div class="flex bg-[#1a1a1a] rounded-xl p-1">
                <button
                    @click="activeTab = 'chats'"
                    class="flex-1 py-2 text-sm rounded-lg transition"
                    :class="
                        activeTab === 'chats'
                            ? 'bg-[#7ED957] text-black font-semibold'
                            : 'text-gray-400'
                    "
                >
                    Chats
                </button>

                <button
                    @click="activeTab = 'new'"
                    class="flex-1 py-2 text-sm rounded-lg transition"
                    :class="
                        activeTab === 'new'
                            ? 'bg-[#7ED957] text-black font-semibold'
                            : 'text-gray-400'
                    "
                >
                    New
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div v-if="loading" class="p-6 text-gray-400">Loading chats...</div>

            <div
                v-else-if="
                    (activeTab === 'chats' && activeChats.length === 0) ||
                    (activeTab === 'new' && newChats.length === 0)
                "
                class="p-6 text-gray-500 text-center"
            >
                No {{ activeTab === "chats" ? "chats" : "users" }} yet
            </div>

            <div v-else class="divide-y divide-gray-800">
                <div
                    v-for="chat in activeTab === 'chats'
                        ? activeChats
                        : newChats"
                    :key="chat.id"
                    @click="goToChat(chat.id)"
                    class="flex items-center gap-4 p-4 hover:bg-[#1a1a1a] cursor-pointer transition"
                >
                    <div class="relative shrink-0">
                        <img
                            :src="getImage(chat.profile_photo, chat.name)"
                            class="w-12 h-12 rounded-full object-cover"
                        />
                        <span
                            v-if="isUnread(chat)"
                            class="absolute top-0 right-0 w-3 h-3 bg-[#7ED957] rounded-full border-2 border-[#0f0f0f]"
                        ></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p
                                class="font-semibold truncate"
                                :class="
                                    isUnread(chat)
                                        ? 'text-white'
                                        : 'text-gray-200'
                                "
                            >
                                {{ chat.name }}
                            </p>
                            <span
                                v-if="chat.isTrainer"
                                class="text-xs bg-[#7ED957]/20 text-[#7ED957] px-2 py-0.5 rounded-full shrink-0"
                            >
                                Trainer
                            </span>
                        </div>

                        <p
                            class="text-sm truncate"
                            :class="
                                isUnread(chat)
                                    ? 'text-white font-medium'
                                    : 'text-gray-500'
                            "
                        >
                            <span
                                v-if="
                                    chat.last_message &&
                                    chat.sender_id === me?.id
                                "
                            >
                                You:&nbsp;
                            </span>
                            {{
                                chat.last_message || "Tap to start chatting 💬"
                            }}
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span v-if="chat.time" class="text-xs text-gray-500">
                            {{ formatTime(chat.time) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
