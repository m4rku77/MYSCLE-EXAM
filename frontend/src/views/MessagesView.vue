<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const chats = ref([]);
const loading = ref(true);
const activeTab = ref("chats");
const me = ref(null);

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

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");

        const meRes = await axios.get("http://localhost:8000/api/user", {
            headers: { Authorization: `Bearer ${token}` },
        });
        me.value = meRes.data;

        const res = await axios.get("http://localhost:8000/api/friends", {
            headers: { Authorization: `Bearer ${token}` },
        });

        const friends = res.data;

        const chatData = await Promise.all(
            friends.map(async (user) => {
                try {
                    const msgRes = await axios.get(
                        `http://localhost:8000/api/messages/${user.id}`,
                        {
                            headers: { Authorization: `Bearer ${token}` },
                        },
                    );

                    const messages = msgRes.data.data ?? msgRes.data;
                    const last = messages[messages.length - 1];

                    return {
                        ...user,
                        last_message: last?.message || null,
                        time: last?.created_at || null,
                        sender_id: last?.sender_id || null,
                    };
                } catch {
                    return {
                        ...user,
                        last_message: null,
                        time: null,
                        sender_id: null,
                    };
                }
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

const goToChat = (id) => {
    router.push(`/messages/${id}`);
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
                    <img
                        :src="getImage(chat.profile_photo, chat.name)"
                        class="w-12 h-12 rounded-full object-cover"
                    />

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">
                            {{ chat.name }}
                        </p>

                        <p
                            class="text-sm truncate"
                            :class="
                                chat.last_message
                                    ? 'text-gray-400'
                                    : 'text-[#7ED957]'
                            "
                        >
                            <span v-if="chat.last_message">
                                {{ chat.sender_id === me?.id ? "You: " : "" }}
                            </span>

                            {{
                                chat.last_message || "Tap to start chatting 💬"
                            }}
                        </p>
                    </div>

                    <span v-if="chat.time" class="text-xs text-gray-500">
                        {{ formatTime(chat.time) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
