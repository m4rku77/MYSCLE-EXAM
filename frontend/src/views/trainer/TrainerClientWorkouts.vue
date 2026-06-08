<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const clientId = route.params.id;
const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const client = ref(null);
const workouts = ref([]);
const loading = ref(true);
const search = ref("");

const showModal = ref(false);
const newWorkoutName = ref("");
const creating = ref(false);

onMounted(async () => {
    try {
        const [clientRes, workoutsRes] = await Promise.all([
            axios.get(`https://myscle-exam-production.up.railway.app/api/users/${clientId}`, {
                headers,
            }),
            axios.get(
                `https://myscle-exam-production.up.railway.app/api/trainer/client/${clientId}/workouts`,
                { headers },
            ),
        ]);
        client.value = clientRes.data.data ?? clientRes.data;
        workouts.value = workoutsRes.data.data ?? workoutsRes.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const openWorkout = (workout) => {
    router.push(`/trainer/client/${clientId}/workouts/${workout.id}`);
};

const createWorkout = async () => {
    if (!newWorkoutName.value.trim()) return;
    creating.value = true;
    try {
        const res = await axios.post(
            `https://myscle-exam-production.up.railway.app/api/trainer/client/${clientId}/workouts`,
            { name: newWorkoutName.value },
            { headers },
        );
        const created = res.data.data ?? res.data;
        workouts.value.push({ ...created, exercises: [] });
        showModal.value = false;
        newWorkoutName.value = "";
        router.push(`/trainer/client/${clientId}/workouts/${created.id}`);
    } catch (err) {
        console.error(err);
    } finally {
        creating.value = false;
    }
};

const filtered = computed(() =>
    workouts.value.filter((w) =>
        w.name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);
</script>

<template>
    <div class="min-h-screen bg-[#080808] text-white flex flex-col">
        <div class="hidden md:flex h-screen">
            <main class="flex-1 p-10">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <button
                                @click="router.back()"
                                class="flex items-center gap-2 text-gray-500 hover:text-white transition-all text-sm font-semibold mb-3"
                            >
                                <i class="fas fa-arrow-left text-xs"></i> Back
                                to Client
                            </button>
                            <p
                                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
                            >
                                {{ client?.name }}
                            </p>
                            <h1 class="text-4xl font-black">Workout Plans</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"
                                ></i>
                                <input
                                    v-model="search"
                                    placeholder="Search workouts..."
                                    class="pl-10 pr-4 py-3 bg-[#111] border border-white/5 rounded-2xl outline-none focus:border-[#7ED957] text-sm w-64 transition-all"
                                />
                            </div>
                            <button
                                @click="showModal = true"
                                class="flex items-center gap-2 px-5 py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                            >
                                <i class="fas fa-plus text-xs"></i> New Workout
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="loading"
                        class="flex items-center justify-center py-20 text-gray-600"
                    >
                        <i class="fas fa-spinner fa-spin mr-3"></i> Loading...
                    </div>

                    <div
                        v-else-if="filtered.length > 0"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <div
                            v-for="w in filtered"
                            :key="w.id"
                            @click="openWorkout(w)"
                            class="group bg-[#111] border border-white/5 rounded-3xl p-6 cursor-pointer hover:border-[#7ED957]/30 hover:-translate-y-1 transition-all duration-300"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-10 h-10 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#7ED957]/20 transition-all"
                                >
                                    <i
                                        class="fas fa-dumbbell text-[#7ED957] text-sm"
                                    ></i>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-700 group-hover:text-[#7ED957] transition-colors"
                                ></i>
                            </div>
                            <h3
                                class="font-bold text-lg mb-1 group-hover:text-[#7ED957] transition-colors"
                            >
                                {{ w.name }}
                            </h3>
                            <p class="text-gray-600 text-xs mb-4">
                                {{ w.exercises?.length || 0 }} exercises
                            </p>
                            <div class="flex items-center gap-4 text-xs">
                                <div class="bg-white/5 rounded-xl px-3 py-1.5">
                                    <span class="text-[#7ED957] font-bold">{{
                                        w.exercises?.length || 0
                                    }}</span>
                                    <span class="text-gray-500 ml-1"
                                        >exercises</span
                                    >
                                </div>
                                <div class="bg-white/5 rounded-xl px-3 py-1.5">
                                    <span class="text-[#7ED957] font-bold">{{
                                        w.exercises?.reduce((s, e) => s + (e.sets?.length || e.exercise_sets?.length || 0), 0)
                                    }}</span>
                                    <span class="text-gray-500 ml-1">sets</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-24 text-gray-600"
                    >
                        <div
                            class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center mb-4"
                        >
                            <i class="fas fa-dumbbell text-2xl opacity-40"></i>
                        </div>
                        <p class="font-semibold mb-1">No workouts yet</p>
                        <p class="text-sm text-gray-700 mb-6">
                            Create the first workout plan for this client
                        </p>
                        <button
                            @click="showModal = true"
                            class="px-6 py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all"
                        >
                            Create Workout
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <div class="md:hidden flex flex-col h-[100dvh]">
            <div
                class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0"
            >
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <button
                            @click="router.back()"
                            class="flex items-center gap-1.5 text-black/60 text-xs font-semibold mb-2"
                        >
                            <i class="fas fa-arrow-left text-xs"></i> Back
                        </button>
                        <p
                            class="text-black/50 text-xs font-semibold uppercase tracking-wider"
                        >
                            {{ client?.name }}
                        </p>
                        <h1 class="text-3xl font-black">Workouts</h1>
                    </div>
                    <div
                        class="w-10 h-10 bg-black/10 rounded-2xl flex items-center justify-center"
                    >
                        <i class="fas fa-dumbbell text-black"></i>
                    </div>
                </div>
                <div class="relative">
                    <i
                        class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-black/40 text-sm"
                    ></i>
                    <input
                        v-model="search"
                        placeholder="Search workouts..."
                        class="w-full pl-10 pr-4 py-3 bg-black/15 rounded-2xl outline-none placeholder-black/40 text-black text-sm font-medium"
                    />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-5 pb-28 space-y-3">
                <div
                    v-if="loading"
                    class="flex items-center justify-center py-20 text-gray-600"
                >
                    <i class="fas fa-spinner fa-spin mr-3"></i>
                </div>

                <template v-else-if="filtered.length > 0">
                    <div
                        v-for="w in filtered"
                        :key="w.id"
                        @click="openWorkout(w)"
                        class="bg-[#111] border border-white/5 rounded-2xl p-5 cursor-pointer active:scale-[0.98] transition-all"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-base truncate mb-1">
                                    {{ w.name }}
                                </h3>
                                <div
                                    class="flex items-center gap-3 text-xs text-gray-500"
                                >
                                    <span
                                        ><span
                                            class="text-[#7ED957] font-bold"
                                            >{{
                                                w.exercises?.length || 0
                                            }}</span
                                        >
                                        exercises</span
                                    >
                                    <span
                                        class="w-1 h-1 bg-gray-700 rounded-full"
                                    ></span>
                                    <span
                                        ><span
                                            class="text-[#7ED957] font-bold"
                                            >{{
                                                w.exercises?.reduce(
                                                    (s, e) =>
                                                        s +
                                                        (e.exercise_sets
                                                            ?.length || 0),
                                                    0,
                                                ) || 0
                                            }}</span
                                        >
                                        sets</span
                                    >
                                </div>
                            </div>
                            <i
                                class="fas fa-chevron-right text-gray-700 ml-4"
                            ></i>
                        </div>
                    </div>
                </template>

                <div
                    v-else
                    class="flex flex-col items-center justify-center py-20 text-gray-600"
                >
                    <div
                        class="w-14 h-14 bg-white/5 rounded-3xl flex items-center justify-center mb-3"
                    >
                        <i class="fas fa-dumbbell text-xl opacity-40"></i>
                    </div>
                    <p class="font-semibold text-sm">No workouts yet</p>
                    <p class="text-xs text-gray-700 mt-1">
                        Tap + to create the first plan
                    </p>
                </div>
            </div>

            <button
                @click="showModal = true"
                class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50"
            >
                <i class="fas fa-plus text-lg"></i>
            </button>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center px-5"
            @click.self="showModal = false"
        >
            <div
                class="w-full max-w-md bg-[#111] border border-white/10 rounded-3xl p-6"
            >
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold">New Workout</h2>
                    <button
                        @click="showModal = false"
                        class="text-gray-500 hover:text-white w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/5 transition-all text-lg"
                    >
                        ×
                    </button>
                </div>
                <input
                    v-model="newWorkoutName"
                    placeholder="e.g. Push Day, Leg Day..."
                    class="w-full bg-[#0a0a0a] border border-white/5 rounded-2xl px-4 py-3 outline-none focus:border-[#7ED957] text-sm mb-4 transition-all"
                    autofocus
                    @keyup.enter="createWorkout"
                />
                <button
                    @click="createWorkout"
                    :disabled="creating || !newWorkoutName.trim()"
                    class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all disabled:opacity-50 shadow-lg shadow-[#7ED957]/20"
                >
                    {{ creating ? "Creating..." : "Create Workout" }}
                </button>
            </div>
        </div>
    </div>
</template>
