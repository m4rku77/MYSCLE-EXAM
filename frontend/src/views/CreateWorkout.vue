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
        console.error(err.response?.data || err.message);
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
        console.error(err);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div
        class="h-screen overflow-y-auto bg-[#0f0f0f] text-white px-4 py-10 md:py-16"
    >
        <div class="w-full max-w-2xl mx-auto pb-72">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl md:text-3xl font-bold">Create Workout</h1>

                <button
                    @click="router.push('/dashboard')"
                    class="text-sm text-gray-400 hover:text-white"
                >
                    Cancel
                </button>
            </div>

            <div class="mb-10">
                <label class="text-xs text-gray-500 mb-2 block"
                    >Workout Name</label
                >

                <input
                    v-model="name"
                    placeholder="Push Day"
                    class="w-full bg-[#151515] border border-gray-700 rounded-xl px-4 py-3 outline-none focus:border-[#7ED957]"
                />
            </div>

            <div class="mb-8">
                <div
                    v-for="(ex, exIndex) in exercises"
                    :key="ex.id"
                    class="bg-[#151515] rounded-xl p-5 mb-8"
                >
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-gray-400"
                            >Exercise {{ exIndex + 1 }}</span
                        >

                        <button
                            @click="removeExercise(exIndex)"
                            class="w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-md text-xs hover:bg-red-600 transition"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="relative w-full mb-5">
                        <input
                            v-model="searchQueries[ex.id]"
                            @focus="activeDropdown = ex.id"
                            placeholder="Search or create exercise..."
                            class="w-full px-4 py-3 rounded-lg bg-[#0f0f0f] border border-gray-700 focus:border-[#7ED957] outline-none"
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

                    <div class="mb-4">
                        <div
                            v-for="(set, setIndex) in ex.sets"
                            :key="setIndex"
                            class="flex items-center gap-3 bg-[#0f0f0f] px-3 py-2 rounded-lg mb-3"
                        >
                            <span class="text-xs text-gray-400 w-6">{{
                                setIndex + 1
                            }}</span>

                            <input
                                v-model="set.reps"
                                type="number"
                                placeholder="Reps"
                                class="flex-1 min-w-0 bg-transparent outline-none text-center"
                            />

                            <input
                                v-model="set.weight"
                                type="number"
                                placeholder="kg"
                                class="flex-1 min-w-0 bg-transparent outline-none text-center"
                            />

                            <button
                                @click="removeSet(ex, setIndex)"
                                class="text-red-400 text-xs ml-auto"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <button @click="addSet(ex)" class="text-[#7ED957] text-sm">
                        + Add Set
                    </button>
                </div>
            </div>

            <p v-if="error" class="text-red-400 text-sm mb-3">{{ error }}</p>
        </div>

        <div class="fixed bottom-24 left-0 w-full flex justify-center px-4">
            <div
                class="bg-[#151515] border border-gray-800 rounded-2xl p-3 shadow-lg w-full max-w-2xl"
            >
                <button
                    @click="addExercise"
                    class="w-full py-2 text-sm bg-[#0f0f0f] rounded-lg text-[#7ED957] hover:bg-[#1a1a1a] transition mb-2"
                >
                    + Add Exercise
                </button>

                <button
                    @click="saveWorkout"
                    class="w-full py-2.5 text-sm bg-[#7ED957] text-black rounded-lg font-semibold hover:scale-[1.01] transition"
                >
                    {{ loading ? "Saving..." : "Save Workout" }}
                </button>
            </div>
        </div>
    </div>
</template>
