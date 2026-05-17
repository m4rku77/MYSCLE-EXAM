<script setup>
import axios from "axios";
import { ref } from "vue";

const loading = ref(false);
const token = localStorage.getItem("token");

const checkout = async () => {
    loading.value = true;
    try {
        const res = await axios.post(
            "http://localhost:8000/api/stripe/checkout",
            {},
            { headers: { Authorization: `Bearer ${token}` } },
        );
        window.location.href = res.data.url;
    } catch (err) {
        console.error(err);
        loading.value = false;
    }
};
</script>

<template>
    <div
        class="min-h-screen bg-[#080808] text-white flex items-center justify-center px-5"
    >
        <div
            class="max-w-md w-full bg-[#111] border border-white/5 rounded-3xl p-8 text-center"
        >
            <p
                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-2"
            >
                One-time payment
            </p>
            <h1 class="text-4xl font-bold mb-2">Become a Trainer</h1>
            <p class="text-gray-400 mb-8">
                Unlock trainer features — manage clients, assign workouts, track
                progress.
            </p>

            <div class="bg-[#0f0f0f] rounded-2xl p-6 mb-8 text-left space-y-3">
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-check text-[#7ED957]"></i>
                    <span>Manage unlimited clients</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-check text-[#7ED957]"></i>
                    <span>Assign and track workouts</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-check text-[#7ED957]"></i>
                    <span>View client statistics & progress</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-check text-[#7ED957]"></i>
                    <span>Coach notes & messaging</span>
                </div>
            </div>

            <div class="mb-6">
                <span class="text-5xl font-bold">$29</span>
                <span class="text-gray-400">.99 one-time</span>
            </div>

            <button
                @click="checkout"
                :disabled="loading"
                class="w-full py-4 bg-[#7ED957] text-black rounded-2xl font-bold text-lg disabled:opacity-50"
            >
                {{ loading ? "Redirecting..." : "Upgrade Now" }}
            </button>

            <p class="text-xs text-gray-600 mt-4">Secure payment via Stripe</p>
        </div>
    </div>
</template>
