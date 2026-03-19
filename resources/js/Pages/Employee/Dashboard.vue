<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import confetti from "canvas-confetti";

interface Workshop {
    id: number;
    title: string;
    description: string;
    starts_at: string;
    ends_at: string;
    capacity: number;
    confirmed_registrations_count: number;
    available_seats: number;
    user_registration_status: "confirmed" | "waitlisted" | null;
}

interface Props {
    workshops: {
        data: Workshop[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}

defineProps<Props>();

const register = (workshopId: number) => {
    useForm({}).post(route("employee.workshops.register", workshopId), {
        onSuccess: () => {
            confetti({
                particleCount: 150,
                spread: 80,
                origin: { y: 0.6 },
            });
        },
    });
};

const cancel = (workshopId: number) => {
    useForm({}).delete(route("employee.workshops.unregister", workshopId));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Workshop disponibili
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="workshops.data.length === 0"
                    class="bg-white p-6 shadow-sm sm:rounded-lg text-center text-sm text-gray-500"
                >
                    Nessun workshop in programma.
                </div>

                <div
                    v-for="workshop in workshops.data"
                    :key="workshop.id"
                    class="bg-white p-6 shadow-sm sm:rounded-lg"
                >
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ workshop.title }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ workshop.description }}
                            </p>
                            <div class="flex gap-4 text-sm text-gray-500 pt-1">
                                <span
                                    >📅
                                    {{
                                        new Date(
                                            workshop.starts_at,
                                        ).toLocaleString("it-IT")
                                    }}</span
                                >
                                <span
                                    >⏱
                                    {{
                                        new Date(
                                            workshop.ends_at,
                                        ).toLocaleString("it-IT")
                                    }}</span
                                >
                                <span
                                    >👥
                                    {{
                                        workshop.confirmed_registrations_count
                                    }}
                                    / {{ workshop.capacity }} iscritti</span
                                >
                            </div>
                        </div>

                        <div
                            class="ml-6 flex flex-col items-end gap-2 min-w-[160px]"
                        >
                            <!-- Già iscritto confermato -->
                            <template
                                v-if="
                                    workshop.user_registration_status ===
                                    'confirmed'
                                "
                            >
                                <span
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800"
                                >
                                    Iscritto
                                </span>
                                <button
                                    @click="cancel(workshop.id)"
                                    class="text-xs text-red-600 hover:text-red-900 whitespace-nowrap"
                                >
                                    Cancella iscrizione
                                </button>
                            </template>

                            <!-- In waiting list -->
                            <template
                                v-else-if="
                                    workshop.user_registration_status ===
                                    'waitlisted'
                                "
                            >
                                <span
                                    class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 text-center whitespace-nowrap"
                                >
                                    In lista d'attesa
                                </span>
                                <button
                                    @click="cancel(workshop.id)"
                                    class="text-xs text-red-600 hover:text-red-900 whitespace-nowrap"
                                >
                                    Rimuoviti dalla lista
                                </button>
                            </template>

                            <!-- Non iscritto -->
                            <template v-else>
                                <span
                                    v-if="workshop.available_seats > 0"
                                    class="text-xs text-gray-500"
                                >
                                    {{ workshop.available_seats }} posti
                                    disponibili
                                </span>
                                <span
                                    v-else
                                    class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800"
                                >
                                    Pieno
                                </span>
                                <button
                                    @click="register(workshop.id)"
                                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 whitespace-nowrap"
                                >
                                    {{
                                        workshop.available_seats > 0
                                            ? "Iscriviti"
                                            : "Lista d'attesa"
                                    }}
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Paginazione -->
                <div class="mt-4 flex justify-center space-x-1">
                    <template v-for="link in workshops.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700'
                            "
                            class="rounded px-3 py-1 text-sm border"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="rounded px-3 py-1 text-sm border bg-gray-100 text-gray-400"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
