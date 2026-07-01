<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    registrations: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

watch([search, status], ([s, st]) => {
    router.get(route('admin.registrations.index'), { search: s, status: st }, { preserveState: true, replace: true });
});

const runRankingForm = useForm({});
const runRanking = () => {
    Swal.fire({
        title: 'Jalankan Ranking Otomatis?',
        text: 'Status siswa akan berubah sesuai kuota!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1B5E20',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Jalankan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            runRankingForm.post(route('admin.registrations.runRanking'));
        }
    });
};

const deleteRegistration = (id) => {
    Swal.fire({
        title: 'Yakin Hapus Pendaftar?',
        text: 'Tindakan ini akan menghapus data pendaftar secara permanen!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#808080',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.registrations.destroy', id), { preserveScroll: true });
        }
    });
};

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
    <Head title="Data Pendaftar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-h5 font-weight-bold">Daftar Pendaftar</h2>
        </template>

        <v-row class="mb-6">
            <v-col cols="12" sm="3">
                <v-card class="pa-4 bg-primary text-white">
                    <div class="text-subtitle-2">Total Pendaftar</div>
                    <div class="text-h4 font-weight-bold">{{ stats.total }}</div>
                </v-card>
            </v-col>
            <v-col cols="12" sm="3">
                <v-card class="pa-4 bg-info text-white">
                    <div class="text-subtitle-2">Menunggu Verifikasi</div>
                    <div class="text-h4 font-weight-bold">{{ stats.pending }}</div>
                </v-card>
            </v-col>
            <v-col cols="12" sm="3">
                <v-card class="pa-4 bg-success text-white">
                    <div class="text-subtitle-2">Terverifikasi</div>
                    <div class="text-h4 font-weight-bold">{{ stats.verified }}</div>
                </v-card>
            </v-col>
            <v-col cols="12" sm="3">
                <v-card class="pa-4 bg-warning text-white">
                    <div class="text-subtitle-2">Belum Lengkap</div>
                    <div class="text-h4 font-weight-bold">{{ stats.incomplete }}</div>
                </v-card>
            </v-col>
        </v-row>

        <v-card class="pa-4">
            <div class="d-flex flex-wrap gap-2 mb-4 justify-space-between align-center">
                <h3 class="text-h6 font-weight-bold mb-0">Tabel Data Pendaftar</h3>
                <div>
                    <v-btn color="error" class="mr-2 mb-2" prepend-icon="mdi-file-pdf-box" :href="route('admin.registrations.exportPdf')" target="_blank">
                        Export PDF
                    </v-btn>
                    <v-btn color="success" class="mr-2 mb-2" prepend-icon="mdi-file-excel" :href="route('admin.registrations.exportExcel')">
                        Export Excel
                    </v-btn>
                    <v-btn color="primary" class="mb-2" prepend-icon="mdi-trophy" @click="runRanking" :loading="runRankingForm.processing">
                        Jalankan Ranking
                    </v-btn>
                </div>
            </div>
            
            <v-divider class="mb-4"></v-divider>

            <v-row class="mb-4">
                <v-col cols="12" md="6">
                    <v-text-field
                        v-model="search"
                        label="Cari Nama / NISN / No. Reg"
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        hide-details
                    ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-select
                        v-model="status"
                        :items="[
                            {title: 'Semua Status', value: ''},
                            {title: 'Belum Lengkap', value: 'incomplete'},
                            {title: 'Menunggu Verifikasi', value: 'pending'},
                            {title: 'Terverifikasi', value: 'verified'},
                            {title: 'Diterima', value: 'passed'},
                            {title: 'Tidak Diterima', value: 'failed'},
                        ]"
                        label="Filter Status"
                        variant="outlined"
                        density="compact"
                        hide-details
                    ></v-select>
                </v-col>
            </v-row>

            <v-table>
                <thead>
                    <tr>
                        <th>No. Reg</th>
                        <th>Tgl Daftar</th>
                        <th>Nama Lengkap</th>
                        <th>NISN</th>
                        <th>Asal Sekolah</th>
                        <th>Rata-rata</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reg in registrations.data" :key="reg.id">
                        <td>
                            <div class="font-weight-bold text-primary">{{ reg.registration_number }}</div>
                            <div class="text-caption text-grey">Akses: <strong class="text-warning-darken-3">{{ reg.access_code }}</strong></div>
                        </td>
                        <td>
                            <div class="text-body-2">{{ new Date(reg.created_at).toLocaleDateString('id-ID') }}</div>
                        </td>
                        <td class="font-weight-bold">{{ reg.student_detail.full_name }}</td>
                        <td>{{ reg.student_detail.nisn }}</td>
                        <td>{{ reg.student_detail.origin_school_name }}</td>
                        <td>{{ reg.average_score }}</td>
                        <td>
                            <v-chip size="small" :color="statusMap[reg.status]?.color">
                                {{ statusMap[reg.status]?.text }}
                            </v-chip>
                        </td>
                        <td class="text-no-wrap">
                            <v-btn
                                icon="mdi-eye"
                                variant="text"
                                color="info"
                                size="small"
                                @click="router.get(route('admin.registrations.show', reg.id))"
                                title="Lihat Detail"
                            ></v-btn>
                            <v-btn
                                icon="mdi-pencil"
                                variant="text"
                                color="primary"
                                size="small"
                                @click="router.get(route('admin.registrations.edit', reg.id))"
                                title="Edit Data"
                            ></v-btn>
                            <v-btn
                                icon="mdi-delete"
                                variant="text"
                                color="error"
                                size="small"
                                @click="deleteRegistration(reg.id)"
                                title="Hapus Data"
                            ></v-btn>
                        </td>
                    </tr>
                    <tr v-if="registrations.data.length === 0">
                        <td colspan="7" class="text-center py-4">Tidak ada data pendaftar.</td>
                    </tr>
                </tbody>
            </v-table>

            <div class="mt-4 d-flex justify-center">
                <v-pagination
                    v-model="registrations.current_page"
                    :length="registrations.last_page"
                    @update:model-value="v => router.get(route('admin.registrations.index'), { page: v, search, status }, { preserveState: true })"
                ></v-pagination>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>
