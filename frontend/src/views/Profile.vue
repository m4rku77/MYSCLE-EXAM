<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";

const route = useRoute();

const router = useRouter();
const role = localStorage.getItem("role");
const user = ref(null);
const loading = ref(true);
const name = ref("");
const email = ref("");
const goal = ref("");
const weight = ref("");
const height = ref("");
const age = ref("");
const gender = ref("");
const bio = ref("");
const file = ref(null);
const preview = ref(null);
const trainerRequests = ref([]);
const success = ref("");
const showPassword = ref(false);
const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");
const passwordError = ref("");
const unit = ref(localStorage.getItem("unit") || "kg");
const friendRequests = ref([]);
const original = ref({});

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const isTrainerMode = computed(() => route.path.startsWith("/trainer"));



const switchMode = () => {
    if (isTrainerMode.value) {
        router.push("/dashboard");
    } else {
        router.push("/trainer");
    }
};

const hasChanges = computed(
    () =>
        name.value !== original.value.name ||
        goal.value !== original.value.goal ||
        String(weight.value) !== String(original.value.weight) ||
        String(height.value) !== String(original.value.height) ||
        String(age.value) !== String(original.value.age) ||
        gender.value !== original.value.gender ||
        bio.value !== original.value.bio ||
        file.value !== null,
);

const setUnit = (value) => {
    if (value === "lbs" && unit.value === "kg" && weight.value) {
        weight.value = Math.round(Number(weight.value) * 2.20462 * 10) / 10;
    } else if (value === "kg" && unit.value === "lbs" && weight.value) {
        weight.value = Math.round((Number(weight.value) / 2.20462) * 10) / 10;
    }
    unit.value = value;
    localStorage.setItem("unit", value);
};

const fetchProfile = async () => {
    try {
        const res = await axios.get("https://myscle-exam-production.up.railway.app/api/me", {
            headers,
        });
        user.value = res.data;
        name.value = res.data.name;
        email.value = res.data.email;
        goal.value = res.data.goal ?? "";
        weight.value = res.data.weight ?? "";
        if (unit.value === "lbs" && weight.value) {
            weight.value = Math.round(Number(weight.value) * 2.20462 * 10) / 10;
        }
        height.value = res.data.height ?? "";
        age.value = res.data.age ?? "";
        gender.value = res.data.gender ?? "";
        bio.value = res.data.bio ?? "";
        original.value = {
            name: name.value,
            goal: goal.value,
            weight: weight.value,
            height: height.value,
            age: age.value,
            gender: gender.value,
            bio: bio.value,
        };
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const fetchTrainerRequests = async () => {
    try {
        const res = await axios.get(
            "https://myscle-exam-production.up.railway.app/api/my/trainer-requests",
            { headers },
        );
        trainerRequests.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

const fetchFriendRequests = async () => {
    try {
        const res = await axios.get(
            "https://myscle-exam-production.up.railway.app/api/friends/requests",
            { headers },
        );
        friendRequests.value = Array.isArray(res.data)
            ? res.data
            : res.data.data || [];
    } catch (err) {
        console.error(err);
    }
};

const saveProfile = async () => {
    try {
        await axios.put("https://myscle-exam-production.up.railway.app/api/me", {
            name: name.value,
            email: email.value,
            goal: goal.value || null,
            weight: weight.value ? Number(String(weight.value).replace(/[^0-9.]/g, '')) : null,
            height: height.value ? Number(String(height.value).replace(/[^0-9.]/g, '')) : null,
            age: age.value ? Number(age.value) : null,
            gender: gender.value || null,
            bio: bio.value || null,
        }, { headers });
        original.value = { name: name.value, goal: goal.value, weight: weight.value, height: height.value, age: age.value, gender: gender.value, bio: bio.value };
        success.value = "Profile updated";
        setTimeout(() => (success.value = ""), 3000);
    } catch (err) {
        console.log(err.response?.data?.errors);
    }
};

const updatePassword = async () => {
    if (newPassword.value !== confirmPassword.value) {
        passwordError.value = "Passwords do not match";
        return;
    }
    try {
        await axios.put(
        "https://myscle-exam-production.up.railway.app/api/me/password",
        {
            current_password: currentPassword.value,
            new_password: newPassword.value,
            new_password_confirmation: confirmPassword.value,
        },
        { headers }
    );
        success.value = "Password updated";
        passwordError.value = "";
        showPassword.value = false;
    } catch (err) {
        passwordError.value = err.response?.data?.message || "Error";
    }
};

const handleFile = async (e) => {
    file.value = e.target.files[0];
    preview.value = URL.createObjectURL(file.value);
    
    try {
        const formData = new FormData();
        formData.append("photo", file.value);
        const res = await axios.post(
            "https://myscle-exam-production.up.railway.app/api/me/photo", 
            formData, 
            { 
                headers: {
                    ...headers,
                    'Content-Type': 'multipart/form-data'
                }
            }
        );
        user.value.profile_photo = res.data.photo;
        preview.value = null;
        file.value = null;
        success.value = "Photo updated";
        setTimeout(() => (success.value = ""), 3000);
    } catch (err) {
        console.error("Photo error:", err.response?.data);
        console.error("Status:", err.response?.status);
    }
};

const acceptFriend = async (id) => {
    await axios.post(
        `https://myscle-exam-production.up.railway.app/api/friends/accept/${id}`,
        {},
        { headers },
    );
    fetchFriendRequests();
};
const declineFriend = async (id) => {
    await axios.delete(`https://myscle-exam-production.up.railway.app/api/friends/decline/${id}`, {
        headers,
    });
    fetchFriendRequests();
};
const acceptTrainer = async (id) => {
    await axios.post(
        `https://myscle-exam-production.up.railway.app/api/my/trainer-requests/accept/${id}`,
        {},
        { headers },
    );
    fetchTrainerRequests();
};
const declineTrainer = async (id) => {
    await axios.delete(
        `https://myscle-exam-production.up.railway.app/api/my/trainer-requests/decline/${id}`,
        { headers },
    );
    fetchTrainerRequests();
};
const logout = () => {
    localStorage.removeItem("token");
    window.location.href = "/login";
};

const subscription = ref(null);

const fetchSubscription = async () => {
    try {
        const res = await axios.get(
            "https://myscle-exam-production.up.railway.app/api/my/subscription",
            { headers },
        );
        subscription.value = res.data;
    } catch {}
};

const showCancelPopup = ref(false);

const cancelSubscription = async () => {
    try {
        await axios.delete("https://myscle-exam-production.up.railway.app/api/my/subscription", { headers });
        subscription.value = null;
        user.value.role = 'user';
        localStorage.setItem('role', 'user');
        showCancelPopup.value = false;
        success.value = "Subscription cancelled";
        setTimeout(() => router.push('/dashboard'), 1500);
    } catch (err) {
        console.error(err);
    }
};

onMounted(() => {
    fetchProfile();
    fetchTrainerRequests();
    fetchFriendRequests();
    fetchSubscription();
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
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-4 rounded-b-3xl shrink-0"
            >
                <p
                    class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                >
                    Account
                </p>
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-black">Profile</h1>
                    <div class="flex gap-2">
                        <button
                            v-if="hasChanges"
                            @click="saveProfile"
                            class="flex items-center gap-1.5 px-4 py-2 bg-black text-[#7ED957] rounded-xl text-sm font-bold hover:bg-black/80 transition-all"
                        >
                            <i class="fas fa-check text-xs"></i> Save
                        </button>
                    </div>
                </div>
                <div
                    v-if="success"
                    class="mt-3 bg-black/15 text-black text-xs py-2 rounded-xl text-center font-semibold"
                >
                    {{ success }}
                </div>
            </div>
            <div
                class="hidden md:flex bg-[#0f0f0f] border-b border-white/5 px-8 py-4 shrink-0 items-center justify-between"
            >
                <div>
                    <p
                        class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-0.5"
                    >
                        Account
                    </p>
                    <h1 class="text-2xl font-bold">Profile</h1>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        v-if="success"
                        class="bg-[#7ED957]/10 border border-[#7ED957]/30 text-[#7ED957] text-xs py-2 px-4 rounded-xl font-semibold"
                    >
                        {{ success }}
                    </div>

                    <button
                        v-if="hasChanges"
                        @click="saveProfile"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#7ED957] text-black rounded-xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                    >
                        <i class="fas fa-check"></i> Save Changes
                    </button>
                </div>
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-8 pt-6 pb-32 md:pb-10"
            >
                <div class="md:hidden max-w-2xl mx-auto space-y-5">
                    <div
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 flex flex-col items-center gap-5"
                    >
                        <div class="relative">
                            <img
                                :src="
                                    preview
                                        ? preview
                                        : user?.profile_photo
                                          ? 'https://myscle-exam-production.up.railway.app/storage/' +
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
                        <button
                            @click="logout"
                            class="flex items-center gap-2 px-4 py-2.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl font-semibold text-sm hover:bg-red-500/20 transition-all"
                        >
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                        <button
                            v-if="role === 'user'"
                            @click="router.push('/upgrade')"
                            class="flex items-center gap-2 px-4 py-2 bg-[#7ED957] text-black rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                        >
                            <i class="fas fa-crown text-xs"></i> Upgrade to
                            Trainer
                        </button>
                        <button
                            v-if="role === 'trainer'"
                            @click="switchMode"
                            class="flex items-center gap-2 px-4 py-2 bg-[#7ED957]/10 border border-[#7ED957]/20 text-[#7ED957] rounded-xl text-xs font-semibold hover:bg-[#7ED957]/20 transition-all"
                        >
                            <i
                                :class="
                                    isTrainerMode
                                        ? 'fas fa-dumbbell'
                                        : 'fas fa-crown'
                                "
                                class="text-xs"
                            ></i>
                            {{
                                isTrainerMode
                                    ? "Switch to Athlete"
                                    : "Switch to Trainer"
                            }}
                        </button>
                        <button
                            v-if="subscription && user?.role === 'trainer'"
                            @click="cancelSubscription"
                            class="flex items-center gap-2 px-4 py-2.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl font-semibold text-sm hover:bg-red-500/20 transition-all"
                        >
                            <i class="fas fa-times-circle text-xs"></i> Cancel
                            Subscription
                        </button>
                    </div>

                    <div
                        v-if="friendRequests.length > 0"
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 space-y-4"
                    >
                        <p
                            class="text-xs text-gray-500 uppercase tracking-wider"
                        >
                            Friend Requests
                            <span
                                class="ml-2 bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20 px-2 py-0.5 rounded-full text-xs font-bold"
                                >{{ friendRequests.length }}</span
                            >
                        </p>
                        <div
                            v-for="req in friendRequests"
                            :key="req.id"
                            class="flex items-center gap-4 bg-[#0a0a0a] rounded-2xl p-4 border border-white/5"
                        >
                            <img
                                :src="
                                    req.profile_photo
                                        ? 'https://myscle-exam-production.up.railway.app/storage/' +
                                          req.profile_photo
                                        : `https://ui-avatars.com/api/?name=${req.name}&background=1a1a1a&color=7ED957`
                                "
                                class="w-10 h-10 rounded-full object-cover shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">
                                    {{ req.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Wants to be your friend
                                </p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button
                                    @click="acceptFriend(req.id)"
                                    class="bg-[#7ED957] text-black px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all"
                                >
                                    Accept
                                </button>
                                <button
                                    @click="declineFriend(req.id)"
                                    class="bg-white/5 text-gray-400 px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-white/10 transition-all"
                                >
                                    Decline
                                </button>
                            </div>
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
                                        ? 'https://myscle-exam-production.up.railway.app/storage/' +
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
                            ><input
                                v-model="name"
                                
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                            />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1.5 block"
                                >Email</label
                            ><input
                                v-model="email"
                                min="0"
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
                            Fitness Profile
                        </p>
                        <div>
                            <label class="text-xs text-gray-500 mb-1.5 block"
                                >Goal</label
                            ><select
                                v-model="goal"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all appearance-none"
                            >
                                <option value="">Select goal</option>
                                <option value="lose_weight">Lose Weight</option>
                                <option value="build_muscle">
                                    Build Muscle
                                </option>
                                <option value="increase_strength">
                                    Increase Strength
                                </option>
                                <option value="improve_endurance">
                                    Improve Endurance
                                </option>
                                <option value="stay_fit">Stay Fit</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="text-xs text-gray-500 mb-1.5 block"
                                    >Weight ({{ unit }})</label
                                ><input
                                    v-model="weight"
                                    min="0"
                                    type="number"
                                    placeholder="e.g. 75"
                                    class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                />
                            </div>
                            <div>
                                <label
                                    class="text-xs text-gray-500 mb-1.5 block"
                                    >Height (cm)</label
                                ><input
                                    v-model="height"
                                    min="0"
                                    type="number"
                                    placeholder="e.g. 180"
                                    class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="text-xs text-gray-500 mb-1.5 block"
                                    >Age</label
                                ><input
                                    v-model="age"
                                    min="0"
                                    type="number"
                                    placeholder="e.g. 25"
                                    class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                />
                            </div>
                            <div>
                                <label
                                    class="text-xs text-gray-500 mb-1.5 block"
                                    >Gender</label
                                ><select
                                    v-model="gender"
                                    class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all appearance-none"
                                >
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1.5 block"
                                >Bio</label
                            ><textarea
                                v-model="bio"
                                placeholder="Tell us about yourself..."
                                rows="3"
                                class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all resize-none"
                            ></textarea>
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
                </div>

                <div class="hidden md:block max-w-6xl mx-auto">
                    <div
                        v-if="
                            friendRequests.length > 0 ||
                            trainerRequests.length > 0
                        "
                        class="grid grid-cols-2 gap-5 mb-5"
                    >
                        <div
                            v-if="friendRequests.length > 0"
                            class="bg-[#111] border border-white/5 rounded-3xl p-6 space-y-4"
                        >
                            <p
                                class="text-xs text-gray-500 uppercase tracking-wider"
                            >
                                Friend Requests
                                <span
                                    class="ml-2 bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20 px-2 py-0.5 rounded-full text-xs font-bold"
                                    >{{ friendRequests.length }}</span
                                >
                            </p>
                            <div
                                v-for="req in friendRequests"
                                :key="req.id"
                                class="flex items-center gap-4 bg-[#0a0a0a] rounded-2xl p-4 border border-white/5"
                            >
                                <img
                                    :src="
                                        req.profile_photo
                                            ? 'https://myscle-exam-production.up.railway.app/storage/' +
                                              req.profile_photo
                                            : `https://ui-avatars.com/api/?name=${req.name}&background=1a1a1a&color=7ED957`
                                    "
                                    class="w-10 h-10 rounded-full object-cover shrink-0"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm truncate">
                                        {{ req.name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Wants to be your friend
                                    </p>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <button
                                        @click="acceptFriend(req.id)"
                                        class="bg-[#7ED957] text-black px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-[#6bc947] transition-all"
                                    >
                                        Accept
                                    </button>
                                    <button
                                        @click="declineFriend(req.id)"
                                        class="bg-white/5 text-gray-400 px-3 py-1.5 rounded-xl text-xs font-semibold hover:bg-white/10 transition-all"
                                    >
                                        Decline
                                    </button>
                                </div>
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
                                            ? 'https://myscle-exam-production.up.railway.app/storage/' +
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
                    </div>

                    <div class="grid grid-cols-3 gap-5 items-stretch">
                        <div
                            class="bg-[#111] border border-white/5 rounded-3xl p-6 flex flex-col gap-6 h-full"
                        >
                            <div class="flex flex-col items-center gap-4">
                                <div class="relative">
                                    <img
                                        :src="
                                            preview
                                                ? preview
                                                : user?.profile_photo
                                                  ? 'https://myscle-exam-production.up.railway.app/storage/' +
                                                    user.profile_photo
                                                  : `https://ui-avatars.com/api/?name=${user?.name}&background=1a1a1a&color=7ED957`
                                        "
                                        class="w-28 h-28 rounded-full object-cover ring-4 ring-[#7ED957]/20"
                                    />
                                    <label
                                        class="absolute bottom-0 right-0 w-9 h-9 bg-[#7ED957] rounded-full flex items-center justify-center cursor-pointer hover:bg-[#6bc947] transition-all"
                                    >
                                        <i
                                            class="fas fa-camera text-black text-sm"
                                        ></i>
                                        <input
                                            type="file"
                                            @change="handleFile"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold">
                                        {{ user?.name }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        {{ email }}
                                    </p>
                                </div>
                                <div
                                    class="w-full grid grid-cols-2 gap-2 text-center"
                                >
                                    <div
                                        class="bg-[#0a0a0a] rounded-2xl p-3 border border-white/5"
                                    >
                                        <p class="text-xs text-gray-500 mb-1">
                                            Workouts
                                        </p>
                                        <p
                                            class="text-xl font-bold text-[#7ED957]"
                                        >
                                            {{ user?.completed_workouts ?? 0 }}
                                        </p>
                                    </div>
                                <div class="bg-[#0a0a0a] rounded-2xl p-3 border border-white/5">
                                    <p class="text-xs text-gray-500 mb-1">Role</p>
                                    <p class="text-sm font-bold capitalize">{{ user?.role ?? "—" }}</p>
                                    <button v-if="subscription && user?.role === 'trainer'" @click="showCancelPopup = true"
                                        class="mt-2 w-full text-xs bg-red-500/10 border border-red-500/20 text-red-400 py-1.5 rounded-xl hover:bg-red-500/20 transition-all">
                                        Cancel subscription
                                    </button>
                                </div>
                            </div>
                            </div>
                            <div class="border-t border-white/5 pt-5 space-y-3">
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider"
                                >
                                    Security
                                </p>
                                <button
                                    @click="showPassword = !showPassword"
                                    class="w-full py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all"
                                >
                                    {{
                                        showPassword
                                            ? "Cancel"
                                            : "Change Password"
                                    }}
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
                        </div>

                        <div
                            class="bg-[#111] border border-white/5 rounded-3xl p-6 flex flex-col gap-5 h-full"
                        >
                            <div class="space-y-4">
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider"
                                >
                                    Account Info
                                </p>
                                <div>
                                    <label
                                        class="text-xs text-gray-500 mb-1.5 block"
                                        >Name</label
                                    ><input
                                        v-model="name"
                                        class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="text-xs text-gray-500 mb-1.5 block"
                                        >Email</label
                                    ><input
                                        v-model="email"
                                        disabled
                                        class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm opacity-50 cursor-not-allowed"
                                    />
                                </div>
                                <div
                                    class="flex items-center justify-between bg-[#0a0a0a] border border-white/5 rounded-2xl px-4 py-3"
                                >
                                    <span class="text-sm">Weight Unit</span>
                                    <div
                                        class="flex bg-[#1a1a1a] rounded-xl p-1"
                                    >
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
                                class="border-t border-white/5 pt-5 flex-1 flex flex-col"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-3"
                                >
                                    Bio
                                </p>
                                <textarea
                                    v-model="bio"
                                    placeholder="Tell us about yourself..."
                                    class="flex-1 w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all resize-none min-h-[120px]"
                                ></textarea>
                            </div>
                        </div>

                        <div
                            class="bg-[#111] border border-white/5 rounded-3xl p-6 flex flex-col gap-5 h-full"
                        >
                            <div class="space-y-4 flex-1">
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider"
                                >
                                    Fitness Profile
                                </p>
                                <div>
                                    <label
                                        class="text-xs text-gray-500 mb-1.5 block"
                                        >Goal</label
                                    >
                                    <select
                                        v-model="goal"
                                        class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all appearance-none"
                                    >
                                        <option value="">Select goal</option>
                                        <option value="lose_weight">
                                            Lose Weight
                                        </option>
                                        <option value="build_muscle">
                                            Build Muscle
                                        </option>
                                        <option value="increase_strength">
                                            Increase Strength
                                        </option>
                                        <option value="improve_endurance">
                                            Improve Endurance
                                        </option>
                                        <option value="stay_fit">
                                            Stay Fit
                                        </option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 mb-1.5 block"
                                            >Weight ({{ unit }})</label
                                        ><input
                                            v-model="weight"
                                            min="0"
                                            type="number"
                                            placeholder="e.g. 75"
                                            class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 mb-1.5 block"
                                            >Height (cm)</label
                                        ><input
                                            v-model="height"
                                            min="0"
                                            type="number"
                                            placeholder="e.g. 180"
                                            class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                        />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 mb-1.5 block"
                                            >Age</label
                                        ><input
                                            v-model="age"
                                            min="0"
                                            type="number"
                                            placeholder="e.g. 25"
                                            class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 mb-1.5 block"
                                            >Gender</label
                                        >
                                        <select
                                            v-model="gender"
                                            class="w-full px-4 py-3 bg-[#0a0a0a] border border-white/5 rounded-2xl text-sm outline-none focus:border-[#7ED957] transition-all appearance-none"
                                        >
                                            <option value="">
                                                Select gender
                                            </option>
                                            <option value="male">Male</option>
                                            <option value="female">
                                                Female
                                            </option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div v-if="showCancelPopup"
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center px-5"
    @click.self="showCancelPopup = false">
    <div class="w-full max-w-md bg-[#111] border border-white/10 rounded-3xl p-6">
        <div class="w-12 h-12 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-times-circle text-red-400 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-center mb-2">Cancel Subscription</h3>
        <p class="text-gray-400 text-sm text-center mb-6">
            Are you sure you want to cancel your trainer subscription? You will lose access to all trainer features immediately.
        </p>
        <div class="flex gap-3">
            <button
                @click="showCancelPopup = false"
                class="flex-1 py-3 bg-white/5 border border-white/10 rounded-2xl text-sm font-semibold hover:bg-white/10 transition-all">
                Keep Subscription
            </button>
            <button
                @click="cancelSubscription"
                class="flex-1 py-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl text-sm font-semibold hover:bg-red-500/20 transition-all">
                Yes, Cancel
            </button>
        </div>
    </div>
</div>
</template>