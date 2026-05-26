<script setup>
import { useRouter } from "vue-router";
const router = useRouter();
</script>

<template>
    <div
        class="min-h-screen bg-[#080808] text-white flex items-center justify-center text-center px-5"
    >
        <div>
            <i class="fas fa-times-circle text-red-400 text-6xl mb-6"></i>
            <h1 class="text-3xl font-bold mb-2">Payment Cancelled</h1>
            <p class="text-gray-400 mb-8">
                No charges were made. You can try again anytime.
            </p>
            <div class="flex gap-3 justify-center">
                <button
                    @click="router.push('/upgrade')"
                    class="px-6 py-3 bg-[#7ED957] text-black rounded-2xl font-semibold hover:bg-[#6bc947] transition-all"
                >
                    Try Again
                </button>
                <button
                    @click="router.push('/')"
                    class="px-6 py-3 bg-white/5 border border-white/10 text-white rounded-2xl font-semibold hover:bg-white/10 transition-all"
                >
                    Go Home
                </button>
            </div>
        </div>
    </div>
</template>
