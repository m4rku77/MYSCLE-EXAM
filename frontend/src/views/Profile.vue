<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const user = ref(null);
const loading = ref(true);
const name = ref("");
const email = ref("");
const file = ref(null);
const preview = ref(null);
const requests = ref([]);
const trainerRequests = ref([]);
const success = ref("");
const showPassword = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");
const passwordError = ref("");
const unit = ref(localStorage.getItem("unit") || "kg");

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const setUnit = (value) => {
    unit.value = value;
    localStorage.setItem("unit", value);
};

const fetchProfile = async () => {
    try {
        const res = await axios.get("http://localhost:8000/api/me", {
            headers,
        });
        user.value = res.data;
        name.value = res.data.name;
        email.value = res.data.email;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const fetchRequests = async () => {
    try {
        const res = await axios.get(
            "http://localhost:8000/api/friends/requests",
            { headers },
        );
        requests.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

const fetchTrainerRequests = async () => {
    try {
        const res = await axios.get(
            "http://localhost:8000/api/my/trainer-requests",
            { headers },
        );
        trainerRequests.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

const saveProfile = async () => {
    try {
        await axios.put(
            "http://localhost:8000/api/me",
            { name: name.value },
            { headers },
        );
        if (file.value) {
            const formData = new FormData();
            formData.append("photo", file.value);
            const res = await axios.post(
                "http://localhost:8000/api/me/photo",
                formData,
                { headers },
            );
            user.value.profile_photo = res.data.photo;
            preview.value = null;
        }
        success.value = "Profile updated";
        setTimeout(() => (success.value = ""), 3000);
    } catch (err) {
        console.log(err.response?.data || err.message);
    }
};

const updatePassword = async () => {
    if (newPassword.value !== confirmPassword.value) {
        passwordError.value = "Passwords do not match";
        return;
    }
    try {
        await axios.put(
            "http://localhost:8000/api/me/password",
            {
                current_password: currentPassword.value,
                new_password: newPassword.value,
            },
            { headers },
        );
        success.value = "Password updated";
        passwordError.value = "";
        showPassword.value = false;
    } catch (err) {
        passwordError.value = err.response?.data?.message || "Error";
    }
};

const handleFile = (e) => {
    file.value = e.target.files[0];
    preview.value = URL.createObjectURL(file.value);
};

const accept = async (id) => {
    await axios.post(
        `http://localhost:8000/api/friends/accept/${id}`,
        {},
        { headers },
    );
    fetchRequests();
};

const decline = async (id) => {
    await axios.delete(`http://localhost:8000/api/friends/decline/${id}`, {
        headers,
    });
    fetchRequests();
};

const acceptTrainer = async (id) => {
    await axios.post(
        `http://localhost:8000/api/my/trainer-requests/accept/${id}`,
        {},
        { headers },
    );
    fetchTrainerRequests();
};

const declineTrainer = async (id) => {
    await axios.delete(
        `http://localhost:8000/api/my/trainer-requests/decline/${id}`,
        { headers },
    );
    fetchTrainerRequests();
};

const logout = () => {
    localStorage.removeItem("token");
    window.location.href = "/login";
};

onMounted(() => {
    fetchProfile();
    fetchRequests();
    fetchTrainerRequests();
});
</script>

<template>
    <div class="h-screen bg-[#080808] text-white flex">
        <aside
            class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full"
        >
            <div class="flex items-center gap-3 mb-12">
                <img src="/logo.png" class="h-8" />
                <span class="font-black text-lg tracking-widest uppercase"
                    >Myscle</span
                >
            </div>
            <nav class="space-y-1 flex-1">
                <div
                    @click="router.push('/dashboard')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-dumbbell w-4"></i> Workouts
                </div>
                <div
                    @click="router.push('/statistics')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-chart-line w-4"></i> Statistics
                </div>
                <div
                    @click="router.push('/friends')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-users w-4"></i> Friends
                </div>
                <div
                    @click="router.push('/messages')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-comment w-4"></i> Messages
                </div>
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm"
                >
                    <i class="fas fa-user w-4"></i> Profile
                </div>
            </nav>
            <button
                @click="logout"
                class="w-full py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl font-semibold text-sm hover:bg-red-500/20 transition-all"
            >
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0"
            >
                <p
                    class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                >
                    Account
                </p>
                <h1 class="text-3xl font-black">Profile</h1>
            </div>

            <div
                class="hidden md:block bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0"
            >
                <p
                    class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
                >
                    Account
                </p>
                <h1 class="text-3xl font-bold">Profile</h1>
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-8 pt-6 pb-32 md:pb-10"
            >
                <div class="max-w-2xl mx-auto space-y-5">
                    <div
                        v-if="success"
                        class="bg-[#7ED957]/10 border border-[#7ED957]/30 text-[#7ED957] text-sm py-3 rounded-2xl text-center font-semibold"
                    >
                        {{ success }}
                    </div>

                    <div
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 flex flex-col items-center gap-5"
                    >
                        <div class="relative">
                            <img
                                :src="
                                    preview
                                        ? preview
                                        : user?.profile_photo
                                          ? 'http://localhost:8000/storage/' +
                                            user.profile_photo
                                          : `https://ui-avatars.com/api/?name=${user?.name}&background=1a1a1a&color=7ED957`
                                "
                                class="w-24 h-24 rounded-full object-cover ring-4 ring-[#7ED957]/20"
                            />
                            <label
                                class="absolute bottom-0 right-0 w-8 h-8 bg-[#7ED957] rounded-full flex items-center justify-center cursor-pointer hover:bg-[#6bc947] transition-all"
                            >
                                <i class="fas fa-camera text-black text-xs"></i>
                                <input
                                    type="file"
                                    @change="handleFile"
                                    class="hidden"
                                />
                            </label>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold">{{ user?.name }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ email }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="trainerRequests.length > 0"
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 space-y-4"
                    >
                        <p
                            class="text-xs text-gray-500 uppercase tracking-wider"
                        >
                            Trainer Requests
                        </p>
                        <div
                            v-for="trainer in trainerRequests"
                            :key="trainer.id"
                            class="flex items-center gap-4 bg-[#0a0a0a] rounded-2xl p-4 border border-white/5"
                        >
                            <img
                                :src="
                                    trainer.profile_photo
                                        ? 'http://localhost:8000/storage/' +
                                          trainer.profile_photo
                                        : `https://ui-avatars.com/api/?name=${trainer.name}&background=1a1a1a&color=7ED957`
                                "
                                class="w-10 h-10 rounded-full object-cover shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">
                                    {{ trainer.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Wants to add you as a client
                                </p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button
                                    @click="acceptTrainer(trainer.id)"
                                    class="bg-[#7ED957] text-black px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all"
                                >
                                    Accept
                                </button>
                                <button
                                    @click="declineTrainer(trainer.id)"
                                    class="bg-white/5 text-gray-400 px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-white/10 transition-all"
                                >
                                    Decline
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 space-y-4"
                    >
                        <p
                            class="text-xs text-gray-500 uppercase tracking-wider"
                        >
                            Account Info
                        </p>

                        <div>
                            <label class="text-xs text-gray-500 mb-1.5 block"
                                >Name</label
                            >
                            <input
                                v-model="name"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                            />
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1.5 block"
                                >Email</label
                            >
                            <input
                                v-model="email"
                                disabled
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm opacity-50 cursor-not-allowed"
                            />
                        </div>

                        <div
                            class="flex items-center justify-between bg-[#0a0a0a] border border-white/5 rounded-2xl px-4 py-3"
                        >
                            <span class="text-sm">Weight Unit</span>
                            <div class="flex bg-[#1a1a1a] rounded-xl p-1">
                                <button
                                    @click="setUnit('kg')"
                                    :class="
                                        unit === 'kg'
                                            ? 'bg-[#7ED957] text-black'
                                            : 'text-gray-500'
                                    "
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                                >
                                    KG
                                </button>
                                <button
                                    @click="setUnit('lbs')"
                                    :class="
                                        unit === 'lbs'
                                            ? 'bg-[#7ED957] text-black'
                                            : 'text-gray-500'
                                    "
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                                >
                                    LBS
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 space-y-4"
                    >
                        <p
                            class="text-xs text-gray-500 uppercase tracking-wider"
                        >
                            Security
                        </p>

                        <button
                            @click="showPassword = !showPassword"
                            class="w-full py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all"
                        >
                            {{ showPassword ? "Cancel" : "Change Password" }}
                        </button>

                        <div v-if="showPassword" class="space-y-3">
                            <input
                                v-model="currentPassword"
                                type="password"
                                placeholder="Current Password"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                            />
                            <input
                                v-model="newPassword"
                                type="password"
                                placeholder="New Password"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                            />
                            <input
                                v-model="confirmPassword"
                                type="password"
                                placeholder="Confirm Password"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                            />
                            <p
                                v-if="passwordError"
                                class="text-red-400 text-xs px-1"
                            >
                                {{ passwordError }}
                            </p>
                            <button
                                @click="updatePassword"
                                class="w-full py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all"
                            >
                                Update Password
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pb-4">
                        <button
                            @click="saveProfile"
                            class="flex-1 py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                        >
                            Save Changes
                        </button>
                        <button
                            @click="logout"
                            class="flex-1 py-3.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl font-bold text-sm hover:bg-red-500/20 transition-all md:hidden"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
