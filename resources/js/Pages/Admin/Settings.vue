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
            <h2 class="text-h5 font-weight-bold text-grey-darken-3">Identitas Aplikasi & Sekolah</h2>
        </template>

        <v-row justify="center">
            <v-col cols="12" md="9" lg="7">
                
                <v-alert v-if="$page.props.flash.success" type="success" variant="tonal" class="mb-4 rounded-xl" closable>
                    {{ $page.props.flash.success }}
                </v-alert>

                <v-form @submit.prevent="submit">
                    <!-- App Identity Card -->
                    <v-card class="pa-6 rounded-xl elevation-2 mb-6">
                        <div class="d-flex align-center mb-6 border-b pb-3">
                            <v-icon color="primary" class="mr-2" size="28">mdi-application-cog</v-icon>
                            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Profil Aplikasi</h3>
                        </div>

                        <!-- Logo Sekolah -->
                        <div class="mb-6 d-flex flex-column align-center">
                            <v-avatar size="120" color="grey-lighten-3" class="mb-3 elevation-2 border">
                                <v-img v-if="previewLogo" :src="previewLogo" cover></v-img>
                                <v-icon v-else size="48" color="grey">mdi-domain</v-icon>
                            </v-avatar>
                            
                            <div class="d-flex ga-2 mb-2">
                                <v-btn color="primary" variant="tonal" size="small" class="rounded-lg overflow-hidden position-relative font-weight-bold">
                                    {{ previewLogo ? 'Ganti Logo' : 'Unggah Logo Sekolah' }}
                                    <input type="file" class="position-absolute opacity-0" style="left:0; top:0; width:100%; height:100%; cursor:pointer" @change="handleLogoChange" accept="image/png, image/jpeg, image/jpg">
                                </v-btn>
                                
                                <v-btn v-if="previewLogo && !form.school_logo" color="error" variant="tonal" size="small" class="rounded-lg font-weight-bold" @click="deleteLogo">
                                    Hapus
                                </v-btn>
                            </div>
                            
                            <div class="text-caption text-grey">Format: JPG/PNG, Max: 2MB</div>
                            <div v-if="form.errors.school_logo" class="text-caption text-error">{{ form.errors.school_logo }}</div>
                        </div>

                        <!-- Tahun Ajaran -->
                        <div class="mb-4">
                            <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Tahun Pelajaran Aktif</div>
                            <v-text-field
                                v-model="form.academic_year"
                                placeholder="Contoh: 2026/2027"
                                variant="outlined"
                                color="primary"
                                density="comfortable"
                                :error-messages="form.errors.academic_year"
                                prepend-inner-icon="mdi-calendar-range"
                            ></v-text-field>
                        </div>

                        <v-row>
                            <!-- Nama Sekolah -->
                            <v-col cols="12" md="6">
                                <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Nama Sekolah</div>
                                <v-text-field
                                    v-model="form.school_name"
                                    placeholder="Contoh: SMP Bustanul Ulum"
                                    variant="outlined"
                                    color="primary"
                                    density="comfortable"
                                    :error-messages="form.errors.school_name"
                                    prepend-inner-icon="mdi-school-outline"
                                ></v-text-field>
                            </v-col>
                            
                            <!-- Nama Aplikasi -->
                            <v-col cols="12" md="6">
                                <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Nama Aplikasi</div>
                                <v-text-field
                                    v-model="form.app_name"
                                    placeholder="Contoh: SPMB Online"
                                    variant="outlined"
                                    color="primary"
                                    density="comfortable"
                                    :error-messages="form.errors.app_name"
                                    prepend-inner-icon="mdi-application"
                                ></v-text-field>
                            </v-col>

                            <!-- Nama Pengembang -->
                            <v-col cols="12" md="6">
                                <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Nama Pengembang</div>
                                <v-text-field
                                    v-model="form.developer_name"
                                    placeholder="Contoh: Tim IT Sekolah"
                                    variant="outlined"
                                    color="primary"
                                    density="comfortable"
                                    :error-messages="form.errors.developer_name"
                                    prepend-inner-icon="mdi-code-tags"
                                ></v-text-field>
                            </v-col>

                            <!-- Versi Aplikasi -->
                            <v-col cols="12" md="6">
                                <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Versi Aplikasi</div>
                                <v-text-field
                                    v-model="form.app_version"
                                    placeholder="Contoh: 1.0.0"
                                    variant="outlined"
                                    color="primary"
                                    density="comfortable"
                                    :error-messages="form.errors.app_version"
                                    prepend-inner-icon="mdi-source-branch"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card>

                    <!-- Save Button -->
                    <div class="d-flex justify-end">
                        <v-btn
                            color="primary"
                            size="large"
                            type="submit"
                            :loading="form.processing"
                            class="rounded-lg font-weight-bold px-8"
                            prepend-icon="mdi-content-save"
                        >
                            Simpan Identitas Aplikasi
                        </v-btn>
                    </div>
                </v-form>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>
