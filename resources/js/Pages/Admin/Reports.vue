<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    totalRegistrations: Number,
    byStatus: Array,
    byGender: Array,
    bySchool: Array,
});

const statusMap = {
    'incomplete': { text: 'Belum Lengkap', color: 'warning' },
    'pending': { text: 'Menunggu Verifikasi', color: 'info' },
    'revision': { text: 'Perlu Perbaikan', color: 'orange' },
    'verified': { text: 'Terverifikasi', color: 'success' },
    'passed': { text: 'Diterima', color: 'success' },
    'failed': { text: 'Tidak Diterima', color: 'error' },
};
</script>

<template>
    <Head title="Laporan Pendaftaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-h5 font-weight-bold text-grey-darken-3">Laporan Pendaftaran</h2>
        </template>

        <div class="d-flex justify-end gap-2 mb-4">
            <v-btn color="error" prepend-icon="mdi-file-pdf-box" class="mr-2" :href="route('admin.reports.exportPdf')" target="_blank">
                Export PDF
            </v-btn>
            <v-btn color="success" prepend-icon="mdi-file-excel" :href="route('admin.reports.exportExcel')">
                Export Excel
            </v-btn>
        </div>

        <v-row>
            <!-- Total Pendaftar -->
            <v-col cols="12" md="4">
                <v-card class="pa-6 rounded-xl elevation-2 bg-gradient-to-br from-indigo-500 to-blue-600 text-white" style="height: 100%;">
                    <div class="d-flex justify-space-between align-center">
                        <div>
                            <div class="text-subtitle-1 font-weight-medium opacity-80">Total Pendaftar</div>
                            <div class="text-h2 font-weight-bold mt-2">{{ totalRegistrations }}</div>
                        </div>
                        <v-icon size="64" class="opacity-30">mdi-account-group</v-icon>
                    </div>
                </v-card>
            </v-col>

            <!-- Berdasarkan Jenis Kelamin -->
            <v-col cols="12" md="8">
                <v-card class="pa-4 rounded-xl elevation-2" style="height: 100%;">
                    <h3 class="text-h6 font-weight-bold mb-4">Berdasarkan Jenis Kelamin</h3>
                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left font-weight-bold">Jenis Kelamin</th>
                                <th class="text-right font-weight-bold">Jumlah Pendaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="gender in byGender" :key="gender.label">
                                <td class="font-weight-medium">
                                    <v-icon :color="gender.label === 'Laki-laki' ? 'blue' : 'pink'" size="small" class="mr-2">
                                        {{ gender.label === 'Laki-laki' ? 'mdi-gender-male' : 'mdi-gender-female' }}
                                    </v-icon>
                                    {{ gender.label }}
                                </td>
                                <td class="text-right font-weight-bold">{{ gender.total }}</td>
                            </tr>
                            <tr v-if="byGender.length === 0">
                                <td colspan="2" class="text-center text-grey">Belum ada data.</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </v-col>
        </v-row>

        <v-row class="mt-4">
            <!-- Berdasarkan Status -->
            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-xl elevation-2" style="height: 100%;">
                    <h3 class="text-h6 font-weight-bold mb-4">Status Pendaftaran</h3>
                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left font-weight-bold">Status</th>
                                <th class="text-right font-weight-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="status in byStatus" :key="status.status">
                                <td>
                                    <v-chip size="small" :color="statusMap[status.status]?.color || 'grey'" class="font-weight-bold">
                                        {{ statusMap[status.status]?.text || status.status }}
                                    </v-chip>
                                </td>
                                <td class="text-right font-weight-bold">{{ status.total }}</td>
                            </tr>
                            <tr v-if="byStatus.length === 0">
                                <td colspan="2" class="text-center text-grey">Belum ada data.</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </v-col>

            <!-- Berdasarkan Asal Sekolah -->
            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-xl elevation-2" style="height: 100%;">
                    <h3 class="text-h6 font-weight-bold mb-4">Top 10 Asal Sekolah</h3>
                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left font-weight-bold">Nama Sekolah</th>
                                <th class="text-right font-weight-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="school in bySchool" :key="school.origin_school_name">
                                <td class="font-weight-medium">{{ school.origin_school_name || 'Tidak Diketahui' }}</td>
                                <td class="text-right font-weight-bold text-primary">{{ school.total }}</td>
                            </tr>
                            <tr v-if="bySchool.length === 0">
                                <td colspan="2" class="text-center text-grey">Belum ada data sekolah.</td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>

<style scoped>
.opacity-80 { opacity: 0.8; }
.opacity-30 { opacity: 0.3; }
</style>
