<script setup>
import { ref, onMounted } from "vue";
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

const showModal = ref(false);
const newWorkoutName = ref("");
const creating = ref(false);

onMounted(async () => {
    try {
        const [clientRes, workoutsRes] = await Promise.all([
            axios.get(`http://localhost:8000/api/users/${clientId}`, {
                headers,
            }),
            axios.get(
                `http://localhost:8000/api/trainer/client/${clientId}/workouts`,
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
            `http://localhost:8000/api/trainer/client/${clientId}/workouts`,
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
</script>

<template>
    <div class="h-[100dvh] bg-[#0f0f0f] text-white flex flex-col">
        <div
            v-if="loading"
            class="flex-1 flex items-center justify-center text-gray-400"
        >
            Loading...
        </div>

        <template v-else>
            <div
                class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black p-6 pb-8 rounded-b-3xl"
            >
                <div class="flex items-center justify-between mb-4">
                    <button
                        @click="router.back()"
                        class="w-10 h-10 flex items-center justify-center bg-black/15 rounded-xl text-black text-lg font-bold"
                    >
                        ←
                    </button>
                </div>

                <h1 class="text-4xl font-bold tracking-tight">
                    {{ client?.name }}
                </h1>
                <p class="text-black/60 text-sm mt-1">
                    {{ workouts.length }} workout plans
                </p>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-6 pb-28 space-y-3">
                <div
                    v-for="workout in workouts"
                    :key="workout.id"
                    @click="openWorkout(workout)"
                    class="flex items-center gap-4 bg-white/5 border border-white/5 rounded-2xl p-5 cursor-pointer active:scale-[0.98] transition-transform"
                >
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-base truncate">
                            {{ workout.name }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ workout.exercises?.length || 0 }} exercises ·
                            {{
                                workout.exercises?.reduce(
                                    (s, e) =>
                                        s + (e.exercise_sets?.length || 0),
                                    0,
                                ) || 0
                            }}
                            sets
                        </p>
                    </div>

                    <span class="text-gray-600 text-lg">›</span>
                </div>

                <div
                    v-if="workouts.length === 0"
                    class="flex flex-col items-center justify-center text-gray-500 mt-20"
                >
                    <i class="fas fa-dumbbell text-3xl mb-3 opacity-30"></i>
                    <p>No workouts yet</p>
                </div>
            </div>

            <button
                @click="showModal = true"
                class="fixed bottom-24 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
            >
                <i class="fas fa-plus text-xl"></i>
            </button>
        </template>

        <div
            v-if="showModal"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center px-4"
        >
            <div
                class="w-full max-w-lg bg-[#151515] rounded-3xl p-6 border border-white/10"
            >
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold">New Workout</h2>
                    <button
                        @click="showModal = false"
                        class="text-gray-400 text-2xl leading-none w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10"
                    >
                        ×
                    </button>
                </div>

                <input
                    v-model="newWorkoutName"
                    placeholder="e.g. Push Day, Leg Day..."
                    class="w-full bg-[#101010] border border-white/10 rounded-2xl px-4 py-4 outline-none mb-5 text-white placeholder-gray-500"
                    autofocus
                    @keyup.enter="createWorkout"
                />

                <button
                    @click="createWorkout"
                    :disabled="creating || !newWorkoutName.trim()"
                    class="w-full py-3 bg-[#7ED957] text-black rounded-2xl font-bold disabled:opacity-50"
                >
                    {{ creating ? "Creating..." : "Create Workout" }}
                </button>
            </div>
        </div>
    </div>
</template>
