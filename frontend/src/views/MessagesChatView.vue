<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const userId = route.params.id;

const messages = ref([]);
const newMessage = ref("");
const user = ref(null);
const loading = ref(true);

const messagesContainer = ref(null);
const me = ref(null);

let interval = null;

const goToProfile = () => {
    router.push(`/user/${userId}`);
};
const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

const formatTime = (date) => {
    const d = new Date(date);
    return d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

// 🔽 SCROLL
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
            {
                receiver_id: userId,
                message: newMessage.value,
            },
            {
                headers: { Authorization: `Bearer ${token}` },
            },
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
    <div class="h-[100dvh] flex justify-center bg-[#0f0f0f] text-white">
        <div class="w-full max-w-2xl flex flex-col">
            <div
                class="flex items-center gap-3 cursor-pointer hover:opacity-80 active:scale-95 transition px-4 py-3 border-b border-gray-800 sticky top-0 bg-[#0f0f0f] z-10"
            >
                <button @click="router.back()" class="text-[#7ED957] text-2xl">
                    ←
                </button>

                <div
                    class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition"
                    @click="goToProfile"
                >
                    <img
                        v-if="user"
                        :src="getImage(user.profile_photo, user.name)"
                        class="w-10 h-10 rounded-full object-cover"
                    />

                    <p class="font-semibold">{{ user?.name }}</p>
                </div>
            </div>

            <div
                ref="messagesContainer"
                class="flex-1 overflow-y-auto px-3 py-2 space-y-1.5 overscroll-contain"
            >
                <div v-if="loading" class="text-center text-gray-400">
                    Loading chat...
                </div>

                <div
                    v-for="msg in messages"
                    :key="msg.id"
                    class="flex px-1"
                    :class="msg.me ? 'justify-end' : 'justify-start'"
                >
                    <div
                        class="max-w-[80%] sm:max-w-[70%] flex flex-col gap-[2px]"
                    >
                        <div
                            class="px-4 py-2 text-sm rounded-2xl"
                            :class="
                                msg.me
                                    ? 'bg-[#7ED957] text-black rounded-br-none'
                                    : 'bg-[#1f1f1f] text-white rounded-bl-none'
                            "
                        >
                            {{ msg.text }}
                        </div>

                        <span
                            class="text-[9px] text-gray-500 px-1"
                            :class="msg.me ? 'text-right' : 'text-left'"
                        >
                            {{ formatTime(msg.created_at) }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="px-3 py-2 pb-6 md:pb-4 border-t border-gray-800 flex gap-2 bg-[#0f0f0f]"
            >
                <input
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    placeholder="Message..."
                    class="flex-1 px-4 py-3 bg-[#1a1a1a] rounded-full border border-gray-700 outline-none text-base"
                />

                <button
                    @click="sendMessage"
                    class="h-[48px] w-[48px] flex items-center justify-center bg-[#7ED957] text-black rounded-full font-bold"
                >
                    ➤
                </button>
            </div>
        </div>
    </div>
</template>
