<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    logs: Object,
});

const actionColorMap = {
    'verify_registration': 'info',
    'update_registration': 'primary',
    'delete_registration': 'error',
    'run_ranking': 'success',
    'update_settings': 'warning',
};

const actionLabelMap = {
    'verify_registration': 'Verifikasi Pendaftaran',
    'update_registration': 'Update Data Pendaftar',
    'delete_registration': 'Hapus Pendaftar',
    'run_ranking': 'Jalankan Ranking Seleksi',
    'update_settings': 'Ubah Pengaturan',
};

const formatDetails = (detailsJson) => {
    try {
        const data = JSON.parse(detailsJson);
        if (typeof data === 'object') {
            return Object.entries(data)
                .map(([key, val]) => `${key}: ${val}`)
                .join(', ');
        }
        return detailsJson;
    } catch (e) {
        return detailsJson;
    }
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};

import Swal from 'sweetalert2';

const clearLogs = () => {
    Swal.fire({
        title: 'Kosongkan Log?',
        text: "Anda yakin ingin menghapus SEMUA log aktivitas secara permanen? Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.logs.clear'), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire(
                        'Terhapus!',
                        'Semua log aktivitas telah berhasil dibersihkan.',
                        'success'
                    );
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Log Aktivitas Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-800">Log Aktivitas Sistem</h2>
        </template>

        <div class="d-flex justify-end mb-4">
            <button @click="clearLogs" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                Hapus Semua Log
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="overflow-x-auto"><table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr>
                        <th class="text-left font-weight-bold text-grey-darken-2">Waktu</th>
                        <th class="text-left font-weight-bold text-grey-darken-2">Pengguna</th>
                        <th class="text-left font-weight-bold text-grey-darken-2">Aksi</th>
                        <th class="text-left font-weight-bold text-grey-darken-2">Modul</th>
                        <th class="text-left font-weight-bold text-grey-darken-2">Detail Perubahan / IP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs.data" :key="log.id">
                        <td class="text-caption text-no-wrap">{{ formatDate(log.created_at) }}</td>
                        <td class="font-weight-medium">{{ log.user ? log.user.name : 'System' }}</td>
                        <td>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border bg-slate-100 text-slate-700 border-slate-200">
                                {{ actionLabelMap[log.action] || log.action }}
                            </span>
                        </td>
                        <td><span class="text-caption font-weight-bold text-grey">{{ log.module }}</span></td>
                        <td class="text-caption text-grey-darken-2">
                            <div>{{ formatDetails(log.details) }}</div>
                            <div class="text-grey-lighten-1">IP: {{ log.ip_address }}</div>
                        </td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td colspan="5" class="text-center py-4 text-grey">Belum ada log aktivitas tercatat.</td>
                    </tr>
                </tbody>
            </table></div>

            <div class="mt-4 d-flex justify-center">
                <v-pagination
                    v-model="logs.current_page"
                    :length="logs.last_page"
                    @update:model-value="v => router.get(route('admin.logs.index'), { page: v })"
                ></v-pagination>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
