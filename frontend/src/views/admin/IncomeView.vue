<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Chart from "chart.js/auto";

const subscriptions = ref([]);
const loading = ref(true);
const selectedYear = ref(new Date().getFullYear());
const chartRef = ref(null);
let chartInstance = null;

const PRICE_PER_MONTH = 29.99;

const token = localStorage.getItem("token");
const headers = { Authorization: `Bearer ${token}` };

const months = [
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
];

onMounted(async () => {
    try {
        const res = await axios.get(
            "http://localhost:8000/api/admin/subscriptions",
            { headers },
        );
        subscriptions.value = res.data.data ?? res.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
        await nextTick();
        createChart();
    }
});

import { nextTick, watch } from "vue";

const activeSubscriptions = computed(() =>
    subscriptions.value.filter(
        (s) => s.status === "active" || s.status === "trialing",
    ),
);

const monthlyData = computed(() => {
    return months.map((_, i) => {
        const count = subscriptions.value.filter((s) => {
            if (!s.created_at) return false;
            const d = new Date(s.created_at);
            return (
                d.getFullYear() === selectedYear.value &&
                d.getMonth() === i &&
                s.status !== "canceled"
            );
        }).length;
        return { month: months[i], count, income: count * PRICE_PER_MONTH };
    });
});

const totalIncome = computed(() =>
    monthlyData.value.reduce((s, m) => s + m.income, 0),
);
const totalSold = computed(() =>
    monthlyData.value.reduce((s, m) => s + m.count, 0),
);
const bestMonth = computed(() =>
    monthlyData.value.reduce(
        (best, m) => (m.income > best.income ? m : best),
        monthlyData.value[0],
    ),
);

const createChart = () => {
    if (!chartRef.value) return;
    if (chartInstance) chartInstance.destroy();
    const ctx = chartRef.value.getContext("2d");
    const data = monthlyData.value;
    chartInstance = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.map((d) => d.month),
            datasets: [
                {
                    label: "Income ($)",
                    data: data.map((d) => d.income),
                    backgroundColor: data.map((d) =>
                        d.income === Math.max(...data.map((x) => x.income)) &&
                        d.income > 0
                            ? "#7ED957"
                            : "rgba(126,217,87,0.2)",
                    ),
                    borderRadius: 8,
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
                    callbacks: { label: (c) => ` $${c.raw.toFixed(2)}` },
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
                        callback: (v) => `$${v}`,
                    },
                    grid: { color: "rgba(255,255,255,0.04)" },
                    beginAtZero: true,
                },
            },
        },
    });
};

watch([selectedYear, subscriptions], async () => {
    await nextTick();
    createChart();
});
</script>

<template>
    <div class="h-full bg-[#080808] text-white flex flex-col overflow-hidden">
        <div class="bg-[#0f0f0f] border-b border-white/5 px-8 py-6 shrink-0">
            <p
                class="text-xs text-[#7ED957] uppercase tracking-widest font-semibold mb-1"
            >
                Admin
            </p>
            <h1 class="text-3xl font-bold">Income</h1>
        </div>

        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Total Income
                    </p>
                    <p class="text-3xl font-bold text-[#7ED957]">
                        ${{ totalIncome.toFixed(2) }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">{{ selectedYear }}</p>
                </div>
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Sold
                    </p>
                    <p class="text-3xl font-bold text-[#7ED957]">
                        {{ totalSold }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">subscriptions</p>
                </div>
                <div class="bg-[#111] border border-white/5 rounded-2xl p-5">
                    <p
                        class="text-xs text-gray-500 uppercase tracking-wider mb-2"
                    >
                        Best Month
                    </p>
                    <p class="text-3xl font-bold text-[#7ED957]">
                        {{ bestMonth?.month }}
                    </p>
                    <p class="text-xs text-gray-600 mt-1">
                        ${{ bestMonth?.income.toFixed(2) }}
                    </p>
                </div>
            </div>

            <div class="bg-[#111] border border-white/5 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">
                        Monthly Income
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
                <div class="h-56"><canvas ref="chartRef"></canvas></div>
            </div>

            <div
                class="bg-[#111] border border-white/5 rounded-2xl overflow-hidden"
            >
                <div
                    class="grid grid-cols-4 gap-4 px-6 py-3 border-b border-white/5 text-xs text-gray-500 uppercase tracking-wider"
                >
                    <span>Month</span>
                    <span class="text-center">Subscriptions Sold</span>
                    <span class="text-center">Income</span>
                    <span class="text-center">Status</span>
                </div>

                <div v-if="loading" class="text-center text-gray-600 py-12">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                </div>

                <div v-else class="max-h-72 overflow-y-auto">
                    <div
                        v-for="row in monthlyData"
                        :key="row.month"
                        class="grid grid-cols-4 gap-4 px-6 py-4 border-b border-white/5 last:border-0 hover:bg-white/3 transition-all items-center"
                    >
                        <span class="font-semibold text-sm"
                            >{{ row.month }} {{ selectedYear }}</span
                        >
                        <span class="text-center text-sm text-gray-300">{{
                            row.count
                        }}</span>
                        <span
                            class="text-center font-bold text-sm"
                            :class="
                                row.income > 0
                                    ? 'text-[#7ED957]'
                                    : 'text-gray-600'
                            "
                            >${{ row.income.toFixed(2) }}</span
                        >
                        <div class="flex justify-center">
                            <span
                                v-if="
                                    row.income > 0 &&
                                    row.income === bestMonth?.income
                                "
                                class="px-3 py-1 rounded-xl text-xs font-bold bg-[#7ED957]/10 text-[#7ED957] border border-[#7ED957]/20"
                                >Best</span
                            >
                            <span
                                v-else-if="row.income > 0"
                                class="px-3 py-1 rounded-xl text-xs font-bold bg-white/5 text-gray-400"
                                >Active</span
                            >
                            <span
                                v-else
                                class="px-3 py-1 rounded-xl text-xs font-bold bg-white/5 text-gray-700"
                                >—</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</template>
