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

const scrollTo = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth" });
};

const mobileMenuOpen = ref(false);

onMounted(async () => {
    window.addEventListener("scroll", () => {
        scrolled.value = window.scrollY > 50;
        const total = document.documentElement.scrollHeight - window.innerHeight;
        scrollProgress.value = total > 0 ? (window.scrollY / total) * 100 : 0;
    });
    setTimeout(() => { visible.value = true; }, 100);
    try {
        const res = await axios.get("https://myscle-exam-production.up.railway.app/api/stats");
        stats.value = res.data;
    } catch (e) {}
    finally { loading.value = false; }
});
</script>

<template>
    <div class="bg-[#080808] text-white overflow-x-hidden font-sans">

        <header :class="['fixed w-full z-50 px-6 md:px-8 py-4 flex items-center justify-between md:grid md:grid-cols-3 transition-all duration-500 overflow-hidden', scrolled ? 'bg-black/60 backdrop-blur-2xl border-b border-white/10' : 'bg-transparent']">
            <div class="flex items-center gap-3">
                <img src="/logo.png" class="h-8" />
                <span style="font-family: 'Bebas Neue', sans-serif;" class="text-2xl tracking-widest">MYSCLE</span>
            </div>
            <div class="hidden md:flex items-center gap-6 justify-center">
                <button @click="scrollTo('features')" class="text-sm text-gray-400 hover:text-[#6bc947] transition">Features</button>
                <button @click="scrollTo('how-it-works')" class="text-sm text-gray-400 hover:text-[#6bc947] transition">How it works</button>
                <button @click="scrollTo('pricing')" class="text-sm text-gray-400 hover:text-[#6bc947] transition">Pricing</button>
            </div>
            <div class="hidden md:flex gap-2 items-center justify-end">
                <button @click="goLogin" class="px-4 py-2 text-sm text-gray-400 hover:text-[#6bc947] transition">Login</button>
                <button @click="goRegister" class="px-5 py-2.5 rounded-xl font-semibold text-black bg-[#7ED957] hover:bg-[#6bc947] transition-all text-sm">Get Started</button>
            </div>
            <div class="flex justify-end md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-400 hover:text-white transition">
                    <i :class="mobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'" class="text-xl"></i>
                </button>
            </div>
            <div class="absolute bottom-0 left-0 h-[2px] bg-[#7ED957] transition-all duration-100 rounded-full" :style="{ width: scrollProgress + '%' }"></div>
        </header>

        <div v-if="mobileMenuOpen" class="fixed top-[60px] left-0 w-full z-40 bg-black/90 backdrop-blur-2xl border-b border-white/10 px-6 py-6 flex flex-col gap-4">
            <button @click="scrollTo('features'); mobileMenuOpen = false;" class="text-sm text-gray-300 hover:text-white text-left transition">Features</button>
            <button @click="scrollTo('how-it-works'); mobileMenuOpen = false;" class="text-sm text-gray-300 hover:text-white text-left transition">How it works</button>
            <button @click="scrollTo('pricing'); mobileMenuOpen = false;" class="text-sm text-gray-300 hover:text-white text-left transition">Pricing</button>
            <div class="border-t border-white/10 pt-4 flex gap-3">
                <button @click="goLogin" class="flex-1 py-2.5 text-sm text-gray-400 border border-white/10 rounded-2xl hover:bg-white/5 transition">Login</button>
                <button @click="goRegister" class="flex-1 py-2.5 text-sm font-bold text-black bg-[#7ED957] rounded-2xl hover:bg-[#6bc947] transition">Get Started</button>
            </div>
        </div>

    <section class="min-h-screen flex items-center px-6 md:px-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-[#080808]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#7ED957]/6 blur-[160px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 max-w-6xl mx-auto w-full pt-28 pb-16 transition-all duration-1000" :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-[1.1] tracking-tight mb-12">
                <span class="text-white">Bored of juggling</span><br/>
                <span class="text-white/20">apps, notes and</span><br/>
                <span class="text-white/20">spreadsheets just to</span><br/>
                <span class="text-[#7ED957]">track your gains?</span>
            </h1>

            <div class="flex flex-col sm:flex-row items-start gap-4">
                <button @click="goRegister" class="bg-[#7ED957] text-black px-8 py-4 rounded-full font-bold text-base hover:bg-[#6bc947] transition-all shadow-xl shadow-[#7ED957]/20 hover:scale-105">
                    Start for Free →
                </button>
                <button @click="goLogin" class="border border-white/10 bg-white/5 px-8 py-4 rounded-full font-semibold text-base hover:bg-white/10 transition-all text-gray-400 hover:text-white">
                    Sign In
                </button>
            </div>
        </div>
    </section>
        <section id="features" class="py-32 px-6 md:px-16 max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div>
                    <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-3">— 02 / Features</p>
                    <h2 class="text-4xl md:text-6xl font-black leading-tight">Built for<br/>performance</h2>
                </div>
                <p class="text-gray-500 max-w-xs text-sm leading-relaxed">Every feature designed to help you train harder, recover smarter, and grow faster.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-7 bg-[#7ED957]/5 border border-[#7ED957]/15 p-8 md:p-12 hover:border-[#7ED957]/40 hover:bg-[#7ED957]/10 transition-all rounded-3xl">
                    <div class="flex items-start justify-between mb-8">
                        <span class="text-6xl font-black text-[#7ED957]/20 font-mono">01</span>
                        <span class="text-xs font-mono text-[#7ED957]/50 uppercase tracking-widest bg-[#7ED957]/10 px-3 py-1 rounded-full">Analytics</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black mb-4 text-[#7ED957]">Track Progress</h3>
                    <p class="text-[#7ED957]/50 leading-relaxed max-w-sm">Monitor strength gains with advanced charts and personal records. See exactly how far you've come.</p>
                </div>

                <div class="md:col-span-5 bg-[#7ED957]/5 border border-[#7ED957]/15 p-8 hover:border-[#7ED957]/40 hover:bg-[#7ED957]/10 transition-all rounded-3xl">
                    <div class="flex items-start justify-between mb-8">
                        <span class="text-6xl font-black text-[#7ED957]/20 font-mono">02</span>
                        <span class="text-xs font-mono text-[#7ED957]/50 uppercase tracking-widest bg-[#7ED957]/10 px-3 py-1 rounded-full">Training</span>
                    </div>
                    <h3 class="text-2xl font-black mb-4 text-[#7ED957]">Build Plans</h3>
                    <p class="text-[#7ED957]/50 leading-relaxed">Create fully custom training programs with exercises, sets, reps, and rest timers built in.</p>
                </div>

                <div class="md:col-span-12 bg-[#7ED957]/5 border border-[#7ED957]/15 p-8 hover:border-[#7ED957]/40 hover:bg-[#7ED957]/10 transition-all flex flex-col md:flex-row md:items-center gap-6 md:gap-16 rounded-3xl">
                    <div class="flex items-start gap-6">
                        <span class="text-6xl font-black text-[#7ED957]/20 font-mono shrink-0">03</span>
                        <div>
                            <span class="text-xs font-mono text-[#7ED957]/50 uppercase tracking-widest bg-[#7ED957]/10 px-3 py-1 rounded-full inline-block mb-3">Consistency</span>
                            <h3 class="text-2xl font-black text-[#7ED957]">Stay Consistent</h3>
                        </div>
                    </div>
                    <p class="text-[#7ED957]/50 leading-relaxed md:max-w-sm md:ml-auto">Streak tracking, goal systems, and performance insights keep you accountable every single day.</p>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="py-20 px-6 md:px-16 max-w-7xl mx-auto">
            <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-16">— 03 / How it works</p>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="bg-[#111] border border-white/5 rounded-3xl p-10 md:p-12">
                    <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-6 bg-[#7ED957]/10 inline-block px-3 py-1 rounded-full">Analytics</p>
                    <h2 class="text-3xl md:text-4xl font-black mb-6 leading-tight">Measure every rep.<br/>See every gain.</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed text-sm">Staying motivated is easier when you can see how far you've come. Visualize your strength curve over time.</p>
                    <ul class="space-y-2">
                        <li v-for="item in ['Advanced Exercise Charts', 'Personal Records', 'Calculate One Rep Max', 'Custom Exercises', 'Exercise History']" :key="item" class="flex items-center gap-3 text-gray-400 text-sm">
                            <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0">✓</span> {{ item }}
                        </li>
                    </ul>
                </div>
                <div class="bg-[#0f0f0f] rounded-3xl overflow-hidden border border-white/5" style="min-height: 360px;">
                    <img src="/measure.webp" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-all" style="min-height: 360px;" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="bg-[#0f0f0f] rounded-3xl overflow-hidden border border-white/5 order-2 md:order-1" style="min-height: 360px;">
                    <img src="/fitnessstats.jpg" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-all" style="min-height: 360px;" />
                </div>
                <div class="bg-[#111] border border-white/5 rounded-3xl p-10 md:p-12 order-1 md:order-2">
                    <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-6 bg-[#7ED957]/10 inline-block px-3 py-1 rounded-full">Training</p>
                    <h2 class="text-3xl md:text-4xl font-black mb-6 leading-tight">Build workouts<br/>that actually work.</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed text-sm">Create structured training plans with full control over exercises, volume, and intensity.</p>
                    <ul class="space-y-2">
                        <li v-for="item in ['Custom Workout Builder', 'Exercise Library', 'Supersets & Dropsets', 'Rest Timer', 'Track Every Set']" :key="item" class="flex items-center gap-3 text-gray-400 text-sm">
                            <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0">✓</span> {{ item }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-[#111] border border-white/5 rounded-3xl p-10 md:p-12">
                    <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-6 bg-[#7ED957]/10 inline-block px-3 py-1 rounded-full">Habits</p>
                    <h2 class="text-3xl md:text-4xl font-black mb-6 leading-tight">Never miss a<br/>workout again.</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed text-sm">Build unstoppable habits with streak tracking, smart reminders, and daily performance insights.</p>
                    <ul class="space-y-2">
                        <li v-for="item in ['Daily Tracking', 'Goal System', 'Streak Tracking', 'Performance Insights']" :key="item" class="flex items-center gap-3 text-gray-400 text-sm">
                            <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0">✓</span> {{ item }}
                        </li>
                    </ul>
                </div>
                <div class="bg-[#0f0f0f] rounded-3xl overflow-hidden border border-white/5" style="min-height: 360px;">
                    <img src="/measurestrength.jpg" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-all" style="min-height: 360px;" />
                </div>
            </div>
        </section>

        <section id="pricing" class="py-32 px-6 md:px-16">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-3">— 04 / Pricing</p>
                        <h2 class="text-4xl md:text-6xl font-black leading-tight">Simple,<br/>transparent.</h2>
                    </div>
                    <p class="text-gray-500 max-w-xs text-sm">Start free for 30 days. Credit card needed. Cancel anytime.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border border-white/5 rounded-3xl p-10 md:p-12 bg-[#111]">
                        <div class="flex items-center justify-between mb-8">
                            <p class="text-xs font-mono text-gray-500 uppercase tracking-widest">Athlete</p>
                            <span class="text-xs font-mono text-gray-600 bg-white/5 px-3 py-1 rounded-full">/ free</span>
                        </div>
                        <div class="mb-10">
                            <span class="text-6xl font-black">Free</span>
                            <p class="text-gray-600 text-sm mt-2 font-mono">forever, no limits</p>
                        </div>
                        <div class="space-y-3 mb-10">
                            <p v-for="item in ['Track workouts & sets', 'Exercise library', 'Personal statistics', 'Friends & messaging', 'Workout calendar']" :key="item" class="text-sm text-gray-400 flex items-center gap-3">
                                <span class="w-5 h-5 bg-white/10 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">✓</span> {{ item }}
                            </p>
                        </div>
                        <button @click="goRegister" class="w-full py-4 border border-white/10 text-white text-sm font-bold hover:bg-white/5 transition-all rounded-2xl">
                            Get Started Free
                        </button>
                    </div>

                    <div class="border border-[#7ED957]/30 bg-[#7ED957]/5 rounded-3xl p-10 md:p-12 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-48 h-48 bg-[#7ED957]/10 blur-[60px] rounded-full pointer-events-none"></div>
                        <div class="flex items-center justify-between mb-8">
                            <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest">Trainer</p>
                            <span class="text-xs font-mono text-[#7ED957]/80 border border-[#7ED957]/20 px-3 py-1 rounded-full bg-[#7ED957]/10">Pro</span>
                        </div>
                        <div class="mb-3">
                            <div class="flex items-baseline gap-1">
                                <span class="text-6xl font-black">$29</span>
                                <span class="text-gray-400 text-lg">.99<span class="text-sm">/mo</span></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs font-mono text-[#7ED957] border border-[#7ED957]/20 px-3 py-1 rounded-full bg-[#7ED957]/10">First month FREE</span>
                            <span class="text-gray-600 text-xs font-mono">then $29.99/mo</span>
                        </div>
                        <p class="text-gray-600 text-xs font-mono mb-10">Cancel anytime · No hidden fees</p>
                        <div class="space-y-3 mb-10">
                            <p v-for="item in ['Everything in Athlete', 'Manage unlimited clients', 'Assign custom workouts', 'View client statistics', 'Strength progress tracking', 'Coach notes & messaging', 'Client calendar heatmap']" :key="item" class="text-sm text-[#7ED957]/70 flex items-center gap-3">
                                <span class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0">✓</span> {{ item }}
                            </p>
                        </div>
                        <button @click="goUpgrade" class="w-full py-4 bg-[#7ED957] text-black text-sm font-bold hover:bg-[#6bc947] transition-all rounded-2xl shadow-xl shadow-[#7ED957]/20">
                            Start Free Trial →
                        </button>
                        <p class="text-center text-gray-600 text-xs font-mono mt-3">Secure payment via Stripe</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-32 px-6 md:px-16 relative overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-[#7ED957]/5 blur-[120px] rounded-full pointer-events-none"></div>
            <div class="relative z-10 max-w-7xl mx-auto">
                <p class="text-xs font-mono text-[#7ED957] uppercase tracking-widest mb-8">— 05 / Start now</p>
                <div class="bg-[#111] border border-white/5 rounded-3xl p-10 md:p-16 flex flex-col md:flex-row md:items-center justify-between gap-10">
                    <h2 class="text-4xl md:text-6xl font-black leading-tight tracking-tight">
                        Your transformation<br/><span class="text-[#7ED957]">starts today.</span>
                    </h2>
                    <div class="flex flex-col gap-4 md:items-end shrink-0">
                        <p class="text-gray-500 text-sm max-w-xs leading-relaxed md:text-right">Use Myscle to reach your peak.</p>
                        <button @click="goRegister" class="bg-[#7ED957] text-black px-10 py-4 font-bold text-base hover:bg-[#6bc947] transition-all rounded-2xl shadow-xl shadow-[#7ED957]/20 hover:scale-105 w-full md:w-auto">
                            Create Free Account →
                        </button>
                        <p class="text-gray-700 text-xs font-mono">Free forever · Cancel anytime</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-10 px-6 md:px-16 border-t border-white/5">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="/logo.png" class="h-6 opacity-40" />
                    <span class="font-mono text-xs text-gray-600 uppercase tracking-widest">Myscle</span>
                </div>
                <p class="text-gray-700 text-xs font-mono">© 2026 — Built for athletes.</p>
            </div>
        </footer>

    </div>
</template>