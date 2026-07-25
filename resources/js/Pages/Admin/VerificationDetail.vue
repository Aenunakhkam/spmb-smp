<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StudentDataViewer from '@/Components/StudentDataViewer.vue';

const props = defineProps({
    registration: Object,
});

const form = useForm({
    status: props.registration.status,
    admin_notes: props.registration.admin_notes || '',
});

const submit = () => {
    form.post(route('admin.registrations.updateStatus', props.registration.id));
};
</script>

<template>
    <Head title="Verifikasi Pendaftar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-800">Verifikasi: {{ registration.student_detail.full_name }}</h2>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 md:col-span-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="pa-4 bg-grey-lighten-4 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                            <div class="col-span-12">Nomor Pendaftaran</div>
                            <div class="col-span-12">: <span class="text-primary">{{ registration.registration_number }}</span></div>
                            <div class="col-span-12">Kode Akses</div>
                            <div class="col-span-12">: <span class="text-warning-darken-3">{{ registration.access_code }}</span></div>
                        </div>
                    </div>
                    
                    <div class="pa-4 pt-0 mt-4">
                        <StudentDataViewer :registration="registration" />
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-4">
                <!-- Action Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Aksi Verifikasi</h3>
                    <v-form @submit.prevent="submit">
                        <v-select
                            v-model="form.status"
                            :items="[
                                {title: 'Belum Lengkap', value: 'incomplete'},
                                {title: 'Menunggu Verifikasi', value: 'pending'},
                                {title: 'Perlu Perbaikan', value: 'revision'},
                                {title: 'Terverifikasi', value: 'verified'},
                                {title: 'DITERIMA (LULUS)', value: 'passed'},
                                {title: 'TIDAK DITERIMA', value: 'failed'},
                            ]"
                            label="Ubah Status"
                            variant="outlined"
                            class="mb-4"
                        ></v-select>

                        <v-textarea
                            v-model="form.admin_notes"
                            label="Catatan untuk Siswa"
                            variant="outlined"
                            rows="4"
                            placeholder="Tulis alasan jika status perlu perbaikan..."
                        ></v-textarea>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            Simpan Verifikasi
                        </button>
                        
                        <button @click="router.get(route('admin.registrations.index'))" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            Kembali
                        </button>
                    </v-form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
