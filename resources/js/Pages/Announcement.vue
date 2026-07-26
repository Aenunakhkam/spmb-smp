<script setup>
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    registration: Object,
});

const statusMap = {
    'incomplete': { text: 'Belum Lengkap', color: 'warning', icon: 'mdi-alert-circle' },
    'pending': { text: 'Menunggu Verifikasi', color: 'info', icon: 'mdi-clock-outline' },
    'revision': { text: 'Perlu Perbaikan', color: 'orange', icon: 'mdi-pencil-circle' },
    'verified': { text: 'Terverifikasi', color: 'primary', icon: 'mdi-shield-check' },
    'passed': { text: 'DITERIMA', color: 'success', icon: 'mdi-check-decagram' },
    'failed': { text: 'TIDAK DITERIMA', color: 'error', icon: 'mdi-close-octagon' },
};

const getStatus = (status) => statusMap[status] || { text: status, color: 'grey', icon: 'mdi-help-circle' };
</script>

<template>
    <Head title="Hasil Seleksi" />
    <v-app>
        <!-- App Bar -->
        <v-app-bar color="white" elevation="1" height="70">
            <v-container class="d-flex align-center pa-0">
                <v-avatar color="primary" size="40" class="mr-3 elevation-1">
                    <v-icon color="white" size="22">mdi-school</v-icon>
                </v-avatar>
                <div>
                    <div class="text-subtitle-1 font-weight-black text-primary">Portal Pengumuman</div>
                    <div class="text-caption text-grey-darken-1">SPMB Digital 2026</div>
                </div>
                <v-spacer></v-spacer>
                <v-btn variant="outlined" color="primary" size="small" prepend-icon="mdi-home" @click="router.get(route('home'))" class="rounded-lg font-weight-bold">
                    Beranda
                </v-btn>
            </v-container>
        </v-app-bar>

        <v-main class="bg-grey-lighten-4">
            <v-container class="py-10">
                <v-row justify="center">
                    <v-col cols="12" md="10" lg="8">
                        <v-card class="rounded-xl elevation-4 overflow-hidden border-0">
                            <!-- Premium Gradient Header -->
                            <div class="pa-8 text-center text-white position-relative" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                                <v-avatar size="90" color="white" class="elevation-5 mb-4">
                                    <v-icon size="50" color="primary">mdi-account-school</v-icon>
                                </v-avatar>
                                <h2 class="text-h4 font-weight-black mb-1 letter-spacing-tight">{{ registration.student_detail.full_name }}</h2>
                                <div class="text-subtitle-1 opacity-80 mb-6">
                                    No. Pendaftaran: <span class="font-weight-bold text-yellow-lighten-2">{{ registration.registration_number }}</span>
                                </div>
                                
                                <v-chip :color="getStatus(registration.status).color" size="x-large" class="font-weight-black elevation-3 text-uppercase px-6" variant="elevated">
                                    <v-icon start size="28">{{ getStatus(registration.status).icon }}</v-icon>
                                    {{ getStatus(registration.status).text }}
                                </v-chip>
                            </div>

                            <v-card-text class="pa-md-8 pa-4">
                                <v-row>
                                    <!-- Biodata Section -->
                                    <v-col cols="12" md="7">
                                        <v-card variant="outlined" class="rounded-xl h-100 border-opacity-25" color="grey-lighten-2">
                                            <v-card-title class="d-flex align-center px-6 pt-6 pb-2 text-primary font-weight-bold">
                                                <v-icon start color="primary">mdi-card-account-details-outline</v-icon>
                                                Informasi Peserta
                                            </v-card-title>
                                            <v-card-text class="px-6 pb-6 pt-2">
                                                <v-table density="comfortable" class="bg-transparent text-body-1">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-grey-darken-2 font-weight-medium" width="40%">NISN</td>
                                                            <td class="font-weight-bold text-grey-darken-4">{{ registration.student_detail.nisn }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-grey-darken-2 font-weight-medium">Asal Sekolah</td>
                                                            <td class="font-weight-bold text-grey-darken-4">{{ registration.student_detail.origin_school_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-grey-darken-2 font-weight-medium">Tahun Ajaran</td>
                                                            <td class="font-weight-bold text-grey-darken-4">{{ registration.academic_year }}</td>
                                                        </tr>
                                                    </tbody>
                                                </v-table>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Score Section -->
                                    <v-col cols="12" md="5">
                                        <v-card class="rounded-xl h-100 text-center d-flex flex-column justify-center align-center pa-6" color="indigo-lighten-5" elevation="0" border>
                                            <div class="text-button mb-3 text-indigo-darken-1 font-weight-bold">Nilai Akhir Rata-rata</div>
                                            <v-progress-circular
                                                :model-value="registration.average_score"
                                                :size="130"
                                                :width="14"
                                                color="indigo"
                                                class="mb-5 font-weight-black text-h3 shadow-sm"
                                            >
                                                {{ registration.average_score }}
                                            </v-progress-circular>
                                            <v-chip v-if="registration.rank" color="indigo" class="font-weight-bold px-4" variant="elevated" elevation="2">
                                                <v-icon start size="small">mdi-trophy-variant</v-icon>
                                                Ranking: #{{ registration.rank }}
                                            </v-chip>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <!-- Notes from Admin -->
                                <v-slide-y-transition>
                                    <div v-if="registration.admin_notes" class="mt-8">
                                        <v-alert type="warning" variant="tonal" border="start" class="rounded-xl pa-5" icon="mdi-message-alert-outline">
                                            <template v-slot:title>
                                                <div class="text-subtitle-1 font-weight-bold mb-1">Catatan Panitia</div>
                                            </template>
                                            <div class="text-body-1">{{ registration.admin_notes }}</div>
                                        </v-alert>
                                    </div>
                                </v-slide-y-transition>

                                <!-- Call to Action -->
                                <div class="mt-10 text-center">
                                    <template v-if="registration.status === 'passed'">
                                        <v-alert type="success" variant="tonal" class="mb-6 rounded-xl font-weight-medium pa-4 text-h6" icon="mdi-party-popper">
                                            Selamat! Anda dinyatakan <strong class="font-weight-black">LULUS</strong> seleksi penerimaan siswa baru.
                                        </v-alert>
                                        <v-btn color="success" size="x-large" class="rounded-pill elevation-4 px-10 font-weight-bold text-button" prepend-icon="mdi-printer">
                                            Cetak Surat Kelulusan
                                        </v-btn>
                                    </template>
                                    <template v-else-if="registration.status === 'failed'">
                                        <v-alert type="error" variant="tonal" class="mb-6 rounded-xl font-weight-medium pa-4 text-h6" icon="mdi-emoticon-sad-outline">
                                            Mohon maaf, Anda dinyatakan <strong class="font-weight-black">TIDAK LULUS</strong> seleksi penerimaan siswa baru. Jangan patah semangat!
                                        </v-alert>
                                    </template>
                                    <template v-else-if="registration.status === 'incomplete' || registration.status === 'revision'">
                                        <v-btn color="primary" size="x-large" variant="elevated" class="rounded-pill px-10 font-weight-bold elevation-4 text-button" prepend-icon="mdi-pencil-circle" @click="router.get(route('student.dashboard'))">
                                            Lengkapi / Perbaiki Data
                                        </v-btn>
                                    </template>
                                    <template v-else>
                                        <p class="text-grey-darken-1">Pendaftaran Anda sedang diproses oleh panitia. Harap cek secara berkala.</p>
                                    </template>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>
<style scoped>
.letter-spacing-tight {
    letter-spacing: -0.02em !important;
}
</style>
