<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    registration_status: props.settings.registration_status,
    quota: props.settings.quota,
    report_semester: props.settings.report_semester,
    available_subjects: [...props.settings.available_subjects],
    subjects_required: [...props.settings.subjects_required],
    
    opt_pendidikan: [...props.settings.opt_pendidikan],
    opt_pekerjaan: [...props.settings.opt_pekerjaan],
    opt_penghasilan: [...props.settings.opt_penghasilan],
    opt_kebutuhan_khusus: [...props.settings.opt_kebutuhan_khusus],
    opt_tempat_tinggal: [...props.settings.opt_tempat_tinggal],
    opt_ekstrakurikuler: [...props.settings.opt_ekstrakurikuler],
    opt_moda_transportasi: [...props.settings.opt_moda_transportasi],
    opt_alasan_kip: [...props.settings.opt_alasan_kip],
});

const newSubject = ref({ key: '', label: '' });

const addSubject = () => {
    if (newSubject.value.key && newSubject.value.label) {
        // check if key exists
        if (!form.available_subjects.find(s => s.key === newSubject.value.key)) {
            form.available_subjects.push({ ...newSubject.value });
            newSubject.value = { key: '', label: '' };
        } else {
            alert('Kode mata pelajaran sudah ada!');
        }
    }
};

const removeSubject = (index, key) => {
    form.available_subjects.splice(index, 1);
    const reqIndex = form.subjects_required.indexOf(key);
    if (reqIndex !== -1) {
        form.subjects_required.splice(reqIndex, 1);
    }
};

const submit = () => {
    form.post(route('admin.admission-settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pengaturan PPDB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-h5 font-weight-bold text-grey-darken-3">Pengaturan PPDB</h2>
        </template>

        <v-row justify="center">
            <v-col cols="12" md="10" lg="9">
                
                <v-alert v-if="$page.props.flash.success" type="success" variant="tonal" class="mb-6 rounded-xl border-s-lg border-success" closable>
                    {{ $page.props.flash.success }}
                </v-alert>

                <v-form @submit.prevent="submit">
                    <v-row>
                        <!-- Parameter Penerimaan Murid Baru -->
                        <v-col cols="12" md="6">
                            <v-card class="pa-6 rounded-xl elevation-2 h-100 mb-0">
                                <div class="d-flex align-center mb-6 border-b pb-3">
                                    <v-icon color="primary" class="mr-2" size="28">mdi-school</v-icon>
                                    <h3 class="text-h6 font-weight-bold text-grey-darken-3">Parameter Penerimaan Murid Baru</h3>
                                </div>

                                <!-- Kuota Penerimaan -->
                                <div class="mb-4">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Kuota Penerimaan (Siswa)</div>
                                    <v-text-field
                                        v-model="form.quota"
                                        type="number"
                                        variant="outlined"
                                        color="primary"
                                        density="comfortable"
                                        :error-messages="form.errors.quota"
                                        prepend-inner-icon="mdi-account-multiple"
                                        hide-details="auto"
                                    ></v-text-field>
                                </div>

                                <!-- Status Pendaftaran -->
                                <div>
                                    <div class="text-subtitle-2 font-weight-bold mb-2 text-grey-darken-2">Status Penerimaan</div>
                                    <v-radio-group v-model="form.registration_status" inline color="primary" hide-details="auto">
                                        <v-radio label="Buka Pendaftaran" value="open" class="mr-4"></v-radio>
                                        <v-radio label="Tutup Pendaftaran" value="closed"></v-radio>
                                    </v-radio-group>
                                    
                                    <v-alert
                                        v-if="form.registration_status === 'closed'"
                                        type="warning"
                                        variant="tonal"
                                        class="rounded-lg mt-3"
                                        density="compact"
                                    >
                                        <strong>Penting:</strong> Calon siswa tidak dapat registrasi baru saat status ditutup.
                                    </v-alert>
                                </div>
                            </v-card>
                        </v-col>

                        <!-- Konfigurasi Nilai Rapor -->
                        <v-col cols="12" md="6">
                            <v-card class="pa-6 rounded-xl elevation-2 h-100 mb-0">
                                <div class="d-flex align-center mb-6 border-b pb-3">
                                    <v-icon color="success" class="mr-2" size="28">mdi-book-education</v-icon>
                                    <h3 class="text-h6 font-weight-bold text-grey-darken-3">Konfigurasi Nilai Rapor</h3>
                                </div>

                                <!-- Semester Rapor -->
                                <div class="mb-6">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Keterangan Rapor yang Digunakan</div>
                                    <div class="text-caption text-grey mb-2">Akan ditampilkan sebagai judul di form input nilai siswa.</div>
                                    <v-text-field
                                        v-model="form.report_semester"
                                        placeholder="Contoh: Kelas 6 Semester 2"
                                        variant="outlined"
                                        color="success"
                                        density="comfortable"
                                        :error-messages="form.errors.report_semester"
                                        prepend-inner-icon="mdi-notebook"
                                        hide-details="auto"
                                    ></v-text-field>
                                </div>

                                <!-- Master Mata Pelajaran -->
                                <div class="mb-6">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Mata Pelajaran (Master Data)</div>
                                    <div class="text-caption text-grey mb-3">Kelola mata pelajaran yang tersedia.</div>
                                    
                                    <div class="d-flex align-center ga-2 mb-3">
                                        <v-text-field v-model="newSubject.key" label="Kode (cth: mtk)" density="compact" variant="outlined" hide-details></v-text-field>
                                        <v-text-field v-model="newSubject.label" label="Nama Mata Pelajaran" density="compact" variant="outlined" hide-details></v-text-field>
                                        <v-btn color="success" @click="addSubject" icon="mdi-plus" density="comfortable" variant="tonal"></v-btn>
                                    </div>
                                    <v-alert v-if="form.errors.available_subjects" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
                                        {{ form.errors.available_subjects }}
                                    </v-alert>
                                </div>

                                <!-- Mata Pelajaran Wajib -->
                                <div>
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Mata Pelajaran Wajib Diinput</div>
                                    <div class="text-caption text-grey mb-3">Centang mata pelajaran yang harus diisi. Klik tombol (Hapus) untuk menghapus dari Master Data.</div>
                                    
                                    <v-alert v-if="form.errors.subjects_required" type="error" variant="tonal" class="mb-3 rounded-lg" density="compact">
                                        {{ form.errors.subjects_required }}
                                    </v-alert>

                                    <v-list density="compact" class="bg-transparent pa-0">
                                        <v-list-item v-for="(subj, index) in form.available_subjects" :key="index" class="px-0">
                                            <template v-slot:prepend>
                                                <v-checkbox-btn
                                                    v-model="form.subjects_required"
                                                    :value="subj.key"
                                                    color="success"
                                                ></v-checkbox-btn>
                                            </template>
                                            <v-list-item-title class="text-wrap" style="line-height: 1.2;">{{ subj.label }} <span class="text-caption text-grey">({{ subj.key }})</span></v-list-item-title>
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="removeSubject(index, subj.key)"></v-btn>
                                            </template>
                                        </v-list-item>
                                    </v-list>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>

                    <!-- Master Data Referensi Card -->
                    <v-card class="pa-6 rounded-xl elevation-2 mt-6 mb-6">
                        <div class="d-flex align-center mb-6 border-b pb-3">
                            <v-icon color="info" class="mr-2" size="28">mdi-format-list-bulleted</v-icon>
                            <h3 class="text-h6 font-weight-bold text-grey-darken-3">Master Data Referensi (Opsi Form)</h3>
                        </div>
                        <p class="text-body-2 text-grey-darken-1 mb-6">Kelola opsi-opsi di bawah ini. Anda dapat <strong>Ketik lalu tekan Enter</strong> untuk menambahkan opsi baru. Klik tombol (x) pada chip untuk menghapus opsi.</p>

                        <!-- Group 1: Profil & Keseharian Siswa -->
                        <v-card variant="outlined" class="rounded-xl border-info mb-6">
                            <v-card-title class="bg-info-lighten-5 text-info text-subtitle-1 font-weight-bold py-3 px-4 border-b">
                                <v-icon start size="small">mdi-account-details</v-icon> Profil & Keseharian Siswa
                            </v-card-title>
                            <v-card-text class="pa-4">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Tempat Tinggal</div>
                                        <v-combobox
                                            v-model="form.opt_tempat_tinggal"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Moda Transportasi</div>
                                        <v-combobox
                                            v-model="form.opt_moda_transportasi"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Kebutuhan Khusus</div>
                                        <v-combobox
                                            v-model="form.opt_kebutuhan_khusus"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Ekstrakurikuler</div>
                                        <v-combobox
                                            v-model="form.opt_ekstrakurikuler"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>

                        <!-- Group 2: Sosial Ekonomi Keluarga -->
                        <v-card variant="outlined" class="rounded-xl border-success">
                            <v-card-title class="bg-success-lighten-5 text-success text-subtitle-1 font-weight-bold py-3 px-4 border-b">
                                <v-icon start size="small">mdi-human-male-female-child</v-icon> Latar Belakang & Ekonomi Keluarga
                            </v-card-title>
                            <v-card-text class="pa-4">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Pendidikan Orang Tua</div>
                                        <v-combobox
                                            v-model="form.opt_pendidikan"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Pekerjaan Orang Tua</div>
                                        <v-combobox
                                            v-model="form.opt_pekerjaan"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Penghasilan Orang Tua</div>
                                        <v-combobox
                                            v-model="form.opt_penghasilan"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Opsi Alasan Layak KIP / PIP</div>
                                        <v-combobox
                                            v-model="form.opt_alasan_kip"
                                            chips
                                            multiple
                                            closable-chips
                                            variant="outlined"
                                            density="comfortable"
                                            hide-details="auto"
                                            hint="Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus."
                                            persistent-hint
                                        ></v-combobox>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
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
                            Simpan Semua Pengaturan
                        </v-btn>
                    </div>
                </v-form>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>
