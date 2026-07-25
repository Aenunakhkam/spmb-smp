<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Chart.js imports
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

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
        confirmButtonColor: '#3b82f6', // modern blue
        cancelButtonColor: '#ef4444', // modern red
        confirmButtonText: 'Ya, Jalankan!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg px-6',
            cancelButton: 'rounded-lg px-6'
        }
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

// -- Chart Configurations -- //

// Status Chart (Doughnut)
const statusChartData = computed(() => ({
    labels: ['Menunggu', 'Terverifikasi', 'Belum Lengkap', 'Diterima', 'Ditolak'],
    datasets: [
        {
            data: [
                props.stats?.pending || 0,
                props.stats?.verified || 0,
                props.stats?.incomplete || 0,
                props.stats?.passed || 0,
                props.stats?.failed || 0
            ],
            backgroundColor: [
                '#f59e0b', // amber-500
                '#10b981', // emerald-500
                '#ef4444', // red-500
                '#3b82f6', // blue-500
                '#64748b'  // slate-500
            ],
            borderWidth: 0,
            hoverOffset: 4
        }
    ]
}));

const statusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: { font: { family: 'Inter', size: 12 }, usePointStyle: true, boxWidth: 8 }
        }
    },
    cutout: '75%'
};

// Gender Chart (Doughnut)
const genderChartData = computed(() => ({
    labels: ['Laki-laki', 'Perempuan'],
    datasets: [
        {
            data: [
                props.stats?.gender?.L || 0,
                props.stats?.gender?.P || 0
            ],
            backgroundColor: ['#0ea5e9', '#ec4899'], // sky-500, pink-500
            borderWidth: 0,
            hoverOffset: 4
        }
    ]
}));

const genderChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { font: { family: 'Inter', size: 12 }, usePointStyle: true, boxWidth: 8 }
        }
    },
    cutout: '70%'
};

// Top Schools Chart (Bar)
const schoolsChartData = computed(() => {
    const schools = props.stats?.top_schools || [];
    return {
        labels: schools.map(s => s.origin_school_name.length > 15 ? s.origin_school_name.substring(0, 15) + '...' : s.origin_school_name),
        datasets: [
            {
                label: 'Jumlah Pendaftar',
                data: schools.map(s => s.total),
                backgroundColor: '#6366f1', // indigo-500
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 24,
            }
        ]
    };
});

const schoolsChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false }, // slate-100
            ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' } // slate-500
        },
        x: {
            grid: { display: false, drawBorder: false },
            ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
        }
    }
};

</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight mb-1">Dashboard Utama</h2>
                <div class="text-sm text-slate-500 font-medium flex items-center space-x-2">
                    <span>Tahun Ajaran: <strong class="text-slate-700">{{ stats?.academic_year || '-' }}</strong></span>
                    <span class="text-slate-300">|</span>
                    <span>Status: 
                        <span :class="stats?.status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2.5 py-0.5 rounded-full text-xs font-bold ml-1 border" :style="stats?.status === 'open' ? 'border-color: #bbf7d0' : 'border-color: #fecdd3'">
                            {{ stats?.status === 'open' ? 'Buka Pendaftaran' : 'Tutup' }}
                        </span>
                    </span>
                </div>
            </div>
        </template>


        <!-- Quick Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 p-4 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800">Ranking Otomatis</div>
                    <div class="text-xs text-slate-500">Perbarui status kelulusan berdasarkan nilai rapor siswa</div>
                </div>
            </div>
            <button @click="runRanking" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Jalankan Ranking
            </button>
        </div>

        <!-- Stats Overview Row -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <!-- Total -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-blue-100 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-blue-700 mb-1 uppercase tracking-wider">Total Pendaftar</div>
                    <div class="text-3xl font-black text-blue-900">{{ stats?.total || 0 }}</div>
                </div>
            </div>

            <!-- Menunggu -->
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-amber-100 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-amber-700 mb-1 uppercase tracking-wider">Menunggu</div>
                    <div class="text-3xl font-black text-amber-900">{{ stats?.pending || 0 }}</div>
                </div>
            </div>

            <!-- Terverifikasi -->
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-emerald-100 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-emerald-700 mb-1 uppercase tracking-wider">Terverifikasi</div>
                    <div class="text-3xl font-black text-emerald-900">{{ stats?.verified || 0 }}</div>
                </div>
            </div>

            <!-- Belum Lengkap -->
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-red-100 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-red-700 mb-1 uppercase tracking-wider">Belum Lengkap</div>
                    <div class="text-3xl font-black text-red-900">{{ stats?.incomplete || 0 }}</div>
                </div>
            </div>

            <!-- Diterima -->
            <div class="bg-sky-50 border border-sky-100 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-sky-100 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-sky-700 mb-1 uppercase tracking-wider">Diterima (Lulus)</div>
                    <div class="text-3xl font-black text-sky-900">{{ stats?.passed || 0 }}</div>
                </div>
            </div>

            <!-- Ditolak -->
            <div class="bg-slate-100 border border-slate-200 rounded-xl p-4 relative overflow-hidden shadow-sm group hover:shadow-md transition-all">
                <div class="absolute -right-4 -bottom-4 text-slate-200 opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Ditolak</div>
                    <div class="text-3xl font-black text-slate-700">{{ stats?.failed || 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Quota Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Kapasitas Kuota Pendaftar</h3>
                    <p class="text-sm text-slate-500">Persentase kursi terisi dari total kuota pendaftaran ({{ stats?.quota || 0 }} kursi)</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-black" :class="quotaPercentage >= 100 ? 'text-green-600' : 'text-blue-600'">{{ quotaPercentage }}%</div>
                    <div class="text-xs text-slate-500 font-bold uppercase">{{ stats?.verified || 0 }} Terisi</div>
                </div>
            </div>
            
            <div class="w-full bg-slate-100 rounded-full h-4 mb-2 overflow-hidden border border-slate-200">
                <div class="h-4 rounded-full transition-all duration-1000 relative overflow-hidden" 
                     :class="quotaPercentage >= 100 ? 'bg-green-500' : (quotaPercentage > 80 ? 'bg-amber-500' : 'bg-blue-600')"
                     :style="'width: ' + quotaPercentage + '%'">
                     <div class="absolute inset-0 bg-white/20" style="background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="w-full space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Status Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Distribusi Status Pendaftar</h3>
                    </div>
                    <div style="height: 250px" class="relative">
                        <Doughnut :data="statusChartData" :options="statusChartOptions" />
                    </div>
                </div>
                
                <!-- Gender Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-lg bg-pink-50 flex items-center justify-center text-pink-600 mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Komposisi Gender</h3>
                    </div>
                    <div style="height: 250px" class="relative">
                        <Doughnut :data="genderChartData" :options="genderChartOptions" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
