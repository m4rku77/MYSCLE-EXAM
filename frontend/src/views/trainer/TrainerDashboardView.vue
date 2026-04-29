<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import TrainerBottomNav from "../../components/TrainerBottomNav.vue";

const router = useRouter();

const users = ref([]);
const loading = ref(true);

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

        const res = await axios.get("http://localhost:8000/api/users", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        const data = res.data.data ?? res.data;

        users.value = data;
    } catch (err) {
        console.error(err);
        users.value = [];
    } finally {
        loading.value = false;
    }
});

const openUser = (user) => {
    router.push(`/trainer/user/${user.id}`);
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

        <div class="flex-1 flex flex-col relative">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black p-6 pb-8 rounded-b-3xl"
            >
                <h1 class="text-4xl font-bold mb-4">Clients</h1>

                <input
                    placeholder="Search users"
                    class="w-full bg-black/20 rounded-xl px-4 py-3 outline-none placeholder-black/50"
                />
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-10 pt-6 pb-28 md:pb-10 space-y-10 max-w-4xl mx-auto w-full"
            >
                <div v-if="users.length > 0" class="space-y-3">
                    <div
                        v-for="user in users"
                        :key="user.id"
                        @click="openUser(user)"
                        class="flex items-center gap-4 border border-white/5 bg-white/5 backdrop-blur-xl rounded-2xl p-4 cursor-pointer transition-all duration-300 hover:bg-white/10 hover:scale-[1.02]"
                    >
                        <img
                            :src="getImage(user.profile_photo, user.name)"
                            class="w-12 h-12 rounded-full object-cover"
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
                            class="text-gray-500 text-lg group-hover:text-[#7ED957]"
                        >
                            →
                        </span>
                    </div>
                </div>

                <div
                    v-else-if="!loading"
                    class="flex flex-col items-center justify-center text-gray-500 mt-20"
                >
                    <p>No users found</p>
                </div>

                <div v-if="loading" class="text-center text-gray-400">
                    Loading users...
                </div>
            </div>

            <TrainerBottomNav class="fixed bottom-0 left-0 w-full z-40" />
        </div>
    </div>
</template>
