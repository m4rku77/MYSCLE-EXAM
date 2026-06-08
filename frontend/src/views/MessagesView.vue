<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
const role = localStorage.getItem("role");

const router = useRouter();
const chats = ref([]);
const loading = ref(true);
const activeTab = ref("chats");
const me = ref(null);

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `https://myscle-exam-production.up.railway.app/storage/${path.replace("storage/", "")}`;
};

const formatTime = (date) => {
    if (!date) return "";
    const d = new Date(date);
    return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

const openChat = (id) => {
    if (role === "trainer") {
        router.push(`/trainer/messages/${id}`);
    } else if (role === "admin") {
        router.push(`/admin/messages/${id}`);
    } else {
        router.push(`/messages/${id}`);
    }
};

const getLastMessage = async (userId) => {
    try {
        const res = await axios.get(
            `https://myscle-exam-production.up.railway.app/api/messages/${userId}/last`,
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
        const meRes = await axios.get("https://myscle-exam-production.up.railway.app/api/user", {
            headers,
        });
        me.value = meRes.data;

        let contacts = [];

        if (me.value.role === "trainer") {
            const clientsRes = await axios.get(
                "https://myscle-exam-production.up.railway.app/api/trainer/clients",
                { headers },
            );
            const raw = clientsRes.data.data ?? clientsRes.data;
            contacts = raw.map((c) => ({
                id: c.id ?? c.client_id ?? c.user_id,
                name: c.name,
                profile_photo: c.profile_photo,
            }));
        } else {
            const friendsRes = await axios.get(
                "https://myscle-exam-production.up.railway.app/api/friends",
                { headers },
            );
            contacts = friendsRes.data;
            try {
                const trainerRes = await axios.get(
                    "https://myscle-exam-production.up.railway.app/api/my/trainer",
                    { headers },
                );
                const trainerData = trainerRes.data?.data ?? trainerRes.data;
                if (trainerData && trainerData.id) {
                    const alreadyIn = contacts.some(
                        (c) => c.id === trainerData.id,
                    );
                    if (!alreadyIn)
                        contacts.push({ ...trainerData, isTrainer: true });
                }
            } catch (e) {}
        }

        const validContacts = contacts.filter((c) => c.id);

        const chatData = await Promise.all(
            validContacts.map(async (user) => {
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

const isUnread = (chat) =>
    chat.last_message && chat.sender_id !== me.value?.id && !chat.read_at;

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
    <div class="h-screen bg-[#080808] text-white flex">
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0"
            >
                <p
                    class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                >
                    Inbox
                </p>
                <h1 class="text-3xl font-black">Messages</h1>
            </div>

            <div
                class="hidden md:block bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0"
            >
                <p
                    class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
                >
                    Inbox
                </p>
                <h1 class="text-3xl font-bold">Messages</h1>
            </div>

            <div class="px-5 md:px-8 pt-4 shrink-0 max-w-2xl mx-auto w-full">
                <div
                    class="flex bg-[#111] border border-white/5 rounded-2xl p-1 w-full md:w-fit md:min-w-64 md:mx-auto"
                >
                    <button
                        @click="activeTab = 'chats'"
                        class="flex-1 py-2.5 text-sm rounded-xl transition font-semibold"
                        :class="
                            activeTab === 'chats'
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-500 hover:text-white'
                        "
                    >
                        Chats
                    </button>
                    <button
                        @click="activeTab = 'new'"
                        class="flex-1 py-2.5 text-sm rounded-xl transition font-semibold"
                        :class="
                            activeTab === 'new'
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-500 hover:text-white'
                        "
                    >
                        New
                    </button>
                </div>
            </div>

           <div class="flex-1 overflow-y-auto pt-4 pb-32 md:pb-8">
            <div class="max-w-2xl mx-auto px-5 md:px-8 space-y-2">
                <div v-if="loading" class="text-gray-600 text-center py-10">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading chats...
                </div>

                <div
                    v-else-if="
                        (activeTab === 'chats' && activeChats.length === 0) ||
                        (activeTab === 'new' && newChats.length === 0)
                    "
                    class="text-center text-gray-600 py-10"
                >
                    <i class="fas fa-comment text-3xl mb-3 opacity-20"></i>
                    <p>
                        No {{ activeTab === "chats" ? "chats" : "users" }} yet
                    </p>
                </div>

                <div
                    v-else
                    v-for="chat in activeTab === 'chats'
                        ? activeChats
                        : newChats"
                    :key="chat.id"
                    @click="chat.id && goToChat(chat.id)"
                    class="bg-[#111] border border-white/5 rounded-2xl p-4 flex items-center gap-4 cursor-pointer hover:border-white/10 transition-all"
                >
                    <div class="relative shrink-0">
                        <img
                            :src="getImage(chat.profile_photo, chat.name)"
                            class="w-12 h-12 rounded-full object-cover ring-2 ring-white/5"
                        />
                        <span
                            v-if="isUnread(chat)"
                            class="absolute top-0 right-0 w-3 h-3 bg-[#7ED957] rounded-full border-2 border-[#111]"
                        ></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p
                                class="font-bold text-sm truncate"
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
                                class="text-xs bg-[#7ED957]/20 text-[#7ED957] px-2 py-0.5 rounded-full shrink-0 font-semibold"
                                >Trainer</span
                            >
                        </div>
                        <p
                            class="text-xs truncate"
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
                                >You:
                            </span>
                            {{
                                chat.last_message || "Tap to start chatting 💬"
                            }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span v-if="chat.time" class="text-xs text-gray-600">{{
                            formatTime(chat.time)
                        }}</span>
                        <div
                            v-if="isUnread(chat)"
                            class="w-2 h-2 rounded-full bg-[#7ED957]"
                        ></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>
