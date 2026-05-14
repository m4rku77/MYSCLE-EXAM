<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const client = ref(null);
const loading = ref(true);
const saving = ref(false);

const notes = ref([]);
const newNote = ref("");

const stats = ref({
    workouts: 0,
    weight: "",
    goal: "",
    height: "",
    age: "",
    gender: "",
    bio: "",
});

const showPopup = ref(false);
const popupTitle = ref("");
const popupMessage = ref("");

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const getImage = (path, name) => {
    if (!path)
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;
    if (path.startsWith("http")) return path;
    return `http://localhost:8000/storage/${path.replace("storage/", "")}`;
};

const fetchClient = async () => {
    const res = await axios.get(
        `http://localhost:8000/api/users/${route.params.id}`,
        { headers },
    );
    client.value = res.data.data ?? res.data;
    stats.value = {
        workouts: client.value.completed_workouts ?? 0,
        weight: client.value.weight ?? "",
        goal: client.value.goal ?? "",
        height: client.value.height ?? "",
        age: client.value.age ?? "",
        gender: client.value.gender ?? "",
        bio: client.value.bio ?? "",
    };
};

const formatDate = (date) => {
    if (!date) return "Now";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const fetchNotes = async () => {
    try {
        const res = await axios.get(
            `http://localhost:8000/api/trainer/client/${route.params.id}/notes`,
            { headers },
        );
        notes.value = res.data.data ?? res.data;
    } catch {
        notes.value = [];
    }
};

onMounted(async () => {
    loading.value = true;
    await Promise.all([fetchClient(), fetchNotes()]);
    loading.value = false;
});

const saveClientInfo = async () => {
    try {
        saving.value = true;
        await axios.put(
            `http://localhost:8000/api/trainer/client/${route.params.id}`,
            {
                weight: stats.value.weight,
                goal: stats.value.goal,
                height: stats.value.height,
                age: stats.value.age,
                gender: stats.value.gender,
                bio: stats.value.bio,
            },
            { headers },
        );
        popupTitle.value = "Saved";
        popupMessage.value = "Client information updated successfully.";
        showPopup.value = true;
    } catch {
        popupTitle.value = "Error";
        popupMessage.value = "Failed to save client information.";
        showPopup.value = true;
    } finally {
        saving.value = false;
    }
};

const addNote = async () => {
    if (!newNote.value.trim()) return;
    try {
        const res = await axios.post(
            `http://localhost:8000/api/trainer/client/${route.params.id}/notes`,
            { note: newNote.value },
            { headers },
        );
        notes.value.unshift(res.data.data ?? res.data);
        newNote.value = "";
    } catch (err) {
        console.error(err);
    }
};

const deleteNote = async (id) => {
    try {
        await axios.delete(
            `http://localhost:8000/api/trainer/client/notes/${id}`,
            { headers },
        );
        notes.value = notes.value.filter((n) => n.id !== id);
    } catch (err) {
        console.error(err);
    }
};

const openMessages = () => {
    router.push(`/trainer/messages/${client.value.id}`);
};
</script>

<template>
    <div class="min-h-screen bg-[#0b0b0b] text-white">
        <div
            v-if="loading"
            class="h-screen flex items-center justify-center text-gray-400"
        >
            Loading client...
        </div>

        <div v-else-if="client">
            <div class="border-b border-white/5 bg-[#111111]">
                <div class="max-w-5xl mx-auto px-5 py-8">
                    <button
                        @click="router.back()"
                        class="text-gray-400 hover:text-white transition mb-6"
                    >
                        ← Back
                    </button>

                    <div
                        class="flex flex-col md:flex-row md:items-center gap-6"
                    >
                        <img
                            :src="getImage(client.profile_photo, client.name)"
                            class="w-24 h-24 rounded-3xl object-cover border border-white/10"
                        />

                        <div class="flex-1">
                            <h1 class="text-4xl font-bold">
                                {{ client.name }}
                            </h1>
                            <p class="text-gray-400 mt-2">{{ client.email }}</p>

                            <div class="flex flex-wrap gap-3 mt-6">
                                <button
                                    @click="openMessages"
                                    class="bg-[#7ED957] text-black px-5 py-3 rounded-2xl font-semibold"
                                >
                                    Message
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-5 py-6 space-y-6 pb-32">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        class="bg-[#151515] border border-white/5 rounded-3xl p-6"
                    >
                        <p class="text-sm text-gray-500">Completed Workouts</p>
                        <h3 class="text-3xl font-bold mt-2">
                            {{ stats.workouts }}
                        </h3>
                    </div>

                    <div
                        class="bg-[#151515] border border-white/5 rounded-3xl p-6"
                    >
                        <p class="text-sm text-gray-500 mb-3">Current Weight</p>
                        <input
                            v-model="stats.weight"
                            placeholder="e.g. 82kg"
                            class="w-full bg-[#101010] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                        />
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-3xl p-6">
                    <div
                        class="flex items-center justify-between flex-wrap gap-4 mb-6"
                    >
                        <div>
                            <h2 class="text-2xl font-bold">Client Metrics</h2>
                            <p class="text-gray-500 mt-1">
                                Editable athlete data
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-[#101010] rounded-2xl p-5">
                            <p class="text-sm text-gray-500 mb-3">Goal</p>
                            <input
                                v-model="stats.goal"
                                placeholder="e.g. Muscle Gain"
                                class="w-full bg-[#181818] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                            />
                        </div>

                        <div class="bg-[#101010] rounded-2xl p-5">
                            <p class="text-sm text-gray-500 mb-3">Height</p>
                            <input
                                v-model="stats.height"
                                placeholder="e.g. 182cm"
                                class="w-full bg-[#181818] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                            />
                        </div>

                        <div class="bg-[#101010] rounded-2xl p-5">
                            <p class="text-sm text-gray-500 mb-3">Age</p>
                            <input
                                v-model="stats.age"
                                type="number"
                                placeholder="e.g. 23"
                                class="w-full bg-[#181818] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                            />
                        </div>

                        <div class="bg-[#101010] rounded-2xl p-5">
                            <p class="text-sm text-gray-500 mb-3">Gender</p>
                            <select
                                v-model="stats.gender"
                                class="w-full bg-[#181818] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                            >
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="bg-[#101010] rounded-2xl p-5 md:col-span-2">
                            <p class="text-sm text-gray-500 mb-3">Bio</p>
                            <textarea
                                v-model="stats.bio"
                                placeholder="Client bio..."
                                rows="3"
                                class="w-full bg-[#181818] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957] resize-none"
                            />
                        </div>
                    </div>

                    <button
                        @click="saveClientInfo"
                        :disabled="saving"
                        class="mt-6 w-full py-3 bg-[#7ED957] text-black rounded-2xl font-semibold"
                    >
                        {{ saving ? "Saving..." : "Save Changes" }}
                    </button>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-3xl p-6">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold">Coach Notes</h2>
                        <p class="text-gray-500 mt-1">Private trainer notes</p>
                    </div>

                    <div class="flex gap-3 mb-6">
                        <input
                            v-model="newNote"
                            placeholder="Write a note..."
                            class="flex-1 bg-[#101010] border border-white/10 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957]"
                        />
                        <button
                            @click="addNote"
                            class="bg-[#7ED957] text-black px-5 rounded-2xl font-semibold"
                        >
                            Add
                        </button>
                    </div>

                    <div v-if="notes.length > 0" class="space-y-4">
                        <div
                            v-for="note in notes"
                            :key="note.id"
                            class="bg-[#101010] border border-white/5 rounded-2xl p-5"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-[#7ED957]">{{
                                    formatDate(note.created_at)
                                }}</span>
                                <button
                                    @click="deleteNote(note.id)"
                                    class="text-red-400 text-sm hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </div>
                            <p class="text-gray-300 leading-relaxed">
                                {{ note.note }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="text-center text-gray-500 py-10">
                        No notes yet
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showPopup"
            class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
            @click.self="showPopup = false"
        >
            <div
                class="bg-[#151515] border border-white/10 rounded-3xl p-6 w-full max-w-md shadow-2xl mx-4"
            >
                <h3 class="text-xl font-bold text-white mb-3">
                    {{ popupTitle }}
                </h3>
                <p class="text-gray-300 mb-6">{{ popupMessage }}</p>
                <div class="flex justify-end">
                    <button
                        @click="showPopup = false"
                        class="bg-[#1a1a1a] border border-white/10 px-4 py-2 rounded-2xl text-sm"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
