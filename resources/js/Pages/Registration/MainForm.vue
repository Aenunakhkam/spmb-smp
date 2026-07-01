<script setup>
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    registration: Object,
    options: Object,
});

const page = usePage();
const step = ref(props.registration.step);

// Forms
const bioForm = useForm({
    registration_id: props.registration?.id,
    nik: props.registration?.student_detail?.nik?.startsWith('PENDING-') ? '' : props.registration?.student_detail?.nik,
    no_kk: props.registration?.student_detail?.additional_data?.no_kk || '',
    gender: props.registration.student_detail?.gender || 'L',
    place_of_birth: props.registration.student_detail?.place_of_birth === 'Belum Diisi' ? '' : props.registration.student_detail?.place_of_birth,
    date_of_birth: props.registration.student_detail?.date_of_birth || '',
    religion: props.registration.student_detail?.religion || 'Islam',
    province: props.registration.student_detail?.province || '',
    city: props.registration.student_detail?.city || '',
    district: props.registration.student_detail?.district || '',
    village: props.registration.student_detail?.village || '',
    address: props.registration.student_detail?.address || '',
    postal_code: props.registration.student_detail?.postal_code || '',
    origin_school_name: props.registration.student_detail?.origin_school_name || '',
    special_needs: props.registration.student_detail?.additional_data?.special_needs || 'Tidak Ada',
    residence: props.registration.student_detail?.additional_data?.residence || 'Bersama Orang Tua',
    extracurricular: props.registration.student_detail?.additional_data?.extracurricular || 'Tidak Ada',
    transportation: props.registration.student_detail?.additional_data?.transportation || 'Jalan Kaki',
});

const parentForm = useForm({
    registration_id: props.registration.id,
    father_name: props.registration.parent_detail?.father_name || '',
    father_occupation: props.registration.parent_detail?.father_occupation || '',
    mother_name: props.registration.parent_detail?.mother_name || '',
    mother_occupation: props.registration.parent_detail?.mother_occupation || '',
    parent_phone: props.registration.parent_detail?.parent_phone || '',
    parent_address: props.registration.parent_detail?.parent_address || '',
    aid_card_number: props.registration.parent_detail?.aid_card_number || '',
    father_education: props.registration.parent_detail?.additional_data?.father_education || '',
    mother_education: props.registration.parent_detail?.additional_data?.mother_education || '',
    parent_income: props.registration.parent_detail?.additional_data?.parent_income || '',
});

const initialGradeData = {
    registration_id: props.registration.id,
    proof_file: null,
};

if (props.options?.subjects_required) {
    const coreCols = ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'];
    props.options.subjects_required.forEach(subj => {
        if (coreCols.includes(subj.key)) {
            initialGradeData[subj.key] = props.registration.grade?.[subj.key] || 0;
        } else {
            initialGradeData[subj.key] = props.registration.grade?.additional_data?.[subj.key] || 0;
        }
    });
}

const gradeForm = useForm(initialGradeData);

const docForm = useForm({
    registration_id: props.registration.id,
    type: '',
    file: null,
});

const finalizeForm = useForm({
    registration_id: props.registration.id,
});

const saveBio = () => bioForm.post(route('register.saveBiodata'), { 
    preserveScroll: true,
    onSuccess: () => step.value = 2
});

const saveParent = () => parentForm.post(route('register.saveParent'), { 
    preserveScroll: true,
    onSuccess: () => step.value = 3
});

const saveGrades = () => gradeForm.post(route('register.saveGrades'), { 
    preserveScroll: true,
    onSuccess: () => step.value = 4
});

const uploadDoc = (type) => {
    docForm.type = type;
    docForm.post(route('register.uploadDocument'), { 
        preserveScroll: true, 
        onSuccess: () => {
            docForm.reset('file');
        } 
    });
};

const finalize = () => {
    if (confirm('Apakah Anda yakin ingin memfinalisasi data? Data tidak dapat diubah setelah ini.')) {
        finalizeForm.post(route('register.finalize'));
    }
};

const docTypes = [
    { key: 'kk', label: 'Kartu Keluarga' },
    { key: 'akta', label: 'Akta Kelahiran' },
    { key: 'ijazah', label: 'Ijazah / SKL' },
    { key: 'ktp_ayah', label: 'KTP Ayah' },
    { key: 'ktp_ibu', label: 'KTP Ibu' },
    { key: 'foto', label: 'Pas Foto 3x4' },
];

const getDoc = (type) => props.registration?.documents?.find(d => d.type === type);
</script>

<template>
    <Head title="Lengkapi Formulir" />
    <v-app class="bg-grey-lighten-4">
        <!-- Floating Header -->
        <v-app-bar color="primary" elevation="4" height="100">
            <v-container class="d-flex align-center">
                <div class="d-flex align-center">
                    <v-avatar color="secondary" size="48" class="mr-4">
                        <v-icon icon="mdi-account-edit" color="primary"></v-icon>
                    </v-avatar>
                    <div>
                        <div class="text-h6 font-weight-black line-height-1">Lengkapi Formulir</div>
                        <div class="text-caption font-weight-bold opacity-70">{{ registration.student_detail.full_name }}</div>
                    </div>
                </div>
                <v-spacer></v-spacer>
                <div class="text-right hidden-sm-and-down">
                    <div class="text-caption opacity-70">No. Pendaftaran: <strong>{{ registration.registration_number }}</strong></div>
                    <div class="text-caption opacity-70">Kode Akses: <span class="text-secondary font-weight-black">{{ registration.access_code }}</span></div>
                </div>
            </v-container>
        </v-app-bar>

        <v-main class="pt-12 pb-16">
            <v-container>
                <!-- Status Alerts -->
                <v-alert
                    v-if="registration.status === 'incomplete'"
                    type="warning"
                    variant="tonal"
                    border="start"
                    class="mb-8 rounded-xl bg-white"
                >
                    <template v-slot:prepend>
                        <v-icon icon="mdi-alert-circle" size="28"></v-icon>
                    </template>
                    Data pendaftaran Anda belum lengkap. Silakan lengkapi setiap langkah di bawah ini untuk dapat diverifikasi oleh panitia.
                </v-alert>

                <v-alert
                    v-if="registration.status === 'pending'"
                    type="success"
                    variant="tonal"
                    border="start"
                    class="mb-8 rounded-xl bg-white"
                >
                    <template v-slot:prepend>
                        <v-icon icon="mdi-check-circle" size="28"></v-icon>
                    </template>
                    Pendaftaran Anda telah difinalisasi. Mohon tunggu proses verifikasi berkas oleh panitia.
                </v-alert>

                <v-row>
                    <v-col cols="12" lg="8">
                        <v-stepper v-model="step" class="rounded-xl elevation-4 border-0">
                            <v-stepper-header class="elevation-0 border-b">
                                <v-stepper-item :value="1" :complete="step > 1" color="primary">
                                    <template v-slot:title>Biodata</template>
                                </v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item :value="2" :complete="step > 2" color="primary">
                                    <template v-slot:title>Orang Tua</template>
                                </v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item :value="3" :complete="step > 3" color="primary">
                                    <template v-slot:title>Nilai</template>
                                </v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item :value="4" :complete="step > 4" color="primary">
                                    <template v-slot:title>Berkas</template>
                                </v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item :value="5" :complete="registration.status !== 'incomplete'" color="primary">
                                    <template v-slot:title>Selesai</template>
                                </v-stepper-item>
                            </v-stepper-header>

                            <v-stepper-window class="pa-6 pa-md-10">
                                <!-- Step 1: Biodata -->
                                <v-stepper-window-item :value="1">
                                    <h3 class="text-h5 font-weight-black mb-6 color-main">Informasi Pribadi</h3>
                                    <v-form @submit.prevent="saveBio">
                                        <v-row>
                                            <!-- NIK -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NIK (16 Digit Angka) <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.nik" type="number" placeholder="Masukkan 16 Digit NIK" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" :error-messages="bioForm.errors.nik" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- NO KK -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">No. KK (16 Digit Angka) <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.no_kk" type="number" placeholder="Masukkan 16 Digit No KK" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" :error-messages="bioForm.errors.no_kk" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Jenis Kelamin -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Jenis Kelamin <span class="text-error">*</span></div>
                                                    <v-select v-model="bioForm.gender" :items="[{title:'Laki-laki', value:'L'}, {title:'Perempuan', value:'P'}]" placeholder="Pilih Jenis Kelamin" prepend-inner-icon="mdi-gender-male-female" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Tempat Lahir -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Tempat Lahir <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.place_of_birth" placeholder="Kota/Kabupaten Tempat Lahir" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Tanggal Lahir -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Tanggal Lahir <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.date_of_birth" type="date" prepend-inner-icon="mdi-calendar-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Agama -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Agama <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.religion" placeholder="Masukkan Agama" prepend-inner-icon="mdi-hands-pray" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Sekolah Asal -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Nama Sekolah Asal (SD/MI) <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.origin_school_name" placeholder="Asal Sekolah" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Berkebutuhan Khusus -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Berkebutuhan Khusus</div>
                                                    <v-select v-model="bioForm.special_needs" :items="options.kebutuhan_khusus" placeholder="Pilih Jika Ada" prepend-inner-icon="mdi-wheelchair-accessibility" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Tempat Tinggal -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Tempat Tinggal</div>
                                                    <v-select v-model="bioForm.residence" :items="options.tempat_tinggal" placeholder="Pilih Tempat Tinggal" prepend-inner-icon="mdi-home-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Moda Transportasi -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Moda Transportasi</div>
                                                    <v-select v-model="bioForm.transportation" :items="options.moda_transportasi" placeholder="Pilih Kendaraan" prepend-inner-icon="mdi-car-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Ekstrakurikuler -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Pilihan Ekstrakurikuler</div>
                                                    <v-select v-model="bioForm.extracurricular" :items="options.ekstrakurikuler" placeholder="Pilih Ekstrakurikuler" prepend-inner-icon="mdi-basketball" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Alamat -->
                                            <v-col cols="12">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Alamat Lengkap <span class="text-error">*</span></div>
                                                    <v-textarea v-model="bioForm.address" placeholder="Jalan, RT/RW, Dusun" prepend-inner-icon="mdi-map-outline" variant="outlined" bg-color="grey-lighten-5" rows="2" rounded="lg" hide-details="auto"></v-textarea>
                                                </div>
                                            </v-col>
                                            <!-- Desa -->
                                            <v-col cols="12" md="4">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Desa / Kelurahan <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.village" placeholder="Nama Desa" prepend-inner-icon="mdi-home-city-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Kecamatan -->
                                            <v-col cols="12" md="4">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Kecamatan <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.district" placeholder="Nama Kecamatan" prepend-inner-icon="mdi-city-variant-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Kabupaten -->
                                            <v-col cols="12" md="4">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Kabupaten / Kota <span class="text-error">*</span></div>
                                                    <v-text-field v-model="bioForm.city" placeholder="Nama Kabupaten" prepend-inner-icon="mdi-city" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                        </v-row>
                                        <div class="d-flex justify-end mt-8">
                                            <v-btn color="primary" size="large" type="submit" :loading="bioForm.processing" class="rounded-xl px-10 font-weight-black">
                                                Simpan & Lanjut
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-stepper-window-item>

                                <!-- Step 2: Orang Tua -->
                                <v-stepper-window-item :value="2">
                                    <h3 class="text-h5 font-weight-black mb-6 color-main">Data Orang Tua / Wali</h3>
                                    <v-form @submit.prevent="saveParent">
                                        <v-row>
                                            <!-- Ayah -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Nama Lengkap Ayah <span class="text-error">*</span></div>
                                                    <v-text-field v-model="parentForm.father_name" placeholder="Sesuai KK" prepend-inner-icon="mdi-human-male" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Ibu -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Nama Lengkap Ibu <span class="text-error">*</span></div>
                                                    <v-text-field v-model="parentForm.mother_name" placeholder="Sesuai KK" prepend-inner-icon="mdi-human-female" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- Pendidikan Ayah -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Pendidikan Terakhir Ayah</div>
                                                    <v-select v-model="parentForm.father_education" :items="options.pendidikan" placeholder="Pilih Pendidikan" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Pendidikan Ibu -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Pendidikan Terakhir Ibu</div>
                                                    <v-select v-model="parentForm.mother_education" :items="options.pendidikan" placeholder="Pilih Pendidikan" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Pekerjaan Ayah -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Pekerjaan Ayah</div>
                                                    <v-select v-model="parentForm.father_occupation" :items="options.pekerjaan" placeholder="Pilih Pekerjaan" prepend-inner-icon="mdi-briefcase-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Pekerjaan Ibu -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Pekerjaan Ibu</div>
                                                    <v-select v-model="parentForm.mother_occupation" :items="options.pekerjaan" placeholder="Pilih Pekerjaan" prepend-inner-icon="mdi-briefcase-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- Penghasilan -->
                                            <v-col cols="12">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Penghasilan Rata-rata Orang Tua</div>
                                                    <v-select v-model="parentForm.parent_income" :items="options.penghasilan" placeholder="Pilih Rentang Penghasilan" prepend-inner-icon="mdi-cash-multiple" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
                                                </div>
                                            </v-col>
                                            <!-- No WA -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">No. WhatsApp Aktif <span class="text-error">*</span></div>
                                                    <v-text-field v-model="parentForm.parent_phone" placeholder="08xxxxxxxxxx" prepend-inner-icon="mdi-whatsapp" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <!-- KIP/PKH -->
                                            <v-col cols="12" md="6">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">Nomor KIP/PKH (Jika Ada)</div>
                                                    <v-text-field v-model="parentForm.aid_card_number" placeholder="Kosongkan jika tidak ada" prepend-inner-icon="mdi-card-text-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                        </v-row>
                                        <div class="d-flex justify-space-between mt-8">
                                            <v-btn variant="text" @click="step = 1" class="rounded-xl font-weight-bold">Kembali</v-btn>
                                            <v-btn color="primary" size="large" type="submit" :loading="parentForm.processing" class="rounded-xl px-10 font-weight-black">
                                                Simpan & Lanjut
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-stepper-window-item>

                                <!-- Step 3: Nilai Rapor -->
                                <v-stepper-window-item :value="3">
                                    <h3 class="text-h5 font-weight-black mb-6 color-main">{{ options.report_semester || 'Nilai Rapor' }}</h3>
                                    <v-form @submit.prevent="saveGrades">
                                        <v-row>
                                            <v-col v-for="subj in options.subjects_required" :key="subj.key" cols="6" md="3">
                                                <div class="mb-2">
                                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">{{ subj.label }} <span class="text-error">*</span></div>
                                                    <v-text-field v-model="gradeForm[subj.key]" type="number" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
                                                </div>
                                            </v-col>
                                            <v-col cols="12">
                                                <div class="bg-grey-lighten-5 pa-6 rounded-xl border-dashed border-2 mt-4">
                                                    <div class="text-subtitle-2 font-weight-bold mb-2 text-grey-darken-3">Upload Scan Rapor / Bukti Nilai (JPG/PDF) <span class="text-error">*</span></div>
                                                    <v-file-input 
                                                        variant="underlined"
                                                        prepend-icon="mdi-camera"
                                                        @change="e => gradeForm.proof_file = e.target.files[0]"
                                                        hide-details
                                                    ></v-file-input>
                                                    <div v-if="registration.grade?.proof_file_path" class="mt-4">
                                                        <v-chip color="success" size="small" prepend-icon="mdi-check-circle">File sudah terunggah</v-chip>
                                                    </div>
                                                </div>
                                            </v-col>
                                        </v-row>
                                        <div class="d-flex justify-space-between mt-8">
                                            <v-btn variant="text" @click="step = 2" class="rounded-xl font-weight-bold">Kembali</v-btn>
                                            <v-btn color="primary" size="large" type="submit" :loading="gradeForm.processing" class="rounded-xl px-10 font-weight-black">
                                                Simpan & Lanjut
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-stepper-window-item>

                                <!-- Step 4: Dokumen -->
                                <v-stepper-window-item :value="4">
                                    <h3 class="text-h5 font-weight-black mb-2 color-main">Upload Berkas Pendukung</h3>
                                    <p class="text-body-2 text-grey mb-8">Pastikan hasil scan jelas dan dapat terbaca dengan baik.</p>
                                    
                                    <v-row>
                                        <v-col v-for="doc in docTypes" :key="doc.key" cols="12" sm="6" md="4">
                                            <v-card variant="outlined" class="pa-5 rounded-xl text-center h-100 d-flex flex-column justify-center border-dashed border-2 position-relative">
                                                <v-icon v-if="getDoc(doc.key)" color="success" class="position-absolute top-0 right-0 ma-2">mdi-check-circle</v-icon>
                                                <div class="text-subtitle-2 font-weight-black mb-4">{{ doc.label }}</div>
                                                <v-btn color="primary" variant="tonal" size="small" class="rounded-lg mb-2 overflow-hidden">
                                                    {{ getDoc(doc.key) ? 'Ganti File' : 'Pilih File' }}
                                                    <input type="file" class="position-absolute opacity-0" style="left:0; top:0; width:100%; height:100%; cursor:pointer" @change="e => { docForm.file = e.target.files[0]; uploadDoc(doc.key); }">
                                                </v-btn>
                                                <div class="text-tiny opacity-50" v-if="!getDoc(doc.key)">Format: JPG, PNG, PDF</div>
                                                <div class="text-tiny text-success font-weight-bold" v-else>Terunggah</div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                    <div class="d-flex justify-space-between mt-12">
                                        <v-btn variant="text" @click="step = 3" class="rounded-xl font-weight-bold">Kembali</v-btn>
                                        <v-btn color="primary" size="large" @click="step = 5" class="rounded-xl px-10 font-weight-black">
                                            Lanjut ke Finalisasi
                                        </v-btn>
                                    </div>
                                </v-stepper-window-item>

                                <!-- Step 5: Finalisasi -->
                                <v-stepper-window-item :value="5">
                                    <div class="text-center py-10">
                                        <v-icon size="100" color="warning" class="mb-6 animate-pulse">mdi-shield-check-outline</v-icon>
                                        <h3 class="text-h4 font-weight-black mb-4 color-main">Konfirmasi Akhir</h3>
                                        <p class="text-body-1 text-grey mb-10 max-width-600 mx-auto">
                                            Silakan periksa kembali seluruh data Anda. Setelah melakukan finalisasi, data akan dikunci dan dikirim ke sistem untuk proses verifikasi panitia.
                                        </p>
                                        
                                        <v-card variant="tonal" color="primary" class="pa-6 rounded-xl mx-auto mb-10" style="max-width: 500px">
                                            <v-table bg-color="transparent" class="text-left">
                                                <tbody>
                                                    <tr><td class="font-weight-black py-2">Nama</td><td>: {{ registration.student_detail.full_name }}</td></tr>
                                                    <tr><td class="font-weight-black py-2">NISN</td><td>: {{ registration.student_detail.nisn }}</td></tr>
                                                    <tr><td class="font-weight-black py-2">Asal Sekolah</td><td>: {{ registration.student_detail.origin_school_name }}</td></tr>
                                                </tbody>
                                            </v-table>
                                        </v-card>

                                        <div class="d-flex justify-center ga-4">
                                            <v-btn variant="outlined" color="primary" size="large" @click="step = 4" class="rounded-xl px-10 font-weight-black border-2">
                                                Cek Ulang Berkas
                                            </v-btn>
                                            <v-btn 
                                                color="success" 
                                                size="large" 
                                                class="rounded-xl px-10 font-weight-black elevation-12"
                                                :disabled="registration.status !== 'incomplete'"
                                                @click="finalize"
                                                :loading="finalizeForm.processing"
                                            >
                                                FINALISASI DATA
                                            </v-btn>
                                        </div>
                                    </div>
                                </v-stepper-window-item>
                            </v-stepper-window>
                        </v-stepper>
                    </v-col>

                    <!-- Sidebar Info -->
                    <v-col cols="12" lg="4">
                        <v-card class="rounded-xl pa-6 bg-primary text-white mb-6 elevation-4">
                            <h4 class="text-subtitle-1 font-weight-black mb-4">Butuh Bantuan?</h4>
                            <p class="text-body-2 opacity-80 mb-6">Jika Anda mengalami kendala saat mengisi formulir, silakan hubungi tim IT kami.</p>
                            <v-btn block color="secondary" class="rounded-lg font-weight-black py-4 h-auto" prepend-icon="mdi-whatsapp">
                                Hubungi WhatsApp
                            </v-btn>
                        </v-card>

                        <v-card class="rounded-xl pa-6 border elevation-0 bg-white">
                            <h4 class="text-subtitle-1 font-weight-black mb-4 color-main">Informasi Keamanan</h4>
                            <div class="d-flex ga-4 mb-4">
                                <v-icon icon="mdi-shield-lock" color="primary"></v-icon>
                                <span class="text-caption opacity-70">Data Anda dilindungi oleh enkripsi sistem terbaru.</span>
                            </div>
                            <div class="d-flex ga-4">
                                <v-icon icon="mdi-cloud-check" color="primary"></v-icon>
                                <span class="text-caption opacity-70">Sistem melakukan backup data secara otomatis setiap 24 jam.</span>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<style scoped>
.line-height-1 { line-height: 1.1; }
.opacity-70 { opacity: 0.7; }
.color-main { color: #0a2a12 !important; }

:deep(.v-stepper-header) {
    box-shadow: none !important;
    background: #ffffff;
}

:deep(.border-dashed) {
    border-style: dashed !important;
}

:deep(.border-2) {
    border-width: 2px !important;
}

.animate-pulse {
    animation: pulse-icon 2s infinite;
}

@keyframes pulse-icon {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

:deep(.v-stepper-item--active .v-stepper-item__avatar) {
    background: #1B5E20 !important;
    box-shadow: 0 0 15px rgba(27, 94, 32, 0.4);
}

:deep(.v-stepper-window) {
    transition: all 0.5s ease;
}

/* Modern Input Group Aesthetics */
:deep(.v-field--variant-outlined) {
    border-radius: 8px !important;
    transition: all 0.3s ease;
    background: #ffffff !important;
    padding-inline-start: 0 !important;
    overflow: hidden;
    --v-field-border-opacity: 1 !important;
}

:deep(.v-field--variant-outlined:not(.v-field--focused) .v-field__outline) {
    color: #e2e8f0 !important;
}

:deep(.v-field--variant-outlined:hover) {
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

:deep(.v-field--variant-outlined.v-field--focused) {
    box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
}

:deep(.v-field--variant-outlined .v-field__prepend-inner) {
    background-color: #f8fafc !important;
    border-right: 1px solid #e2e8f0 !important;
    padding: 0 16px !important;
    align-self: stretch !important;
    display: flex;
    align-items: center;
    margin-inline-end: 12px !important;
}

:deep(.v-field--variant-outlined .v-field__prepend-inner .v-icon) {
    opacity: 1 !important;
    color: #64748b !important;
}

:deep(.v-field--variant-outlined .v-field__input) {
    padding-top: 14px !important;
    padding-bottom: 14px !important;
    color: #334155 !important;
}

:deep(.v-field--variant-outlined .v-field__input::placeholder) {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

/* Smooth Buttons */
:deep(.v-btn) {
    text-transform: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

:deep(.v-btn[type="submit"]:hover), :deep(.v-btn.bg-success:hover) {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(27, 94, 32, 0.2) !important;
}

:deep(.v-stepper-window-item) {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
