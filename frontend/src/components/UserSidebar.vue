<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();
const showBack = computed(() => route.path.startsWith("/workout/"));
const isActive = (path) =>
    route.path === path || route.path.startsWith(path + "/");

const role = localStorage.getItem("role");

const switchMode = () => {
    if (route.path.startsWith("/trainer")) {
        router.push("/dashboard");
    } else {
        router.push("/trainer");
    }
};

const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    router.push("/");
};
</script>

<template>
    <aside
        class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full z-40"
    >
        <div class="flex items-center gap-3 mb-12">
            <img src="/logo.png" class="h-8" />
            <span class="font-black text-lg tracking-widest uppercase"
                >Myscle</span
            >
        </div>
        <div
            v-if="showBack"
            @click="router.back()"
            class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
        >
            <i class="fas fa-arrow-left w-4"></i> Back
        </div>
        <nav class="space-y-1 flex-1">
            <div
                @click="router.push('/dashboard')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    isActive('/dashboard')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i class="fas fa-dumbbell w-4"></i> Workouts
            </div>
            <div
                @click="router.push('/statistics')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    isActive('/statistics')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i class="fas fa-chart-line w-4"></i> Statistics
            </div>
            <div
                @click="router.push('/friends')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    isActive('/friends')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i class="fas fa-users w-4"></i> Friends
            </div>
            <div
                @click="router.push('/messages')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    isActive('/messages')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i class="fas fa-comment w-4"></i> Messages
            </div>
            <div
                @click="router.push('/profile')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                :class="
                    isActive('/profile')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
            >
                <i class="fas fa-user w-4"></i> Profile
            </div>
        </nav>

        <div class="space-y-2 pt-4">
            <div
                v-if="role === 'trainer'"
                @click="switchMode"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] font-semibold text-sm cursor-pointer hover:bg-[#7ED957]/20 transition-all"
            >
                <i class="fas fa-repeat w-4"></i>
                {{
                    route.path.startsWith("/trainer")
                        ? "Switch to Athlete"
                        : "Switch to Trainer"
                }}
            </div>
            <div
                v-else
                @click="router.push('/upgrade')"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-[#7ED957] text-black font-bold text-sm cursor-pointer hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
            >
                <i class="fas fa-crown w-4"></i> Upgrade to Trainer
            </div>
            <div class="border-t border-white/5 pt-2">
                <div
                    @click="logout"
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl text-red-400 hover:text-white hover:bg-red-500/10 font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                </div>
            </div>
        </div>
    </aside>
</template>
