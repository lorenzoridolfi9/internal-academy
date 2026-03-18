<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

interface User {
    id: number;
    name: string;
    email: string;
}

interface Registration {
    id: number;
    user: User;
    registered_at: string;
    status: "confirmed" | "waitlisted";
}

interface Workshop {
    id: number;
    title: string;
    description: string;
    starts_at: string;
    ends_at: string;
    capacity: number;
    confirmed_registrations: Registration[];
    waitlisted_registrations: Registration[];
}

defineProps<{ workshop: Workshop }>();
</script>

<template>
    <Head :title="workshop.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ workshop.title }}
                </h2>
                <div class="flex gap-4">
                    <Link
                        :href="route('admin.workshops.edit', workshop.id)"
                        class="rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-600"
                    >
                        Modifica
                    </Link>
                    <Link
                        :href="route('admin.workshops.index')"
                        class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
                    >
                        Torna alla lista
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Dettagli workshop -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Dettagli
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-500"
                                >Descrizione</span
                            >
                            <p class="mt-1 text-gray-900">
                                {{ workshop.description }}
                            </p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500"
                                >Data inizio</span
                            >
                            <p class="mt-1 text-gray-900">
                                {{
                                    new Date(workshop.starts_at).toLocaleString(
                                        "it-IT",
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500"
                                >Data fine</span
                            >
                            <p class="mt-1 text-gray-900">
                                {{
                                    new Date(workshop.ends_at).toLocaleString(
                                        "it-IT",
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500"
                                >Capienza</span
                            >
                            <p class="mt-1 text-gray-900">
                                {{ workshop.confirmed_registrations.length }} /
                                {{ workshop.capacity }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Iscritti confermati -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Iscritti confermati ({{
                            workshop.confirmed_registrations.length
                        }})
                    </h3>
                    <table
                        class="min-w-full divide-y divide-gray-200"
                        v-if="workshop.confirmed_registrations.length > 0"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Nome
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Iscritto il
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="registration in workshop.confirmed_registrations"
                                :key="registration.id"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ registration.user.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ registration.user.email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        new Date(
                                            registration.registered_at,
                                        ).toLocaleString("it-IT")
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-sm text-gray-500">
                        Nessun iscritto confermato.
                    </p>
                </div>

                <!-- Waiting list -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Waiting list ({{
                            workshop.waitlisted_registrations.length
                        }})
                    </h3>
                    <table
                        class="min-w-full divide-y divide-gray-200"
                        v-if="workshop.waitlisted_registrations.length > 0"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Posizione
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Nome
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    In lista dal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="(
                                    registration, index
                                ) in workshop.waitlisted_registrations"
                                :key="registration.id"
                            >
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    # {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ registration.user.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ registration.user.email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        new Date(
                                            registration.registered_at,
                                        ).toLocaleString("it-IT")
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-sm text-gray-500">
                        Nessuno in lista d'attesa.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
