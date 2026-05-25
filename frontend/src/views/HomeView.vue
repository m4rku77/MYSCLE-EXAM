<script setup>
import { useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from "axios";

const scrollProgress = ref(0);
const router = useRouter();

const goLogin = () => router.push("/login");
const goRegister = () => router.push("/login?register=true");
const goUpgrade = () => router.push("/login?redirect=/upgrade");

const scrolled = ref(false);
const visible = ref(false);
const loading = ref(true);

const stats = ref({ workouts: 0, users: 0, consistency: 0 });

onMounted(async () => {
    window.addEventListener("scroll", () => {
        scrolled.value = window.scrollY > 50;
        const total =
            document.documentElement.scrollHeight - window.innerHeight;
        scrollProgress.value = total > 0 ? (window.scrollY / total) * 100 : 0;
    });
    setTimeout(() => {
        visible.value = true;
    }, 100);
    try {
        const res = await axios.get("http://127.0.0.1:8000/api/stats");
        stats.value = res.data;
    } catch (e) {
        console.log("API error:", e);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="bg-[#080808] text-white overflow-x-hidden font-sans">
        <header
            :class="[
                'fixed w-full z-50 px-8 py-4 flex justify-between items-center transition-all duration-500 overflow-hidden',
                scrolled
                    ? 'bg-black/60 backdrop-blur-2xl border-b border-white/10'
                    : 'bg-transparent',
            ]"
        >
            <div class="flex items-center gap-3">
                <img src="/logo.png" class="h-8" />
                <span class="font-bold text-lg tracking-widest uppercase"
                    >Myscle</span
                >
            </div>
            <div class="flex gap-2 items-center">
                <button
                    @click="goLogin"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition"
                >
                    Login
                </button>
                <button
                    @click="goRegister"
                    class="px-5 py-2.5 rounded-xl font-semibold text-black bg-[#7ED957] hover:bg-[#6bc947] transition-all text-sm"
                >
                    Get Started
                </button>
            </div>
            <div
                class="absolute bottom-0 left-0 h-[2px] bg-[#7ED957] transition-all duration-100 rounded-full"
                :style="{ width: scrollProgress + '%' }"
            ></div>
        </header>

        <section
            class="min-h-screen flex items-center justify-center text-center px-6 relative overflow-hidden"
        >
            <div class="absolute inset-0 bg-[#080808]"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] bg-[#7ED957]/10 blur-[200px] rounded-full pointer-events-none"
            ></div>
            <div
                class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.015%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"
            ></div>

            <div
                class="relative z-10 max-w-5xl transition-all duration-1000"
                :class="
                    visible
                        ? 'opacity-100 translate-y-0'
                        : 'opacity-0 translate-y-12'
                "
            >
                <div
                    class="inline-flex items-center gap-2 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-full px-4 py-1.5 text-sm text-[#7ED957] mb-8 font-medium"
                >
                    <span
                        class="w-2 h-2 bg-[#7ED957] rounded-full animate-pulse"
                    ></span>
                    Trusted by athletes worldwide
                </div>
                <h1
                    class="text-6xl md:text-8xl font-black leading-[1.05] mb-6 tracking-tight"
                >
                    Train Smarter.<br />
                    <span class="text-[#7ED957]">Get Stronger.</span>
                </h1>
                <p
                    class="text-gray-400 text-xl mb-10 max-w-2xl mx-auto leading-relaxed"
                >
                    The all-in-one fitness platform to track workouts, build
                    plans, and crush your goals — built for serious athletes.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button
                        @click="goRegister"
                        class="bg-[#7ED957] text-black px-10 py-4 rounded-2xl font-bold text-lg hover:bg-[#6bc947] transition-all shadow-2xl shadow-[#7ED957]/20 hover:scale-105"
                    >
                        Start for Free
                    </button>
                    <button
                        @click="goLogin"
                        class="border border-white/10 bg-white/5 px-10 py-4 rounded-2xl font-semibold text-lg hover:bg-white/10 transition-all"
                    >
                        Sign In
                    </button>
                </div>
                <p class="text-gray-600 text-sm mt-5">
                    No credit card required · First month free
                </p>
            </div>
        </section>

        <section class="py-20 px-6 border-y border-white/5 bg-[#0a0a0a]">
            <div class="max-w-4xl mx-auto grid grid-cols-3 gap-8 text-center">
                <div>
                    <p class="text-5xl font-black text-[#7ED957] mb-2">
                        {{ loading ? "—" : stats.workouts + "+" }}
                    </p>
                    <p class="text-gray-500 text-sm uppercase tracking-widest">
                        Workouts Logged
                    </p>
                </div>
                <div>
                    <p class="text-5xl font-black text-[#7ED957] mb-2">
                        {{ loading ? "—" : stats.users + "+" }}
                    </p>
                    <p class="text-gray-500 text-sm uppercase tracking-widest">
                        Active Users
                    </p>
                </div>
                <div>
                    <p class="text-5xl font-black text-[#7ED957] mb-2">
                        {{ loading ? "—" : stats.trainers + "+" }}
                    </p>
                    <p class="text-gray-500 text-sm uppercase tracking-widest">
                        Active Trainers
                    </p>
                </div>
            </div>
        </section>

        <section class="py-32 px-6 max-w-6xl mx-auto">
            <div class="text-center mb-20">
                <p
                    class="text-[#7ED957] text-sm uppercase tracking-widest font-semibold mb-3"
                >
                    Everything you need
                </p>
                <h2 class="text-5xl font-black mb-4">Built for performance</h2>
                <p class="text-gray-400 max-w-xl mx-auto">
                    Every feature designed to help you train harder, recover
                    smarter, and grow faster.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    class="group bg-[#111] border border-white/5 p-8 rounded-3xl hover:border-[#7ED957]/30 transition-all hover:-translate-y-1"
                >
                    <div
                        class="w-12 h-12 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center mb-5 text-2xl group-hover:bg-[#7ED957]/20 transition-all"
                    >
                        📊
                    </div>
                    <h3 class="text-xl font-bold mb-3">Track Progress</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Monitor strength gains with advanced charts and personal
                        records. See exactly how far you've come.
                    </p>
                </div>
                <div
                    class="group bg-[#111] border border-white/5 p-8 rounded-3xl hover:border-[#7ED957]/30 transition-all hover:-translate-y-1"
                >
                    <div
                        class="w-12 h-12 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center mb-5 text-2xl group-hover:bg-[#7ED957]/20 transition-all"
                    >
                        🏋️
                    </div>
                    <h3 class="text-xl font-bold mb-3">Build Plans</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Create fully custom training programs with exercises,
                        sets, reps, and rest timers built in.
                    </p>
                </div>
                <div
                    class="group bg-[#111] border border-white/5 p-8 rounded-3xl hover:border-[#7ED957]/30 transition-all hover:-translate-y-1"
                >
                    <div
                        class="w-12 h-12 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center mb-5 text-2xl group-hover:bg-[#7ED957]/20 transition-all"
                    >
                        ⚡
                    </div>
                    <h3 class="text-xl font-bold mb-3">Stay Consistent</h3>
                    <p class="text-gray-500 leading-relaxed">
                        Streak tracking, goal systems, and performance insights
                        keep you accountable every single day.
                    </p>
                </div>
            </div>
        </section>

        <section class="py-20 px-6 max-w-6xl mx-auto space-y-32">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p
                        class="text-[#7ED957] text-xs uppercase tracking-widest font-semibold mb-3"
                    >
                        Analytics
                    </p>
                    <h2 class="text-4xl font-black mb-5 leading-tight">
                        Measure every rep. See every gain.
                    </h2>
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Staying motivated is easier when you can see how far
                        you've come. Visualize your strength curve over time.
                    </p>
                    <ul class="space-y-3">
                        <li
                            v-for="item in [
                                'Advanced Exercise Charts',
                                'Personal Records',
                                'Calculate One Rep Max',
                                'Custom Exercises',
                                'Exercise History',
                            ]"
                            :key="item"
                            class="flex items-center gap-3 text-gray-300"
                        >
                            <span
                                class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0"
                                >✓</span
                            >
                            {{ item }}
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center">
                    <img
                        src="/measure.webp"
                        class="rounded-3xl shadow-2xl max-h-[480px] w-full object-cover"
                    />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="flex justify-center order-2 md:order-1">
                    <img
                        src="/fitnessstats.jpg"
                        class="rounded-3xl shadow-2xl max-h-[480px] w-full object-cover"
                    />
                </div>
                <div class="order-1 md:order-2">
                    <p
                        class="text-[#7ED957] text-xs uppercase tracking-widest font-semibold mb-3"
                    >
                        Training
                    </p>
                    <h2 class="text-4xl font-black mb-5 leading-tight">
                        Build workouts that actually work.
                    </h2>
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Create structured training plans with full control over
                        exercises, volume, and intensity.
                    </p>
                    <ul class="space-y-3">
                        <li
                            v-for="item in [
                                'Custom Workout Builder',
                                'Exercise Library',
                                'Supersets & Dropsets',
                                'Rest Timer',
                                'Track Every Set',
                            ]"
                            :key="item"
                            class="flex items-center gap-3 text-gray-300"
                        >
                            <span
                                class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0"
                                >✓</span
                            >
                            {{ item }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p
                        class="text-[#7ED957] text-xs uppercase tracking-widest font-semibold mb-3"
                    >
                        Habits
                    </p>
                    <h2 class="text-4xl font-black mb-5 leading-tight">
                        Never miss a workout again.
                    </h2>
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Build unstoppable habits with streak tracking, smart
                        reminders, and daily performance insights.
                    </p>
                    <ul class="space-y-3">
                        <li
                            v-for="item in [
                                'Daily Tracking',
                                'Goal System',
                                'Streak Tracking',
                                'Performance Insights',
                            ]"
                            :key="item"
                            class="flex items-center gap-3 text-gray-300"
                        >
                            <span
                                class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0"
                                >✓</span
                            >
                            {{ item }}
                        </li>
                    </ul>
                </div>
                <div class="flex justify-center">
                    <img
                        src="/measurestrength.jpg"
                        class="rounded-3xl shadow-2xl max-h-[480px] w-full object-cover"
                    />
                </div>
            </div>
        </section>

        <section class="py-32 px-6">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <p
                    class="text-[#7ED957] text-sm uppercase tracking-widest font-semibold mb-3"
                >
                    Pricing
                </p>
                <h2 class="text-5xl font-black mb-4">
                    Simple, transparent pricing
                </h2>
                <p class="text-gray-400 max-w-xl mx-auto">
                    Start free for 30 days. No credit card needed. Cancel
                    anytime.
                </p>
            </div>

            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6">
                <div class="bg-[#111] border border-white/5 rounded-3xl p-8">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-4"
                    >
                        Athlete
                    </p>
                    <div class="mb-6">
                        <span class="text-5xl font-black">Free</span>
                        <p class="text-gray-500 text-sm mt-1">
                            Forever, no limits
                        </p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li
                            v-for="item in [
                                'Track workouts & sets',
                                'Exercise library',
                                'Personal statistics',
                                'Friends & messaging',
                                'Workout calendar',
                            ]"
                            :key="item"
                            class="flex items-center gap-3 text-gray-300 text-sm"
                        >
                            <span
                                class="w-5 h-5 bg-white/10 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                                >✓</span
                            >
                            {{ item }}
                        </li>
                    </ul>
                    <button
                        @click="goRegister"
                        class="w-full py-3.5 bg-white/5 border border-white/10 text-white rounded-2xl font-bold text-sm hover:bg-white/10 transition-all"
                    >
                        Get Started Free
                    </button>
                </div>

                <div
                    class="bg-gradient-to-br from-[#111] to-[#0f0f0f] border border-[#7ED957]/30 rounded-3xl p-8 relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-[#7ED957]/5 blur-[80px] rounded-full pointer-events-none"
                    ></div>
                    <div class="flex items-center justify-between mb-4">
                        <p
                            class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold"
                        >
                            Trainer
                        </p>
                        <span
                            class="bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] text-xs font-bold px-3 py-1 rounded-full"
                            >🏆 Pro</span
                        >
                    </div>
                    <div class="mb-2">
                        <div class="flex items-baseline gap-1">
                            <span class="text-5xl font-black">$29</span>
                            <span class="text-gray-400 text-lg"
                                >.99<span class="text-sm">/mo</span></span
                            >
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span
                                class="bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] text-xs font-bold px-3 py-1 rounded-full"
                                >First month FREE</span
                            >
                            <span class="text-gray-600 text-xs"
                                >then $29.99/mo</span
                            >
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs mb-6">
                        Cancel anytime · No hidden fees
                    </p>
                    <ul class="space-y-3 mb-8">
                        <li
                            v-for="item in [
                                'Everything in Athlete',
                                'Manage unlimited clients',
                                'Assign custom workouts',
                                'View client statistics',
                                'Strength progress tracking',
                                'Coach notes & messaging',
                                'Client calendar heatmap',
                            ]"
                            :key="item"
                            class="flex items-center gap-3 text-gray-300 text-sm"
                        >
                            <span
                                class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0"
                                >✓</span
                            >
                            {{ item }}
                        </li>
                    </ul>
                    <button
                        @click="goUpgrade"
                        class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                    >
                        Start Free Trial
                    </button>
                    <p class="text-center text-gray-600 text-xs mt-3">
                        Secure payment via Stripe · Instant access
                    </p>
                </div>
            </div>
        </section>

        <section class="py-32 text-center px-6 relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-t from-[#7ED957]/5 to-transparent pointer-events-none"
            ></div>
            <div class="relative z-10">
                <h2 class="text-6xl font-black mb-6 leading-tight">
                    Your transformation<br />starts today.
                </h2>
                <p class="text-gray-400 text-xl mb-10 max-w-lg mx-auto">
                    Join thousands of athletes already using Myscle to reach
                    their peak.
                </p>
                <button
                    @click="goRegister"
                    class="bg-[#7ED957] text-black px-12 py-5 rounded-2xl font-bold text-xl hover:bg-[#6bc947] transition-all shadow-2xl shadow-[#7ED957]/20 hover:scale-105"
                >
                    Create Free Account
                </button>
                <p class="text-gray-600 text-sm mt-4">
                    No credit card · Free forever · Cancel anytime
                </p>
            </div>
        </section>

        <footer
            class="py-10 text-center text-gray-600 text-sm border-t border-white/5"
        >
            <div class="flex items-center justify-center gap-3 mb-3">
                <img src="/logo.png" class="h-6 opacity-40" />
                <span
                    class="font-semibold uppercase tracking-widest text-gray-500"
                    >Myscle</span
                >
            </div>
            © 2026 MYSCLE. Built for athletes.
        </footer>
    </div>
</template>
