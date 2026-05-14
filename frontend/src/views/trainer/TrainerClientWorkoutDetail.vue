<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const clientId = route.params.id;
const workoutId = route.params.workoutId;
const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const workout = ref(null);
const exercises = ref([]);
const library = ref([]);
const loading = ref(true);
const isEditing = ref(false);
const isStarted = ref(false);
const saving = ref(false);
const finishing = ref(false);
const showSuccess = ref(false);

const unit = localStorage.getItem("unit") || "kg";

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
    try {
        const [workoutsRes, libRes] = await Promise.all([
            axios.get(
                `http://localhost:8000/api/trainer/client/${clientId}/workouts`,
                { headers },
            ),
            axios.get("http://localhost:8000/api/exercise-library", {
                headers,
            }),
        ]);

        const workouts = workoutsRes.data.data ?? workoutsRes.data;
        const found = workouts.find((w) => w.id === parseInt(workoutId));
        if (found) {
            workout.value = found;
            exercises.value = JSON.parse(JSON.stringify(found.exercises ?? []));
            exercises.value.forEach((ex) => {
                searchQueries.value[ex.id] = ex.name;
                ex.exercise_sets = (ex.exercise_sets ?? []).map((s, i) => ({
                    ...s,
                    set_number: i + 1,
                    done: false,
                }));
            });
        }

        library.value = libRes.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => clearInterval(timer));

const startWorkout = async () => {
    try {
        const res = await axios.post(
            `http://localhost:8000/api/trainer/client/${clientId}/workout-logs/start`,
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
            ex.exercise_sets.forEach((set, i) => {
                sets.push({
                    exercise_name: ex.name,
                    set_number: i + 1,
                    reps: set.reps,
                    weight: set.weight,
                });
            });
        });

        await axios.post(
            `http://localhost:8000/api/trainer/client/${clientId}/workout-logs/${logId.value}/finish`,
            { duration_seconds: seconds.value, sets },
            { headers },
        );

        router.back();
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
        console.error(err);
    }
};

const addExercise = () => {
    const id = Date.now();
    exercises.value.push({ id, name: "", library_id: null, exercise_sets: [] });
    searchQueries.value[id] = "";
};

const removeExercise = (index) => {
    const ex = exercises.value[index];
    delete searchQueries.value[ex.id];
    exercises.value.splice(index, 1);
};

const addSet = (ex) => {
    ex.exercise_sets.push({ reps: 0, weight: 0, done: false });
};
const removeSet = (ex, index) => {
    ex.exercise_sets.splice(index, 1);
};

const saveWorkout = async () => {
    saving.value = true;
    try {
        for (const ex of exercises.value) {
            if (ex.id && typeof ex.id === "number" && ex.id < 1000000000) {
                await axios.put(
                    `http://localhost:8000/api/exercises/${ex.id}`,
                    { name: ex.name, sets_data: ex.exercise_sets },
                    { headers },
                );
            } else {
                await axios.post(
                    "http://localhost:8000/api/exercises",
                    {
                        workout_id: workout.value.id,
                        name: ex.name,
                        library_id: ex.library_id,
                        sets_data: ex.exercise_sets,
                    },
                    { headers },
                );
            }
        }
        isEditing.value = false;
        showSuccess.value = true;
        setTimeout(() => (showSuccess.value = false), 1500);
    } catch (err) {
        console.error(err);
    } finally {
        saving.value = false;
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

        <template v-else-if="workout">
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

                    <span
                        v-if="isStarted"
                        class="text-2xl font-mono font-bold tracking-widest"
                    >
                        {{ formatTime(seconds) }}
                    </span>

                    <button
                        v-if="!isEditing"
                        @click="isEditing = true"
                        class="bg-black/15 text-black px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        Edit
                    </button>

                    <button
                        v-else
                        @click="saveWorkout"
                        :disabled="saving"
                        class="bg-black text-[#7ED957] px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        {{ saving ? "Saving..." : "Save" }}
                    </button>
                </div>

                <h1 class="text-4xl font-bold tracking-tight">
                    {{ workout.name }}
                </h1>
                <p class="text-black/60 text-sm mt-1">
                    {{ exercises.length }} exercises
                </p>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-6 pb-28 space-y-4">
                <div
                    v-for="(ex, exIndex) in exercises"
                    :key="ex.id"
                    class="bg-[#1a1a1a] border border-white/5 rounded-2xl p-5"
                >
                    <div v-if="isEditing" class="relative w-full mb-4">
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
                                class="px-4 py-2 text-[#7ED957] cursor-pointer hover:bg-[#2a2a2a] text-sm"
                            >
                                + Add "{{ searchQueries[ex.id] }}"
                            </div>
                        </div>
                    </div>

                    <p v-else class="font-semibold text-base mb-4">
                        {{ ex.name }}
                    </p>

                    <div class="space-y-2 mb-3">
                        <div
                            v-for="(set, setIndex) in ex.exercise_sets"
                            :key="setIndex"
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
                                {{ setIndex + 1 }}
                            </span>

                            <input
                                v-model.number="set.reps"
                                type="number"
                                placeholder="Reps"
                                :disabled="!isEditing && !isStarted"
                                @click.stop
                                class="flex-1 min-w-0 bg-transparent outline-none text-center text-sm"
                                :class="
                                    set.done ? 'text-[#7ED957]' : 'text-white'
                                "
                            />

                            <span class="text-gray-600 text-xs shrink-0"
                                >×</span
                            >

                            <input
                                v-model.number="set.weight"
                                type="number"
                                :placeholder="unit"
                                :disabled="!isEditing && !isStarted"
                                @click.stop
                                class="flex-1 min-w-0 bg-transparent outline-none text-center text-sm"
                                :class="
                                    set.done ? 'text-[#7ED957]' : 'text-white'
                                "
                            />

                            <span class="text-gray-500 text-xs shrink-0">{{
                                unit
                            }}</span>

                            <i
                                v-if="isStarted"
                                class="fas fa-check text-xs shrink-0"
                                :class="
                                    set.done
                                        ? 'text-[#7ED957]'
                                        : 'text-gray-700'
                                "
                            ></i>

                            <button
                                v-if="isEditing"
                                @click.stop="removeSet(ex, setIndex)"
                                class="text-red-500 shrink-0 text-xs"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button
                            v-if="isEditing"
                            @click="addSet(ex)"
                            class="text-[#7ED957] text-sm"
                        >
                            + Add Set
                        </button>
                        <button
                            v-if="isEditing"
                            @click="removeExercise(exIndex)"
                            class="text-red-500 text-sm ml-auto"
                        >
                            Remove Exercise
                        </button>
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
                v-if="!isStarted && !isEditing"
                @click="startWorkout"
                class="fixed bottom-24 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
            >
                <i class="fas fa-play text-xl"></i>
            </button>

            <button
                v-if="isStarted"
                @click="finishWorkout"
                :disabled="finishing"
                class="fixed bottom-24 right-6 w-16 h-16 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg hover:scale-110 transition-all duration-300 z-50"
            >
                <i class="fas fa-flag-checkered text-xl"></i>
            </button>
        </template>

        <transition name="fade">
            <div
                v-if="showSuccess"
                class="fixed inset-0 flex items-center justify-center z-50"
            >
                <div
                    class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                ></div>
                <div
                    class="relative bg-[#1a1a1a] border border-[#7ED957]/30 px-8 py-6 rounded-2xl shadow-xl text-center"
                >
                    <p class="text-[#7ED957] text-lg font-semibold">
                        Saved successfully
                    </p>
                </div>
            </div>
        </transition>
    </div>
</template>
