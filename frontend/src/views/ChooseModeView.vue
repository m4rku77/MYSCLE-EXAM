<script setup>
import { useRouter } from "vue-router";

const router = useRouter();
const role = localStorage.getItem("role");

const goAthlete = () => router.push("/dashboard");
const goTrainer = () => router.push("/trainer");
</script>

<template>
    <div
        class="h-screen bg-[#080808] text-white flex items-center justify-center px-6 relative overflow-hidden"
    >
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#7ED957]/5 blur-[150px] rounded-full pointer-events-none"
        ></div>

        <div class="relative z-10 w-full max-w-sm">
            <div class="text-center mb-10">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <img src="/logo.png" class="h-9" />
                    <span class="font-black text-xl tracking-widest uppercase"
                        >Myscle</span
                    >
                </div>
                <h1 class="text-3xl font-black mb-2">Continue as</h1>
                <p class="text-gray-500 text-sm">
                    How do you want to use Myscle today?
                </p>
            </div>

            <div class="space-y-3">
                <button
                    @click="goAthlete"
                    class="group w-full bg-[#111] border border-white/5 hover:border-white/20 rounded-2xl p-5 text-left transition-all hover:-translate-y-0.5"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/5 group-hover:bg-white/10 rounded-xl flex items-center justify-center shrink-0 transition-all"
                        >
                            <i class="fas fa-dumbbell text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold mb-0.5">Athlete</p>
                            <p class="text-gray-500 text-xs">
                                Track your own workouts & progress
                            </p>
                        </div>
                        <i
                            class="fas fa-chevron-right text-gray-700 group-hover:text-white transition-colors text-sm"
                        ></i>
                    </div>
                </button>

                <button
                    @click="goTrainer"
                    class="group w-full bg-[#7ED957]/10 border border-[#7ED957]/20 hover:border-[#7ED957]/40 rounded-2xl p-5 text-left transition-all hover:-translate-y-0.5"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-[#7ED957]/15 group-hover:bg-[#7ED957]/25 rounded-xl flex items-center justify-center shrink-0 transition-all"
                        >
                            <i class="fas fa-crown text-[#7ED957]"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold mb-0.5">Trainer</p>
                            <p class="text-gray-500 text-xs">
                                Manage clients & assign workouts
                            </p>
                        </div>
                        <i
                            class="fas fa-chevron-right text-[#7ED957]/40 group-hover:text-[#7ED957] transition-colors text-sm"
                        ></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>
