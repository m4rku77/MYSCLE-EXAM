<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const workoutId = route.params.id;
const token = localStorage.getItem("token");
const unit = localStorage.getItem("unit") || "kg";
const headers = {
    Authorization: `Bearer ${token}`,
    Accept: "application/json",
};
const isFavorite = ref(false);
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
        isFavorite.value = data.is_favorite ?? false;
        exercises.value = data.exercises ?? [];
        exercises.value.forEach((ex) => {
            searchQueries.value[ex.id] = ex.name;
            ex.notes = ex.notes ?? "";
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
        if (err.response?.status === 401) {
            localStorage.removeItem("token");
            router.push("/login");
        }
    } finally {
        loading.value = false;
    }
});

const toggleFavorite = async () => {
    try {
        isFavorite.value = !isFavorite.value;
        await axios.put(
            `http://localhost:8000/api/workouts/${workoutId}`,
            { is_favorite: isFavorite.value },
            { headers },
        );
    } catch (err) {
        isFavorite.value = !isFavorite.value;
        console.error(err);
    }
};

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
                        notes: ex.notes,
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
    exercises.value.push({
        id,
        name: "",
        library_id: null,
        sets: [],
        notes: "",
    });
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
        class="h-[100dvh] flex items-center justify-center bg-[#080808] text-gray-600"
    >
        <i class="fas fa-spinner fa-spin mr-3"></i> Loading...
    </div>

    <div
        v-else-if="workout"
        class="h-[100dvh] bg-[#080808] text-white flex flex-col"
    >
        <div class="hidden md:flex h-full">
            <aside
                class="w-64 bg-[#0f0f0f] border-r border-white/5 flex flex-col px-6 py-8 fixed h-full"
            >
                <div class="flex items-center gap-3 mb-12">
                    <img src="/logo.png" class="h-8" />
                    <span class="font-black text-lg tracking-widest uppercase"
                        >Myscle</span
                    >
                </div>
                <nav class="space-y-1 flex-1">
                    <div
                        @click="router.back()"
                        class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                    >
                        <i class="fas fa-arrow-left w-4"></i> Back
                    </div>
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
                        @click="router.push('/profile')"
                        class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                    >
                        <i class="fas fa-user w-4"></i> Profile
                    </div>
                </nav>
                <div
                    v-if="isStarted"
                    class="bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl p-4 text-center mb-3"
                >
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-1"
                    >
                        Timer
                    </p>
                    <p class="text-3xl font-mono font-black text-[#7ED957]">
                        {{ formatTime(seconds) }}
                    </p>
                </div>
                <button
                    v-if="!isStarted"
                    @click="startWorkout"
                    class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2"
                >
                    <i class="fas fa-play text-xs"></i> Start Workout
                </button>
                <button
                    v-else
                    @click="finishWorkout"
                    :disabled="finishing"
                    class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2 disabled:opacity-50"
                >
                    <i class="fas fa-flag-checkered text-xs"></i> Finish Workout
                </button>
            </aside>

            <main class="ml-64 flex-1 overflow-y-auto">
                <div class="max-w-3xl mx-auto px-10 py-10">
                    <div class="flex items-start justify-between mb-10">
                        <div>
                            <p
                                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-2"
                            >
                                Workout
                            </p>

                            <div class="flex items-center gap-3">
                                <h1 class="text-4xl font-black">
                                    {{ workout.name }}
                                </h1>
                                <button
                                    @click="toggleFavorite"
                                    class="transition-all hover:scale-110"
                                >
                                    <i
                                        class="fas fa-star text-2xl"
                                        :class="
                                            isFavorite
                                                ? 'text-yellow-400'
                                                : 'text-gray-700'
                                        "
                                    ></i>
                                </button>
                            </div>
                            <div
                                class="flex items-center gap-3 mt-2 text-sm text-gray-500"
                            >
                                <span>{{ exercises.length }} exercises</span>
                                <span
                                    class="w-1 h-1 bg-gray-700 rounded-full"
                                ></span>
                                <span
                                    >{{
                                        exercises.reduce(
                                            (s, ex) => s + ex.sets.length,
                                            0,
                                        )
                                    }}
                                    sets</span
                                >
                            </div>
                        </div>

                        <button
                            v-if="!isEditing"
                            @click="isEditing = true"
                            class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-2xl font-semibold text-sm hover:bg-white/10 transition-all"
                        >
                            Edit
                        </button>
                        <button
                            v-else
                            @click="saveChanges"
                            class="px-5 py-2.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all"
                        >
                            Save Changes
                        </button>
                    </div>

                    <div class="space-y-4">
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
                                    class="w-full px-4 py-3 rounded-xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all"
                                />
                                <div
                                    v-if="
                                        activeDropdown === ex.id &&
                                        searchQueries[ex.id]
                                    "
                                    class="absolute w-full mt-2 bg-[#151515] border border-white/10 rounded-xl shadow-2xl max-h-48 overflow-y-auto z-50"
                                >
                                    <div
                                        v-for="item in getFilteredLibrary(ex)"
                                        :key="item.id"
                                        @click="selectExercise(ex, item)"
                                        class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm"
                                    >
                                        {{ item.name
                                        }}<span
                                            class="text-gray-500 ml-2 text-xs"
                                            >({{ item.muscle_group }})</span
                                        >
                                    </div>
                                    <div
                                        v-if="
                                            getFilteredLibrary(ex).length === 0
                                        "
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
                                    v-for="(set, index) in ex.sets"
                                    :key="index"
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
                                        >{{ index + 1 }}</span
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
                                            @click.stop="removeSet(ex, index)"
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
            </main>
        </div>

        <div class="md:hidden flex flex-col h-full">
            <div
                class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl"
            >
                <div class="flex items-center justify-between mb-5">
                    <button
                        @click="router.back()"
                        class="w-10 h-10 flex items-center justify-center bg-black/15 rounded-xl text-black font-bold text-lg"
                    >
                        ←
                    </button>
                    <span
                        v-if="isStarted"
                        class="text-2xl font-mono font-black tracking-widest"
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
                        @click="saveChanges"
                        class="bg-black text-[#7ED957] px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        Save
                    </button>
                </div>

                <div class="flex items-center gap-2 mt-1">
                    <h1 class="text-3xl font-black tracking-tight">
                        {{ workout.name }}
                    </h1>
                    <button @click="toggleFavorite" class="transition-all">
                        <i
                            class="fas fa-star"
                            :class="
                                isFavorite ? 'text-yellow-400' : 'text-black/30'
                            "
                        ></i>
                    </button>
                </div>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-black/50 text-sm"
                        >{{ exercises.length }} exercises</span
                    >
                    <span class="w-1 h-1 bg-black/30 rounded-full"></span>
                    <span class="text-black/50 text-sm"
                        >{{
                            exercises.reduce((s, ex) => s + ex.sets.length, 0)
                        }}
                        sets</span
                    >
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-5 pb-36 space-y-4">
                <div
                    v-for="(ex, exIndex) in exercises"
                    :key="ex.id"
                    class="bg-[#111] border border-white/5 rounded-2xl p-5"
                >
                    <div v-if="isEditing" class="relative mb-4">
                        <input
                            v-model="searchQueries[ex.id]"
                            @focus="activeDropdown = ex.id"
                            placeholder="Search or create exercise..."
                            class="w-full px-4 py-3 rounded-xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all"
                        />
                        <div
                            v-if="
                                activeDropdown === ex.id && searchQueries[ex.id]
                            "
                            class="absolute w-full mt-2 bg-[#151515] border border-white/10 rounded-xl shadow-2xl max-h-48 overflow-y-auto z-50"
                        >
                            <div
                                v-for="item in getFilteredLibrary(ex)"
                                :key="item.id"
                                @click="selectExercise(ex, item)"
                                class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm transition-colors"
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
                    <p v-else class="font-bold text-base mb-4">{{ ex.name }}</p>

                    <div
                        class="grid grid-cols-12 gap-2 text-xs text-gray-600 uppercase tracking-wider px-1 mb-2"
                    >
                        <span class="col-span-1">#</span>
                        <span class="col-span-4 text-center">Reps</span>
                        <span class="col-span-1 text-center text-gray-700"
                            >×</span
                        >
                        <span class="col-span-4 text-center">{{ unit }}</span>
                        <span class="col-span-2"></span>
                    </div>

                    <div class="space-y-2 mb-3">
                        <div
                            v-for="(set, index) in ex.sets"
                            :key="index"
                            class="grid grid-cols-12 gap-2 items-center rounded-xl px-3 py-2.5 transition-all"
                            :class="
                                isStarted
                                    ? set.done
                                        ? 'bg-[#7ED957]/15 border border-[#7ED957]/30 cursor-pointer'
                                        : 'bg-[#0a0a0a] border border-white/5 cursor-pointer'
                                    : 'bg-[#0a0a0a]'
                            "
                            @click="isStarted ? toggleSet(set) : null"
                        >
                            <span
                                class="col-span-1 text-sm"
                                :class="
                                    set.done
                                        ? 'text-[#7ED957] font-bold'
                                        : 'text-gray-600'
                                "
                                >{{ index + 1 }}</span
                            >
                            <input
                                v-model.number="set.reps"
                                type="number"
                                placeholder="0"
                                :disabled="!isEditing && !isStarted"
                                @click.stop
                                class="col-span-4 bg-transparent outline-none text-center text-sm"
                                :class="
                                    set.done ? 'text-[#7ED957]' : 'text-white'
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
                                class="col-span-4 bg-transparent outline-none text-center text-sm"
                                :class="
                                    set.done ? 'text-[#7ED957]' : 'text-white'
                                "
                            />
                            <div class="col-span-2 flex justify-end">
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
                                    @click.stop="removeSet(ex, index)"
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
                        class="flex items-center justify-between pt-1"
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
                            Remove
                        </button>
                    </div>
                </div>

                <button
                    v-if="isEditing"
                    @click="addExercise"
                    class="w-full py-3.5 bg-white/5 border border-white/10 text-white rounded-2xl font-semibold text-sm hover:bg-white/10 transition-all"
                >
                    + Add Exercise
                </button>
            </div>

            <button
                v-if="!isStarted && !isEditing"
                @click="startWorkout"
                class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50"
            >
                <i class="fas fa-play text-lg"></i>
            </button>
            <button
                v-if="isStarted"
                @click="finishWorkout"
                :disabled="finishing"
                class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50 disabled:opacity-50"
            >
                <i class="fas fa-flag-checkered text-lg"></i>
            </button>
        </div>
    </div>

    <div
        v-else
        class="h-[100dvh] flex items-center justify-center bg-[#080808] text-gray-500"
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
                class="relative bg-[#111] border border-[#7ED957]/30 px-8 py-6 rounded-2xl shadow-xl text-center"
            >
                <i class="fas fa-check-circle text-[#7ED957] text-2xl mb-2"></i>
                <p class="text-[#7ED957] font-bold">Saved successfully</p>
            </div>
        </div>
    </transition>
</template>
