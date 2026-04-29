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
const library = ref([]);
const loading = ref(true);

const searchQueries = ref({});
const activeDropdown = ref(null);

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
        console.error(err);

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
            {
                name: searchQueries.value[ex.id],
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
        );

        const newExercise = res.data;

        library.value.push(newExercise);

        ex.name = newExercise.name;
        ex.library_id = newExercise.id;

        activeDropdown.value = null;
    } catch (err) {
        console.error("CREATE ERROR:", err.response?.data || err.message);
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
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    },
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
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    },
                );
            }
        }

        // ✅ SHOW SUCCESS
        triggerSuccess();
    } catch (err) {
        console.error("SAVE ERROR:", err.response?.data || err.message);
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
    });

    searchQueries.value[id] = "";
};

const removeExercise = (index) => {
    const ex = exercises.value[index];
    delete searchQueries.value[ex.id];
    exercises.value.splice(index, 1);
};
const showSuccess = ref(false);

const triggerSuccess = () => {
    showSuccess.value = true;

    setTimeout(() => {
        showSuccess.value = false;
    }, 1500);
};
</script>

<template>
    <div
        v-if="loading"
        class="h-screen flex items-center justify-center bg-[#0f0f0f] text-gray-400"
    >
        Loading workout...
    </div>

    <div
        v-else-if="workout"
        class="h-screen flex flex-col bg-[#0f0f0f] text-white"
    >
        <div class="p-6 border-b border-gray-800 flex justify-between">
            <div class="flex items-center gap-4">
                <button @click="router.back()" class="text-[#7ED957] text-xl">
                    ←
                </button>

                <input
                    v-model="workout.name"
                    class="bg-black/20 px-4 py-2 rounded-lg text-lg font-semibold max-w-xs w-full"
                />
            </div>

            <button
                @click="saveChanges"
                class="bg-[#7ED957] text-black px-5 py-2 rounded-lg font-semibold"
            >
                Save
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div
                v-for="(ex, exIndex) in exercises"
                :key="ex.id"
                class="bg-[#1a1a1a] border border-white/5 rounded-2xl p-5 flex flex-col gap-4"
            >
                <div class="relative w-full">
                    <input
                        v-model="searchQueries[ex.id]"
                        @focus="activeDropdown = ex.id"
                        placeholder="Search or create exercise..."
                        class="w-full px-4 py-3 rounded-lg bg-[#111] border border-gray-700 focus:border-[#7ED957] outline-none"
                    />

                    <div
                        v-if="activeDropdown === ex.id && searchQueries[ex.id]"
                        class="absolute w-full mt-2 bg-[#1a1a1a] border border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50"
                    >
                        <div
                            v-for="item in getFilteredLibrary(ex)"
                            :key="item.id"
                            @click="selectExercise(ex, item)"
                            class="px-4 py-2 hover:bg-[#2a2a2a] cursor-pointer text-sm"
                        >
                            {{ item.name }}
                            <span class="text-gray-500 ml-2 text-xs">
                                ({{ item.muscle_group }})
                            </span>
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

                <div class="space-y-2">
                    <div
                        v-for="(set, index) in ex.sets"
                        :key="index"
                        class="flex gap-3 items-center w-full"
                    >
                        <span class="w-6 text-gray-400 shrink-0">
                            {{ index + 1 }}
                        </span>

                        <input
                            v-model.number="set.reps"
                            type="number"
                            placeholder="Reps"
                            class="flex-1 min-w-0 px-3 py-2 bg-[#111] rounded border border-gray-700 text-center"
                        />

                        <input
                            v-model.number="set.weight"
                            type="number"
                            placeholder="kg"
                            class="flex-1 min-w-0 px-3 py-2 bg-[#111] rounded border border-gray-700 text-center"
                        />

                        <button
                            @click="removeSet(ex, index)"
                            class="text-red-500 shrink-0"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-2">
                    <button @click="addSet(ex)" class="text-[#7ED957] text-sm">
                        + Add Set
                    </button>

                    <button
                        @click="removeExercise(exIndex)"
                        class="text-red-500 text-sm hover:text-red-400"
                    >
                        Remove Exercise
                    </button>
                </div>
            </div>

            <button
                @click="addExercise"
                class="w-full py-3 bg-[#7ED957] text-black rounded-xl font-semibold"
            >
                + Add Exercise
            </button>
        </div>

        <BottomNav />
    </div>

    <div
        v-else
        class="h-screen flex items-center justify-center bg-[#0f0f0f] text-gray-400"
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
