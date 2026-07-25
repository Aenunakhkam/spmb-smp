<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    registration: Object,
});

const form = useForm({
    student_detail: {
        full_name: props.registration.student_detail.full_name,
        nisn: props.registration.student_detail.nisn,
        phone: props.registration.student_detail.phone,
    }
});

const submit = () => {
    form.put(route('admin.registrations.update', props.registration.id));
};
</script>

<template>
    <Head title="Edit Data Pendaftar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex justify-space-between align-center">
                <h2 class="text-xl font-bold text-slate-800">Edit Pendaftar #{{ registration.registration_number }}</h2>
                <v-btn color="secondary" variant="outlined" prepend-icon="mdi-arrow-left" :href="route('admin.registrations.index')">
                    Kembali
                </v-btn>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 md:col-span-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Informasi Dasar (Biodata Awal)</h3>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                        <div class="d-flex justify-space-between flex-wrap">
                            <span>Nomor Pendaftaran: <strong>{{ registration.registration_number }}</strong></span>
                            <span>Kode Akses: <strong class="text-warning-darken-3">{{ registration.access_code }}</strong></span>
                        </div>
                    </div>

                    <v-form @submit.prevent="submit">
                        <div class="mb-4"><label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label><input type="text" v-model="form.student_detail.full_name" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-slate-50 hover:bg-white focus:bg-white"></div>

                        <div class="mb-4"><label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NISN</label><input type="text" v-model="form.student_detail.nisn" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-slate-50 hover:bg-white focus:bg-white"></div>

                        <div class="mb-4"><label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon (WhatsApp)</label><input type="text" v-model="form.student_detail.phone" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-slate-50 hover:bg-white focus:bg-white"></div>

                        <div class="d-flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </v-form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
