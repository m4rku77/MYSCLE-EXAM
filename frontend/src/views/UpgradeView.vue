<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const loading = ref(true);
const checkoutLoading = ref(false);
const token = localStorage.getItem("token");
const hadTrial = ref(false);

onMounted(async () => {
    try {
        const res = await axios.get(
            "https://myscle-exam-production.up.railway.app/api/my/subscription",
            {
                headers: { Authorization: `Bearer ${token}` },
            },
        );
        hadTrial.value = !!res.data;
    } catch {
        hadTrial.value = false;
    } finally {
        loading.value = false;
    }
});

const checkout = async () => {
    checkoutLoading.value = true;
    try {
        const res = await axios.post(
            "https://myscle-exam-production.up.railway.app/api/stripe/checkout",
            {},
            { headers: { Authorization: `Bearer ${token}` } },
        );
        window.location.href = res.data.url;
    } catch (err) {
        console.error(err);
        checkoutLoading.value = false;
    }
};
</script>

<template>
    <button
        @click="router.back()"
        class="absolute top-6 left-6 flex items-center gap-2 text-gray-500 hover:text-white text-sm font-semibold transition-all"
    >
        <i class="fas fa-arrow-left text-xs"></i> Back
    </button>
    <div
        class="min-h-screen bg-[#080808] text-white flex items-center justify-center px-5 rleative"
    >
        <div
            class="max-w-md w-full bg-[#111] border border-white/5 rounded-3xl p-8 text-center"
        >
            <div v-if="loading" class="py-10 text-gray-600">
                <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
            </div>

            <template v-else>
                <div
                    class="w-14 h-14 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center mx-auto mb-5"
                >
                    <i class="fas fa-crown text-[#7ED957] text-xl"></i>
                </div>

                <p
                    class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-2"
                >
                    {{ hadTrial ? "Monthly Subscription" : "Start Free Trial" }}
                </p>
                <h1 class="text-4xl font-bold mb-2">Become a Trainer</h1>
                <p class="text-gray-400 mb-8">
                    Unlock trainer features — manage clients, assign workouts,
                    track progress.
                </p>

                <div
                    class="bg-[#0f0f0f] rounded-2xl p-6 mb-8 text-left space-y-3"
                >
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check text-[#7ED957]"></i
                        ><span>Manage unlimited clients</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check text-[#7ED957]"></i
                        ><span>Assign and track workouts</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check text-[#7ED957]"></i
                        ><span>View client statistics & progress</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <i class="fas fa-check text-[#7ED957]"></i
                        ><span>Coach notes & messaging</span>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-5xl font-bold">$29</span>
                        <span class="text-gray-400 text-lg"
                            >.99<span class="text-sm">/mo</span></span
                        >
                    </div>
                    <div
                        v-if="!hadTrial"
                        class="mt-3 flex items-center justify-center gap-2"
                    >
                        <span
                            class="bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] text-xs font-bold px-3 py-1 rounded-full"
                            >First month FREE</span
                        >
                        <span class="text-gray-600 text-xs"
                            >then $29.99/mo</span
                        >
                    </div>
                    <p v-else class="text-gray-500 text-sm mt-2">
                        Billed monthly · Cancel anytime
                    </p>
                </div>

                <p class="text-gray-600 text-xs mb-6">
                    Cancel anytime · No hidden fees
                </p>

                <button
                    @click="checkout"
                    :disabled="checkoutLoading"
                    class="w-full py-4 bg-[#7ED957] text-black rounded-2xl font-bold text-lg hover:bg-[#6bc947] transition-all disabled:opacity-50 shadow-lg shadow-[#7ED957]/20"
                >
                    {{
                        checkoutLoading
                            ? "Redirecting..."
                            : hadTrial
                              ? "Subscribe Now"
                              : "Start Free Trial"
                    }}
                </button>

                <p class="text-xs text-gray-600 mt-4">
                    Secure payment via Stripe · Instant access
                </p>
            </template>
        </div>
    </div>
</template>
