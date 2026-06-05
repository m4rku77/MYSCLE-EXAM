<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();

const isRegister = ref(false);

const firstName = ref("");
const lastName = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");

const error = ref("");
const success = ref("");
const loading = ref(false);
const visible = ref(false);

onMounted(() => {
    if (route.query.register === "true") {
        isRegister.value = true;
    }
    setTimeout(() => {
        visible.value = true;
    }, 100);
});

const validateEmail = (e) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e);

const validate = () => {
    error.value = "";
    if (!email.value || !password.value) {
        error.value = "Please fill in all required fields";
        return false;
    }
    if (!validateEmail(email.value)) {
        error.value = "Invalid email format";
        return false;
    }
    if (isRegister.value && password.value.length < 6) {
        error.value = "Password must be at least 6 characters";
        return false;
    }
    if (isRegister.value) {
        if (!firstName.value || !lastName.value) {
            error.value = "Please enter your name";
            return false;
        }
        if (password.value !== password_confirmation.value) {
            error.value = "Passwords do not match";
            return false;
        }
    }
    return true;
};

const login = async () => {
    if (!validate()) return;
    loading.value = true;
    try {
        const response = await axios.post("http://127.0.0.1:8000/api/login", {
            email: email.value,
            password: password.value,
        });
        localStorage.setItem("token", response.data.token);
        localStorage.setItem("role", response.data.user.role);
        const role = response.data.user.role;
        const redirect = route.query.redirect;
        if (redirect) {
            router.push(redirect);
        } else if (role === "admin") {
            router.push("/admin");
        } else if (role === "trainer") {
            router.push("/choose-mode");
        } else {
            router.push("/dashboard");
        }
    } catch (e) {
        error.value = e.response?.data?.message || "Invalid credentials";
    } finally {
        loading.value = false;
    }
};

const register = async () => {
    if (!validate()) return;
    loading.value = true;
    try {
        const response = await axios.post(
            "http://127.0.0.1:8000/api/register",
            {
                first_name: firstName.value,
                last_name: lastName.value,
                email: email.value,
                password: password.value,
                password_confirmation: password_confirmation.value,
            },
        );
        localStorage.setItem("token", response.data.token);
        localStorage.setItem("role", response.data.user.role);
        success.value = "Account created!";
        const redirect = route.query.redirect;
        setTimeout(() => {
            router.push(redirect || "/dashboard");
        }, 1000);
    } catch (e) {
        const backendErrors = e.response?.data?.errors;
        error.value = backendErrors
            ? Object.values(backendErrors).flat().join(", ")
            : e.response?.data?.message || "Registration failed";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex bg-[#080808] text-white overflow-hidden">
        <div
            class="hidden lg:flex lg:w-1/2 relative items-center justify-center p-12"
        >
            <div
                class="absolute inset-0 bg-gradient-to-br from-[#0f0f0f] to-[#080808]"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#7ED957]/10 blur-[150px] rounded-full"
            ></div>

            <div class="relative z-10 max-w-md">
                <router-link to="/" class="inline-flex items-center gap-4 mb-16 group">
                    <div class="flex items-center gap-2.5 bg-white/5 border border-white/10 rounded-full px-4 py-2.5 group-hover:border-[#7ED957]/30 group-hover:bg-[#7ED957]/5 transition-all">
                        <img src="/logo.png" class="h-4" />
                        <span style="font-family: 'Bebas Neue', sans-serif;" class="text-lg tracking-widest text-white group-hover:text-[#7ED957] transition-colors">MYSCLE</span>
                        <span class="w-px h-3 bg-white/10"></span>
                        <span class="text-gray-600 text-xs group-hover:text-gray-400 transition-colors">← home</span>
                    </div>
                </router-link>

                <h2 class="text-5xl font-black leading-tight mb-6">
                    Train Smarter.<br />
                    <span
                        class="italic bg-clip-text text-transparent bg-gradient-to-r from-[#00ff41] via-[#7ED957] to-[#39ff14] animate-gradient bg-[length:300%_300%]"
                    >
                        Get Stronger.
                    </span>
                </h2>

                <p class="text-gray-400 text-lg leading-relaxed mb-10">
                    The all-in-one fitness platform built for serious athletes.
                </p>

                <div class="space-y-4">
                    <div
                        v-for="item in [
                            'Track progress with advanced charts',
                            'Build custom training plans',
                            'Stay consistent with streak tracking',
                            'Connect with professional trainers',
                        ]"
                        :key="item"
                        class="flex items-center gap-3 text-gray-300"
                    >
                        <span
                            class="w-5 h-5 bg-[#7ED957] rounded-full flex items-center justify-center text-black text-xs font-bold shrink-0"
                            >✓</span
                        >
                        {{ item }}
                    </div>
                </div>
            </div>
        </div>

        <div
            class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 relative"
        >
            <div class="absolute inset-0 bg-[#0a0a0a]"></div>
            <div
                class="absolute top-0 right-0 w-[400px] h-[400px] bg-[#7ED957]/5 blur-[120px] rounded-full pointer-events-none"
            ></div>
            <div
                class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-[#7ED957]/5 blur-[100px] rounded-full pointer-events-none"
            ></div>

            <div
                class="relative w-full max-w-md transition-all duration-700"
                :class="
                    visible
                        ? 'opacity-100 translate-y-0'
                        : 'opacity-0 translate-y-8'
                "
            >
                <div
                    class="lg:hidden flex items-center gap-3 mb-10 justify-center"
                >
                    <router-link to="/" class="flex items-center gap-3">
                        <img src="/logo.png" class="h-8" />
                        <span
                            class="font-black text-lg tracking-widest uppercase"
                            >Myscle</span
                        >
                    </router-link>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-black mb-2">
                        {{ isRegister ? "Create account" : "Welcome back" }}
                    </h1>
                    <p class="text-gray-500">
                        {{
                            isRegister
                                ? "Start your fitness journey today"
                                : "Login to continue your training"
                        }}
                    </p>
                    <p
                        v-if="route.query.redirect"
                        class="text-[#7ED957] text-sm mt-1 font-medium"
                    >
                        Login to continue →
                    </p>
                </div>

                <div
                    class="flex bg-[#111] border border-white/5 rounded-2xl p-1 mb-8"
                >
                    <button
                        @click="
                            isRegister = false;
                            error = '';
                        "
                        :class="
                            !isRegister
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-400'
                        "
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                    >
                        Login
                    </button>
                    <button
                        @click="
                            isRegister = true;
                            error = '';
                        "
                        :class="
                            isRegister
                                ? 'bg-[#7ED957] text-black'
                                : 'text-gray-400'
                        "
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all"
                    >
                        Register
                    </button>
                </div>

                <form
                    @submit.prevent="isRegister ? register() : login()"
                    class="space-y-4"
                >
                    <div v-if="isRegister" class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                                >First Name</label
                            >
                            <input
                                v-model="firstName"
                                type="text"
                                placeholder="John"
                                class="mt-2 w-full px-4 py-3 rounded-xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none transition text-sm placeholder-gray-600"
                            />
                        </div>
                        <div>
                            <label
                                class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                                >Last Name</label
                            >
                            <input
                                v-model="lastName"
                                type="text"
                                placeholder="Doe"
                                class="mt-2 w-full px-4 py-3 rounded-xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none transition text-sm placeholder-gray-600"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                            >Email</label
                        >
                        <input
                            v-model="email"
                            type="email"
                            placeholder="you@example.com"
                            class="mt-2 w-full px-4 py-3 rounded-xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none transition text-sm placeholder-gray-600"
                        />
                    </div>

                    <div>
                        <label
                            class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                            >Password</label
                        >
                        <input
                            v-model="password"
                            type="password"
                            placeholder="••••••••"
                            class="mt-2 w-full px-4 py-3 rounded-xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none transition text-sm placeholder-gray-600"
                        />
                    </div>

                    <div v-if="isRegister">
                        <label
                            class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                            >Confirm Password</label
                        >
                        <input
                            v-model="password_confirmation"
                            type="password"
                            placeholder="••••••••"
                            class="mt-2 w-full px-4 py-3 rounded-xl bg-[#111] border border-white/5 focus:border-[#7ED957] outline-none transition text-sm placeholder-gray-600"
                        />
                    </div>

                    <div
                        v-if="error"
                        class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm"
                    >
                        {{ error }}
                    </div>

                    <div
                        v-if="success"
                        class="bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-xl px-4 py-3 text-[#7ED957] text-sm"
                    >
                        {{ success }}
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-4 rounded-xl font-bold text-black bg-[#7ED957] hover:bg-[#6bc947] transition-all hover:scale-[1.02] shadow-lg shadow-[#7ED957]/20 disabled:opacity-50 disabled:scale-100 mt-2"
                    >
                        <span v-if="loading">{{
                            isRegister ? "Creating..." : "Logging in..."
                        }}</span>
                        <span v-else>{{
                            isRegister ? "Create Account" : "Login"
                        }}</span>
                    </button>
                </form>

                <p class="text-center text-gray-600 text-xs mt-6">
                    By continuing you agree to our Terms of Service
                </p>
            </div>
        </div>
    </div>
</template>
