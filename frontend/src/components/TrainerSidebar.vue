<script setup>
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    router.push("/login");
};

const isActive = (path) => route.path === path;
</script>

<template>
    <aside
        class="hidden md:flex fixed left-0 top-0 z-50 h-full w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8"
    >
        <div class="flex items-center gap-3 mb-12">
            <img src="/logo.png" class="h-8" />
            <span class="font-black text-lg tracking-widest uppercase">
                Myscle
            </span>
        </div>

        <nav class="space-y-1 flex-1">
            <div
                @click="router.push('/trainer')"
                :class="
                    route.path === '/trainer'
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
            >
                <i class="fas fa-users w-4"></i>
                Clients
            </div>

            <div
                @click="router.push('/trainer/messages')"
                :class="
                    route.path.startsWith('/trainer/messages')
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
            >
                <i class="fas fa-comment w-4"></i>
                Messages
            </div>

            <div
                @click="router.push('/trainer/profile')"
                :class="
                    route.path === '/trainer/profile'
                        ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957]'
                        : 'text-gray-500 hover:text-white hover:bg-white/5'
                "
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
            >
                <i class="fas fa-user w-4"></i>
                Profile
            </div>
        </nav>

        <button
            @click="logout"
            class="w-full py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl font-semibold text-sm hover:bg-red-500/20 transition-all"
        >
            <i class="fas fa-sign-out-alt mr-2"></i>
            Logout
        </button>
    </aside>
</template>
