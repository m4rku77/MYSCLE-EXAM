<script setup>
import AdminSidebar from "../components/admin/AdminSidebar.vue";
</script>

<template>
    <div class="min-h-screen flex bg-[#080808] text-white">
        <AdminSidebar />
        <div class="flex-1 md:ml-64 flex flex-col">
            <RouterView />
        </div>
    </div>
</template>
