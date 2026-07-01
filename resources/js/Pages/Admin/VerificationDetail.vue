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
            <h2 class="text-h5 font-weight-bold">Verifikasi: {{ registration.student_detail.full_name }}</h2>
        </template>

        <v-row>
            <v-col cols="12" md="8">
                <v-card class="mb-6 rounded-xl overflow-hidden elevation-2">
                    <div class="pa-4 bg-grey-lighten-4 border-b">
                        <v-row dense>
                            <v-col cols="12" sm="4" class="text-grey-darken-1 font-weight-medium">Nomor Pendaftaran</v-col>
                            <v-col cols="12" sm="8" class="font-weight-bold">: <span class="text-primary">{{ registration.registration_number }}</span></v-col>
                            <v-col cols="12" sm="4" class="text-grey-darken-1 font-weight-medium">Kode Akses</v-col>
                            <v-col cols="12" sm="8" class="font-weight-bold">: <span class="text-warning-darken-3">{{ registration.access_code }}</span></v-col>
                        </v-row>
                    </div>
                    
                    <div class="pa-4 pt-0 mt-4">
                        <StudentDataViewer :registration="registration" />
                    </div>
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <!-- Action Card -->
                <v-card class="pa-4 position-sticky" style="top: 80px">
                    <h3 class="text-h6 font-weight-bold mb-4">Aksi Verifikasi</h3>
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

                        <v-btn block color="success" size="large" type="submit" :loading="form.processing">
                            Simpan Verifikasi
                        </v-btn>
                        
                        <v-btn block variant="text" class="mt-2" @click="router.get(route('admin.registrations.index'))">
                            Kembali
                        </v-btn>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>
