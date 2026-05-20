<script setup>
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Chart from "chart.js/auto";

const router = useRouter();

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const logs = ref([]);
const loading = ref(true);

const activeTab = ref("overview");
const viewMode = ref("month");
const selectedMonth = ref(new Date().getMonth());
const selectedYear = ref(new Date().getFullYear());

const chartRef = ref(null);
const strengthChartRef = ref(null);
const monthlyWeightChartRef = ref(null);
let chartInstance = null;
let strengthChart = null;
let monthlyWeightChart = null;

const selectedDay = ref(null);
const showDayModal = ref(false);
const selectedExercise = ref(null);
const exerciseSearch = ref("");
const showExerciseDropdown = ref(false);

const hideDropdown = () => {
    setTimeout(() => (showExerciseDropdown.value = false), 150);
};

const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

onMounted(async () => {
    try {
        const res = await axios.get("http://localhost:8000/api/workout-logs", {
            headers,
        });
        logs.value = res.data.data ?? res.data;
        if (exerciseList.value.length) {
            selectedExercise.value = exerciseList.value[0];
            exerciseSearch.value = exerciseList.value[0];
        }
        await nextTick();
        setTimeout(() => createChart(), 50);
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
});

const totalWorkouts = computed(() => logs.value.length);
const totalSets = computed(() =>
    logs.value.reduce((s, l) => s + (l.sets?.length || 0), 0),
);
const avgDuration = computed(() => {
    const w = logs.value.filter((l) => l.duration_seconds);
    if (!w.length) return 0;
    return Math.round(w.reduce((s, l) => s + l.duration_seconds, 0) / w.length);
});
const longestStreak = computed(() => {
    if (!logs.value.length) return 0;
    const dates = [
        ...new Set(
            logs.value.map((l) => new Date(l.created_at).toDateString()),
        ),
    ].sort((a, b) => new Date(a) - new Date(b));
    let max = 1,
        cur = 1;
    for (let i = 1; i < dates.length; i++) {
        const diff = (new Date(dates[i]) - new Date(dates[i - 1])) / 86400000;
        cur = diff === 1 ? cur + 1 : 1;
        if (cur > max) max = cur;
    }
    return max;
});
const workoutDaysSet = computed(() => {
    const set = new Set();
    logs.value.forEach((l) => {
        if (!l.created_at) return;
        const d = new Date(l.created_at);
        set.add(`${d.getFullYear()}-${d.getMonth()}-${d.getDate()}`);
    });
    return set;
});
const hasWorkout = (year, month, day) =>
    workoutDaysSet.value.has(`${year}-${month}-${day}`);
const filteredLogs = computed(() =>
    logs.value.filter((l) => {
        if (!l.created_at) return false;
        const d = new Date(l.created_at);
        if (viewMode.value === "year")
            return d.getFullYear() === selectedYear.value;
        return (
            d.getFullYear() === selectedYear.value &&
            d.getMonth() === selectedMonth.value
        );
    }),
);
const getCalendarDays = () => {
    const firstDay = new Date(selectedYear.value, selectedMonth.value, 1);
    const lastDate = new Date(
        selectedYear.value,
        selectedMonth.value + 1,
        0,
    ).getDate();
    const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay();
    const days = [];
    for (let i = 1; i < startDay; i++) days.push(null);
    for (let d = 1; d <= lastDate; d++) {
        const dayLogs = logs.value.filter((l) => {
            if (!l.created_at) return false;
            const date = new Date(l.created_at);
            return (
                date.getFullYear() === selectedYear.value &&
                date.getMonth() === selectedMonth.value &&
                date.getDate() === d
            );
        });
        days.push({
            day: d,
            count: dayLogs.length,
            sets: dayLogs.reduce((s, l) => s + (l.sets?.length || 0), 0),
            reps: dayLogs.reduce(
                (s, l) =>
                    s +
                    (l.sets?.reduce(
                        (a, set) => a + (Number(set.reps) || 0),
                        0,
                    ) || 0),
                0,
            ),
        });
    }
    return days;
};
const getYearGrid = () => {
    const result = [];
    for (let m = 0; m < 12; m++) {
        const firstDay = new Date(selectedYear.value, m, 1);
        const lastDate = new Date(selectedYear.value, m + 1, 0).getDate();
        const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay();
        const days = [];
        for (let i = 1; i < startDay; i++) days.push(null);
        for (let d = 1; d <= lastDate; d++)
            days.push({ day: d, worked: hasWorkout(selectedYear.value, m, d) });
        result.push({
            name: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ][m],
            days,
        });
    }
    return result;
};
const openDay = (day) => {
    if (!day || day.count === 0) return;
    selectedDay.value = day;
    showDayModal.value = true;
};
const formatDate = (date) => {
    if (!date) return "--";
    return new Date(date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
const formatDuration = (seconds) => {
    if (!seconds) return "--";
    return `${Math.floor(seconds / 60)}m ${seconds % 60}s`;
};
const createChart = () => {
    if (!chartRef.value) return;
    if (chartInstance) chartInstance.destroy();
    const ctx = chartRef.value.getContext("2d");
    const monthData = Array(12).fill(0);
    logs.value.forEach((l) => {
        if (!l.created_at) return;
        const d = new Date(l.created_at);
        if (d.getFullYear() === selectedYear.value) monthData[d.getMonth()]++;
    });
    chartInstance = new Chart(ctx, {
        type: "bar",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            datasets: [
                {
                    data: monthData,
                    backgroundColor: monthData.map((v) =>
                        v === Math.max(...monthData) && v > 0
                            ? "#7ED957"
                            : "rgba(126,217,87,0.2)",
                    ),
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1a1a1a",
                    borderColor: "rgba(126,217,87,0.3)",
                    borderWidth: 1,
                    titleColor: "#7ED957",
                    bodyColor: "#d1d5db",
                    callbacks: { label: (c) => ` ${c.raw} sessions` },
                },
            },
            scales: {
                x: {
                    ticks: { color: "#4b5563", font: { size: 11 } },
                    grid: { display: false },
                },
                y: {
                    ticks: {
                        color: "#4b5563",
                        font: { size: 11 },
                        stepSize: 1,
                    },
                    grid: { color: "rgba(255,255,255,0.04)" },
                    beginAtZero: true,
                },
            },
        },
    });
};

const exerciseList = computed(() => {
    const names = new Set();
    logs.value.forEach((log) =>
        log.sets?.forEach((set) => {
            if (set.exercise_name) names.add(set.exercise_name);
        }),
    );
    return [...names].sort();
});
const filteredExercises = computed(() => {
    if (!exerciseSearch.value) return exerciseList.value;
    return exerciseList.value.filter((e) =>
        e.toLowerCase().includes(exerciseSearch.value.toLowerCase()),
    );
});
const selectExercise = (ex) => {
    selectedExercise.value = ex;
    exerciseSearch.value = ex;
    showExerciseDropdown.value = false;
};
const exerciseData = computed(() => {
    if (!selectedExercise.value) return [];
    const byDate = {};
    logs.value.forEach((log) => {
        if (!log.created_at) return;
        const dateKey = new Date(log.created_at).toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
        });
        const sets =
            log.sets?.filter(
                (s) => s.exercise_name === selectedExercise.value,
            ) ?? [];
        if (!sets.length) return;
        const maxWeight = Math.max(...sets.map((s) => Number(s.weight) || 0));
        const maxReps =
            sets.find((s) => Number(s.weight) === maxWeight)?.reps || 0;
        const orm = Math.round(maxWeight * (1 + Number(maxReps) / 30));
        if (!byDate[dateKey] || orm > byDate[dateKey].orm) {
            byDate[dateKey] = {
                date: dateKey,
                maxWeight,
                maxReps: Number(maxReps),
                orm,
            };
        }
    });
    return Object.values(byDate);
});
const monthlyProgress = computed(() => {
    if (!selectedExercise.value) return [];
    const byMonth = {};
    logs.value.forEach((log) => {
        if (!log.created_at) return;
        const d = new Date(log.created_at);
        const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
        const label = d.toLocaleDateString("en-US", {
            month: "short",
            year: "numeric",
        });
        const sets =
            log.sets?.filter(
                (s) => s.exercise_name === selectedExercise.value,
            ) ?? [];
        if (!sets.length) return;
        const maxWeight = Math.max(...sets.map((s) => Number(s.weight) || 0));
        if (!byMonth[key] || maxWeight > byMonth[key].weight) {
            byMonth[key] = { key, label, weight: maxWeight };
        }
    });
    return Object.values(byMonth).sort((a, b) => a.key.localeCompare(b.key));
});
const bestEver = computed(() =>
    exerciseData.value.length
        ? exerciseData.value.reduce((best, d) => (d.orm > best.orm ? d : best))
        : null,
);
const trend = computed(() => {
    const data = exerciseData.value;
    if (data.length < 2) return 0;
    return data[data.length - 1].maxWeight - data[data.length - 2].maxWeight;
});
const createStrengthChart = async () => {
    await nextTick();
    if (!strengthChartRef.value || !exerciseData.value.length) return;
    if (strengthChart) strengthChart.destroy();
    const ctx = strengthChartRef.value.getContext("2d");
    const data = exerciseData.value;
    strengthChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.map((d) => d.date),
            datasets: [
                {
                    label: "Max Weight (kg)",
                    data: data.map((d) => d.maxWeight),
                    borderColor: "rgba(126,217,87,0.4)",
                    backgroundColor: "transparent",
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: "rgba(126,217,87,0.4)",
                    pointRadius: 3,
                    borderWidth: 1.5,
                    borderDash: [4, 4],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: "#6b7280",
                        boxWidth: 12,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    backgroundColor: "#1a1a1a",
                    borderColor: "rgba(126,217,87,0.3)",
                    borderWidth: 1,
                    titleColor: "#7ED957",
                    bodyColor: "#d1d5db",
                    padding: 10,
                },
            },
            scales: {
                x: {
                    ticks: { color: "#4b5563", font: { size: 11 } },
                    grid: { color: "rgba(255,255,255,0.04)" },
                },
                y: {
                    ticks: { color: "#4b5563", font: { size: 11 } },
                    grid: { color: "rgba(255,255,255,0.04)" },
                    beginAtZero: false,
                },
            },
        },
    });
};
const createMonthlyWeightChart = async () => {
    await nextTick();
    if (!monthlyWeightChartRef.value || !monthlyProgress.value.length) return;
    if (monthlyWeightChart) monthlyWeightChart.destroy();
    const ctx = monthlyWeightChartRef.value.getContext("2d");
    const data = monthlyProgress.value;
    monthlyWeightChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.map((d) => d.label),
            datasets: [
                {
                    label: "Max Weight (kg)",
                    data: data.map((d) => d.weight),
                    borderColor: "#7ED957",
                    backgroundColor: "rgba(126,217,87,0.1)",
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: "#7ED957",
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1a1a1a",
                    borderColor: "rgba(126,217,87,0.3)",
                    borderWidth: 1,
                    titleColor: "#7ED957",
                    bodyColor: "#d1d5db",
                    callbacks: { label: (c) => ` ${c.raw} kg` },
                },
            },
            scales: {
                x: {
                    ticks: { color: "#4b5563", font: { size: 11 } },
                    grid: { display: false },
                },
                y: {
                    ticks: { color: "#4b5563", font: { size: 11 } },
                    grid: { color: "rgba(255,255,255,0.04)" },
                    beginAtZero: false,
                },
            },
        },
    });
};

watch(selectedYear, async () => {
    await nextTick();
    createChart();
});
watch(selectedExercise, async () => {
    await createStrengthChart();
    await createMonthlyWeightChart();
});
watch(activeTab, async (val) => {
    await nextTick();
    if (val === "overview") createChart();
    if (val === "strength") {
        await createStrengthChart();
        await createMonthlyWeightChart();
    }
});
</script>

<template>
    <div class="h-screen bg-[#080808] text-white flex">
        <aside
            class="hidden md:flex w-64 bg-[#0f0f0f] border-r border-white/5 flex-col px-6 py-8 fixed h-full"
        >
            <div class="flex items-center gap-3 mb-12">
                <img src="/logo.png" class="h-8" />
                <span class="font-black text-lg tracking-widest uppercase"
                    >Myscle</span
                >
            </div>
            <nav class="space-y-1 flex-1">
                <div
                    @click="router.push('/dashboard')"
                    class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-2xl font-semibold text-sm cursor-pointer transition-all"
                >
                    <i class="fas fa-dumbbell w-4"></i> Workouts
                </div>
                <div
                    class="flex items-center gap-3 px-4 py-3 bg-[#7ED957]/10 border border-[#7ED957]/20 rounded-2xl text-[#7ED957] font-semibold text-sm"
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
        </aside>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <div
                v-if="loading"
                class="h-full flex items-center justify-center text-gray-600"
            >
                Loading statistics...
            </div>

            <div v-else class="flex-1 overflow-y-auto">
                <div
                    class="bg-[#0f0f0f] border-b border-white/5 px-5 py-6 max-w-5xl mx-auto w-full"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
                            >
                                Training Report
                            </p>
                            <h1 class="text-3xl font-bold">My Statistics</h1>
                        </div>
                        <div
                            class="flex bg-[#1a1a1a] border border-white/10 rounded-2xl p-1"
                        >
                            <button
                                @click="activeTab = 'overview'"
                                :class="
                                    activeTab === 'overview'
                                        ? 'bg-[#7ED957] text-black'
                                        : 'text-gray-400'
                                "
                                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            >
                                Overview
                            </button>
                            <button
                                @click="activeTab = 'strength'"
                                :class="
                                    activeTab === 'strength'
                                        ? 'bg-[#7ED957] text-black'
                                        : 'text-gray-400'
                                "
                                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            >
                                Strength
                            </button>
                        </div>
                    </div>
                </div>

                <div class="max-w-5xl mx-auto px-5 py-6 space-y-5 pb-20">
                    <template v-if="activeTab === 'overview'">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div
                                class="bg-[#111] border border-white/5 rounded-2xl p-5"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                >
                                    Workouts
                                </p>
                                <p class="text-3xl font-bold text-[#7ED957]">
                                    {{ totalWorkouts }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    completed
                                </p>
                            </div>
                            <div
                                class="bg-[#111] border border-white/5 rounded-2xl p-5"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                >
                                    Total Sets
                                </p>
                                <p class="text-3xl font-bold text-[#7ED957]">
                                    {{ totalSets }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    all time
                                </p>
                            </div>
                            <div
                                class="bg-[#111] border border-white/5 rounded-2xl p-5"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                >
                                    Avg Session
                                </p>
                                <p class="text-3xl font-bold text-[#7ED957]">
                                    {{ Math.floor(avgDuration / 60)
                                    }}<span class="text-lg">m</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    duration
                                </p>
                            </div>
                            <div
                                class="bg-[#111] border border-white/5 rounded-2xl p-5"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                >
                                    Best Streak
                                </p>
                                <p class="text-3xl font-bold text-[#7ED957]">
                                    {{ longestStreak
                                    }}<span class="text-lg">d</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    consecutive days
                                </p>
                            </div>
                        </div>

                        <div
                            class="bg-[#111] border border-white/5 rounded-2xl p-6"
                        >
                            <div class="flex items-center justify-between mb-5">
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider"
                                >
                                    Sessions per Month
                                </p>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="selectedYear--"
                                        class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 text-xs flex items-center justify-center"
                                    >
                                        ‹
                                    </button>
                                    <span class="text-sm font-semibold px-1">{{
                                        selectedYear
                                    }}</span>
                                    <button
                                        @click="selectedYear++"
                                        class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 text-xs flex items-center justify-center"
                                    >
                                        ›
                                    </button>
                                </div>
                            </div>
                            <div class="h-48">
                                <canvas ref="chartRef"></canvas>
                            </div>
                        </div>

                        <div
                            class="bg-[#111] border border-white/5 rounded-2xl p-6"
                        >
                            <div class="flex items-center justify-between mb-5">
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider"
                                >
                                    Workout Calendar
                                </p>
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="viewMode = 'month'"
                                        :class="
                                            viewMode === 'month'
                                                ? 'bg-[#7ED957] text-black'
                                                : 'bg-white/5 text-gray-400'
                                        "
                                        class="px-3 py-1 rounded-lg text-xs font-semibold"
                                    >
                                        Month
                                    </button>
                                    <button
                                        @click="viewMode = 'year'"
                                        :class="
                                            viewMode === 'year'
                                                ? 'bg-[#7ED957] text-black'
                                                : 'bg-white/5 text-gray-400'
                                        "
                                        class="px-3 py-1 rounded-lg text-xs font-semibold"
                                    >
                                        Year
                                    </button>
                                </div>
                            </div>

                            <div v-if="viewMode === 'month'">
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <button
                                        @click="
                                            selectedMonth > 0
                                                ? selectedMonth--
                                                : ((selectedMonth = 11),
                                                  selectedYear--)
                                        "
                                        class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 text-xs flex items-center justify-center"
                                    >
                                        ‹
                                    </button>
                                    <p class="text-sm font-semibold">
                                        {{ months[selectedMonth] }}
                                        {{ selectedYear }}
                                    </p>
                                    <button
                                        @click="
                                            selectedMonth < 11
                                                ? selectedMonth++
                                                : ((selectedMonth = 0),
                                                  selectedYear++)
                                        "
                                        class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 text-xs flex items-center justify-center"
                                    >
                                        ›
                                    </button>
                                </div>
                                <div
                                    class="grid grid-cols-7 text-center text-xs text-gray-600 mb-2"
                                >
                                    <span
                                        v-for="d in [
                                            'Mo',
                                            'Tu',
                                            'We',
                                            'Th',
                                            'Fr',
                                            'Sa',
                                            'Su',
                                        ]"
                                        :key="d"
                                        >{{ d }}</span
                                    >
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <div
                                        v-for="(d, i) in getCalendarDays()"
                                        :key="i"
                                        @click="openDay(d)"
                                        class="aspect-square rounded-lg flex items-center justify-center text-xs transition-all"
                                        :class="
                                            !d
                                                ? ''
                                                : d.count === 0
                                                  ? 'bg-white/5 text-gray-700'
                                                  : 'bg-[#7ED957] text-black font-bold cursor-pointer hover:scale-105'
                                        "
                                    >
                                        <span v-if="d">{{ d.day }}</span>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 text-xs text-gray-600 text-center"
                                >
                                    {{ filteredLogs.length }} session{{
                                        filteredLogs.length !== 1 ? "s" : ""
                                    }}
                                    this month
                                </div>
                            </div>

                            <div v-else class="space-y-4">
                                <div
                                    v-for="mth in getYearGrid()"
                                    :key="mth.name"
                                >
                                    <p class="text-xs text-gray-500 mb-2">
                                        {{ mth.name }}
                                    </p>
                                    <div class="grid grid-cols-7 gap-0.5">
                                        <div
                                            v-for="(d, i) in mth.days"
                                            :key="i"
                                            class="aspect-square rounded-sm"
                                            :class="
                                                !d
                                                    ? ''
                                                    : d.worked
                                                      ? 'bg-[#7ED957]'
                                                      : 'bg-white/5'
                                            "
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-[#111] border border-white/5 rounded-2xl p-6"
                        >
                            <p
                                class="text-xs text-gray-500 uppercase tracking-wider mb-1"
                            >
                                Session History
                            </p>
                            <p class="text-sm text-gray-400 mb-5">
                                {{ filteredLogs.length }} sessions shown
                            </p>
                            <div
                                v-if="filteredLogs.length > 0"
                                class="space-y-2 max-h-80 overflow-y-auto"
                            >
                                <div
                                    v-for="log in filteredLogs"
                                    :key="log.id"
                                    class="flex items-center gap-4 bg-[#0f0f0f] border border-white/5 rounded-xl px-4 py-3"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full bg-[#7ED957] shrink-0"
                                    ></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium">
                                            {{ formatDate(log.created_at) }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex items-center gap-5 text-xs text-gray-500 shrink-0"
                                    >
                                        <div class="text-center">
                                            <p
                                                class="text-white font-semibold text-sm"
                                            >
                                                {{ log.sets?.length || 0 }}
                                            </p>
                                            <p>sets</p>
                                        </div>
                                        <div class="text-center">
                                            <p
                                                class="text-white font-semibold text-sm"
                                            >
                                                {{
                                                    log.sets?.reduce(
                                                        (s, set) =>
                                                            s +
                                                            (Number(set.reps) ||
                                                                0),
                                                        0,
                                                    ) || 0
                                                }}
                                            </p>
                                            <p>reps</p>
                                        </div>
                                        <div
                                            class="text-center hidden md:block"
                                        >
                                            <p
                                                class="text-white font-semibold text-sm"
                                            >
                                                {{
                                                    formatDuration(
                                                        log.duration_seconds,
                                                    )
                                                }}
                                            </p>
                                            <p>time</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-600 py-10">
                                <i
                                    class="fas fa-calendar text-2xl mb-3 opacity-20"
                                ></i>
                                <p>No sessions for this period</p>
                            </div>
                        </div>
                    </template>

                    <template v-if="activeTab === 'strength'">
                        <div
                            v-if="exerciseList.length === 0"
                            class="text-center text-gray-600 py-20"
                        >
                            <i
                                class="fas fa-dumbbell text-4xl mb-4 opacity-20"
                            ></i>
                            <p>No exercise data yet</p>
                        </div>

                        <template v-else>
                            <div
                                class="bg-[#111] border border-white/5 rounded-2xl p-6"
                            >
                                <p
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-3"
                                >
                                    Select Exercise
                                </p>
                                <div class="relative">
                                    <input
                                        v-model="exerciseSearch"
                                        @focus="showExerciseDropdown = true"
                                        @blur="hideDropdown"
                                        placeholder="Search exercise..."
                                        class="w-full bg-[#0f0f0f] border border-white/10 rounded-xl px-4 py-3 outline-none focus:border-[#7ED957] text-sm"
                                    />
                                    <div
                                        v-if="
                                            showExerciseDropdown &&
                                            filteredExercises.length
                                        "
                                        class="absolute w-full mt-2 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl max-h-48 overflow-y-auto z-50"
                                    >
                                        <div
                                            v-for="ex in filteredExercises"
                                            :key="ex"
                                            @mousedown="selectExercise(ex)"
                                            class="px-4 py-3 text-sm cursor-pointer transition-all hover:bg-white/5"
                                            :class="
                                                selectedExercise === ex
                                                    ? 'text-[#7ED957] font-semibold'
                                                    : 'text-gray-300'
                                            "
                                        >
                                            {{ ex }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <template
                                v-if="
                                    selectedExercise && exerciseData.length > 0
                                "
                            >
                                <div
                                    class="grid grid-cols-2 md:grid-cols-3 gap-3"
                                >
                                    <div
                                        class="bg-[#111] border border-white/5 rounded-2xl p-5"
                                    >
                                        <p
                                            class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                        >
                                            Best Weight
                                        </p>
                                        <p
                                            class="text-3xl font-bold text-[#7ED957]"
                                        >
                                            {{ bestEver?.maxWeight
                                            }}<span class="text-lg">kg</span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            × {{ bestEver?.maxReps }} reps
                                        </p>
                                    </div>
                                    <div
                                        class="bg-[#111] border border-white/5 rounded-2xl p-5"
                                    >
                                        <p
                                            class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                        >
                                            Trend
                                        </p>
                                        <p
                                            class="text-3xl font-bold"
                                            :class="
                                                trend >= 0
                                                    ? 'text-[#7ED957]'
                                                    : 'text-red-400'
                                            "
                                        >
                                            {{ trend >= 0 ? "+" : "" }}{{ trend
                                            }}<span class="text-lg">kg</span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            vs last session
                                        </p>
                                    </div>
                                    <div
                                        class="bg-[#111] border border-white/5 rounded-2xl p-5"
                                    >
                                        <p
                                            class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                                        >
                                            Sessions
                                        </p>
                                        <p
                                            class="text-3xl font-bold text-[#7ED957]"
                                        >
                                            {{ exerciseData.length }}
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            logged
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="bg-[#111] border border-white/5 rounded-2xl p-6"
                                >
                                    <p
                                        class="text-xs text-gray-500 uppercase tracking-wider mb-1"
                                    >
                                        Weight Progress by Month
                                    </p>
                                    <p class="text-sm text-gray-400 mb-5">
                                        Max weight used per month
                                    </p>
                                    <div class="h-48">
                                        <canvas
                                            ref="monthlyWeightChartRef"
                                        ></canvas>
                                    </div>
                                </div>

                                <div
                                    class="bg-[#111] border border-white/5 rounded-2xl p-6"
                                >
                                    <p
                                        class="text-xs text-gray-500 uppercase tracking-wider mb-4"
                                    >
                                        Session Breakdown
                                    </p>
                                    <div
                                        class="space-y-2 max-h-80 overflow-y-auto"
                                    >
                                        <div
                                            v-for="d in [
                                                ...exerciseData,
                                            ].reverse()"
                                            :key="d.date"
                                            class="flex items-center gap-4 bg-[#0f0f0f] border border-white/5 rounded-xl px-4 py-3"
                                        >
                                            <div
                                                class="w-2 h-2 rounded-full bg-[#7ED957] shrink-0"
                                            ></div>
                                            <p
                                                class="text-sm text-gray-400 flex-1"
                                            >
                                                {{ d.date }}
                                            </p>
                                            <div
                                                class="flex items-center gap-5 text-xs text-gray-500 shrink-0"
                                            >
                                                <div class="text-center">
                                                    <p
                                                        class="text-white font-semibold text-sm"
                                                    >
                                                        {{ d.maxWeight
                                                        }}<span
                                                            class="text-gray-600"
                                                            >kg</span
                                                        >
                                                    </p>
                                                    <p>weight</p>
                                                </div>
                                                <div class="text-center">
                                                    <p
                                                        class="text-white font-semibold text-sm"
                                                    >
                                                        {{ d.maxReps }}
                                                    </p>
                                                    <p>reps</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div
                                v-else-if="selectedExercise"
                                class="text-center text-gray-600 py-10"
                            >
                                <p>No data for {{ selectedExercise }}</p>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="showDayModal"
        class="fixed inset-0 bg-black/70 flex items-end md:items-center justify-center z-50"
        @click.self="showDayModal = false"
    >
        <div
            class="bg-[#111] border border-white/10 w-full md:max-w-md rounded-t-2xl md:rounded-2xl p-6"
        >
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold">Day {{ selectedDay?.day }}</h2>
                <button
                    @click="showDayModal = false"
                    class="text-gray-500 text-xl"
                >
                    ✕
                </button>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center text-sm">
                <div class="bg-[#0f0f0f] rounded-xl p-3">
                    <p class="text-[#7ED957] font-bold text-xl">
                        {{ selectedDay?.count }}
                    </p>
                    <p class="text-gray-500 text-xs mt-1">sessions</p>
                </div>
                <div class="bg-[#0f0f0f] rounded-xl p-3">
                    <p class="text-white font-bold text-xl">
                        {{ selectedDay?.sets }}
                    </p>
                    <p class="text-gray-500 text-xs mt-1">sets</p>
                </div>
                <div class="bg-[#0f0f0f] rounded-xl p-3">
                    <p class="text-white font-bold text-xl">
                        {{ selectedDay?.reps }}
                    </p>
                    <p class="text-gray-500 text-xs mt-1">reps</p>
                </div>
            </div>
        </div>
    </div>
</template>
