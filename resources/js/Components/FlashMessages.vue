<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const flash = computed(
    () =>
        usePage().props.flash as {
            success?: string | null;
            error?: string | null;
        },
);
</script>

<template>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
            v-if="flash.success"
            class="mb-4 rounded-md bg-green-50 p-4 text-green-800 border border-green-200"
        >
            ✅ {{ flash.success }}
        </div>
        <div
            v-if="flash.error"
            class="mb-4 rounded-md bg-red-50 p-4 text-red-800 border border-red-200"
        >
            ❌ {{ flash.error }}
        </div>
    </div>
</template>
