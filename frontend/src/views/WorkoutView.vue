<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const workoutId = route.params.id;
const token = localStorage.getItem("token");
const headers = {
    Authorization: `Bearer ${token}`,
    Accept: "application/json",
};

const workout = ref(null);
const exercises = ref([]);
const library = ref([]);
const loading = ref(true);
const isEditing = ref(false);
const isStarted = ref(false);
const finishing = ref(false);
const showSuccess = ref(false);

const searchQueries = ref({});
const activeDropdown = ref(null);

const logId = ref(null);
const seconds = ref(0);
let timer = null;

const formatTime = (s) => {
    const m = Math.floor(s / 60)
        .toString()
        .padStart(2, "0");
    const sec = (s % 60).toString().padStart(2, "0");
    return `${m}:${sec}`;
};

onMounted(async () => {
    if (!token) {
        router.push("/login");
        return;
    }

    try {
        const res = await axios.get(
            `http://localhost:8000/api/workouts/${workoutId}`,
            { headers },
        );

        const data = res.data.data ?? res.data;
        workout.value = data;
        exercises.value = data.exercises ?? [];

        exercises.value.forEach((ex) => {
            searchQueries.value[ex.id] = ex.name;
            ex.sets = (ex.sets ?? []).map((s, i) => ({
                ...s,
                set_number: i + 1,
                done: false,
            }));
        });

        const libRes = await axios.get(
            "http://localhost:8000/api/exercise-library",
            { headers },
        );
        library.value = libRes.data;
    } catch (err) {
        console.error(err);
        if (err.response?.status === 401) {
            localStorage.removeItem("token");
            router.push("/login");
        }
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => clearInterval(timer));

const startWorkout = async () => {
    try {
        const res = await axios.post(
            "http://localhost:8000/api/workout-logs/start",
            { training_plan_id: workoutId },
            { headers },
        );
        logId.value = res.data.id;
        isStarted.value = true;
        timer = setInterval(() => seconds.value++, 1000);
    } catch (err) {
        console.error(err);
    }
};

const finishWorkout = async () => {
    finishing.value = true;
    clearInterval(timer);

    try {
        const sets = [];
        exercises.value.forEach((ex) => {
            ex.sets.forEach((set, i) => {
                sets.push({
                    exercise_name: ex.name,
                    set_number: i + 1,
                    reps: set.reps,
                    weight: set.weight,
                });
            });
        });

        await axios.post(
            `http://localhost:8000/api/workout-logs/${logId.value}/finish`,
            { duration_seconds: seconds.value, sets },
            { headers },
        );

        router.push("/dashboard");
    } catch (err) {
        console.error(err);
        finishing.value = false;
    }
};

const toggleSet = (set) => {
    set.done = !set.done;
};

const getFilteredLibrary = (ex) => {
    const query = searchQueries.value[ex.id]?.toLowerCase() || "";
    return library.value
        .filter((item) => item.name.toLowerCase().includes(query))
        .slice(0, 5);
};

const selectExercise = (ex, item) => {
    ex.name = item.name;
    ex.library_id = item.id;
    searchQueries.value[ex.id] = item.name;
    activeDropdown.value = null;
};

const addCustomExercise = async (ex) => {
    try {
        const res = await axios.post(
            "http://localhost:8000/api/exercise-library",
            { name: searchQueries.value[ex.id] },
            { headers },
        );
        const newExercise = res.data;
        library.value.push(newExercise);
        ex.name = newExercise.name;
        ex.library_id = newExercise.id;
        activeDropdown.value = null;
    } catch (err) {
        console.error(err.response?.data || err.message);
    }
};

const saveChanges = async () => {
    try {
        for (const ex of exercises.value) {
            if (ex.id && typeof ex.id === "number" && ex.id < 1000000000) {
                await axios.put(
                    `http://localhost:8000/api/exercises/${ex.id}`,
                    {
                        name: ex.name,
                        library_id: ex.library_id,
                        sets_data: ex.sets,
                    },
                    { headers },
                );
            } else {
                await axios.post(
                    `http://localhost:8000/api/exercises`,
                    {
                        workout_id: workout.value.id,
                        name: ex.name,
                        library_id: ex.library_id,
                        sets_data: ex.sets,
                    },
                    { headers },
                );
            }
        }
        isEditing.value = false;
        showSuccess.value = true;
        setTimeout(() => (showSuccess.value = false), 1500);
    } catch (err) {
        console.error(err.response?.data || err.message);
    }
};

const addSet = (ex) => {
    ex.sets.push({ reps: 0, weight: 0, done: false });
};
const removeSet = (ex, index) => {
    ex.sets.splice(index, 1);
};

const addExercise = () => {
    const id = Date.now();
    exercises.value.push({ id, name: "", library_id: null, sets: [] });
    searchQueries.value[id] = "";
};

const removeExercise = (index) => {
    const ex = exercises.value[index];
    delete searchQueries.value[ex.id];
    exercises.value.splice(index, 1);
};
</script>

<template>
    <div
        v-if="loading"
        class="h-[100dvh] flex items-center justify-center bg-[#0f0f0f] text-gray-400"
    >
        Loading...
    </div>

    <div
        v-else-if="workout"
        class="h-[100dvh] flex flex-col bg-[#0f0f0f] text-white"
    >
        <div
            class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black p-6 pb-10 rounded-b-3xl"
        >
            <div class="flex items-center justify-between mb-6">
                <button
                    @click="router.back()"
                    class="w-10 h-10 flex items-center justify-center bg-black/15 rounded-xl text-black text-lg font-bold"
                >
                    ←
                </button>

                <span
                    v-if="isStarted"
                    class="text-2xl font-mono font-bold tracking-widest"
                >
                    {{ formatTime(seconds) }}
                </span>

                <div v-else>
                    <button
                        v-if="!isEditing"
                        @click="isEditing = true"
                        class="bg-black/15 text-black px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        Edit
                    </button>

                    <button
                        v-else
                        @click="saveChanges"
                        class="bg-black text-[#7ED957] px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        Save
                    </button>
                </div>
            </div>

            <h1 class="text-4xl font-bold tracking-tight">
                {{ workout.name }}
            </h1>

            <div class="flex items-center gap-4 mt-2">
                <span class="text-black/60 text-sm"
                    >{{ exercises.length }} exercises</span
                >
                <span class="w-1 h-1 bg-black/30 rounded-full"></span>
                <span class="text-black/60 text-sm">
                    {{ exercises.reduce((sum, ex) => sum + ex.sets.length, 0) }}
                    sets
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-5 pt-6 pb-36 space-y-4">
            <div
                v-for="(ex, exIndex) in exercises"
                :key="ex.id"
                class="bg-[#1a1a1a] border border-white/5 rounded-2xl p-5"
            >
                <p class="font-semibold text-base mb-3">{{ ex.name }}</p>

                <div class="space-y-2 mb-3">
                    <div
                        v-for="(set, index) in ex.sets"
                        :key="index"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 transition-all"
                        :class="
                            isStarted
                                ? set.done
                                    ? 'bg-[#7ED957]/20 border border-[#7ED957]/40 cursor-pointer'
                                    : 'bg-[#111] border border-white/5 cursor-pointer'
                                : 'bg-[#111]'
                        "
                        @click="isStarted ? toggleSet(set) : null"
                    >
                        <span
                            class="text-sm w-6 shrink-0"
                            :class="
                                set.done
                                    ? 'text-[#7ED957] font-bold'
                                    : 'text-gray-500'
                            "
                        >
                            {{ index + 1 }}
                        </span>

                        <input
                            v-model.number="set.reps"
                            type="number"
                            placeholder="Reps"
                            :disabled="!isEditing && !isStarted"
                            @click.stop
                            class="flex-1 min-w-0 bg-transparent outline-none text-center text-sm"
                            :class="set.done ? 'text-[#7ED957]' : 'text-white'"
                        />

                        <span class="text-gray-600 text-xs shrink-0">×</span>

                        <input
                            v-model.number="set.weight"
                            type="number"
                            placeholder="kg"
                            :disabled="!isEditing && !isStarted"
                            @click.stop
                            class="flex-1 min-w-0 bg-transparent outline-none text-center text-sm"
                            :class="set.done ? 'text-[#7ED957]' : 'text-white'"
                        />

                        <i
                            v-if="isStarted"
                            class="fas fa-check text-xs shrink-0"
                            :class="
                                set.done ? 'text-[#7ED957]' : 'text-gray-700'
                            "
                        ></i>

                        <button
                            v-if="isEditing"
                            @click.stop="removeSet(ex, index)"
                            class="text-red-500 shrink-0"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <div v-if="isEditing">
                    <div class="relative w-full mb-4">
                        <input
                            v-model="searchQueries[ex.id]"
                            @focus="activeDropdown = ex.id"
                            placeholder="Search or create exercise..."
                            class="w-full px-4 py-3 rounded-lg bg-[#111] border border-gray-700 focus:border-[#7ED957] outline-none text-sm"
                        />

                        <div
                            v-if="
                                activeDropdown === ex.id && searchQueries[ex.id]
                            "
                            class="absolute w-full mt-2 bg-[#1a1a1a] border border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50"
                        >
                            <div
                                v-for="item in getFilteredLibrary(ex)"
                                :key="item.id"
                                @click="selectExercise(ex, item)"
                                class="px-4 py-2 hover:bg-[#2a2a2a] cursor-pointer text-sm"
                            >
                                {{ item.name }}
                                <span class="text-gray-500 ml-2 text-xs"
                                    >({{ item.muscle_group }})</span
                                >
                            </div>

                            <div
                                v-if="getFilteredLibrary(ex).length === 0"
                                @click="addCustomExercise(ex)"
                                class="px-4 py-2 text-[#7ED957] cursor-pointer hover:bg-[#2a2a2a]"
                            >
                                + Add "{{ searchQueries[ex.id] }}"
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button
                            @click="addSet(ex)"
                            class="text-[#7ED957] text-sm"
                        >
                            + Add Set
                        </button>
                        <button
                            @click="removeExercise(exIndex)"
                            class="text-red-500 text-sm"
                        >
                            Remove Exercise
                        </button>
                    </div>
                </div>
            </div>

            <button
                v-if="isEditing"
                @click="addExercise"
                class="w-full py-3 bg-white/5 border border-white/10 text-white rounded-xl font-semibold"
            >
                + Add Exercise
            </button>
        </div>

        <button
            v-if="!isStarted"
            @click="startWorkout"
            class="fixed bottom-24 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
        >
            <i class="fas fa-play text-xl"></i>
        </button>

        <button
            v-else
            @click="finishWorkout"
            :disabled="finishing"
            class="fixed bottom-24 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
        >
            <i class="fas fa-flag-checkered text-xl"></i>
        </button>
    </div>

    <div
        v-else
        class="h-[100dvh] flex items-center justify-center bg-[#0f0f0f] text-gray-400"
    >
        Workout not found.
    </div>

    <transition name="fade">
        <div
            v-if="showSuccess"
            class="fixed inset-0 flex items-center justify-center z-50"
        >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div
                class="relative bg-[#1a1a1a] border border-[#7ED957]/30 px-8 py-6 rounded-2xl shadow-xl text-center"
            >
                <p class="text-[#7ED957] text-lg font-semibold">
                    Saved successfully
                </p>
            </div>
        </div>
    </transition>
</template>
