<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    stats: Object,
    recentLogs: Array,
});

const quotaPercentage = computed(() => {
    if (!props.stats || !props.stats.quota) return 0;
    return Math.min(Math.round(((props.stats.verified || 0) / props.stats.quota) * 100), 100);
});

const runRanking = () => {
    Swal.fire({
        title: 'Jalankan Ranking Otomatis?',
        text: 'Status kelulusan siswa akan diperbarui berdasarkan rata-rata nilai rapor.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1B5E20',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Jalankan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.registrations.runRanking'));
        }
    });
};

const actionLabelMap = {
    'verify_registration': 'Verifikasi Pendaftaran',
    'update_registration': 'Update Data Pendaftar',
    'delete_registration': 'Hapus Pendaftar',
    'run_ranking': 'Jalankan Ranking Otomatis',
    'update_settings': 'Ubah Pengaturan',
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex justify-space-between align-center wrap-sm">
                <div>
                    <h2 class="text-h5 font-weight-bold mb-1 text-grey-darken-3">Dashboard Ringkasan</h2>
                    <span class="text-caption text-grey">Tahun Ajaran: <strong>{{ stats?.academic_year || '-' }}</strong> | Status Pendaftaran: 
                        <v-chip size="small" :color="stats?.status === 'open' ? 'success' : 'error'" class="font-weight-bold">
                            {{ stats?.status === 'open' ? 'Buka' : 'Tutup' }}
                        </v-chip>
                    </span>
                </div>
            </div>
        </template>

        <!-- Stats Overview Row -->
        <v-row class="mb-6">
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-blue-lighten-5 text-blue-darken-4 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Total Pendaftar</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.total || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-blue-lighten-2 mt-2">mdi-account-multiple</v-icon>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-orange-lighten-5 text-orange-darken-4 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Menunggu</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.pending || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-orange-lighten-2 mt-2">mdi-clock-outline</v-icon>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-green-lighten-5 text-green-darken-4 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Terverifikasi</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.verified || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-green-lighten-2 mt-2">mdi-checkbox-marked-circle-outline</v-icon>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-red-lighten-5 text-red-darken-4 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Belum Lengkap</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.incomplete || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-red-lighten-2 mt-2">mdi-alert-circle-outline</v-icon>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-teal-lighten-5 text-teal-darken-4 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Diterima</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.passed || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-teal-lighten-2 mt-2">mdi-school-outline</v-icon>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="4" lg="2">
                <v-card class="pa-5 rounded-xl border-0 hover-lift bg-grey-lighten-3 text-grey-darken-3 fill-height d-flex flex-column justify-space-between" style="border: 1px solid rgba(0,0,0,0.03) !important;">
                    <div>
                        <div class="text-subtitle-2 font-weight-medium opacity-8">Ditolak</div>
                        <div class="text-h4 font-weight-black my-1">{{ stats?.failed || 0 }}</div>
                    </div>
                    <v-icon size="40" class="align-self-end text-grey-darken-1 mt-2">mdi-account-cancel-outline</v-icon>
                </v-card>
            </v-col>
        </v-row>

        <!-- Quota Progress Card -->
        <v-row class="mb-6">
            <v-col cols="12">
                <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card">
                    <div class="d-flex justify-space-between align-center mb-4">
                        <div>
                            <h3 class="text-h6 font-weight-bold text-grey-darken-4">Kapasitas Kuota Pendaftar Terverifikasi</h3>
                            <div class="text-body-2 text-grey-darken-1">Siswa terverifikasi yang memperebutkan kuota masuk sekolah.</div>
                        </div>
                        <div class="text-right">
                            <span class="text-h5 font-weight-black text-primary">{{ stats?.verified || 0 }}</span>
                            <span class="text-subtitle-2 text-grey-darken-1"> / {{ stats?.quota || 0 }} Kuota</span>
                        </div>
                    </div>
                    
                    <v-progress-linear
                        :model-value="quotaPercentage"
                        color="success"
                        height="24"
                        rounded
                        striped
                        class="mb-2 border"
                    >
                        <template v-slot:default="{ value }">
                            <span class="text-caption font-weight-bold text-white">{{ value }}% Terisi</span>
                        </template>
                    </v-progress-linear>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <!-- Left Info Panel (Gender, Schools, Assistance) -->
            <v-col cols="12" md="8">
                <v-row>
                    <!-- Gender Card -->
                    <v-col cols="12" sm="6" class="d-flex">
                        <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card w-100">
                            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-grey-darken-4">Distribusi Jenis Kelamin</h3>
                            <div class="d-flex justify-space-around align-center py-4">
                                <div class="text-center hover-lift pa-3 rounded-lg border bg-white w-50 mr-2">
                                    <v-avatar color="blue-lighten-5" size="56" class="mb-2">
                                        <v-icon color="blue-darken-2" size="28">mdi-gender-male</v-icon>
                                    </v-avatar>
                                    <div class="font-weight-black text-h5 text-grey-darken-4">{{ stats?.gender?.L || 0 }}</div>
                                    <div class="text-caption font-weight-medium text-grey-darken-1">Laki-laki</div>
                                </div>
                                <div class="text-center hover-lift pa-3 rounded-lg border bg-white w-50 ml-2">
                                    <v-avatar color="pink-lighten-5" size="56" class="mb-2">
                                        <v-icon color="pink-darken-2" size="28">mdi-gender-female</v-icon>
                                    </v-avatar>
                                    <div class="font-weight-black text-h5 text-grey-darken-4">{{ stats?.gender?.P || 0 }}</div>
                                    <div class="text-caption font-weight-medium text-grey-darken-1">Perempuan</div>
                                </div>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Aid/Assistance Card -->
                    <v-col cols="12" sm="6" class="d-flex">
                        <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card w-100 d-flex flex-column justify-space-between">
                            <div>
                                <h3 class="text-subtitle-1 font-weight-bold mb-1 text-grey-darken-4">Bantuan Sosial</h3>
                                <div class="text-body-2 text-grey-darken-1 mb-4">Siswa pemegang kartu bantuan KIP / PKH / KKS.</div>
                            </div>
                            <div class="d-flex align-center justify-space-between bg-amber-lighten-5 pa-5 rounded-xl border border-amber-lighten-3">
                                <div class="d-flex align-center">
                                    <v-icon color="amber-darken-3" size="36" class="mr-3">mdi-card-text</v-icon>
                                    <span class="font-weight-bold text-body-1 text-grey-darken-4">Pemegang Kartu</span>
                                </div>
                                <span class="text-h4 font-weight-black text-amber-darken-4">{{ stats?.aid_recipients || 0 }}</span>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Top School Origins -->
                    <v-col cols="12">
                        <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card">
                            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-grey-darken-4">5 Asal Sekolah Terbanyak</h3>
                            <v-table density="comfortable" class="bg-transparent">
                                <thead>
                                    <tr>
                                        <th class="text-left text-overline text-grey-darken-1 font-weight-bold border-b">Nama Sekolah Asal</th>
                                        <th class="text-right text-overline text-grey-darken-1 font-weight-bold border-b">Jumlah Pendaftar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="school in (stats?.top_schools || [])" :key="school.origin_school_name" class="hover-lift">
                                        <td class="font-weight-medium text-body-2 text-grey-darken-3 py-3">{{ school.origin_school_name }}</td>
                                        <td class="text-right font-weight-bold text-primary py-3">{{ school.total }} siswa</td>
                                    </tr>
                                    <tr v-if="!stats?.top_schools || stats.top_schools.length === 0">
                                        <td colspan="2" class="text-center py-6 text-grey text-body-2">Belum ada sekolah pendaftar yang tercatat.</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>

            <!-- Right Info Panel (Recent Logs & Quick Actions) -->
            <v-col cols="12" md="4">
                <v-row>
                    <!-- Quick Links/Actions -->
                    <v-col cols="12">
                        <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card">
                            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-grey-darken-4">Navigasi Cepat</h3>
                            <div class="d-flex flex-column ga-3">
                                <v-btn block prepend-icon="mdi-account-group" class="justify-start text-left font-weight-bold rounded-lg py-4 height-auto border text-none bg-blue-grey-lighten-5 hover-lift shadow-none text-blue-darken-3" @click="router.get(route('admin.registrations.index'))">
                                    Kelola Pendaftar
                                </v-btn>
                                <v-btn block prepend-icon="mdi-cog" class="justify-start text-left font-weight-bold rounded-lg py-4 height-auto border text-none bg-blue-grey-lighten-5 hover-lift shadow-none text-grey-darken-3" @click="router.get(route('admin.settings.index'))">
                                    Pengaturan Sistem
                                </v-btn>
                                <v-btn block prepend-icon="mdi-history" class="justify-start text-left font-weight-bold rounded-lg py-4 height-auto border text-none bg-blue-grey-lighten-5 hover-lift shadow-none text-teal-darken-3" @click="router.get(route('admin.logs.index'))">
                                    Log Aktivitas Admin
                                </v-btn>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Recent Activity Logs -->
                    <v-col cols="12">
                        <v-card class="pa-6 rounded-xl border-0 soft-shadow glass-card">
                            <div class="d-flex justify-space-between align-center mb-5">
                                <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-4">Aktivitas Terkini</h3>
                                <a href="#" @click.prevent="router.get(route('admin.logs.index'))" class="text-body-2 text-primary font-weight-bold text-decoration-none hover-lift">
                                    Lihat Semua
                                </a>
                            </div>
                            
                            <div class="d-flex flex-column ga-4">
                                <div v-for="log in recentLogs" :key="log.id" class="border-b pb-3 border-opacity-50">
                                    <div class="d-flex justify-space-between align-start mb-1">
                                        <div class="d-flex align-center">
                                            <v-avatar size="24" color="primary-lighten-4" class="mr-2">
                                                <span class="text-caption font-weight-bold text-primary">{{ log.user_name.charAt(0).toUpperCase() }}</span>
                                            </v-avatar>
                                            <span class="text-body-2 font-weight-bold text-grey-darken-4">{{ log.user_name }}</span>
                                        </div>
                                        <span class="text-caption text-grey-darken-1">{{ log.created_at }}</span>
                                    </div>
                                    <div class="text-body-2 text-grey-darken-2 pl-8">
                                        {{ actionLabelMap[log.action] || log.action }} 
                                        <span class="text-caption text-primary ml-1 bg-primary-lighten-5 px-1 rounded">({{ log.module }})</span>
                                    </div>
                                </div>
                                <div v-if="recentLogs.length === 0" class="text-center py-6 text-grey text-body-2">
                                    Belum ada log aktivitas hari ini.
                                </div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>

<style scoped>
.height-auto {
    height: auto !important;
}
@media (max-width: 600px) {
    .wrap-sm {
        flex-direction: column;
        align-items: flex-start !important;
    }
    .mt-sm-2 {
        margin-top: 12px;
        width: 100%;
    }
}
</style>
