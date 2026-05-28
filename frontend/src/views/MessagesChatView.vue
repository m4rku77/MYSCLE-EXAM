<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import UserSidebar from "../components/UserSidebar.vue";
import TrainerSidebar from "../components/TrainerSidebar.vue";

const route = useRoute();
const router = useRouter();

const userId = route.params.id;
const messages = ref([]);
const newMessage = ref("");
const user = ref(null);
const loading = ref(true);

const messagesContainer = ref(null);
const me = ref(null);
const role = localStorage.getItem("role");

let interval = null;

const isTrainerRoute = computed(() => route.path.startsWith("/trainer"));

const goToProfile = () => {
    router.push(`/user/${userId}`);
};

const logout = () => {
    localStorage.removeItem("token");
    window.location.href = "/login";
};

const switchMode = () => {
    if (isTrainerRoute.value) {
        router.push("/dashboard");
    } else {
        router.push("/trainer");
    }
};

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1a1a1a&color=7ED957`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

const formatTime = (date) => {
    const d = new Date(date);
    return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

const formatDate = (date) => {
    const d = new Date(date);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);
    if (d.toDateString() === today.toDateString()) return "Today";
    if (d.toDateString() === yesterday.toDateString()) return "Yesterday";
    return d.toLocaleDateString("en-US", { month: "short", day: "numeric" });
};

const groupedMessages = computed(() => {
    const groups = [];
    let lastDate = null;
    messages.value.forEach((msg) => {
        const msgDate = new Date(msg.created_at).toDateString();
        if (msgDate !== lastDate) {
            groups.push({ type: "date", label: formatDate(msg.created_at) });
            lastDate = msgDate;
        }
        groups.push({ type: "message", ...msg });
    });
    return groups;
});

const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop =
            messagesContainer.value.scrollHeight;
    }
};

const loadMessages = async () => {
    try {
        const token = localStorage.getItem("token");
        const res = await axios.get(
            `http://localhost:8000/api/messages/${userId}`,
            {
                headers: { Authorization: `Bearer ${token}` },
            },
        );
        const data = res.data.data ?? res.data;
        messages.value = data.map((m) => ({
            id: m.id,
            text: m.message,
            me: m.sender_id === me.value.id,
            created_at: m.created_at,
        }));
        scrollToBottom();
    } catch (err) {
        console.log(err.response?.data);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    try {
        const token = localStorage.getItem("token");
        const res = await axios.post(
            "http://localhost:8000/api/messages",
            { receiver_id: userId, message: newMessage.value },
            { headers: { Authorization: `Bearer ${token}` } },
        );
        const msg = res.data.data ?? res.data;
        messages.value.push({
            id: msg.id,
            text: msg.message,
            me: true,
            created_at: msg.created_at,
        });
        newMessage.value = "";
        scrollToBottom();
    } catch (err) {
        console.error(err);
    }
};

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");
        const meRes = await axios.get("http://localhost:8000/api/user", {
            headers: { Authorization: `Bearer ${token}` },
        });
        me.value = meRes.data;
        const res = await axios.get(
            `http://localhost:8000/api/users/${userId}`,
            { headers: { Authorization: `Bearer ${token}` } },
        );
        user.value = res.data;
        await loadMessages();
        interval = setInterval(loadMessages, 3000);
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>

<template>
    <div class="h-[100dvh] bg-[#080808] text-white flex">
        <!-- Desktop Sidebar (user) -->
        <UserSidebar v-if="!isTrainerRoute" />

        <!-- Desktop Sidebar (trainer) -->
        <TrainerSidebar v-if="isTrainerRoute" />

        <!-- Main area -->
        <div class="flex-1 md:ml-64 flex flex-col min-h-0 md:p-6">
            <!-- Mobile: full screen chat -->
            <div class="md:hidden flex flex-col h-full">
                <div
                    class="flex items-center gap-3 px-4 py-4 border-b border-white/5 bg-[#0f0f0f] shrink-0"
                >
                    <button
                        @click="router.back()"
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 text-[#7ED957] text-lg"
                    >
                        ←
                    </button>
                    <div
                        class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition flex-1 min-w-0"
                        @click="goToProfile"
                    >
                        <img
                            v-if="user"
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-10 h-10 rounded-full object-cover shrink-0"
                        />
                        <div class="min-w-0">
                            <p class="font-bold text-sm truncate">
                                {{ user?.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Tap to view profile
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    ref="messagesContainer"
                    class="flex-1 overflow-y-auto px-4 py-4 space-y-1"
                >
                    <div
                        v-if="loading"
                        class="flex items-center justify-center h-full text-gray-600"
                    >
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                    </div>
                    <template
                        v-for="item in groupedMessages"
                        :key="item.id ?? item.label"
                    >
                        <div
                            v-if="item.type === 'date'"
                            class="flex items-center gap-3 py-3"
                        >
                            <div class="flex-1 h-px bg-white/5"></div>
                            <span class="text-xs text-gray-600 font-medium">{{
                                item.label
                            }}</span>
                            <div class="flex-1 h-px bg-white/5"></div>
                        </div>
                        <div
                            v-else
                            class="flex"
                            :class="item.me ? 'justify-end' : 'justify-start'"
                        >
                            <div class="max-w-[75%] flex flex-col gap-[2px]">
                                <div
                                    class="px-4 py-2.5 text-sm rounded-2xl leading-relaxed"
                                    :class="
                                        item.me
                                            ? 'bg-[#7ED957] text-black rounded-br-sm'
                                            : 'bg-[#1a1a1a] border border-white/5 text-white rounded-bl-sm'
                                    "
                                >
                                    {{ item.text }}
                                </div>
                                <span
                                    class="text-[10px] text-gray-600 px-1"
                                    :class="
                                        item.me ? 'text-right' : 'text-left'
                                    "
                                >
                                    {{ formatTime(item.created_at) }}
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
                <div
                    class="px-4 py-3 pb-5 border-t border-white/5 bg-[#0f0f0f] shrink-0 flex gap-3"
                >
                    <input
                        v-model="newMessage"
                        @keyup.enter="sendMessage"
                        placeholder="Message..."
                        class="flex-1 px-4 py-3 bg-[#111] border border-white/5 rounded-2xl outline-none focus:border-[#7ED957] text-sm transition-all"
                    />
                    <button
                        @click="sendMessage"
                        class="w-11 h-11 shrink-0 flex items-center justify-center bg-[#7ED957] text-black rounded-xl font-bold hover:bg-[#6bc947] active:scale-95 transition-all"
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Desktop: contained box -->
            <div
                class="hidden md:flex flex-col h-full w-full bg-[#0f0f0f] border border-white/5 rounded-3xl overflow-hidden"
            >
                <!-- Header -->
                <div
                    class="flex items-center gap-3 px-6 py-4 border-b border-white/5 shrink-0"
                >
                    <button
                        @click="router.back()"
                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-[#7ED957] transition-all text-lg"
                    >
                        ←
                    </button>
                    <div
                        class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition flex-1 min-w-0"
                        @click="goToProfile"
                    >
                        <img
                            v-if="user"
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-white/10"
                        />
                        <div class="min-w-0">
                            <p class="font-bold text-sm truncate">
                                {{ user?.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Click to view profile
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div
                    ref="messagesContainer"
                    class="flex-1 overflow-y-auto px-6 py-4 space-y-1"
                >
                    <div
                        v-if="loading"
                        class="flex items-center justify-center h-full text-gray-600"
                    >
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                    </div>
                    <template
                        v-for="item in groupedMessages"
                        :key="item.id ?? item.label"
                    >
                        <div
                            v-if="item.type === 'date'"
                            class="flex items-center gap-3 py-3"
                        >
                            <div class="flex-1 h-px bg-white/5"></div>
                            <span class="text-xs text-gray-600 font-medium">{{
                                item.label
                            }}</span>
                            <div class="flex-1 h-px bg-white/5"></div>
                        </div>
                        <div
                            v-else
                            class="flex"
                            :class="item.me ? 'justify-end' : 'justify-start'"
                        >
                            <div class="max-w-[60%] flex flex-col gap-[2px]">
                                <div
                                    class="px-4 py-2.5 text-sm rounded-2xl leading-relaxed"
                                    :class="
                                        item.me
                                            ? 'bg-[#7ED957] text-black rounded-br-sm'
                                            : 'bg-[#111] border border-white/5 text-white rounded-bl-sm'
                                    "
                                >
                                    {{ item.text }}
                                </div>
                                <span
                                    class="text-[10px] text-gray-600 px-1"
                                    :class="
                                        item.me ? 'text-right' : 'text-left'
                                    "
                                >
                                    {{ formatTime(item.created_at) }}
                                </span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input -->
                <div
                    class="px-6 py-4 border-t border-white/5 shrink-0 flex gap-3"
                >
                    <input
                        v-model="newMessage"
                        @keyup.enter="sendMessage"
                        placeholder="Message..."
                        class="flex-1 px-4 py-3 bg-[#111] border border-white/5 rounded-2xl outline-none focus:border-[#7ED957] text-sm transition-all"
                    />
                    <button
                        @click="sendMessage"
                        class="w-11 h-11 shrink-0 flex items-center justify-center bg-[#7ED957] text-black rounded-xl font-bold hover:bg-[#6bc947] active:scale-95 transition-all"
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
