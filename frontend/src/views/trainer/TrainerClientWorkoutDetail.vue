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
const client = ref(null);
const exercises = ref([]);
const library = ref([]);
const loading = ref(true);
const isEditing = ref(false);
const isStarted = ref(false);
const saving = ref(false);
const finishing = ref(false);
const showSuccess = ref(false);

const unit = ref(localStorage.getItem("unit") || "kg");

const toDisplay = (weightKg) => {
    if (!weightKg) return 0;
    return unit.value === "lbs"
        ? Math.round(Number(weightKg) * 2.20462 * 10) / 10
        : Number(weightKg);
};

const toKg = (weight) => {
    if (!weight) return 0;
    return unit.value === "lbs"
        ? Math.round((Number(weight) / 2.20462) * 10) / 10
        : Number(weight);
};

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
        const [workoutsRes, libRes, clientRes] = await Promise.all([
            axios.get(
                `http://localhost:8000/api/trainer/client/${clientId}/workouts`,
                { headers },
            ),
            axios.get("http://localhost:8000/api/exercise-library", {
                headers,
            }),
            axios.get(`http://localhost:8000/api/users/${clientId}`, {
                headers,
            }),
        ]);

        client.value = clientRes.data.data ?? clientRes.data;

        const workouts = workoutsRes.data.data ?? workoutsRes.data;
        const found = workouts.find((w) => w.id === parseInt(workoutId));
        if (found) {
            workout.value = found;
            exercises.value = JSON.parse(JSON.stringify(found.exercises ?? []));
            exercises.value.forEach((ex) => {
                searchQueries.value[ex.id] = ex.name;
                ex.notes = ex.notes ?? "";
                ex.exercise_sets = (ex.exercise_sets ?? []).map((s, i) => ({
                    ...s,
                    set_number: i + 1,
                    done: false,
                    weight: toDisplay(s.weight),
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
                    weight: toKg(set.weight),
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
    exercises.value.push({
        id,
        name: "",
        library_id: null,
        exercise_sets: [],
        notes: "",
    });
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
                    {
                        name: ex.name,
                        sets_data: ex.exercise_sets,
                        notes: ex.notes,
                    },
                    { headers },
                );
            } else {
                await axios.post(
                    "http://localhost:8000/api/exercises",
                    {
                        workout_id: workout.value.id,
                        name: ex.name,
                        library_id: ex.library_id,
                        sets_data: ex.exercise_sets.map((s) => ({
                            ...s,
                            weight: toKg(s.weight),
                        })),
                        notes: ex.notes,
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
    <div class="h-[100dvh] bg-[#080808] text-white flex flex-col md:ml-64">
        <div
            v-if="loading"
            class="flex-1 flex items-center justify-center text-gray-400"
        >
            <i class="fas fa-spinner fa-spin mr-3"></i> Loading...
        </div>

        <template v-else-if="workout">
            <div
                class="md:hidden bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0"
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
                        >{{ formatTime(seconds) }}</span
                    >
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
                <h1 class="text-3xl font-black tracking-tight">
                    {{ workout.name }}
                </h1>
                <p class="text-black/60 text-sm mt-1">
                    {{ exercises.length }} exercises
                </p>
            </div>

            <div
                class="hidden md:flex bg-[#0f0f0f] border-b border-white/5 px-8 py-5 shrink-0 items-center justify-between"
            >
                <div class="flex items-center gap-4">
                    <button
                        @click="router.back()"
                        class="flex items-center gap-2 text-gray-400 hover:text-white transition-all text-sm font-semibold"
                    >
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <div class="w-px h-5 bg-white/10"></div>
                    <div>
                        <p
                            class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-0.5"
                        >
                            {{ client?.name ?? "Client" }}
                        </p>
                        <h1 class="text-2xl font-bold">{{ workout.name }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span
                        v-if="isStarted"
                        class="font-mono text-xl font-bold text-[#7ED957] bg-[#7ED957]/10 border border-[#7ED957]/20 px-4 py-2 rounded-xl"
                    >
                        {{ formatTime(seconds) }}
                    </span>
                    <button
                        v-if="!isEditing"
                        @click="isEditing = true"
                        class="px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl font-semibold text-sm hover:bg-white/10 transition-all"
                    >
                        Edit
                    </button>
                    <button
                        v-else
                        @click="saveWorkout"
                        :disabled="saving"
                        class="px-4 py-2.5 bg-[#7ED957] text-black rounded-xl font-bold text-sm hover:bg-[#6bc947] transition-all"
                    >
                        {{ saving ? "Saving..." : "Save Changes" }}
                    </button>
                    <button
                        v-if="!isStarted"
                        @click="startWorkout"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#7ED957] text-black rounded-xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20"
                    >
                        <i class="fas fa-play text-xs"></i> Start Workout
                    </button>
                    <button
                        v-else
                        @click="finishWorkout"
                        :disabled="finishing"
                        class="flex items-center gap-2 px-5 py-2.5 bg-[#7ED957] text-black rounded-xl font-bold text-sm hover:bg-[#6bc947] transition-all disabled:opacity-50"
                    >
                        <i class="fas fa-flag-checkered text-xs"></i> Finish
                        Workout
                    </button>
                </div>
            </div>

            <div
                class="flex-1 overflow-y-auto px-5 md:px-8 pt-5 pb-28 md:pb-10"
            >
                <div class="max-w-3xl mx-auto space-y-4">
                    <div
                        v-for="(ex, exIndex) in exercises"
                        :key="ex.id"
                        class="bg-[#111] border border-white/5 rounded-3xl p-6 hover:border-white/10 transition-all"
                    >
                        <div v-if="isEditing" class="relative mb-5">
                            <input
                                v-model="searchQueries[ex.id]"
                                @focus="activeDropdown = ex.id"
                                placeholder="Search or create exercise..."
                                class="w-full px-4 py-3 rounded-2xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all"
                            />
                            <div
                                v-if="
                                    activeDropdown === ex.id &&
                                    searchQueries[ex.id]
                                "
                                class="absolute w-full mt-2 bg-[#151515] border border-white/10 rounded-2xl shadow-2xl max-h-48 overflow-y-auto z-50"
                            >
                                <div
                                    v-for="item in getFilteredLibrary(ex)"
                                    :key="item.id"
                                    @click="selectExercise(ex, item)"
                                    class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm"
                                >
                                    {{ item.name
                                    }}<span class="text-gray-500 ml-2 text-xs"
                                        >({{ item.muscle_group }})</span
                                    >
                                </div>
                                <div
                                    v-if="getFilteredLibrary(ex).length === 0"
                                    @click="addCustomExercise(ex)"
                                    class="px-4 py-3 text-[#7ED957] cursor-pointer hover:bg-white/5 text-sm"
                                >
                                    + Add "{{ searchQueries[ex.id] }}"
                                </div>
                            </div>
                        </div>
                        <p v-else class="font-bold text-lg mb-4">
                            {{ ex.name }}
                        </p>

                        <div
                            class="grid grid-cols-12 gap-3 text-xs text-gray-600 uppercase tracking-wider px-2 mb-2"
                        >
                            <span class="col-span-1">#</span>
                            <span class="col-span-4 text-center">Reps</span>
                            <span class="col-span-1 text-center">×</span>
                            <span class="col-span-4 text-center">{{
                                unit
                            }}</span>
                            <span class="col-span-2"></span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div
                                v-for="(set, setIndex) in ex.exercise_sets"
                                :key="setIndex"
                                class="grid grid-cols-12 gap-3 items-center rounded-xl px-3 py-3 transition-all"
                                :class="
                                    isStarted
                                        ? set.done
                                            ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 cursor-pointer'
                                            : 'bg-[#0a0a0a] border border-white/5 cursor-pointer hover:border-white/10'
                                        : 'bg-[#0a0a0a]'
                                "
                                @click="isStarted ? toggleSet(set) : null"
                            >
                                <span
                                    class="col-span-1 text-sm font-semibold"
                                    :class="
                                        set.done
                                            ? 'text-[#7ED957]'
                                            : 'text-gray-600'
                                    "
                                    >{{ setIndex + 1 }}</span
                                >
                                <input
                                    v-model.number="set.reps"
                                    type="number"
                                    placeholder="0"
                                    :disabled="!isEditing && !isStarted"
                                    @click.stop
                                    class="col-span-4 bg-transparent outline-none text-center text-sm font-medium"
                                    :class="
                                        set.done
                                            ? 'text-[#7ED957]'
                                            : 'text-white'
                                    "
                                />
                                <span
                                    class="col-span-1 text-gray-700 text-xs text-center"
                                    >×</span
                                >
                                <input
                                    v-model.number="set.weight"
                                    type="number"
                                    placeholder="0"
                                    :disabled="!isEditing && !isStarted"
                                    @click.stop
                                    class="col-span-4 bg-transparent outline-none text-center text-sm font-medium"
                                    :class="
                                        set.done
                                            ? 'text-[#7ED957]'
                                            : 'text-white'
                                    "
                                />
                                <div
                                    class="col-span-2 flex justify-end items-center gap-2"
                                >
                                    <i
                                        v-if="isStarted"
                                        class="fas fa-check text-xs"
                                        :class="
                                            set.done
                                                ? 'text-[#7ED957]'
                                                : 'text-gray-800'
                                        "
                                    ></i>
                                    <button
                                        v-if="isEditing"
                                        @click.stop="removeSet(ex, setIndex)"
                                        class="text-red-500 text-xs"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="!isEditing && !isStarted && ex.notes"
                            class="mt-3 text-sm text-gray-500 bg-[#0a0a0a] rounded-xl px-3 py-2"
                        >
                            {{ ex.notes }}
                        </p>
                        <textarea
                            v-if="isEditing || isStarted"
                            v-model="ex.notes"
                            placeholder="Exercise notes..."
                            rows="2"
                            class="w-full mt-3 bg-[#0a0a0a] border border-white/5 rounded-xl px-3 py-2 text-sm text-gray-400 placeholder-gray-700 outline-none focus:border-[#7ED957] resize-none transition-all"
                        ></textarea>

                        <div
                            v-if="isEditing"
                            class="flex items-center justify-between mt-2"
                        >
                            <button
                                @click="addSet(ex)"
                                class="text-[#7ED957] text-sm font-medium"
                            >
                                + Add Set
                            </button>
                            <button
                                @click="removeExercise(exIndex)"
                                class="text-red-400 text-sm"
                            >
                                Remove Exercise
                            </button>
                        </div>
                    </div>

                    <button
                        v-if="isEditing"
                        @click="addExercise"
                        class="w-full py-4 bg-white/5 border border-white/10 text-white rounded-3xl font-semibold text-sm hover:bg-white/10 transition-all"
                    >
                        + Add Exercise
                    </button>
                </div>
            </div>

            <button
                v-if="!isStarted && !isEditing"
                @click="startWorkout"
                class="md:hidden fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50"
            >
                <i class="fas fa-play text-lg"></i>
            </button>
            <button
                v-if="isStarted"
                @click="finishWorkout"
                :disabled="finishing"
                class="md:hidden fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50 disabled:opacity-50"
            >
                <i class="fas fa-flag-checkered text-lg"></i>
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
                    class="relative bg-[#111] border border-[#7ED957]/30 px-8 py-6 rounded-2xl shadow-xl text-center"
                >
                    <i
                        class="fas fa-check-circle text-[#7ED957] text-2xl mb-2"
                    ></i>
                    <p class="text-[#7ED957] font-bold">Saved successfully</p>
                </div>
            </div>
        </transition>
    </div>
</template>
