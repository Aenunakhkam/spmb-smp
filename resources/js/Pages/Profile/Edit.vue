<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profil
            </h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen relative overflow-hidden">
            <!-- Decorative background blobs -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl dark:bg-indigo-500/5"></div>
                <div class="absolute top-40 -left-20 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl dark:bg-blue-500/5"></div>
            </div>

            <div class="relative mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8 z-10">
                <div
                    class="bg-white p-6 shadow-2xl shadow-slate-200/50 sm:rounded-[2rem] sm:p-10 border border-slate-100 transition-all duration-500 hover:shadow-slate-300/50"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-2xl"
                    />
                </div>

                <div
                    class="bg-white p-6 shadow-2xl shadow-slate-200/50 sm:rounded-[2rem] sm:p-10 border border-slate-100 transition-all duration-500 hover:shadow-slate-300/50"
                >
                    <UpdatePasswordForm class="max-w-2xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
