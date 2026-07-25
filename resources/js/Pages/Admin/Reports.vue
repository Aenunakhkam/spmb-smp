<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    totalRegistrations: Number,
    byStatus: Array,
    byGender: Array,
    bySchool: Array,
});

const statusMap = {
    'incomplete': { label: 'Belum Lengkap',       bg: 'bg-slate-100',   text: 'text-slate-600',   bar: 'bg-slate-400' },
    'pending':    { label: 'Menunggu Verifikasi',  bg: 'bg-amber-100',   text: 'text-amber-700',   bar: 'bg-amber-400' },
    'revision':   { label: 'Perlu Perbaikan',      bg: 'bg-orange-100',  text: 'text-orange-700',  bar: 'bg-orange-400' },
    'verified':   { label: 'Terverifikasi',        bg: 'bg-blue-100',    text: 'text-blue-700',    bar: 'bg-blue-500' },
    'passed':     { label: 'Diterima',             bg: 'bg-emerald-100', text: 'text-emerald-700', bar: 'bg-emerald-500' },
    'failed':     { label: 'Tidak Diterima',       bg: 'bg-red-100',     text: 'text-red-700',     bar: 'bg-red-400' },
};

const totalStatus = computed(() =>
    props.byStatus?.reduce((s, i) => s + i.total, 0) || 0
);

const maxSchool = computed(() =>
    props.bySchool?.length ? Math.max(...props.bySchool.map(s => s.total)) : 1
);
</script>

<template>
    <Head title="Laporan Pendaftaran" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Pendaftaran</h2>
                <p class="text-sm text-slate-500 mt-0.5">Ringkasan statistik data pendaftar</p>
            </div>
        </template>

        <!-- Export Toolbar -->
        <div class="flex items-center justify-between mb-6 p-4 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-700">Unduh Laporan</div>
                    <div class="text-xs text-slate-400">Ekspor data ke format PDF atau Excel</div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap justify-end">
                <!-- Export PDF Statistik -->
                <a
                    :href="route('admin.reports.exportPdf')"
                    target="_blank"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                <!-- Export Excel Statistik -->
                <a
                    :href="route('admin.reports.exportExcel')"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </a>
                <!-- Divider -->
                <div class="w-px h-8 bg-slate-200"></div>
                <!-- Export Master Data -->
                <a
                    :href="route('admin.reports.exportMaster')"
                    class="inline-flex items-center gap-2 bg-indigo-700 hover:bg-indigo-800 active:scale-95 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm"
                    title="Export semua data siswa lengkap (biodata, nilai, orang tua, dll)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/></svg>
                    Export Master Data
                </a>
            </div>
        </div>

        <!-- Row 1: Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <!-- Total Pendaftar -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/20 flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-blue-200 mb-1">Total Pendaftar</div>
                    <div class="text-5xl font-black tracking-tight">{{ totalRegistrations ?? 0 }}</div>
                    <div class="text-xs text-blue-200 mt-2">Tahun ajaran aktif</div>
                </div>
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>

            <!-- Gender Split -->
            <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Berdasarkan Jenis Kelamin</h3>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div
                        v-for="g in byGender" :key="g.label"
                        class="flex-1 rounded-xl p-4 flex items-center gap-4"
                        :class="g.label === 'Laki-laki' ? 'bg-blue-50 border border-blue-100' : 'bg-pink-50 border border-pink-100'"
                    >
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                            :class="g.label === 'Laki-laki' ? 'bg-blue-100' : 'bg-pink-100'"
                        >
                            <svg v-if="g.label === 'Laki-laki'" class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <svg v-else class="w-7 h-7 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wide" :class="g.label === 'Laki-laki' ? 'text-blue-600' : 'text-pink-500'">{{ g.label }}</div>
                            <div class="text-3xl font-black text-slate-800">{{ g.total }}</div>
                        </div>
                    </div>
                    <div v-if="!byGender || byGender.length === 0" class="text-sm text-slate-400 py-4">Belum ada data.</div>
                </div>
            </div>
        </div>

        <!-- Row 2: Status & School -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Status Pendaftaran -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Status Pendaftaran</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div v-for="item in byStatus" :key="item.status" class="flex items-center gap-3">
                        <div class="w-36 shrink-0">
                            <span
                                class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold"
                                :class="[statusMap[item.status]?.bg || 'bg-slate-100', statusMap[item.status]?.text || 'text-slate-600']"
                            >
                                {{ statusMap[item.status]?.label || item.status }}
                            </span>
                        </div>
                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div
                                class="h-2 rounded-full transition-all duration-700"
                                :class="statusMap[item.status]?.bar || 'bg-slate-400'"
                                :style="{ width: totalStatus ? (item.total / totalStatus * 100) + '%' : '0%' }"
                            ></div>
                        </div>
                        <div class="w-8 text-right text-sm font-bold text-slate-700">{{ item.total }}</div>
                    </div>
                    <div v-if="!byStatus || byStatus.length === 0" class="text-sm text-slate-400 py-4 text-center">Belum ada data.</div>
                </div>
            </div>

            <!-- Top Sekolah Asal -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Top Asal Sekolah</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div v-for="(school, idx) in bySchool" :key="school.origin_school_name" class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-700 text-xs font-black flex items-center justify-center shrink-0">
                            {{ idx + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-700 truncate mb-1">
                                {{ school.origin_school_name || 'Tidak Diketahui' }}
                            </div>
                            <div class="bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div
                                    class="h-1.5 bg-indigo-500 rounded-full transition-all duration-700"
                                    :style="{ width: (school.total / maxSchool * 100) + '%' }"
                                ></div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-slate-700 shrink-0">{{ school.total }}</div>
                    </div>
                    <div v-if="!bySchool || bySchool.length === 0" class="text-sm text-slate-400 py-4 text-center">Belum ada data sekolah.</div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
