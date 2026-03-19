<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";

interface Workshop {
    id: number;
    title: string;
    starts_at: string;
    capacity: number;
    confirmed_registrations_count: number;
}

interface Stats {
    totalWorkshops: number;
    totalRegistrations: number;
    totalEmployees: number;
}

interface Props {
    stats: Stats;
    mostPopular: Workshop | null;
    upcomingWorkshops: Workshop[];
}

const props = defineProps<Props>();

const stats = ref<Stats>({ ...props.stats });

let pollingInterval: ReturnType<typeof setInterval> | null = null;

const fetchStats = async () => {
    try {
        const response = await fetch(route("admin.stats"));
        const data = await response.json();
        stats.value = data;
    } catch (error) {
        console.error("Errore nel polling delle statistiche:", error);
    }
};

onMounted(() => {
    pollingInterval = setInterval(fetchStats, 10000);
});

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Statistiche -->
                <div class="grid grid-cols-3 gap-6">
                    <div
                        class="bg-white p-6 shadow-sm sm:rounded-lg text-center"
                    >
                        <p class="text-3xl font-bold text-indigo-600">
                            {{ stats.totalWorkshops }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Workshop totali
                        </p>
                    </div>
                    <div
                        class="bg-white p-6 shadow-sm sm:rounded-lg text-center"
                    >
                        <p class="text-3xl font-bold text-green-600">
                            {{ stats.totalRegistrations }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Iscrizioni confermate
                        </p>
                    </div>
                    <div
                        class="bg-white p-6 shadow-sm sm:rounded-lg text-center"
                    >
                        <p class="text-3xl font-bold text-yellow-600">
                            {{ stats.totalEmployees }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">Dipendenti</p>
                    </div>
                </div>

                <!-- Workshop più popolare -->
                <div
                    class="bg-white p-6 shadow-sm sm:rounded-lg"
                    v-if="mostPopular"
                >
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        🏆 Workshop più popolare
                    </h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ mostPopular.title }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{
                                    new Date(
                                        mostPopular.starts_at,
                                    ).toLocaleString("it-IT")
                                }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ mostPopular.confirmed_registrations_count }}
                            </p>
                            <p class="text-sm text-gray-500">
                                iscritti su {{ mostPopular.capacity }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Prossimi workshop -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Prossimi workshop
                        </h3>
                        <Link
                            :href="route('admin.workshops.index')"
                            class="text-sm text-indigo-600 hover:text-indigo-900"
                        >
                            Vedi tutti →
                        </Link>
                    </div>
                    <table
                        class="min-w-full divide-y divide-gray-200"
                        v-if="upcomingWorkshops.length > 0"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Titolo
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Data
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Iscritti
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Stato
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="workshop in upcomingWorkshops"
                                :key="workshop.id"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'admin.workshops.show',
                                                workshop.id,
                                            )
                                        "
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        {{ workshop.title }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        new Date(
                                            workshop.starts_at,
                                        ).toLocaleString("it-IT")
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ workshop.confirmed_registrations_count }}
                                    / {{ workshop.capacity }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="
                                            workshop.confirmed_registrations_count >=
                                            workshop.capacity
                                                ? 'bg-red-100 text-red-800'
                                                : 'bg-green-100 text-green-800'
                                        "
                                        class="rounded-full px-2 py-1 text-xs font-medium"
                                    >
                                        {{
                                            workshop.confirmed_registrations_count >=
                                            workshop.capacity
                                                ? "Pieno"
                                                : "Disponibile"
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-sm text-gray-500">
                        Nessun workshop in programma.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
