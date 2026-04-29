<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const goUser = () => {
    router.push("/dashboard");
};

const goTrainer = () => {
    router.push("/trainer");
};
</script>

<template>
    <div
        class="h-screen flex items-center justify-center bg-[#0f0f0f] text-white px-4"
    >
        <div class="w-full max-w-sm space-y-6 text-center">
            <h1 class="text-2xl font-semibold">Continue as</h1>

            <button
                @click="goUser"
                class="w-full py-5 rounded-2xl bg-[#1f1f1f] border border-gray-700 hover:bg-[#2a2a2a] transition text-lg font-semibold"
            >
                User
            </button>

            <button
                @click="goTrainer"
                class="w-full py-5 rounded-2xl bg-[#7ED957] text-black font-semibold hover:scale-105 transition"
            >
                Trainer
            </button>
        </div>
    </div>
</template>
