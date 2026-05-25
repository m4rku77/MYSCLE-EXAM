<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from "axios";
import BottomNav from "../components/BottomNav.vue";

const route = useRoute();
const router = useRouter();

const workoutId = route.params.id;
const unit = localStorage.getItem("unit") || "kg";

const workout = ref(null);
const exercises = ref([]);
const library = ref([]);
const loading = ref(true);

const searchQueries = ref({});
const activeDropdown = ref(null);
const showSuccess = ref(false);

onMounted(async () => {
    const token = localStorage.getItem("token");
    if (!token) {
        router.push("/login");
        return;
    }
    try {
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
        exercises.value.forEach((ex) => {
            searchQueries.value[ex.id] = ex.name;
            ex.notes = ex.notes ?? "";
            ex.sets = ex.sets ?? [];
        });
        const libRes = await axios.get(
            "http://localhost:8000/api/exercise-library",
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            },
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
        const token = localStorage.getItem("token");
        const res = await axios.post(
            "http://localhost:8000/api/exercise-library",
            { name: searchQueries.value[ex.id] },
            { headers: { Authorization: `Bearer ${token}` } },
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
        const token = localStorage.getItem("token");
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
                    { headers: { Authorization: `Bearer ${token}` } },
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
                    { headers: { Authorization: `Bearer ${token}` } },
                );
            }
        }
        showSuccess.value = true;
        setTimeout(() => (showSuccess.value = false), 1500);
    } catch (err) {
        console.error(err);
    }
};

const addSet = (ex) => {
    ex.sets.push({ reps: 0, weight: 0 });
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
            </nav>

            <button
                @click="saveChanges"
                class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 flex items-center justify-center gap-2"
            >
                <i class="fas fa-save text-xs"></i> Save Changes
            </button>

            <main class="ml-64 flex-1 overflow-y-auto">
                <div class="max-w-3xl mx-auto px-10 py-10">
                    <div class="flex items-start justify-between mb-10">
                        <div>
                            <p
                                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-2"
                            >
                                Edit Workout
                            </p>
                            <input
                                v-model="workout.name"
                                class="text-4xl font-black bg-transparent outline-none border-b border-transparent focus:border-white/20 transition-all pb-1"
                            />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(ex, exIndex) in exercises"
                            :key="ex.id"
                            class="bg-[#111] border border-white/5 rounded-3xl p-6 hover:border-white/10 transition-all"
                        >
                            <div class="relative mb-5">
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
                                        class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm transition-colors"
                                    >
                                        {{ item.name }}
                                        <span class="text-gray-500 ml-2 text-xs"
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
                                    class="grid grid-cols-12 gap-3 items-center bg-[#0a0a0a] rounded-xl px-3 py-3"
                                >
                                    <span
                                        class="col-span-1 text-sm text-gray-600 font-semibold"
                                        >{{ index + 1 }}</span
                                    >
                                    <input
                                        v-model.number="set.reps"
                                        type="number"
                                        placeholder="0"
                                        class="col-span-4 bg-transparent outline-none text-center text-sm font-medium"
                                    />
                                    <span
                                        class="col-span-1 text-gray-700 text-xs text-center"
                                        >×</span
                                    >
                                    <input
                                        v-model.number="set.weight"
                                        type="number"
                                        placeholder="0"
                                        class="col-span-4 bg-transparent outline-none text-center text-sm font-medium"
                                    />
                                    <div class="col-span-2 flex justify-end">
                                        <button
                                            @click="removeSet(ex, index)"
                                            class="text-red-500 text-xs hover:text-red-400 transition-colors"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p v-if="!ex.notes" class="hidden"></p>
                            <p
                                v-if="ex.notes"
                                class="mt-3 text-sm text-gray-500 bg-[#0a0a0a] rounded-xl px-3 py-2"
                            >
                                {{ ex.notes }}
                            </p>
                            <textarea
                                v-model="ex.notes"
                                placeholder="Exercise notes..."
                                rows="2"
                                class="w-full mt-3 bg-[#0a0a0a] border border-white/5 rounded-xl px-3 py-2 text-sm text-gray-400 placeholder-gray-700 outline-none focus:border-[#7ED957] resize-none transition-all"
                            ></textarea>

                            <div class="flex items-center justify-between mt-2">
                                <button
                                    @click="addSet(ex)"
                                    class="text-[#7ED957] text-sm font-medium hover:text-[#6bc947] transition-colors"
                                >
                                    + Add Set
                                </button>
                                <button
                                    @click="removeExercise(exIndex)"
                                    class="text-red-400 text-sm hover:text-red-300 transition-colors"
                                >
                                    Remove Exercise
                                </button>
                            </div>
                        </div>

                        <button
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
                    <button
                        @click="saveChanges"
                        class="bg-black text-[#7ED957] px-4 py-2 rounded-xl text-sm font-bold"
                    >
                        Save
                    </button>
                </div>
                <p
                    class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                >
                    Edit Workout
                </p>
                <input
                    v-model="workout.name"
                    class="text-3xl font-black bg-transparent outline-none text-black w-full placeholder-black/30"
                    placeholder="Workout name..."
                />
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-5 pb-32 space-y-4">
                <div
                    v-for="(ex, exIndex) in exercises"
                    :key="ex.id"
                    class="bg-[#111] border border-white/5 rounded-2xl p-5"
                >
                    <div class="relative mb-4">
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
                                class="px-4 py-3 hover:bg-white/5 cursor-pointer text-sm"
                            >
                                {{ item.name }}
                                <span class="text-gray-500 ml-2 text-xs"
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

                    <div
                        class="grid grid-cols-12 gap-2 text-xs text-gray-600 uppercase tracking-wider px-1 mb-2"
                    >
                        <span class="col-span-1">#</span>
                        <span class="col-span-4 text-center">Reps</span>
                        <span class="col-span-1 text-center">×</span>
                        <span class="col-span-4 text-center">{{ unit }}</span>
                        <span class="col-span-2"></span>
                    </div>

                    <div class="space-y-2 mb-3">
                        <div
                            v-for="(set, index) in ex.sets"
                            :key="index"
                            class="grid grid-cols-12 gap-2 items-center bg-[#0a0a0a] rounded-xl px-3 py-2.5"
                        >
                            <span class="col-span-1 text-sm text-gray-600">{{
                                index + 1
                            }}</span>
                            <input
                                v-model.number="set.reps"
                                type="number"
                                placeholder="0"
                                class="col-span-4 bg-transparent outline-none text-center text-sm"
                            />
                            <span
                                class="col-span-1 text-gray-700 text-xs text-center"
                                >×</span
                            >
                            <input
                                v-model.number="set.weight"
                                type="number"
                                placeholder="0"
                                class="col-span-4 bg-transparent outline-none text-center text-sm"
                            />
                            <div class="col-span-2 flex justify-end">
                                <button
                                    @click="removeSet(ex, index)"
                                    class="text-red-500 text-xs"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    <p
                        v-if="ex.notes && ex.notes.length > 0"
                        class="mt-3 text-sm text-gray-500 bg-[#0a0a0a] rounded-xl px-3 py-2"
                    >
                        {{ ex.notes }}
                    </p>
                    <textarea
                        v-model="ex.notes"
                        placeholder="Exercise notes..."
                        rows="2"
                        class="w-full mt-3 bg-[#0a0a0a] border border-white/5 rounded-xl px-3 py-2 text-sm text-gray-400 placeholder-gray-700 outline-none focus:border-[#7ED957] resize-none transition-all"
                    ></textarea>

                    <div class="flex items-center justify-between pt-1">
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
                    @click="addExercise"
                    class="w-full py-3.5 bg-white/5 border border-white/10 text-white rounded-2xl font-semibold text-sm hover:bg-white/10 transition-all"
                >
                    + Add Exercise
                </button>
            </div>

            <BottomNav class="fixed bottom-0 left-0 w-full z-40" />
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
