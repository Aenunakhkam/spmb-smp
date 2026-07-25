<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StudentDataViewer from '@/Components/StudentDataViewer.vue';
import TextInput from '@/Components/TextInput.vue';

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
            <h2 class="text-xl font-bold text-slate-800">Data Master Siswa & Orang Tua</h2>
        </template>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                <div class="col-span-12 md:col-span-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <v-icon color="grey-lighten-1">mdi-magnify</v-icon>
                        </div>
                        <TextInput
                            v-model="search"
                            placeholder="Cari Nama / NISN / No. Reg"
                            class="w-full pl-10"
                        />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto"><table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr>
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
                            <button @click="openDetail(reg)" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    <tr v-if="registrations.data.length === 0">
                        <td colspan="6" class="text-center py-4 text-grey">Tidak ada data ditemukan.</td>
                    </tr>
                </tbody>
            </table></div>

            <div class="mt-4 d-flex justify-center">
                <v-pagination
                    v-model="registrations.current_page"
                    :length="registrations.last_page"
                    @update:model-value="v => router.get(route('admin.master.index'), { page: v, search }, { preserveState: true })"
                    active-color="primary"
                ></v-pagination>
            </div>
        </div>

        <!-- Dialog Detail Siswa -->
        <v-dialog v-model="dialog" max-width="800" scrollable>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <span class="text-lg font-bold text-slate-800">Detail Siswa: {{ selectedReg.student_detail?.full_name }}</span>
                    <button @click="dialog = false" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="pa-4 bg-grey-lighten-4 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                            <div class="col-span-12">Nomor Pendaftaran</div>
                            <div class="col-span-12">: <span class="text-primary">{{ selectedReg.registration_number }}</span></div>
                            <div class="col-span-12">Status Verifikasi</div>
                            <div class="col-span-12">: {{ selectedReg.status }}</div>
                            <div class="col-span-12">Tanggal Daftar</div>
                            <div class="col-span-12">: {{ formatDate(selectedReg.created_at) }}</div>
                        </div>
                    </div>

                    <div class="pa-4 pt-0 mt-4">
                        <StudentDataViewer :registration="selectedReg" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <button @click="dialog = false" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">Tutup</button>
                </div>
            </div>
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
