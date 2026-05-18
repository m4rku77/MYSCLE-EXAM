<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const name = ref("");
const exercises = ref([]);
const library = ref([]);
const error = ref("");
const loading = ref(false);
const unit = localStorage.getItem("unit") || "kg";

const searchQueries = ref({});
const activeDropdown = ref(null);

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");
        const res = await axios.get(
            "http://localhost:8000/api/exercise-library",
            {
                headers: { Authorization: `Bearer ${token}` },
            },
        );
        library.value = res.data;
    } catch (err) {
        console.error(err);
    }
});

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

const addSet = (exercise) => {
    exercise.sets.push({
        set_number: exercise.sets.length + 1,
        reps: "",
        weight: "",
    });
};

const removeSet = (exercise, index) => {
    exercise.sets.splice(index, 1);
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

const saveWorkout = async () => {
    if (!name.value) {
        error.value = "Workout name required";
        return;
    }
    try {
        loading.value = true;
        const token = localStorage.getItem("token");
        await axios.post(
            "http://localhost:8000/api/workouts",
            {
                name: name.value,
                exercises: exercises.value.map((ex) => ({
                    name: ex.name,
                    library_id: ex.library_id,
                    sets: ex.sets,
                })),
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            },
        );
        router.push("/dashboard");
    } catch (err) {
        error.value = "Failed to create workout";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-[100dvh] bg-[#080808] text-white flex flex-col">
        <div
            class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl"
        >
            <div
                class="flex items-center justify-between mb-4 max-w-2xl mx-auto"
            >
                <button
                    @click="router.push('/dashboard')"
                    class="w-10 h-10 flex items-center justify-center bg-black/15 rounded-xl text-black font-bold text-lg"
                >
                    ←
                </button>
                <button
                    @click="saveWorkout"
                    :disabled="loading"
                    class="bg-black text-[#7ED957] px-5 py-2 rounded-xl font-bold text-sm disabled:opacity-50"
                >
                    {{ loading ? "Saving..." : "Save" }}
                </button>
            </div>

            <div class="max-w-2xl mx-auto">
                <p
                    class="text-black/50 text-xs uppercase tracking-widest font-semibold mb-1"
                >
                    New Workout
                </p>
                <input
                    v-model="name"
                    placeholder="Workout name..."
                    class="text-3xl font-black bg-transparent outline-none placeholder-black/30 text-black w-full"
                />
            </div>
        </div>

        <div
            class="flex-1 overflow-y-auto px-5 pt-6 pb-40 max-w-2xl mx-auto w-full space-y-4"
        >
            <p
                v-if="error"
                class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm"
            >
                {{ error }}
            </p>

            <div
                v-for="(ex, exIndex) in exercises"
                :key="ex.id"
                class="bg-[#111] border border-white/5 rounded-2xl p-5"
            >
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                        >Exercise {{ exIndex + 1 }}</span
                    >
                    <button
                        @click="removeExercise(exIndex)"
                        class="text-red-400 hover:text-red-300 text-xs transition-colors"
                    >
                        Remove
                    </button>
                </div>

                <div class="relative mb-4">
                    <input
                        v-model="searchQueries[ex.id]"
                        @focus="activeDropdown = ex.id"
                        placeholder="Search or create exercise..."
                        class="w-full px-4 py-3 rounded-xl bg-[#0a0a0a] border border-white/5 focus:border-[#7ED957] outline-none text-sm transition-all"
                    />

                    <div
                        v-if="activeDropdown === ex.id && searchQueries[ex.id]"
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
                            v-if="getFilteredLibrary(ex).length === 0"
                            @click="addCustomExercise(ex)"
                            class="px-4 py-3 text-[#7ED957] cursor-pointer hover:bg-white/5 text-sm transition-colors"
                        >
                            + Add "{{ searchQueries[ex.id] }}"
                        </div>
                    </div>
                </div>

                <div class="space-y-2 mb-3">
                    <div
                        class="grid grid-cols-12 gap-2 text-xs text-gray-600 uppercase tracking-wider px-1 mb-1"
                    >
                        <span class="col-span-1">#</span>
                        <span class="col-span-5 text-center">Reps</span>
                        <span class="col-span-5 text-center">{{ unit }}</span>
                        <span class="col-span-1"></span>
                    </div>

                    <div
                        v-for="(set, setIndex) in ex.sets"
                        :key="setIndex"
                        class="grid grid-cols-12 gap-2 items-center bg-[#0a0a0a] rounded-xl px-3 py-2.5"
                    >
                        <span class="col-span-1 text-gray-600 text-sm">{{
                            setIndex + 1
                        }}</span>

                        <input
                            v-model="set.reps"
                            type="number"
                            placeholder="0"
                            class="col-span-5 bg-transparent outline-none text-center text-sm"
                        />

                        <input
                            v-model="set.weight"
                            type="number"
                            placeholder="0"
                            class="col-span-5 bg-transparent outline-none text-center text-sm"
                        />

                        <button
                            @click="removeSet(ex, setIndex)"
                            class="col-span-1 text-red-500 text-xs flex justify-end"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <button
                    @click="addSet(ex)"
                    class="text-[#7ED957] text-sm font-medium"
                >
                    + Add Set
                </button>
            </div>

            <div
                v-if="exercises.length === 0"
                class="flex flex-col items-center justify-center py-16 text-gray-600"
            >
                <div
                    class="w-14 h-14 bg-white/5 rounded-3xl flex items-center justify-center mb-3"
                >
                    <i class="fas fa-dumbbell text-xl opacity-40"></i>
                </div>
                <p class="text-sm font-medium">No exercises yet</p>
                <p class="text-xs text-gray-700 mt-1">
                    Tap below to add your first exercise
                </p>
            </div>
        </div>

        <div
            class="fixed bottom-0 left-0 w-full px-5 pb-8 pt-4 bg-gradient-to-t from-[#080808] via-[#080808]/90 to-transparent"
        >
            <div class="max-w-2xl mx-auto space-y-2">
                <button
                    @click="addExercise"
                    class="w-full py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white font-semibold text-sm hover:bg-white/10 transition-all"
                >
                    + Add Exercise
                </button>
                <button
                    @click="saveWorkout"
                    :disabled="loading"
                    class="w-full py-3.5 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all shadow-lg shadow-[#7ED957]/20 disabled:opacity-50"
                >
                    {{ loading ? "Saving..." : "Save Workout" }}
                </button>
            </div>
        </div>
    </div>
</template>
