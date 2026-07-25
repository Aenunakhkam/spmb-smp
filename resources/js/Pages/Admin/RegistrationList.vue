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
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Data Pendaftar</h2>
        </template>

        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-blue-700 mb-1 uppercase tracking-wider">Total Pendaftar</div>
                    <div class="text-3xl font-black text-blue-900">{{ stats.total }}</div>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-blue-100 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            </div>
            
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-5 relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-amber-700 mb-1 uppercase tracking-wider">Menunggu</div>
                    <div class="text-3xl font-black text-amber-900">{{ stats.pending }}</div>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-amber-100 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
            </div>

            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5 relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-emerald-700 mb-1 uppercase tracking-wider">Terverifikasi</div>
                    <div class="text-3xl font-black text-emerald-900">{{ stats.verified }}</div>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-emerald-100 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>

            <div class="bg-red-50 border border-red-100 rounded-xl p-5 relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <div class="text-xs font-bold text-red-700 mb-1 uppercase tracking-wider">Belum Lengkap</div>
                    <div class="text-3xl font-black text-red-900">{{ stats.incomplete }}</div>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-red-100 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
            
            <!-- Filters + Export -->
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row gap-3 items-center">
                <div class="w-full md:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" v-model="search" placeholder="Cari Nama / NISN / No. Reg" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
                
                <div class="w-full md:w-56">
                    <select v-model="status" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status Pendaftar</option>
                        <option value="incomplete">Belum Lengkap</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="passed">Diterima</option>
                        <option value="failed">Tidak Diterima</option>
                    </select>
                </div>

                <div class="flex gap-2 md:ml-auto shrink-0">
                    <a :href="route('admin.registrations.exportPdf')" target="_blank"
                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 active:scale-95 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <a :href="route('admin.registrations.exportExcel')"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500">
                            <th class="p-4 font-bold whitespace-nowrap">No. Registrasi</th>
                            <th class="p-4 font-bold whitespace-nowrap">Calon Siswa</th>
                            <th class="p-4 font-bold whitespace-nowrap">Asal Sekolah</th>
                            <th class="p-4 font-bold whitespace-nowrap text-center">Skor</th>
                            <th class="p-4 font-bold whitespace-nowrap text-center">Status</th>
                            <th class="p-4 font-bold whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr v-for="reg in registrations.data" :key="reg.id" class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 align-top">
                                <div class="font-bold text-blue-700 whitespace-nowrap">{{ reg.registration_number }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Kode Akses: <strong class="text-amber-600 font-mono">{{ reg.access_code }}</strong></div>
                                <div class="text-[10px] text-slate-400 mt-1">{{ new Date(reg.created_at).toLocaleDateString('id-ID') }}</div>
                            </td>
                            <td class="p-4 align-top">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold mr-3 shrink-0 uppercase border border-blue-200">
                                        {{ reg.student_detail.full_name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ reg.student_detail.full_name }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">NISN: {{ reg.student_detail.nisn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 align-top text-slate-700 max-w-[200px] truncate" :title="reg.student_detail.origin_school_name">
                                {{ reg.student_detail.origin_school_name }}
                            </td>
                            <td class="p-4 align-top text-center">
                                <div class="font-black text-slate-800 text-lg">{{ reg.final_score }}</div>
                            </td>
                            <td class="p-4 align-top text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border whitespace-nowrap inline-flex items-center"
                                      :class="{
                                          'bg-amber-50 text-amber-700 border-amber-200': reg.status === 'incomplete' || reg.status === 'revision',
                                          'bg-blue-50 text-blue-700 border-blue-200': reg.status === 'pending',
                                          'bg-emerald-50 text-emerald-700 border-emerald-200': reg.status === 'verified' || reg.status === 'passed',
                                          'bg-red-50 text-red-700 border-red-200': reg.status === 'failed',
                                      }">
                                    {{ statusMap[reg.status]?.text || reg.status }}
                                </span>
                            </td>
                            <td class="p-4 align-top text-right whitespace-nowrap">
                                <Link :href="route('admin.registrations.show', reg.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors border border-blue-100 shadow-sm mr-1" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </Link>
                                <Link :href="route('admin.registrations.edit', reg.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors border border-amber-100 shadow-sm mr-1" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </Link>
                                <button @click="deleteRegistration(reg.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors border border-red-100 shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!registrations.data.length">
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p>Tidak ada data pendaftar yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between" v-if="registrations.links && registrations.links.length > 3">
                <div class="text-xs text-slate-500 mb-4 sm:mb-0">
                    Menampilkan <span class="font-bold text-slate-800">{{ registrations.from || 0 }}</span> sampai <span class="font-bold text-slate-800">{{ registrations.to || 0 }}</span> dari <span class="font-bold text-slate-800">{{ registrations.total }}</span> entri
                </div>
                <div class="flex flex-wrap gap-1 justify-center">
                    <Link v-for="(link, k) in registrations.links" :key="k"
                          :href="link.url || '#'" 
                          v-html="link.label.replace('&laquo; Previous', '&laquo;').replace('Next &raquo;', '&raquo;')"
                          class="px-3 py-1 text-sm rounded border"
                          :class="[
                              link.active ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-300',
                              !link.url ? 'opacity-50 cursor-not-allowed bg-slate-50' : ''
                          ]"
                    />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
