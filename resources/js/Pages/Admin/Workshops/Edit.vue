<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

interface Workshop {
    id: number;
    title: string;
    description: string;
    starts_at: string;
    ends_at: string;
    capacity: number;
}

const props = defineProps<{ workshop: Workshop }>();

const form = useForm({
    title: props.workshop.title,
    description: props.workshop.description,
    starts_at: props.workshop.starts_at.slice(0, 16),
    ends_at: props.workshop.ends_at.slice(0, 16),
    capacity: props.workshop.capacity,
});

const submit = () => {
    form.patch(route("admin.workshops.update", props.workshop.id));
};
</script>

<template>
    <Head title="Modifica Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Modifica Workshop
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Titolo -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Titolo</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Descrizione -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Descrizione</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            />
                            <p
                                v-if="form.errors.description"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Data inizio -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Data e ora inizio</label
                            >
                            <input
                                v-model="form.starts_at"
                                type="datetime-local"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.starts_at,
                                }"
                            />
                            <p
                                v-if="form.errors.starts_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.starts_at }}
                            </p>
                        </div>

                        <!-- Data fine -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Data e ora fine</label
                            >
                            <input
                                v-model="form.ends_at"
                                type="datetime-local"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.ends_at,
                                }"
                            />
                            <p
                                v-if="form.errors.ends_at"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.ends_at }}
                            </p>
                        </div>

                        <!-- Capienza -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Capienza massima</label
                            >
                            <input
                                v-model="form.capacity"
                                type="number"
                                min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{
                                    'border-red-500': form.errors.capacity,
                                }"
                            />
                            <p
                                v-if="form.errors.capacity"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.capacity }}
                            </p>
                        </div>

                        <!-- Bottoni -->
                        <div class="flex items-center justify-end gap-6">
                            <a
                                :href="route('admin.workshops.index')"
                                class="text-sm text-gray-600 hover:text-gray-900"
                                >Annulla</a
                            >
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Salva modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
