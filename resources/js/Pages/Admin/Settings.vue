<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    app_name: props.settings.app_name,
    school_name: props.settings.school_name,
    developer_name: props.settings.developer_name,
    app_version: props.settings.app_version,
    academic_year: props.settings.academic_year,
    school_logo: null,
});

const previewLogo = ref(props.settings.school_logo_path ? '/storage/' + props.settings.school_logo_path : null);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.school_logo = file;
        previewLogo.value = URL.createObjectURL(file);
    }
};

const deleteLogo = () => {
    Swal.fire({
        title: 'Hapus Logo Sekolah?',
        text: 'Logo sekolah akan dihapus dari sistem. Anda yakin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.settings.deleteLogo'), {
                preserveScroll: true,
                onSuccess: () => {
                    previewLogo.value = null;
                    form.school_logo = null;
                    Swal.fire('Terhapus!', 'Logo sekolah berhasil dihapus.', 'success');
                }
            });
        }
    });
};

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Reset file input if needed
        }
    });
};
</script>

<template>
    <Head title="Identitas Aplikasi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Identitas Aplikasi & Sekolah</h2>
        </template>

        <div class="max-w-4xl">
            <div v-if="$page.props.flash.success" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ $page.props.flash.success }}</span>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Profil Aplikasi & Instansi</h3>
                </div>

                <div class="p-8">
                    <!-- Logo Section -->
                    <div class="flex flex-col items-center justify-center mb-10 pb-10 border-b border-slate-100">
                        <div class="w-32 h-32 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400 overflow-hidden mb-4 shadow-sm relative group">
                            <img v-if="previewLogo" :src="previewLogo" class="w-full h-full object-contain p-2 bg-white">
                            <svg v-else class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            
                            <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-bold mb-2">Ubah Logo</span>
                            </div>
                            <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleLogoChange" accept="image/png, image/jpeg, image/jpg">
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="relative overflow-hidden bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors cursor-pointer">
                                <span>{{ previewLogo ? 'Ganti File Logo' : 'Unggah File Logo' }}</span>
                                <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleLogoChange" accept="image/png, image/jpeg, image/jpg">
                            </div>
                            <button v-if="previewLogo" type="button" @click="deleteLogo" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold border border-red-100 transition-colors">
                                Hapus
                            </button>
                        </div>
                        
                        <div class="text-[11px] text-slate-500 mt-3 font-medium uppercase tracking-wider">Format: JPG/PNG, Maksimal: 2MB</div>
                        <div v-if="form.errors.school_logo" class="text-xs text-red-600 mt-2 font-bold">{{ form.errors.school_logo }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Tahun Ajaran -->
                        <div class="md:col-span-2 max-w-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tahun Pelajaran Aktif</label>
                            <input v-model="form.academic_year" type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 hover:bg-white focus:bg-white transition-colors" placeholder="Contoh: 2026/2027">
                            <div v-if="form.errors.academic_year" class="text-xs text-red-600 mt-1 font-medium">{{ form.errors.academic_year }}</div>
                        </div>

                        <!-- Nama Sekolah -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Sekolah</label>
                            <input v-model="form.school_name" type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 hover:bg-white focus:bg-white transition-colors" placeholder="Contoh: SMP Bustanul Ulum">
                            <div v-if="form.errors.school_name" class="text-xs text-red-600 mt-1 font-medium">{{ form.errors.school_name }}</div>
                        </div>
                        
                        <!-- Nama Aplikasi -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Aplikasi / Portal</label>
                            <input v-model="form.app_name" type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 hover:bg-white focus:bg-white transition-colors" placeholder="Contoh: SPMB Digital">
                            <div v-if="form.errors.app_name" class="text-xs text-red-600 mt-1 font-medium">{{ form.errors.app_name }}</div>
                        </div>

                        <div class="md:col-span-2 border-t border-slate-100 my-2 pt-6"></div>

                        <!-- Nama Pengembang -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Pengembang / Vendor</label>
                            <input v-model="form.developer_name" type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 hover:bg-white focus:bg-white transition-colors" placeholder="Contoh: Tim IT Sekolah">
                            <div v-if="form.errors.developer_name" class="text-xs text-red-600 mt-1 font-medium">{{ form.errors.developer_name }}</div>
                        </div>

                        <!-- Versi Aplikasi -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Versi Aplikasi</label>
                            <input v-model="form.app_version" type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 hover:bg-white focus:bg-white transition-colors" placeholder="Contoh: 1.0.0">
                            <div v-if="form.errors.app_version" class="text-xs text-red-600 mt-1 font-medium">{{ form.errors.app_version }}</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center hover:-translate-y-0.5 disabled:opacity-70 disabled:hover:translate-y-0">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </button>
                </div>
            </form>
        </div>

    </AuthenticatedLayout>
</template>
