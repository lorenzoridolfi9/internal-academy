<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

interface Workshop {
    id: number;
    title: string;
    starts_at: string;
    ends_at: string;
    capacity: number;
    confirmed_registrations_count: number;
}

interface Props {
    workshops: {
        data: Workshop[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Workshop
                </h2>
                <Link
                    :href="route('admin.workshops.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Nuovo Workshop
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
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
                                    Data inizio
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
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Azioni
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="workshop in workshops.data"
                                :key="workshop.id"
                            >
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900"
                                >
                                    {{ workshop.title }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        new Date(
                                            workshop.starts_at,
                                        ).toLocaleString("it-IT")
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        workshop.confirmed_registrations_count
                                    }}
                                    / {{ workshop.capacity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
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
                                <td
                                    class="px-6 py-4 text-sm font-medium space-x-2"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'admin.workshops.show',
                                                workshop.id,
                                            )
                                        "
                                        class="text-indigo-600 hover:text-indigo-900"
                                        >Dettaglio</Link
                                    >
                                    <Link
                                        :href="
                                            route(
                                                'admin.workshops.edit',
                                                workshop.id,
                                            )
                                        "
                                        class="text-yellow-600 hover:text-yellow-900"
                                        >Modifica</Link
                                    >
                                    <Link
                                        :href="
                                            route(
                                                'admin.workshops.destroy',
                                                workshop.id,
                                            )
                                        "
                                        method="delete"
                                        as="button"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="
                                            return confirm(
                                                'Sei sicuro di voler eliminare questo workshop?',
                                            );
                                        "
                                    >
                                        Elimina
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="workshops.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-4 text-center text-sm text-gray-500"
                                >
                                    Nessun workshop trovato.
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
