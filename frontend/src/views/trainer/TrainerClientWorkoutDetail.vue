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
const showFinished = ref(false);
const showDeleteConfirm = ref(false);

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
    const m = Math.floor(s / 60).toString().padStart(2, "0");
    const sec = (s % 60).toString().padStart(2, "0");
    return `${m}:${sec}`;
};

const deleteWorkout = async () => {
    try {
        await axios.delete(`https://myscle-exam-production.up.railway.app/api/workouts/${workoutId}`, { headers });
        router.back();
    } catch (err) {
        console.error(err);
    }
};

onMounted(async () => {
    try {
        const [workoutsRes, libRes, clientRes] = await Promise.all([
            axios.get(`https://myscle-exam-production.up.railway.app/api/trainer/client/${clientId}/workouts`, { headers }),
            axios.get("https://myscle-exam-production.up.railway.app/api/exercise-library", { headers }),
            axios.get(`https://myscle-exam-production.up.railway.app/api/users/${clientId}`, { headers }),
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
                ex.exercise_sets = (ex.sets ?? ex.exercise_sets ?? []).map((s, i) => ({
                    ...s, set_number: i + 1, done: false, weight: toDisplay(s.weight),
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
    if (logId.value) return;
    try {
        const res = await axios.post(
            `https://myscle-exam-production.up.railway.app/api/trainer/client/${clientId}/workout-logs/start`,
            { training_plan_id: workoutId },
            { headers },
        );
        logId.value = res.data.id;
        isStarted.value = true;
        timer = setInterval(() => seconds.value++, 1000);
    } catch (err) { console.error(err); }
};

const finishWorkout = async () => {
    const totalSets = exercises.value.reduce((s, ex) => s + ex.exercise_sets.length, 0);
    if (totalSets === 0) { alert("Add at least one set before finishing"); return; }
    const hasEmpty = exercises.value.some((ex) => ex.exercise_sets.some((s) => !s.reps || s.reps <= 0));
    if (hasEmpty) { alert("All sets must have at least 1 rep"); return; }

    finishing.value = true;
    clearInterval(timer);
    try {
        const sets = [];
        exercises.value.forEach((ex) => {
            ex.exercise_sets.forEach((set, i) => {
                sets.push({ exercise_name: ex.name, set_number: i + 1, reps: set.reps, weight: toKg(set.weight) });
            });
        });
        await axios.post(
            `https://myscle-exam-production.up.railway.app/api/trainer/client/${clientId}/workout-logs/${logId.value}/finish`,
            { duration_seconds: seconds.value, sets },
            { headers },
        );
        showFinished.value = true;
    } catch (err) {
        console.error(err);
        finishing.value = false;
    }
};

const toggleSet = (set) => { set.done = !set.done; };

const getFilteredLibrary = (ex) => {
    const query = searchQueries.value[ex.id]?.toLowerCase() || "";
    return library.value.filter((item) => item.name.toLowerCase().includes(query)).slice(0, 5);
};

const selectExercise = (ex, item) => {
    ex.name = item.name; ex.library_id = item.id;
    searchQueries.value[ex.id] = item.name; activeDropdown.value = null;
};

const addCustomExercise = async (ex) => {
    try {
        const res = await axios.post("https://myscle-exam-production.up.railway.app/api/exercise-library", { name: searchQueries.value[ex.id] }, { headers });
        library.value.push(res.data); ex.name = res.data.name; ex.library_id = res.data.id; activeDropdown.value = null;
    } catch (err) { console.error(err); }
};

const addExercise = () => {
    const id = Date.now();
    exercises.value.push({ id, name: "", library_id: null, exercise_sets: [], notes: "" });
    searchQueries.value[id] = "";
};

const removeExercise = (index) => {
    const ex = exercises.value[index];
    delete searchQueries.value[ex.id];
    exercises.value.splice(index, 1);
};

const addSet = (ex) => { ex.exercise_sets.push({ reps: 0, weight: 0, done: false }); };
const removeSet = (ex, index) => { ex.exercise_sets.splice(index, 1); };

const saveWorkout = async () => {
    saving.value = true;
    try {
        for (const ex of exercises.value) {
            const setsToSave = ex.exercise_sets.map((s) => ({ ...s, weight: toKg(s.weight) }));
            if (ex.id && typeof ex.id === "number" && ex.id < 1000000000) {
                await axios.put(`https://myscle-exam-production.up.railway.app/api/exercises/${ex.id}`, { name: ex.name, sets_data: setsToSave, notes: ex.notes }, { headers });
            } else {
                await axios.post("https://myscle-exam-production.up.railway.app/api/exercises", { workout_id: workout.value.id, name: ex.name, library_id: ex.library_id, sets_data: setsToSave, notes: ex.notes }, { headers });
            }
        }
        isEditing.value = false;
        showSuccess.value = true;
        setTimeout(() => (showSuccess.value = false), 1500);
    } catch (err) { console.error(err); }
    finally { saving.value = false; }
};
</script>

<template>
    <div class="h-[100dvh] bg-[#080808] text-white flex flex-col">

        <div v-if="loading" class="flex-1 flex items-center justify-center text-gray-400">
            <i class="fas fa-spinner fa-spin mr-3"></i> Loading...
        </div>

        <template v-else-if="workout">

            <div class="hidden md:flex h-full">

                <aside class="w-64 bg-[#0f0f0f] border-r border-white/5 flex flex-col px-6 py-8 fixed h-full z-40">
                    <div class="flex items-center gap-3 mb-12">
                        <img src="/logo.png" class="h-8" />
                        <span class="font-black text-lg tracking-widest uppercase">Myscle</span>
                    </div>
                    <nav class="space-y-1 flex-1">
                        <div @click="router.back()" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                            <i class="fas fa-arrow-left w-4"></i> Back
                        </div>
                        <div @click="router.push('/trainer')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                            <i class="fas fa-users w-4"></i> Clients
                        </div>
                        <div @click="router.push('/trainer/messages')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                            <i class="fas fa-comment w-4"></i> Messages
                        </div>
                        <div @click="router.push('/trainer/profile')" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all">
                            <i class="fas fa-user w-4"></i> Profile
                        </div>
                    </nav>

                    <div v-if="isStarted" class="bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl p-4 text-center mb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Timer</p>
                        <p class="text-3xl font-mono font-black text-[#7ED957]">{{ formatTime(seconds) }}</p>
                    </div>

                    <button v-if="!isStarted" @click="startWorkout"
                        class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2 mb-2">
                        <i class="fas fa-play text-xs"></i> Start Workout
                    </button>
                    <button v-else @click="finishWorkout" :disabled="finishing"
                        class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all flex items-center justify-center gap-2 disabled:opacity-50 mb-2">
                        <i class="fas fa-flag-checkered text-xs"></i> Finish Workout
                    </button>

                    <div @click="router.push('/dashboard')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 font-semibold text-sm cursor-pointer transition-all">
                        <i class="fas fa-dumbbell w-4"></i> Switch to Athlete
                    </div>
                </aside>

                <div class="ml-64 flex-1 flex flex-col overflow-hidden">
                    <div class="bg-[#0f0f0f] border-b border-white/5 px-8 py-5 shrink-0 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-0.5">{{ client?.name ?? "Client" }}</p>
                            <h1 class="text-2xl font-bold">{{ workout.name }}</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <span v-if="isStarted" class="font-mono text-xl font-bold text-[#7ED957] bg-[#7ED957]/10 border border-[#7ED957]/20 px-4 py-2 rounded-xl">
                                {{ formatTime(seconds) }}
                            </span>
                            <button v-if="!isEditing" @click="isEditing = true"
                                class="px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl font-semibold text-sm hover:bg-white/10 transition-all">
                                Edit
                            </button>
                            <button v-else @click="saveWorkout" :disabled="saving"
                                class="px-4 py-2.5 bg-[#7ED957] text-black rounded-xl font-bold text-sm hover:bg-[#6bc947] transition-all">
                                {{ saving ? "Saving..." : "Save Changes" }}
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-8 pt-5 pb-10">
                        <div class="max-w-3xl mx-auto space-y-4">
                            <div v-for="(ex, exIndex) in exercises" :key="ex.id" class="bg-[#111] border border-white/5 rounded-3xl p-6 hover:border-white/10 transition-all">
                                <div v-if="isEditing" class="relative mb-5">
                                    <input v-model="searchQueries[ex.id]" @focus="activeDropdown = ex.id" placeholder="Search or create exercise..." class="w-full px-4 py-3 rounded-2xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all" />
                                    <div v-if="activeDropdown === ex.id && searchQueries[ex.id]" class="absolute w-full mt-2 bg-[#151515] border border-white/10 rounded-2xl shadow-2xl max-h-48 overflow-y-auto z-50">
                                        <div v-for="item in getFilteredLibrary(ex)" :key="item.id" @click="selectExercise(ex, item)" class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm">
                                            {{ item.name }}<span class="text-gray-500 ml-2 text-xs">({{ item.muscle_group }})</span>
                                        </div>
                                        <div v-if="getFilteredLibrary(ex).length === 0" @click="addCustomExercise(ex)" class="px-4 py-3 text-[#7ED957] cursor-pointer hover:bg-white/5 text-sm">
                                            + Add "{{ searchQueries[ex.id] }}"
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="font-bold text-lg mb-4">{{ ex.name }}</p>

                                <div class="grid grid-cols-12 gap-3 text-xs text-gray-600 uppercase tracking-wider px-2 mb-2">
                                    <span class="col-span-1">#</span>
                                    <span class="col-span-4 text-center">Reps</span>
                                    <span class="col-span-1 text-center">×</span>
                                    <span class="col-span-4 text-center">{{ unit }}</span>
                                    <span class="col-span-2"></span>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div v-for="(set, setIndex) in ex.exercise_sets" :key="setIndex"
                                        class="grid grid-cols-12 gap-3 items-center rounded-xl px-3 py-3 transition-all"
                                        :class="isStarted ? set.done ? 'bg-[#7ED957]/10 border border-[#7ED957]/20 cursor-pointer' : 'bg-[#0a0a0a] border border-white/5 cursor-pointer hover:border-white/10' : 'bg-[#0a0a0a]'"
                                        @click="isStarted ? toggleSet(set) : null">
                                        <span class="col-span-1 text-sm font-semibold" :class="set.done ? 'text-[#7ED957]' : 'text-gray-600'">{{ setIndex + 1 }}</span>
                                        <input v-model.number="set.reps" type="number" min="0"  placeholder="0" :disabled="!isEditing && !isStarted" @click.stop class="col-span-4 bg-transparent outline-none text-center text-sm font-medium" :class="set.done ? 'text-[#7ED957]' : 'text-white'" />
                                        <span class="col-span-1 text-gray-700 text-xs text-center">×</span>
                                        <input v-model.number="set.weight" type="number" min="0"  placeholder="0" :disabled="!isEditing && !isStarted" @click.stop class="col-span-4 bg-transparent outline-none text-center text-sm font-medium" :class="set.done ? 'text-[#7ED957]' : 'text-white'" />
                                        <div class="col-span-2 flex justify-end items-center gap-2">
                                            <i v-if="isStarted" class="fas fa-check text-xs" :class="set.done ? 'text-[#7ED957]' : 'text-gray-800'"></i>
                                            <button v-if="isEditing" @click.stop="removeSet(ex, setIndex)" class="text-red-500 text-xs">✕</button>
                                        </div>
                                    </div>
                                </div>

                                <p v-if="!isEditing && !isStarted && ex.notes" class="mt-3 text-sm text-gray-500 bg-[#0a0a0a] rounded-xl px-3 py-2">{{ ex.notes }}</p>
                                <textarea v-if="isEditing || isStarted" v-model="ex.notes" placeholder="Exercise notes..." rows="2" class="w-full mt-3 bg-[#0a0a0a] border border-white/5 rounded-xl px-3 py-2 text-sm text-gray-400 placeholder-gray-700 outline-none focus:border-[#7ED957] resize-none transition-all"></textarea>

                                <div v-if="isEditing" class="flex items-center justify-between mt-2">
                                    <button @click="addSet(ex)" class="text-[#7ED957] text-sm font-medium">+ Add Set</button>
                                    <button @click="removeExercise(exIndex)" class="text-red-400 text-sm">Remove Exercise</button>
                                </div>
                            </div>

                            <button v-if="isEditing" @click="addExercise" class="w-full py-4 bg-white/5 border border-white/10 text-white rounded-3xl font-semibold text-sm hover:bg-white/10 transition-all">
                                + Add Exercise
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:hidden flex flex-col h-full">
                <div class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <button @click="router.back()" class="w-10 h-10 flex items-center justify-center bg-black/15 rounded-xl text-black text-lg font-bold">←</button>
                        <span v-if="isStarted" class="text-2xl font-mono font-bold tracking-widest">{{ formatTime(seconds) }}</span>
                        <button v-if="!isEditing" @click="isEditing = true" class="bg-black/15 text-black px-4 py-2 rounded-xl text-sm font-bold">Edit</button>
                        <button v-else @click="saveWorkout" :disabled="saving" class="bg-black text-[#7ED957] px-4 py-2 rounded-xl text-sm font-bold">{{ saving ? "Saving..." : "Save" }}</button>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight">{{ workout.name }}</h1>
                    <p class="text-black/60 text-sm mt-1">{{ exercises.length }} exercises</p>
                </div>

                <div class="flex-1 overflow-y-auto px-5 pt-5 pb-28 space-y-4">
                    <div v-for="(ex, exIndex) in exercises" :key="ex.id" class="bg-[#111] border border-white/5 rounded-2xl p-5">
                        <div v-if="isEditing" class="relative mb-4">
                            <input v-model="searchQueries[ex.id]" @focus="activeDropdown = ex.id" placeholder="Search or create exercise..." class="w-full px-4 py-3 rounded-xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all" />
                            <div v-if="activeDropdown === ex.id && searchQueries[ex.id]" class="absolute w-full mt-2 bg-[#151515] border border-white/10 rounded-xl shadow-2xl max-h-48 overflow-y-auto z-50">
                                <div v-for="item in getFilteredLibrary(ex)" :key="item.id" @click="selectExercise(ex, item)" class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm">
                                    {{ item.name }}<span class="text-gray-500 ml-2 text-xs">({{ item.muscle_group }})</span>
                                </div>
                                <div v-if="getFilteredLibrary(ex).length === 0" @click="addCustomExercise(ex)" class="px-4 py-3 text-[#7ED957] cursor-pointer hover:bg-white/5 text-sm">
                                    + Add "{{ searchQueries[ex.id] }}"
                                </div>
                            </div>
                        </div>
                        <p v-else class="font-bold text-base mb-4">{{ ex.name }}</p>

                        <div class="grid grid-cols-12 gap-2 text-xs text-gray-600 uppercase tracking-wider px-1 mb-2">
                            <span class="col-span-1">#</span>
                            <span class="col-span-4 text-center">Reps</span>
                            <span class="col-span-1 text-center text-gray-700">×</span>
                            <span class="col-span-4 text-center">{{ unit }}</span>
                            <span class="col-span-2"></span>
                        </div>

                        <div class="space-y-2 mb-3">
                            <div v-for="(set, setIndex) in ex.exercise_sets" :key="setIndex"
                                class="grid grid-cols-12 gap-2 items-center rounded-xl px-3 py-2.5 transition-all"
                                :class="isStarted ? set.done ? 'bg-[#7ED957]/15 border border-[#7ED957]/30 cursor-pointer' : 'bg-[#0a0a0a] border border-white/5 cursor-pointer' : 'bg-[#0a0a0a]'"
                                @click="isStarted ? toggleSet(set) : null">
                                <span class="col-span-1 text-sm" :class="set.done ? 'text-[#7ED957] font-bold' : 'text-gray-600'">{{ setIndex + 1 }}</span>
                                <input v-model.number="set.reps" type="number" min="0"  placeholder="0" :disabled="!isEditing && !isStarted" @click.stop class="col-span-4 bg-transparent outline-none text-center text-sm" :class="set.done ? 'text-[#7ED957]' : 'text-white'" />
                                <span class="col-span-1 text-gray-700 text-xs text-center">×</span>
                                <input v-model.number="set.weight" type="number" min="0" placeholder="0" :disabled="!isEditing && !isStarted" @click.stop class="col-span-4 bg-transparent outline-none text-center text-sm" :class="set.done ? 'text-[#7ED957]' : 'text-white'" />
                                <div class="col-span-2 flex justify-end">
                                    <i v-if="isStarted" class="fas fa-check text-xs" :class="set.done ? 'text-[#7ED957]' : 'text-gray-800'"></i>
                                    <button v-if="isEditing" @click.stop="removeSet(ex, setIndex)" class="text-red-500 text-xs">✕</button>
                                </div>
                            </div>
                        </div>

                        <p v-if="!isEditing && !isStarted && ex.notes" class="mt-3 text-sm text-gray-500 bg-[#0a0a0a] rounded-xl px-3 py-2">{{ ex.notes }}</p>
                        <textarea v-if="isEditing || isStarted" v-model="ex.notes" placeholder="Exercise notes..." rows="2" class="w-full mt-3 bg-[#0a0a0a] border border-white/5 rounded-xl px-3 py-2 text-sm text-gray-400 placeholder-gray-700 outline-none focus:border-[#7ED957] resize-none transition-all"></textarea>

                        <div v-if="isEditing" class="flex items-center justify-between mt-2">
                            <button @click="addSet(ex)" class="text-[#7ED957] text-sm font-medium">+ Add Set</button>
                            <button @click="removeExercise(exIndex)" class="text-red-400 text-sm">Remove Exercise</button>
                        </div>
                    </div>

                    <button v-if="isEditing" @click="addExercise" class="w-full py-4 bg-white/5 border border-white/10 text-white rounded-3xl font-semibold text-sm hover:bg-white/10 transition-all">
                        + Add Exercise
                    </button>
                </div>

                <button v-if="!isStarted && !isEditing" @click="startWorkout" class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50">
                    <i class="fas fa-play text-lg"></i>
                </button>
                <button v-if="isStarted" @click="finishWorkout" :disabled="finishing" class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50 disabled:opacity-50">
                    <i class="fas fa-flag-checkered text-lg"></i>
                </button>
            </div>

        </template>

        <transition name="fade">
            <div v-if="showSuccess" class="fixed inset-0 flex items-center justify-center z-50">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                <div class="relative bg-[#111] border border-[#7ED957]/30 px-8 py-6 rounded-2xl shadow-xl text-center">
                    <i class="fas fa-check-circle text-[#7ED957] text-2xl mb-2"></i>
                    <p class="text-[#7ED957] font-bold">Saved successfully</p>
                </div>
            </div>
        </transition>

        <div v-if="showFinished" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-5">
            <div class="bg-[#111] border border-[#7ED957]/30 rounded-3xl p-10 w-full max-w-md text-center">
                <div class="w-16 h-16 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-flag-checkered text-[#7ED957] text-2xl"></i>
                </div>
                <h2 class="text-3xl font-black text-white mb-2">Workout Done!</h2>
                <p class="text-gray-500 mb-6">{{ workout.name }}</p>
                <div class="flex items-center justify-center gap-6 bg-white/5 rounded-2xl p-5 mb-8">
                    <div class="text-center">
                        <p class="text-2xl font-black text-[#7ED957]">{{ formatTime(seconds) }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">Duration</p>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-[#7ED957]">{{ exercises.reduce((s, ex) => s + ex.exercise_sets.length, 0) }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">Sets</p>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-[#7ED957]">{{ exercises.length }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">Exercises</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button @click="showDeleteConfirm = true" class="flex-1 py-3.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl font-semibold text-sm hover:bg-red-500/20 transition-all">Don't save</button>
                    <button @click="showFinished = false; isStarted = false; logId = null;" class="flex-1 py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all">Save & Close</button>
                </div>
            </div>
        </div>

        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 px-5">
            <div class="bg-[#111] border border-red-500/20 rounded-3xl p-8 w-full max-w-sm text-center">
                <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-400 text-lg"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Don't save?</h3>
                <p class="text-gray-500 text-sm mb-6">This will not save the workout <span class="text-white font-semibold">{{ workout.name }}</span> and all its exercises.</p>
                <div class="flex gap-3">
                    <button @click="router.back()"
                        class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white rounded-2xl font-semibold text-sm hover:bg-white/10 transition-all">
                        Don't Save
                    </button>
                    <button @click="showFinished = false; isStarted = false; logId = null;"
                        class="flex-1 py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all">
                        Save & Close
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>