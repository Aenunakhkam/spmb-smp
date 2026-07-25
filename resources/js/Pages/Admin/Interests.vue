<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    chartData: Array,
    students: Object,
    filters: Object,
    minatOptions: Array,
});

const filterMinat = ref(props.filters?.minat || 'Semua');

watch(filterMinat, (value) => {
    router.get(route('admin.interests'), { minat: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const getMinatColor = (minat) => {
    const map = {
        'Belum Memilih': { bg: 'bg-slate-100', text: 'text-slate-600', border: 'border-slate-200' },
        'BAHASA':        { bg: 'bg-sky-100',   text: 'text-sky-700',   border: 'border-sky-200' },
        'MIPA':          { bg: 'bg-emerald-100', text: 'text-emerald-700', border: 'border-emerald-200' },
        'KUNING':        { bg: 'bg-amber-100', text: 'text-amber-700', border: 'border-amber-200' },
        'TAHFIDZ':       { bg: 'bg-blue-100',  text: 'text-blue-700',  border: 'border-blue-200' },
        'Reguler':       { bg: 'bg-violet-100', text: 'text-violet-700', border: 'border-violet-200' },
        'Sains':         { bg: 'bg-teal-100',  text: 'text-teal-700',  border: 'border-teal-200' },
        'Olahraga/Seni': { bg: 'bg-orange-100', text: 'text-orange-700', border: 'border-orange-200' },
    };
    return map[minat] || { bg: 'bg-slate-100', text: 'text-slate-600', border: 'border-slate-200' };
};

const getStatusColor = (status) => {
    const map = {
        'passed':     { bg: 'bg-emerald-100', text: 'text-emerald-700', label: 'Diterima' },
        'failed':     { bg: 'bg-red-100',     text: 'text-red-700',     label: 'Ditolak' },
        'verified':   { bg: 'bg-blue-100',    text: 'text-blue-700',    label: 'Terverifikasi' },
        'pending':    { bg: 'bg-amber-100',   text: 'text-amber-700',   label: 'Menunggu' },
        'incomplete': { bg: 'bg-slate-100',   text: 'text-slate-600',   label: 'Belum Lengkap' },
    };
    return map[status] || { bg: 'bg-slate-100', text: 'text-slate-600', label: status };
};

const goToPage = (url) => {
    if (!url) return;
    const parsed = new URL(url);
    const params = Object.fromEntries(parsed.searchParams.entries());
    if (filterMinat.value && filterMinat.value !== 'Semua') params.minat = filterMinat.value;
    router.get(route('admin.interests'), params, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head title="Data Peminatan Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Data Peminatan Siswa</h2>
        </template>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">
            <div
                v-for="stat in chartData"
                :key="stat.label"
                class="bg-white rounded-xl border shadow-sm p-4 flex items-center gap-3"
                :class="getMinatColor(stat.label).border"
            >
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                    :class="getMinatColor(stat.label).bg">
                    <svg class="w-5 h-5" :class="getMinatColor(stat.label).text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide" :class="getMinatColor(stat.label).text">{{ stat.label }}</div>
                    <div class="text-2xl font-black text-slate-800">{{ stat.count }}</div>
                </div>
            </div>
            <div v-if="!chartData || chartData.length === 0" class="col-span-full bg-white rounded-xl border border-slate-200 shadow-sm p-6 text-center text-slate-400 text-sm">
                Belum ada data peminatan.
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-base font-bold text-slate-800">Daftar Pendaftar per Minat</h3>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Export PDF -->
                    <a
                        :href="route('admin.interests.exportPdf', { minat: filterMinat })"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <!-- Export Excel -->
                    <a
                        :href="route('admin.interests.exportExcel', { minat: filterMinat })"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                    <!-- Filter -->
                    <select
                        v-model="filterMinat"
                        class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 bg-white text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                        <option v-for="opt in minatOptions" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider">No. Pendaftaran</th>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider">Nama Lengkap</th>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider">L/P</th>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider">No. WhatsApp</th>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider">Peminatan</th>
                            <th class="px-4 py-3 font-bold text-slate-600 text-xs uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="student in students.data"
                            :key="student.id"
                            class="hover:bg-slate-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-mono text-xs font-bold text-slate-700">{{ student.registration_number }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ student.full_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ student.gender }}</td>
                            <td class="px-4 py-3">
                                <a
                                    :href="`https://wa.me/${student.phone.replace(/^0/, '62')}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 font-medium hover:underline"
                                >
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    {{ student.phone }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold border"
                                    :class="[getMinatColor(student.minat).bg, getMinatColor(student.minat).text, getMinatColor(student.minat).border]"
                                >
                                    {{ student.minat }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                                    :class="[getStatusColor(student.status).bg, getStatusColor(student.status).text]"
                                >
                                    {{ getStatusColor(student.status).label }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!students.data || students.data.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 text-sm">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Belum ada data pendaftar untuk minat ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="students.links && students.links.length > 3" class="flex items-center justify-between px-4 py-3 border-t border-slate-100 bg-slate-50">
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan {{ students.from }}–{{ students.to }} dari {{ students.total }} pendaftar
                </div>
                <div class="flex gap-1">
                    <button
                        v-for="link in students.links"
                        :key="link.label"
                        @click="goToPage(link.url)"
                        :disabled="!link.url"
                        v-html="link.label"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border"
                        :class="link.active
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                            : link.url
                                ? 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'
                                : 'bg-white text-slate-300 border-slate-100 cursor-not-allowed'"
                    ></button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
