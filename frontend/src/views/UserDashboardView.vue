<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import BottomNav from "../components/BottomNav.vue";

const router = useRouter();
const workouts = ref([]);
const search = ref("");
const loading = ref(true);

const createWorkout = () => router.push("/create-workout");
const openWorkout = (w) => router.push(`/workout/${w.id}`);

onMounted(async () => {
    try {
        const token = localStorage.getItem("token");
        if (!token) return;
        const res = await axios.get("http://localhost:8000/api/workouts", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });
        const data = res.data.data ?? res.data;
        workouts.value = data.map((w) => ({
            id: w.id,
            name: w.name,
            exercises: w.exercises?.length || 0,
            sets:
                w.exercises?.reduce(
                    (sum, ex) => sum + (ex.sets?.length || 0),
                    0,
                ) || 0,
            reps:
                w.exercises?.reduce(
                    (sum, ex) =>
                        sum +
                        (ex.sets?.reduce((s, set) => s + (set.reps || 0), 0) ||
                            0),
                    0,
                ) || 0,
        }));
    } catch {
        workouts.value = [];
    } finally {
        loading.value = false;
    }
});

const filtered = computed(() =>
    workouts.value.filter((w) =>
        w.name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);
</script>

<template>
    <div class="min-h-screen bg-[#080808] text-white flex flex-col">
        <div class="hidden md:flex h-screen">
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
                        class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm cursor-pointer"
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

                <button
                    @click="createWorkout"
                    class="w-full py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all hover:scale-[1.02] shadow-lg shadow-[#7ED957]/20"
                >
                    <i class="fas fa-plus mr-2"></i> New Workout
                </button>
            </aside>

            <main class="ml-64 flex-1 p-10">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <p
                                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
                            >
                                Dashboard
                            </p>
                            <h1 class="text-4xl font-black">My Workouts</h1>
                        </div>
                        <div class="relative">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"
                            ></i>
                            <input
                                v-model="search"
                                placeholder="Search workouts..."
                                class="pl-10 pr-4 py-3 bg-[#111] border border-white/5 rounded-2xl outline-none focus:border-[#7ED957] text-sm w-64 transition-all"
                            />
                        </div>
                    </div>

                    <div
                        v-if="loading"
                        class="flex items-center justify-center py-20 text-gray-600"
                    >
                        <i class="fas fa-spinner fa-spin mr-3"></i> Loading...
                    </div>

                    <div
                        v-else-if="filtered.length > 0"
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <div
                            v-for="w in filtered"
                            :key="w.id"
                            @click="openWorkout(w)"
                            class="group bg-[#111] border border-white/5 rounded-3xl p-6 cursor-pointer hover:border-[#7ED957]/30 hover:-translate-y-1 transition-all duration-300"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-10 h-10 bg-[#7ED957]/10 rounded-2xl flex items-center justify-center group-hover:bg-[#7ED957]/20 transition-all"
                                >
                                    <i
                                        class="fas fa-dumbbell text-[#7ED957] text-sm"
                                    ></i>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-700 group-hover:text-[#7ED957] transition-colors"
                                ></i>
                            </div>

                            <h3
                                class="font-bold text-lg mb-1 group-hover:text-[#7ED957] transition-colors"
                            >
                                {{ w.name }}
                            </h3>
                            <p class="text-gray-600 text-xs mb-4">
                                Last completed: Never
                            </p>

                            <div class="flex items-center gap-4 text-xs">
                                <div class="bg-white/5 rounded-xl px-3 py-1.5">
                                    <span class="text-[#7ED957] font-bold">{{
                                        w.exercises
                                    }}</span>
                                    <span class="text-gray-500 ml-1"
                                        >exercises</span
                                    >
                                </div>
                                <div class="bg-white/5 rounded-xl px-3 py-1.5">
                                    <span class="text-[#7ED957] font-bold">{{
                                        w.sets
                                    }}</span>
                                    <span class="text-gray-500 ml-1">sets</span>
                                </div>
                                <div class="bg-white/5 rounded-xl px-3 py-1.5">
                                    <span class="text-[#7ED957] font-bold">{{
                                        w.reps
                                    }}</span>
                                    <span class="text-gray-500 ml-1">reps</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-24 text-gray-600"
                    >
                        <div
                            class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center mb-4"
                        >
                            <i class="fas fa-dumbbell text-2xl opacity-40"></i>
                        </div>
                        <p class="font-semibold mb-1">No workouts yet</p>
                        <p class="text-sm text-gray-700 mb-6">
                            Create your first workout to get started
                        </p>
                        <button
                            @click="createWorkout"
                            class="px-6 py-3 bg-[#7ED957] text-black rounded-2xl font-bold text-sm hover:bg-[#6bc947] transition-all"
                        >
                            Create Workout
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- MOBILE VIEW -->
        <div class="md:hidden flex flex-col h-[100dvh]">
            <div
                class="bg-gradient-to-b from-[#7ED957] to-[#5fcf47] text-black px-5 pt-12 pb-8 rounded-b-3xl"
            >
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p
                            class="text-black/50 text-xs font-semibold uppercase tracking-wider"
                        >
                            Dashboard
                        </p>
                        <h1 class="text-3xl font-black">Workouts</h1>
                    </div>
                    <div
                        class="w-10 h-10 bg-black/10 rounded-2xl flex items-center justify-center"
                    >
                        <i class="fas fa-dumbbell text-black"></i>
                    </div>
                </div>

                <div class="relative">
                    <i
                        class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-black/40 text-sm"
                    ></i>
                    <input
                        v-model="search"
                        placeholder="Search workouts..."
                        class="w-full pl-10 pr-4 py-3 bg-black/15 rounded-2xl outline-none placeholder-black/40 text-black text-sm font-medium"
                    />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pt-5 pb-28 space-y-3">
                <div
                    v-if="loading"
                    class="flex items-center justify-center py-20 text-gray-600"
                >
                    <i class="fas fa-spinner fa-spin mr-3"></i>
                </div>

                <template v-else-if="filtered.length > 0">
                    <div
                        v-for="w in filtered"
                        :key="w.id"
                        @click="openWorkout(w)"
                        class="group bg-[#111] border border-white/5 rounded-2xl p-5 cursor-pointer active:scale-[0.98] transition-all"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-base truncate mb-1">
                                    {{ w.name }}
                                </h3>
                                <div
                                    class="flex items-center gap-3 text-xs text-gray-500"
                                >
                                    <span
                                        ><span
                                            class="text-[#7ED957] font-bold"
                                            >{{ w.exercises }}</span
                                        >
                                        ex</span
                                    >
                                    <span
                                        class="w-1 h-1 bg-gray-700 rounded-full"
                                    ></span>
                                    <span
                                        ><span
                                            class="text-[#7ED957] font-bold"
                                            >{{ w.sets }}</span
                                        >
                                        sets</span
                                    >
                                    <span
                                        class="w-1 h-1 bg-gray-700 rounded-full"
                                    ></span>
                                    <span
                                        ><span
                                            class="text-[#7ED957] font-bold"
                                            >{{ w.reps }}</span
                                        >
                                        reps</span
                                    >
                                </div>
                            </div>
                            <i
                                class="fas fa-chevron-right text-gray-700 ml-4"
                            ></i>
                        </div>
                    </div>
                </template>

                <div
                    v-else
                    class="flex flex-col items-center justify-center py-20 text-gray-600"
                >
                    <div
                        class="w-14 h-14 bg-white/5 rounded-3xl flex items-center justify-center mb-3"
                    >
                        <i class="fas fa-dumbbell text-xl opacity-40"></i>
                    </div>
                    <p class="font-semibold text-sm">No workouts yet</p>
                    <p class="text-xs text-gray-700 mt-1">
                        Tap + to create your first
                    </p>
                </div>
            </div>

            <BottomNav class="fixed bottom-0 left-0 w-full z-40" />

            <button
                @click="createWorkout"
                class="fixed bottom-24 right-5 w-14 h-14 bg-[#7ED957] text-black rounded-full grid place-items-center shadow-lg shadow-[#7ED957]/30 hover:scale-110 active:scale-95 transition-all z-50"
            >
                <i class="fas fa-plus text-lg"></i>
            </button>
        </div>
    </div>
</template>
