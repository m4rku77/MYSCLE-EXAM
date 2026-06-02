<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

onMounted(async () => {
    const token = localStorage.getItem("token");
    const res = await axios.get("http://localhost:8000/api/user", {
        headers: { Authorization: `Bearer ${token}` },
    });
    localStorage.setItem("role", res.data.role);
    setTimeout(() => router.push("/trainer"), 3000);
});
</script>

<template>
    <div
        class="min-h-screen bg-[#080808] text-white flex items-center justify-center text-center px-5"
    >
        <div>
            <i class="fas fa-check-circle text-[#7ED957] text-6xl mb-6"></i>
            <h1 class="text-3xl font-bold mb-2">Payment Successful!</h1>
            <p class="text-gray-400">
                Your account has been upgraded. Redirecting...
            </p>
        </div>
    </div>
</template>
