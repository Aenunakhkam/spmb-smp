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
                <h2 class="text-h5 font-weight-bold">Edit Pendaftar #{{ registration.registration_number }}</h2>
                <v-btn color="secondary" variant="outlined" prepend-icon="mdi-arrow-left" :href="route('admin.registrations.index')">
                    Kembali
                </v-btn>
            </div>
        </template>

        <v-row justify="center">
            <v-col cols="12" md="8">
                <v-card class="pa-6 rounded-xl elevation-2">
                    <h3 class="text-h6 font-weight-bold mb-6 border-b pb-2">Informasi Dasar (Biodata Awal)</h3>
                    
                    <v-alert type="info" variant="tonal" class="mb-6 rounded-lg" density="comfortable">
                        <div class="d-flex justify-space-between flex-wrap">
                            <span>Nomor Pendaftaran: <strong>{{ registration.registration_number }}</strong></span>
                            <span>Kode Akses: <strong class="text-warning-darken-3">{{ registration.access_code }}</strong></span>
                        </div>
                    </v-alert>

                    <v-form @submit.prevent="submit">
                        <v-text-field
                            v-model="form.student_detail.full_name"
                            label="Nama Lengkap"
                            variant="outlined"
                            class="mb-4"
                            :error-messages="form.errors['student_detail.full_name']"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.student_detail.nisn"
                            label="NISN"
                            variant="outlined"
                            class="mb-4"
                            :error-messages="form.errors['student_detail.nisn']"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.student_detail.phone"
                            label="Nomor Telepon (WhatsApp)"
                            variant="outlined"
                            class="mb-6"
                            :error-messages="form.errors['student_detail.phone']"
                        ></v-text-field>

                        <div class="d-flex justify-end">
                            <v-btn
                                color="primary"
                                size="large"
                                type="submit"
                                :loading="form.processing"
                                class="rounded-lg font-weight-bold"
                            >
                                Simpan Perubahan
                            </v-btn>
                        </div>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>
