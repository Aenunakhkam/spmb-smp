<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StudentDataViewer from '@/Components/StudentDataViewer.vue';

const props = defineProps({
    registrations: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const dialog = ref(false);
const selectedReg = ref(null);

watch(search, (value) => {
    router.get(route('admin.master.index'), { search: value }, { preserveState: true, replace: true });
});

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

const openDetail = (reg) => {
    selectedReg.value = reg;
    dialog.value = true;
};
</script>

<template>
    <Head title="Data Master" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-h5 font-weight-bold text-grey-darken-3">Data Master Siswa & Orang Tua</h2>
        </template>

        <v-card class="pa-4 rounded-xl elevation-2">
            <v-row class="mb-4">
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="search"
                        label="Cari Nama / NISN / No. Reg"
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        hide-details
                    ></v-text-field>
                </v-col>
            </v-row>

            <v-table class="border">
                <thead>
                    <tr class="bg-grey-lighten-4">
                        <th class="font-weight-bold">No. Pendaftaran</th>
                        <th class="font-weight-bold">NISN</th>
                        <th class="font-weight-bold">Nama Lengkap</th>
                        <th class="font-weight-bold">Jenis Kelamin</th>
                        <th class="font-weight-bold">Asal Sekolah</th>
                        <th class="font-weight-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reg in registrations.data" :key="reg.id" class="hover-bg-grey">
                        <td class="font-weight-medium text-primary">{{ reg.registration_number }}</td>
                        <td>{{ reg.student_detail?.nisn || '-' }}</td>
                        <td class="font-weight-bold">{{ reg.student_detail?.full_name || '-' }}</td>
                        <td>{{ reg.student_detail?.gender === 'L' ? 'Laki-laki' : (reg.student_detail?.gender === 'P' ? 'Perempuan' : '-') }}</td>
                        <td>{{ reg.student_detail?.origin_school_name || '-' }}</td>
                        <td class="text-center">
                            <v-btn
                                color="info"
                                variant="tonal"
                                size="small"
                                prepend-icon="mdi-eye"
                                @click="openDetail(reg)"
                            >
                                Lihat Detail
                            </v-btn>
                        </td>
                    </tr>
                    <tr v-if="registrations.data.length === 0">
                        <td colspan="6" class="text-center py-4 text-grey">Tidak ada data ditemukan.</td>
                    </tr>
                </tbody>
            </v-table>

            <div class="mt-4 d-flex justify-center">
                <v-pagination
                    v-model="registrations.current_page"
                    :length="registrations.last_page"
                    @update:model-value="v => router.get(route('admin.master.index'), { page: v, search }, { preserveState: true })"
                    active-color="primary"
                ></v-pagination>
            </div>
        </v-card>

        <!-- Dialog Detail Siswa -->
        <v-dialog v-model="dialog" max-width="800" scrollable>
            <v-card v-if="selectedReg" class="rounded-xl">
                <v-card-title class="bg-primary text-white d-flex justify-space-between align-center pa-4">
                    <span class="text-h6 font-weight-bold">Detail Siswa: {{ selectedReg.student_detail?.full_name }}</span>
                    <v-btn icon="mdi-close" variant="text" @click="dialog = false" color="white"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0" style="max-height: 70vh;">
                    <div class="pa-4 bg-grey-lighten-4 border-b">
                        <v-row dense>
                            <v-col cols="4" class="text-grey-darken-1 font-weight-medium">Nomor Pendaftaran</v-col>
                            <v-col cols="8" class="font-weight-bold">: <span class="text-primary">{{ selectedReg.registration_number }}</span></v-col>
                            <v-col cols="4" class="text-grey-darken-1 font-weight-medium">Status Verifikasi</v-col>
                            <v-col cols="8" class="font-weight-bold text-capitalize">: {{ selectedReg.status }}</v-col>
                            <v-col cols="4" class="text-grey-darken-1 font-weight-medium">Tanggal Daftar</v-col>
                            <v-col cols="8">: {{ formatDate(selectedReg.created_at) }}</v-col>
                        </v-row>
                    </div>

                    <div class="pa-4 pt-0 mt-4">
                        <StudentDataViewer :registration="selectedReg" />
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4 justify-end">
                    <v-btn color="grey-darken-2" variant="text" @click="dialog = false">Tutup</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </AuthenticatedLayout>
</template>

<style scoped>
.hover-bg-grey:hover {
    background-color: #f5f5f5 !important;
}
.border-b {
    border-bottom: 2px solid #e0e0e0;
}
</style>
