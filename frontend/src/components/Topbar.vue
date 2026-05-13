<script setup>
defineProps({
    title: String,
});
</script>

<template>
    <header
        class="h-16 flex items-center justify-between px-8 border-b border-white/5"
    >
        <h1 class="text-xl font-semibold capitalize">
            {{ title }}
        </h1>

        <div class="flex items-center gap-6">
            <div class="relative">
                <div
                    class="w-2 h-2 bg-[#7ED957] rounded-full absolute top-0 right-0"
                ></div>
                <div
                    class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center"
                >
                    🔔
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-[#7ED957] to-[#5fcf47]"
                ></div>
                <span class="text-sm text-gray-300">John</span>
            </div>
        </div>
    </header>
</template>
