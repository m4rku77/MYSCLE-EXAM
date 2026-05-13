<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

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
    <div class="min-h-screen bg-[#0f0f0f] text-white flex flex-col">
        <div
            class="flex-1 overflow-y-auto px-4 pt-6 pb-40 md:pb-12 space-y-6 w-full max-w-2xl md:max-w-6xl mx-auto"
        >
            <div
                v-if="success"
                class="bg-green-500 text-black text-sm py-3 rounded-xl text-center font-semibold"
            >
                {{ success }}
            </div>

            <div
                class="bg-[#1a1a1a] rounded-2xl p-6 md:p-8 flex flex-col items-center gap-5 border border-white/5"
            >
                <img
                    :src="
                        preview
                            ? preview
                            : user?.profile_photo
                              ? 'http://localhost:8000/storage/' +
                                user.profile_photo
                              : `https://ui-avatars.com/api/?name=${user?.name}`
                    "
                    class="w-24 h-24 rounded-full object-cover border border-gray-700"
                />

                <div class="text-center">
                    <p class="text-lg font-semibold">{{ user?.name }}</p>
                    <p class="text-xs text-gray-400">{{ email }}</p>
                </div>

                <label
                    class="text-xs bg-[#2a2a2a] px-4 py-2 rounded-lg cursor-pointer hover:bg-[#333]"
                >
                    Change Photo
                    <input type="file" @change="handleFile" class="hidden" />
                </label>
            </div>

            <div
                v-if="trainerRequests.length > 0"
                class="bg-[#1a1a1a] rounded-2xl p-5 space-y-4 border border-white/5"
            >
                <h3 class="text-xs text-gray-400 uppercase">
                    Trainer Requests
                </h3>

                <div
                    v-for="trainer in trainerRequests"
                    :key="trainer.id"
                    class="flex items-center gap-4 bg-[#0f0f0f] rounded-xl p-4 border border-white/5"
                >
                    <img
                        :src="
                            trainer.profile_photo
                                ? 'http://localhost:8000/storage/' +
                                  trainer.profile_photo
                                : `https://ui-avatars.com/api/?name=${trainer.name}`
                        "
                        class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                    />

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ trainer.name }}</p>
                        <p class="text-xs text-gray-400">
                            Wants to add you as a client
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            @click="acceptTrainer(trainer.id)"
                            class="bg-[#7ED957] text-black px-3 py-1.5 rounded-lg text-xs font-semibold"
                        >
                            Accept
                        </button>

                        <button
                            @click="declineTrainer(trainer.id)"
                            class="bg-white/10 text-white px-3 py-1.5 rounded-lg text-xs font-semibold"
                        >
                            Decline
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="bg-[#1a1a1a] rounded-2xl p-5 space-y-6 border border-white/5"
            >
                <h3 class="text-xs text-gray-400 uppercase">Account</h3>

                <input
                    v-model="name"
                    class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm"
                />

                <input
                    v-model="email"
                    disabled
                    class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl text-sm opacity-60"
                />

                <div
                    class="flex items-center justify-between bg-[#0f0f0f] border border-gray-700 rounded-xl px-4 py-3"
                >
                    <span class="text-sm">Units</span>

                    <div class="flex bg-[#1a1a1a] rounded-lg p-1">
                        <button
                            @click="setUnit('kg')"
                            :class="
                                unit === 'kg'
                                    ? 'bg-[#7ED957] text-black'
                                    : 'text-gray-400'
                            "
                            class="px-3 py-1 rounded-md text-xs"
                        >
                            KG
                        </button>

                        <button
                            @click="setUnit('lbs')"
                            :class="
                                unit === 'lbs'
                                    ? 'bg-[#7ED957] text-black'
                                    : 'text-gray-400'
                            "
                            class="px-3 py-1 rounded-md text-xs"
                        >
                            LBS
                        </button>
                    </div>
                </div>

                <button
                    @click="showPassword = !showPassword"
                    class="w-full py-3 bg-[#2a2a2a] rounded-xl text-sm"
                >
                    {{ showPassword ? "Cancel" : "Change Password" }}
                </button>

                <div v-if="showPassword" class="space-y-3">
                    <input
                        v-model="currentPassword"
                        type="password"
                        placeholder="Current Password"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl"
                    />

                    <input
                        v-model="newPassword"
                        type="password"
                        placeholder="New Password"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl"
                    />

                    <input
                        v-model="confirmPassword"
                        type="password"
                        placeholder="Confirm Password"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-gray-700 rounded-xl"
                    />

                    <p v-if="passwordError" class="text-red-500 text-xs">
                        {{ passwordError }}
                    </p>

                    <button
                        @click="updatePassword"
                        class="w-full py-3 bg-[#7ED957] text-black rounded-xl"
                    >
                        Update Password
                    </button>
                </div>
            </div>

            <div
                class="fixed bottom-16 left-0 w-full p-4 bg-[#0f0f0f] border-t border-gray-800 md:static md:border-0"
            >
                <div class="flex flex-col md:flex-row gap-3 md:justify-center">
                    <button
                        @click="saveProfile"
                        class="px-10 py-3 bg-[#7ED957] text-black rounded-xl font-semibold"
                    >
                        Save Changes
                    </button>

                    <button
                        @click="logout"
                        class="px-8 py-3 bg-red-500 rounded-xl font-semibold"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
