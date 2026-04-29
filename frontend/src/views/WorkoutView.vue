<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from "axios";
import BottomNav from "../components/BottomNav.vue";

const route = useRoute();
const router = useRouter();

const workoutId = route.params.id;

const workout = ref(null);
const exercises = ref([]);
const loading = ref(true);

const unit = ref(localStorage.getItem("unit") || "kg");

const displayWeight = (weight) => {
    if (!weight) return 0;

    return unit.value === "kg" ? weight : (weight * 2.20462).toFixed(1);
};

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");

        const res = await axios.get(
            `http://localhost:8000/api/workouts/${workoutId}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            },
        );

        const data = res.data.data ?? res.data;

        workout.value = data;
        exercises.value = data.exercises ?? [];
    } catch (err) {
        console.log("ERROR:", err.response?.data);
    } finally {
        loading.value = false;
    }
});

const goToTrack = () => {
    router.push(`/workout/${workoutId}/track`);
};
</script>

<template>
    <div class="h-screen bg-[#0f0f0f] text-white flex flex-col">
        <div
            class="p-6 border-b border-gray-800 flex items-center justify-between"
        >
            <div class="flex items-center gap-4">
                <button @click="router.back()" class="text-[#7ED957] text-xl">
                    ←
                </button>

                <h1 class="text-xl font-semibold">
                    {{ workout?.name || "Loading..." }}
                </h1>
            </div>

            <button
                @click="goToTrack"
                class="flex items-center gap-2 bg-[#7ED957] text-black px-4 py-2 rounded-lg text-sm font-semibold hover:scale-105 transition"
            >
                ✏️ Edit
            </button>
        </div>

        <div class="flex-1 overflow-y-auto min-h-0 p-6">
            <div v-if="loading" class="text-gray-400">Loading workout...</div>

            <div v-else-if="workout" class="space-y-6">
                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                    <h2 class="text-lg font-semibold mb-3">Workout Overview</h2>

                    <div class="flex flex-wrap gap-6 text-sm text-gray-300">
                        <div>
                            <span class="text-[#7ED957] font-semibold">
                                {{ exercises.length }}
                            </span>
                            exercises
                        </div>

                        <div>
                            <span class="text-[#7ED957] font-semibold">
                                {{
                                    exercises.reduce(
                                        (sum, ex) =>
                                            sum + (ex.sets?.length || 0),
                                        0,
                                    )
                                }}
                            </span>
                            total sets
                        </div>

                        <div>
                            <span class="text-[#7ED957] font-semibold">
                                {{
                                    exercises.reduce(
                                        (sum, ex) =>
                                            sum +
                                            (ex.sets?.reduce(
                                                (s, set) => s + (set.reps || 0),
                                                0,
                                            ) || 0),
                                        0,
                                    )
                                }}
                            </span>
                            total reps
                        </div>
                    </div>
                </div>

                <div
                    v-for="ex in exercises"
                    :key="ex.id"
                    class="bg-white/5 border border-white/10 rounded-xl p-5 hover:bg-white/10 transition"
                >
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold text-[#7ED957]">
                            {{ ex.name }}
                        </h2>

                        <span class="text-xs text-gray-500">
                            {{ ex.sets?.length || 0 }} sets
                        </span>
                    </div>

                    <div
                        class="overflow-hidden rounded-lg border border-white/10"
                    >
                        <div
                            class="grid grid-cols-3 bg-white/10 px-4 py-2 text-sm font-medium text-gray-300"
                        >
                            <span>Set</span>
                            <span>Reps</span>
                            <span>Weight</span>
                        </div>

                        <div
                            v-for="(set, index) in ex.sets"
                            :key="set.id || index"
                            class="grid grid-cols-3 px-4 py-2 text-sm border-t border-white/5 text-gray-200"
                        >
                            <span>{{ index + 1 }}</span>
                            <span>{{ set.reps }}</span>

                            <span>
                                {{ displayWeight(set.weight) }} {{ unit }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="exercises.length === 0" class="text-gray-500">
                    No exercises found.
                </div>
            </div>
        </div>

        <BottomNav />
    </div>
</template>
