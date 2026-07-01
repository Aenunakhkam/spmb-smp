<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    registration: Object,
    reportSemester: { type: String, default: 'Kelas 6 Semester 2' },
    subjectsRequired: { type: Array, default: () => ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'] },
    options: Object,
});

const activeTab = ref('ringkasan');
const snackbar = ref(false);
const snackbarMsg = ref('');

const showSuccess = (msg) => {
    snackbarMsg.value = msg;
    snackbar.value = true;
};

// ---- STATUS HELPERS ----
const statusMap = {
    'incomplete': { text: 'Data Belum Lengkap', color: 'warning', icon: 'mdi-alert-circle' },
    'pending': { text: 'Menunggu Verifikasi', color: 'info', icon: 'mdi-clock-outline' },
    'revision': { text: 'Perlu Perbaikan', color: 'orange', icon: 'mdi-pencil-circle' },
    'verified': { text: 'Terverifikasi', color: 'primary', icon: 'mdi-shield-check' },
    'passed': { text: 'DITERIMA (LULUS)', color: 'success', icon: 'mdi-star-circle' },
    'failed': { text: 'TIDAK DITERIMA', color: 'error', icon: 'mdi-close-circle' },
};
const statusInfo = computed(() => statusMap[props.registration.status] || { text: 'Status Tidak Diketahui', color: 'grey', icon: 'mdi-help-circle' });

const sd = props.registration.student_detail || {};
const pd = props.registration.parent_detail || {};
const s_add = sd.additional_data || {};
const p_add = pd.additional_data || {};
const r_add = props.registration.additional_data || {};

// ---- REGION API HELPERS ----
const regions = ref({ provinces: [], cities: [], districts: [], villages: [] });
const loadProvinces = async () => {
    try {
        const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
        regions.value.provinces = await res.json();
        if(form.student_details.province) {
            const p = regions.value.provinces.find(x => x.name === form.student_details.province);
            if(p) {
                const resC = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`);
                regions.value.cities = await resC.json();
                if(form.student_details.city) {
                    const c = regions.value.cities.find(x => x.name === form.student_details.city);
                    if(c) {
                        const resD = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${c.id}.json`);
                        regions.value.districts = await resD.json();
                        if(form.student_details.district) {
                            const d = regions.value.districts.find(x => x.name === form.student_details.district);
                            if(d) {
                                const resV = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${d.id}.json`);
                                regions.value.villages = await resV.json();
                            }
                        }
                    }
                }
            }
        }
    } catch (e) { console.error(e); }
};

const onProvChange = async (val) => {
    form.student_details.city = ''; form.student_details.district = ''; form.student_details.village = '';
    const p = regions.value.provinces.find(x => x.name === val);
    if(p) {
        const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`);
        regions.value.cities = await res.json();
    }
};

const onCityChange = async (val) => {
    form.student_details.district = ''; form.student_details.village = '';
    const c = regions.value.cities.find(x => x.name === val);
    if(c) {
        const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${c.id}.json`);
        regions.value.districts = await res.json();
    }
};

const onDistChange = async (val) => {
    form.student_details.village = '';
    const d = regions.value.districts.find(x => x.name === val);
    if(d) {
        const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${d.id}.json`);
        regions.value.villages = await res.json();
    }
};

onMounted(() => {
    loadProvinces();
});

// MAIN MEGA FORM
const form = useForm({
    registration_id: props.registration.id,
    
    student_details: {
        full_name: sd.full_name || '',
        gender: sd.gender || 'L',
        nisn: sd.nisn || '',
        nik: sd.nik || '',
        place_of_birth: sd.place_of_birth || '',
        date_of_birth: sd.date_of_birth ? sd.date_of_birth.split('T')[0] : '',
        religion: sd.religion || 'Islam',
        address: sd.address || '',
        village: sd.village || '',
        district: sd.district || '',
        city: sd.city || '',
        province: sd.province || '',
        postal_code: sd.postal_code || '',
        phone: sd.phone || '',
        email: sd.email || '',
        origin_school_name: sd.origin_school_name || '',
        origin_school_npsn: sd.origin_school_npsn || '',
        
        additional_data: {
            kk_number: s_add.kk_number || '',
            akta_number: s_add.akta_number || '',
            citizenship: s_add.citizenship || 'WNI',
            country_name: s_add.country_name || 'Indonesia',
            child_order: s_add.child_order || '',
            siblings_count: s_add.siblings_count || '',
            special_needs: s_add.special_needs || 'Tidak Ada',
            rt: s_add.rt || '',
            rw: s_add.rw || '',
            residence_type: s_add.residence_type || 'Bersama Orang Tua',
            
            // Fisik
            height: s_add.height || '',
            weight: s_add.weight || '',
            head_circumference: s_add.head_circumference || '',
            distance_to_school: s_add.distance_to_school || '',
            distance_km: s_add.distance_km || '',
            travel_time: s_add.travel_time || '',
            transportation: s_add.transportation || '',
            
            // Potensi
            extracurricular_interest: s_add.extracurricular_interest || '',
            hobby: s_add.hobby || '',
            ambition: s_add.ambition || '',
            
            // Kesejahteraan
            kks_number: s_add.kks_number || '',
            kps_number: s_add.kps_number || '',
            pkh_number: s_add.pkh_number || '',
            pkh_receiver: s_add.pkh_receiver || 'Tidak',
            pip_eligible: s_add.pip_eligible || 'Tidak',
            pip_reason: s_add.pip_reason || '',
            kip_receiver: s_add.kip_receiver || 'Tidak',
            kip_number: s_add.kip_number || '',
            kip_name: s_add.kip_name || '', // DKIP
            kip_physical: s_add.kip_physical || 'Tidak',
            
            // Prestasi
            achievement_type: s_add.achievement_type || '',
            achievement_level: s_add.achievement_level || '',
            achievement_name: s_add.achievement_name || '',
            achievement_year: s_add.achievement_year || '',
            achievement_organizer: s_add.achievement_organizer || '',
        },
    },
    
    registration: {
        additional_data: {
            major: r_add.major || '', // MINAT SISWA
            registration_type: r_add.registration_type || 'BARU',
            school_type: r_add.school_type || 'SD',
            school_status: r_add.school_status || 'NEGERI',
            school_city: r_add.school_city || '',
            information_source: r_add.information_source || '',
        }
    },
    
    parent_details: {
        father_name: pd.father_name || '',
        father_occupation: pd.father_occupation || '',
        mother_name: pd.mother_name || '',
        mother_occupation: pd.mother_occupation || '',
        parent_phone: pd.parent_phone || '',
        
        additional_data: {
            father_nik: p_add.father_nik || '',
            father_birth_year: p_add.father_birth_year || '',
            father_education: p_add.father_education || '',
            father_income: p_add.father_income || '',
            father_special_needs: p_add.father_special_needs || 'TIDAK ADA',
            
            mother_nik: p_add.mother_nik || '',
            mother_birth_year: p_add.mother_birth_year || '',
            mother_education: p_add.mother_education || '',
            mother_income: p_add.mother_income || '',
            mother_special_needs: p_add.mother_special_needs || 'TIDAK ADA',
            
            guardian_name: p_add.guardian_name || '',
            guardian_birth_year: p_add.guardian_birth_year || '',
            guardian_education: p_add.guardian_education || '',
            guardian_occupation: p_add.guardian_occupation || '',
            guardian_income: p_add.guardian_income || '',
        }
    }
});

const saveSection = (sectionName) => {
    if (sectionName === 'Data Tambahan') {
        const requiredFields = [
            { field: form.student_details.additional_data.height, name: 'Tinggi Badan' },
            { field: form.student_details.additional_data.weight, name: 'Berat Badan' },
            { field: form.student_details.additional_data.head_circumference, name: 'Lingkar Kepala' },
            { field: form.student_details.additional_data.distance_to_school, name: 'Jarak ke Sekolah' },
            { field: form.student_details.additional_data.distance_km, name: 'Jarak dalam KM' },
            { field: form.student_details.additional_data.transportation, name: 'Moda Transportasi' },
            { field: form.student_details.additional_data.extracurricular_interest, name: 'Minat Ekstrakurikuler' },
            { field: form.student_details.additional_data.hobby, name: 'Hobi' },
            { field: form.student_details.additional_data.ambition, name: 'Cita-cita' },
        ];
        
        let missing = [];
        requiredFields.forEach(item => {
            if (item.field === null || item.field === undefined || item.field === '') {
                missing.push(item.name);
            }
        });

        if (missing.length > 0) {
            Swal.fire({
                title: 'Data Belum Lengkap!',
                text: 'Harap isi bagian: ' + missing.join(', '),
                icon: 'warning',
                confirmButtonColor: '#f39c12',
            });
            return;
        }
    }

    form.post(route('register.saveSection'), {
        preserveScroll: true,
        onSuccess: () => showSuccess(`Data ${sectionName} berhasil disimpan!`),
        onError: (errors) => {
            const errorMessages = Object.values(errors).join('\n');
            Swal.fire({
                title: 'Gagal Menyimpan!',
                text: errorMessages || 'Pastikan semua data wajib terisi dengan benar.',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
        }
    });
};

// ---- GRADE FORM ----
const gradeFormData = { registration_id: props.registration.id, proof_file: null };
props.subjectsRequired.forEach(s => {
    const coreCols = ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'];
    if (coreCols.includes(s.key)) {
        gradeFormData[s.key] = props.registration.grade?.[s.key] || 0;
    } else {
        gradeFormData[s.key] = props.registration.grade?.additional_data?.[s.key] || 0;
    }
});
const gradeForm = useForm(gradeFormData);

const saveGrades = () => gradeForm.post(route('register.saveGrades'), {
    preserveScroll: true,
    onSuccess: () => showSuccess('Nilai rapor berhasil disimpan!'),
});

// ---- DOCUMENT FORM ----
const docTypes = [
    { key: 'kk', label: 'Kartu Keluarga', icon: 'mdi-home-city' },
    { key: 'akta', label: 'Akta Kelahiran', icon: 'mdi-baby' },
    { key: 'ijazah', label: 'Ijazah / SKL', icon: 'mdi-certificate' },
    { key: 'ktp_ayah', label: 'KTP Ayah', icon: 'mdi-card-account-details' },
    { key: 'ktp_ibu', label: 'KTP Ibu', icon: 'mdi-card-account-details-outline' },
    { key: 'foto', label: 'Pas Foto 3x4', icon: 'mdi-camera' },
];
const docForm = useForm({ registration_id: props.registration.id, type: '', file: null });
const getDoc = (type) => props.registration.documents?.find(d => d.type === type);
const uploadDoc = (type, file) => {
    docForm.type = type;
    docForm.file = file;
    docForm.post(route('register.uploadDocument'), {
        preserveScroll: true,
        onSuccess: () => showSuccess(`Dokumen ${type} berhasil diunggah!`),
        onError: (errors) => {
            const errorMessages = Object.values(errors).join('\n');
            Swal.fire({
                title: 'Gagal Mengunggah!',
                text: errorMessages || 'Pastikan file berformat JPG/PNG/PDF dan tidak lebih dari 5MB.',
                icon: 'error',
                confirmButtonColor: '#d33',
            });
        }
    });
};

const deleteDoc = (docId) => {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: 'Apakah Anda yakin ingin menghapus dokumen ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('register.deleteDocument', docId), {
                preserveScroll: true,
                onSuccess: () => showSuccess('Dokumen berhasil dihapus!'),
            });
        }
    });
};

// ---- FINALIZE ----
const finalizeForm = useForm({ registration_id: props.registration.id });
const finalize = () => {
    Swal.fire({
        title: 'Konfirmasi Finalisasi',
        text: 'Apakah Anda yakin ingin memfinalisasi? Setelah ini data akan dikunci dan dikirim ke panitia.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Finalisasi',
        cancelButtonText: 'Cek Kembali'
    }).then((result) => {
        if (result.isConfirmed) {
            finalizeForm.post(route('register.finalize'), {
                onSuccess: () => showSuccess('Pendaftaran berhasil difinalisasi!'),
            });
        }
    });
};
// ---- COMPLETION PERCENTAGE ----
const completionPercentage = computed(() => {
    let total = 0;
    let filled = 0;

    const check = (val) => {
        total++;
        if (val !== null && val !== undefined && val !== '' && val !== 0 && val !== '0') filled++;
    }

    // Data Pribadi
    check(form.student_details.full_name);
    check(form.student_details.nisn);
    check(form.student_details.nik);
    check(form.student_details.place_of_birth);
    check(form.student_details.date_of_birth);
    check(form.student_details.religion);
    check(form.student_details.address);
    check(form.student_details.province);
    check(form.student_details.city);
    check(form.student_details.district);
    check(form.student_details.village);

    // Keluarga
    check(form.parent_details.father_name);
    check(form.parent_details.mother_name);
    check(form.parent_details.parent_phone);

    // Akademik
    check(form.student_details.origin_school_name);
    check(form.registration.additional_data.school_type);
    check(form.registration.additional_data.school_status);
    check(form.registration.additional_data.registration_type);
    check(form.registration.additional_data.major);

    // Fisik & Tambahan
    check(form.student_details.additional_data.height);
    check(form.student_details.additional_data.weight);
    check(form.student_details.additional_data.head_circumference);
    check(form.student_details.additional_data.distance_to_school);
    check(form.student_details.additional_data.distance_km);
    check(form.student_details.additional_data.transportation);
    check(form.student_details.additional_data.extracurricular_interest);
    check(form.student_details.additional_data.hobby);
    check(form.student_details.additional_data.ambition);

    // Nilai Rapor
    props.subjectsRequired.forEach(s => check(gradeForm[s.key]));

    // Berkas Penting
    total += 6; 
    if (getDoc('kk')) filled++;
    if (getDoc('akta')) filled++;
    if (getDoc('ijazah')) filled++;
    if (getDoc('ktp_ayah')) filled++;
    if (getDoc('ktp_ibu')) filled++;
    if (getDoc('foto')) filled++;

    return Math.round((filled / total) * 100);
});

const missingRequiredFields = computed(() => {
    let missing = [];

    const check = (val, name) => {
        if (val === null || val === undefined || val === '' || val === 0 || val === '0') missing.push(name);
    }

    // Data Pribadi
    check(form.student_details.full_name, 'Nama Lengkap');
    check(form.student_details.nisn, 'NISN');
    check(form.student_details.nik, 'NIK / KITAS');
    check(form.student_details.place_of_birth, 'Tempat Lahir');
    check(form.student_details.date_of_birth, 'Tanggal Lahir');
    check(form.student_details.religion, 'Agama');
    check(form.student_details.address, 'Alamat Jalan');
    check(form.student_details.province, 'Provinsi');
    check(form.student_details.city, 'Kab/Kota');
    check(form.student_details.district, 'Kecamatan');
    check(form.student_details.village, 'Kelurahan/Desa');

    // Keluarga
    check(form.parent_details.father_name, 'Nama Ayah');
    check(form.parent_details.mother_name, 'Nama Ibu');
    check(form.parent_details.parent_phone, 'HP Ortu');

    // Akademik
    check(form.student_details.origin_school_name, 'Sekolah Asal');
    check(form.registration.additional_data.school_type, 'Jenis Sekolah Asal');
    check(form.registration.additional_data.school_status, 'Status Sekolah Asal');
    check(form.registration.additional_data.registration_type, 'Jenis Pendaftaran');
    check(form.registration.additional_data.major, 'Minat Siswa');

    // Fisik & Tambahan
    check(form.student_details.additional_data.height, 'Tinggi Badan');
    check(form.student_details.additional_data.weight, 'Berat Badan');
    check(form.student_details.additional_data.head_circumference, 'Lingkar Kepala');
    check(form.student_details.additional_data.distance_to_school, 'Jarak ke Sekolah');
    check(form.student_details.additional_data.distance_km, 'Jarak dalam KM');
    check(form.student_details.additional_data.transportation, 'Moda Transportasi');
    check(form.student_details.additional_data.extracurricular_interest, 'Minat Ekstrakurikuler');
    check(form.student_details.additional_data.hobby, 'Hobi');
    check(form.student_details.additional_data.ambition, 'Cita-cita');

    // Nilai Rapor
    props.subjectsRequired.forEach(s => check(gradeForm[s.key], 'Nilai: ' + s.label));

    // Berkas
    if (!getDoc('kk')) missing.push('Berkas: Kartu Keluarga');
    if (!getDoc('akta')) missing.push('Berkas: Akta Kelahiran');
    if (!getDoc('ijazah')) missing.push('Berkas: Ijazah / SKL');
    if (!getDoc('ktp_ayah')) missing.push('Berkas: KTP Ayah');
    if (!getDoc('ktp_ibu')) missing.push('Berkas: KTP Ibu');
    if (!getDoc('foto')) missing.push('Berkas: Pas Foto 3x4');

    return missing;
});

// ---- SUBJECT TRANSLATION ----
const formatSubject = (s) => {
    const translations = {
        'mathematics': 'Matematika',
        'indonesian': 'Bahasa Indonesia',
        'english': 'Bahasa Inggris',
        'religion': 'Pendidikan Agama',
        'ipa': 'Ilmu Pengetahuan Alam (IPA)',
        'ips': 'Ilmu Pengetahuan Sosial (IPS)',
        'pkn': 'Pendidikan Kewarganegaraan (PKN)'
    };
    return (translations[s.toLowerCase()] || s).toUpperCase();
};
</script>

<template>
    <Head title="Dashboard Siswa" />

    <v-app class="bg-grey-lighten-4" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
        <!-- Decorative Background Blob -->
        <div style="position: absolute; top: -150px; right: -100px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(27,94,32,0.05) 0%, rgba(27,94,32,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
        <!-- App Bar -->
        <v-app-bar class="glass-header px-md-4" elevation="0" height="75" app style="background: rgba(255, 255, 255, 0.85);">
            <v-container class="d-flex align-center pa-0" style="max-width: 1400px;">
                <v-avatar color="primary" size="42" class="mr-4 elevation-2">
                    <v-icon color="white" size="24">mdi-school</v-icon>
                </v-avatar>
                <div>
                    <div class="text-subtitle-1 font-weight-bold text-grey-darken-4" style="letter-spacing: -0.02em;">Portal Siswa</div>
                    <div class="text-caption text-primary font-weight-medium" style="letter-spacing: 0.05em;">SPMB DIGITAL</div>
                </div>
                <v-spacer></v-spacer>
                <div class="text-right hidden-sm-and-down mr-6">
                    <div class="text-caption text-grey-darken-1 mb-1">No. Pendaftaran: <strong class="text-grey-darken-4">{{ registration.registration_number }}</strong></div>
                    <div class="text-caption text-grey-darken-1">Akses Kode: <strong class="text-primary bg-primary-lighten-5 px-2 py-1 rounded">{{ registration.access_code }}</strong></div>
                </div>
                <v-btn variant="tonal" color="error" class="text-none font-weight-medium px-4 rounded-lg" size="small" prepend-icon="mdi-logout" @click="router.get(route('home'))">
                    Keluar
                </v-btn>
            </v-container>
        </v-app-bar>

        <v-main class="pb-16" style="position: relative; z-index: 1;">
            <v-container class="pt-8" style="max-width: 1400px;">
                <!-- Missing Required Fields Warning -->
                <v-alert
                    v-if="missingRequiredFields.length > 0 && !['verified', 'passed', 'failed'].includes(registration.status)"
                    type="warning"
                    variant="tonal"
                    class="mb-6 rounded-xl border border-warning"
                    icon="mdi-alert"
                >
                    <div class="font-weight-bold mb-3 text-warning-darken-2">Mohon lengkapi data wajib berikut (berbintang <span class="text-error">*</span>) agar pendaftaran dapat difinalisasi:</div>
                    <div class="d-flex flex-wrap ga-2">
                        <v-chip
                            v-for="(field, index) in missingRequiredFields"
                            :key="index"
                            color="warning-darken-2"
                            variant="outlined"
                            size="small"
                            class="font-weight-bold bg-white"
                        >
                            {{ field }}
                        </v-chip>
                    </div>
                </v-alert>

                <!-- Status Banner -->
                <v-card
                    class="rounded-xl mb-8 soft-shadow hover-lift overflow-hidden"
                    elevation="0"
                    style="border: 1px solid rgba(0,0,0,0.03);"
                >
                    <div class="d-flex" style="position: relative;">
                        <div :class="`bg-${statusInfo.color}`" style="width: 8px;"></div>
                        <div class="pa-5 w-100 d-flex justify-space-between align-center flex-wrap ga-4 bg-white">
                            <div class="d-flex align-center ga-4">
                                <v-avatar :color="`${statusInfo.color}-lighten-4`" size="50" class="rounded-lg">
                                    <v-icon :color="statusInfo.color" size="28">{{ statusInfo.icon }}</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-h6 font-weight-bold text-grey-darken-4" style="letter-spacing: -0.01em;">{{ statusInfo.text }}</div>
                                    <div class="text-body-2 text-grey-darken-1 mt-1" v-if="registration.admin_notes">
                                        <v-icon size="small" class="mr-1">mdi-message-alert</v-icon> Catatan: {{ registration.admin_notes }}
                                    </div>
                                    <div class="text-body-2 text-grey-darken-1 mt-1" v-else>
                                        ID Pendaftaran: {{ registration.registration_number }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <v-btn v-if="['passed', 'failed'].includes(registration.status)" size="large" :color="statusInfo.color" class="rounded-lg text-none font-weight-bold" elevation="0" @click="router.get(route('announcement', [registration.registration_number, registration.access_code]))" prepend-icon="mdi-file-certificate">
                                    Cek Hasil Seleksi
                                </v-btn>
                            </div>
                        </div>
                    </div>
                </v-card>

                <v-row>
                    <!-- Left: Sidebar Tabs -->
                    <v-col cols="12" md="3">
                        <v-card class="rounded-xl soft-shadow mb-6 border-0 overflow-hidden" style="position: sticky; top: 100px;">
                            <!-- Profile Header -->
                            <div class="pa-6 text-center bg-white border-b" style="border-color: rgba(0,0,0,0.05) !important;">
                                <div class="position-relative d-inline-block mb-3">
                                    <v-avatar size="80" color="primary-lighten-4" class="elevation-0" style="border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;">
                                        <v-icon size="40" color="primary">mdi-account</v-icon>
                                    </v-avatar>
                                    <v-icon v-if="completionPercentage === 100" color="success" size="24" class="position-absolute bg-white rounded-circle" style="bottom: 0; right: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">mdi-check-circle</v-icon>
                                </div>
                                <div class="text-h6 font-weight-bold text-grey-darken-4 text-truncate" style="line-height: 1.2;">{{ registration.student_detail.full_name || 'Calon Siswa' }}</div>
                                <div class="text-caption text-grey-darken-1 mt-1">{{ registration.student_detail.nisn || 'NISN Belum Diisi' }}</div>
                                
                                <v-card variant="flat" color="grey-lighten-5" class="mt-5 pa-4 rounded-lg text-left">
                                    <div class="d-flex justify-space-between text-caption font-weight-medium mb-2">
                                        <span class="text-grey-darken-2">Progres Data</span>
                                        <span :class="completionPercentage === 100 ? 'text-success' : 'text-primary'">{{ completionPercentage }}%</span>
                                    </div>
                                    <v-progress-linear :model-value="completionPercentage" :color="completionPercentage === 100 ? 'success' : 'primary'" height="8" rounded></v-progress-linear>
                                </v-card>
                            </div>
                            
                            <!-- Navigation Menu -->
                            <div class="pa-3 custom-scrollbar bg-white" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                                <div class="text-overline text-grey-darken-1 px-4 mb-2 mt-2">MENU PENDAFTARAN</div>
                                <v-tabs v-model="activeTab" direction="vertical" color="primary" class="v-tabs--vertical">
                                    <v-tab value="ringkasan" prepend-icon="mdi-view-dashboard-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none" style="letter-spacing: normal;">Ringkasan</v-tab>
                                    <v-tab value="data_pribadi" prepend-icon="mdi-account-details-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none" style="letter-spacing: normal;">Identitas Diri</v-tab>
                                    <v-tab value="keluarga" prepend-icon="mdi-home-heart" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none" style="letter-spacing: normal;">Data Orang Tua</v-tab>
                                    <v-tab value="akademik" prepend-icon="mdi-school-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none" style="letter-spacing: normal;">Akademik & Nilai</v-tab>
                                    <v-tab value="tambahan" prepend-icon="mdi-star-shooting-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none" style="letter-spacing: normal;">Fisik & Tambahan</v-tab>
                                    <v-divider class="my-2 mx-4"></v-divider>
                                    <v-tab value="berkas" prepend-icon="mdi-folder-upload-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none text-teal-darken-1" style="letter-spacing: normal;">Berkas Dokumen</v-tab>
                                    <v-tab value="finalisasi" prepend-icon="mdi-send-check-outline" class="rounded-lg mb-1 mx-2 text-left justify-start font-weight-medium text-none text-success" :disabled="registration.status !== 'incomplete'" style="letter-spacing: normal;">Finalisasi</v-tab>
                                </v-tabs>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Right: Content -->
                    <v-col cols="12" md="9">
                        <v-window v-model="activeTab" class="h-100">
                            <!-- Ringkasan -->
                            <v-window-item value="ringkasan">
                                <v-card class="rounded-xl soft-shadow pa-8 h-100 border-0">
                                    <div class="mb-8">
                                        <h2 class="text-h4 font-weight-bold text-grey-darken-4 mb-2" style="letter-spacing: -0.02em;">Selamat Datang! 👋</h2>
                                        <p class="text-body-1 text-grey-darken-1">Lengkapi data Anda secara bertahap untuk menyelesaikan proses pendaftaran.</p>
                                    </div>
                                    
                                    <v-card :color="completionPercentage === 100 ? 'success-lighten-5' : 'primary-lighten-5'" class="pa-6 mb-8 rounded-xl border-0 shadow-none">
                                        <div class="d-flex align-center flex-wrap ga-6">
                                            <v-progress-circular :model-value="completionPercentage" :color="completionPercentage === 100 ? 'success' : 'primary'" size="80" width="8">
                                                <span class="text-h6 font-weight-bold">{{ completionPercentage }}%</span>
                                            </v-progress-circular>
                                            <div class="flex-grow-1">
                                                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-1">Status Kelengkapan Berkas</h3>
                                                <p class="text-body-2 text-grey-darken-2 mb-0">
                                                    {{ completionPercentage === 100 ? 'Bagus sekali! Seluruh data Anda sudah terisi. Silakan lanjutkan ke menu Finalisasi untuk mengunci data.' : 'Masih ada data yang kosong. Silakan lengkapi formulir pendaftaran melalui menu di samping kiri secara berurutan.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </v-card>

                                    <div class="d-flex align-start mb-8 pa-4 rounded-lg bg-blue-grey-lighten-5 border">
                                        <v-icon color="info" size="28" class="mr-4 mt-1">mdi-information-outline</v-icon>
                                        <div>
                                            <div class="text-subtitle-1 font-weight-bold text-grey-darken-3 mb-1">Panduan Pengisian</div>
                                            <ul class="text-body-2 text-grey-darken-2 pl-4" style="line-height: 1.6;">
                                                <li>Isi formulir dari atas ke bawah sesuai urutan menu.</li>
                                                <li>Kolom bertanda bintang (<span class="text-error">*</span>) wajib diisi.</li>
                                                <li>Klik tombol <strong>Simpan</strong> di bagian bawah setiap halaman setelah mengisi data.</li>
                                                <li>Data dapat diubah kapan saja sebelum Anda menekan tombol Finalisasi.</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <v-divider class="mb-8"></v-divider>

                                    <div class="d-flex flex-wrap ga-4">
                                        <v-btn v-if="completionPercentage < 100" color="primary" size="x-large" class="rounded-lg px-8 text-none font-weight-bold shadow-none hover-lift" @click="activeTab = 'data_pribadi'">
                                            Lengkapi Data Sekarang <v-icon right class="ml-2">mdi-arrow-right</v-icon>
                                        </v-btn>
                                        
                                        <v-btn v-if="completionPercentage === 100" :href="`/cetak/kartu/${registration.id}`" target="_blank" color="success" size="x-large" prepend-icon="mdi-printer" variant="tonal" class="rounded-lg text-none font-weight-bold">
                                            Cetak Kartu Peserta
                                        </v-btn>
                                        
                                        <v-btn v-if="completionPercentage === 100" :href="`/cetak/formulir/${registration.id}`" target="_blank" color="info" size="x-large" prepend-icon="mdi-file-document-outline" variant="tonal" class="rounded-lg text-none font-weight-bold">
                                            Cetak Formulir
                                        </v-btn>
                                    </div>
                                </v-card>
                            </v-window-item>

                            <!-- Data Pribadi & Alamat -->
                            <v-window-item value="data_pribadi">
                                <v-form @submit.prevent="saveSection('Data Pribadi & Alamat')">
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Data Pribadi</h3>
                                        <v-row>
                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA LENGKAP <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.full_name" :error-messages="form.errors['student_details.full_name']" placeholder="NAMA LENGKAP" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JENIS KELAMIN <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.gender" :error-messages="form.errors['student_details.gender']" :items="[{title:'Laki-laki', value:'L'}, {title:'Perempuan', value:'P'}]" placeholder="JENIS KELAMIN" prepend-inner-icon="mdi-gender-male-female" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NISN <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.nisn" :error-messages="form.errors['student_details.nisn']" placeholder="NISN" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NO. KARTU KELUARGA <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.kk_number" @input="form.student_details.additional_data.kk_number = form.student_details.additional_data.kk_number.replace(/\D/g, '').substring(0, 16)" maxlength="16" :error-messages="form.errors['student_details.additional_data.kk_number']" placeholder="NO. KARTU KELUARGA" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NIK / KITAS <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.nik" @input="form.student_details.nik = form.student_details.nik.replace(/\D/g, '').substring(0, 16)" maxlength="16" :error-messages="form.errors['student_details.nik']" placeholder="NIK / KITAS" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TEMPAT LAHIR <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.place_of_birth" :error-messages="form.errors['student_details.place_of_birth']" placeholder="TEMPAT LAHIR" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TANGGAL LAHIR <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.date_of_birth" :error-messages="form.errors['student_details.date_of_birth']" type="date" placeholder="TANGGAL LAHIR" prepend-inner-icon="mdi-calendar-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NO. AKTE KELAHIRAN <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.akta_number" :error-messages="form.errors['student_details.additional_data.akta_number']" placeholder="NO. AKTE KELAHIRAN" prepend-inner-icon="mdi-calendar-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">AGAMA <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.religion" :error-messages="form.errors['student_details.religion']" :items="['Islam','Kristen','Katolik','Hindu','Budha','Konghucu']" placeholder="AGAMA" prepend-inner-icon="mdi-hands-pray" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KEWARGANEGARAAN <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.citizenship" :error-messages="form.errors['student_details.additional_data.citizenship']" placeholder="KEWARGANEGARAAN" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA NEGARA <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.country_name" :error-messages="form.errors['student_details.additional_data.country_name']" placeholder="NAMA NEGARA" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KEBUTUHAN KHUSUS <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.additional_data.special_needs" :items="options.kebutuhan_khusus" :error-messages="form.errors['student_details.additional_data.special_needs']" placeholder="PILIH KEBUTUHAN KHUSUS" prepend-inner-icon="mdi-wheelchair-accessibility" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">ANAK KE <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.child_order" :error-messages="form.errors['student_details.additional_data.child_order']" type="number" placeholder="ANAK KE" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JUMLAH SAUDARA <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.siblings_count" :error-messages="form.errors['student_details.additional_data.siblings_count']" type="number" placeholder="JUMLAH SAUDARA" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                        </v-row>
                                    </v-card>
                                    
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Data Alamat & Kontak</h3>
                                        <v-row>
                                            <v-col cols="12"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">ALAMAT JALAN <span class="text-error">*</span></div>
        <v-textarea v-model="form.student_details.address" :error-messages="form.errors['student_details.address']" rows="2" placeholder="ALAMAT JALAN" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-textarea>
    </div></v-col>

                                            <v-col cols="12" md="3"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">RT <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.rt" :error-messages="form.errors['student_details.additional_data.rt']" placeholder="RT" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="3"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">RW <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.rw" :error-messages="form.errors['student_details.additional_data.rw']" placeholder="RW" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PROVINSI <span class="text-error">*</span></div>
        <v-autocomplete v-model="form.student_details.province" :error-messages="form.errors['student_details.province']" :items="regions.provinces" @update:model-value="onProvChange" item-title="name" item-value="name" placeholder="PROVINSI" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-autocomplete>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KABUPATEN/KOTA <span class="text-error">*</span></div>
        <v-autocomplete v-model="form.student_details.city" :error-messages="form.errors['student_details.city']" :items="regions.cities" @update:model-value="onCityChange" item-title="name" item-value="name" placeholder="KABUPATEN/KOTA" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-autocomplete>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KECAMATAN <span class="text-error">*</span></div>
        <v-autocomplete v-model="form.student_details.district" :error-messages="form.errors['student_details.district']" :items="regions.districts" @update:model-value="onDistChange" item-title="name" item-value="name" placeholder="KECAMATAN" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-autocomplete>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KELURAHAN/DESA <span class="text-error">*</span></div>
        <v-autocomplete v-model="form.student_details.village" :error-messages="form.errors['student_details.village']" :items="regions.villages" item-title="name" item-value="name" placeholder="KELURAHAN/DESA" prepend-inner-icon="mdi-map-marker-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-autocomplete>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KODE POS <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.postal_code" :error-messages="form.errors['student_details.postal_code']" placeholder="KODE POS" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TEMPAT TINGGAL <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.additional_data.residence_type" :items="options.tempat_tinggal" :error-messages="form.errors['student_details.additional_data.residence_type']" placeholder="PILIH TEMPAT TINGGAL" prepend-inner-icon="mdi-home-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NO.HP <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.phone" :error-messages="form.errors['student_details.phone']" placeholder="NO.HP" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">EMAIL <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.email" :error-messages="form.errors['student_details.email']" placeholder="EMAIL" prepend-inner-icon="mdi-email-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                        </v-row>
                                        <div class="d-flex justify-end mt-4">
                                            <v-btn color="primary" type="submit" size="large" :loading="form.processing" class="rounded-lg">Simpan Data Pribadi</v-btn>
                                        </div>
                                    </v-card>
                                </v-form>
                            </v-window-item>

                            <!-- Keluarga & Bantuan -->
                            <v-window-item value="keluarga">
                                <v-form @submit.prevent="saveSection('Data Keluarga')">
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Data Orang Tua</h3>
                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <div class="text-subtitle-2 font-weight-bold mb-2">Data Ayah</div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA AYAH <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.father_name" :error-messages="form.errors['parent_details.father_name']" placeholder="NAMA AYAH" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NIK AYAH <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.additional_data.father_nik" @input="form.parent_details.additional_data.father_nik = form.parent_details.additional_data.father_nik.replace(/\D/g, '').substring(0, 16)" maxlength="16" :error-messages="form.errors['parent_details.additional_data.father_nik']" placeholder="NIK AYAH" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TAHUN LAHIR AYAH <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.additional_data.father_birth_year" :error-messages="form.errors['parent_details.additional_data.father_birth_year']" placeholder="TAHUN LAHIR AYAH" prepend-inner-icon="mdi-calendar-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENDIDIKAN AYAH <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.father_education" :items="options.pendidikan" :error-messages="form.errors['parent_details.additional_data.father_education']" placeholder="PILIH PENDIDIKAN" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PEKERJAAN AYAH <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.father_occupation" :items="options.pekerjaan" :error-messages="form.errors['parent_details.father_occupation']" placeholder="PILIH PEKERJAAN" prepend-inner-icon="mdi-briefcase-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENGHASILAN AYAH <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.father_income" :items="options.penghasilan" :error-messages="form.errors['parent_details.additional_data.father_income']" placeholder="PILIH PENGHASILAN" prepend-inner-icon="mdi-cash-multiple" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">BERKEBUTUHAN KHUSUS AYAH <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.father_special_needs" :items="options.kebutuhan_khusus" :error-messages="form.errors['parent_details.additional_data.father_special_needs']" placeholder="PILIH KEBUTUHAN KHUSUS" prepend-inner-icon="mdi-wheelchair-accessibility" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                            </v-col>

                                            <v-col cols="12" md="6">
                                                <div class="text-subtitle-2 font-weight-bold mb-2">Data Ibu</div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA IBU <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.mother_name" :error-messages="form.errors['parent_details.mother_name']" placeholder="NAMA IBU" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NIK IBU <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.additional_data.mother_nik" @input="form.parent_details.additional_data.mother_nik = form.parent_details.additional_data.mother_nik.replace(/\D/g, '').substring(0, 16)" maxlength="16" :error-messages="form.errors['parent_details.additional_data.mother_nik']" placeholder="NIK IBU" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TAHUN LAHIR IBU <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.additional_data.mother_birth_year" :error-messages="form.errors['parent_details.additional_data.mother_birth_year']" placeholder="TAHUN LAHIR IBU" prepend-inner-icon="mdi-calendar-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENDIDIKAN IBU <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.mother_education" :items="options.pendidikan" :error-messages="form.errors['parent_details.additional_data.mother_education']" placeholder="PILIH PENDIDIKAN" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PEKERJAAN IBU <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.mother_occupation" :items="options.pekerjaan" :error-messages="form.errors['parent_details.mother_occupation']" placeholder="PILIH PEKERJAAN" prepend-inner-icon="mdi-briefcase-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENGHASILAN IBU <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.mother_income" :items="options.penghasilan" :error-messages="form.errors['parent_details.additional_data.mother_income']" placeholder="PILIH PENGHASILAN" prepend-inner-icon="mdi-cash-multiple" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">BERKEBUTUHAN KHUSUS IBU <span class="text-error">*</span></div>
        <v-select v-model="form.parent_details.additional_data.mother_special_needs" :items="options.kebutuhan_khusus" :error-messages="form.errors['parent_details.additional_data.mother_special_needs']" placeholder="PILIH KEBUTUHAN KHUSUS" prepend-inner-icon="mdi-wheelchair-accessibility" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div>
                                            </v-col>

                                            <v-col cols="12" md="6">
                                                <div class="text-subtitle-2 font-weight-bold mb-2">Data Wali</div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA WALI</div>
        <v-text-field v-model="form.parent_details.additional_data.guardian_name" :error-messages="form.errors['parent_details.additional_data.guardian_name']" placeholder="NAMA WALI" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TAHUN LAHIR WALI</div>
        <v-text-field v-model="form.parent_details.additional_data.guardian_birth_year" :error-messages="form.errors['parent_details.additional_data.guardian_birth_year']" placeholder="TAHUN LAHIR WALI" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENDIDIKAN WALI</div>
        <v-text-field v-model="form.parent_details.additional_data.guardian_education" :error-messages="form.errors['parent_details.additional_data.guardian_education']" placeholder="PENDIDIKAN WALI" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PEKERJAAN WALI</div>
        <v-text-field v-model="form.parent_details.additional_data.guardian_occupation" :error-messages="form.errors['parent_details.additional_data.guardian_occupation']" placeholder="PEKERJAAN WALI" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENGHASILAN WALI</div>
        <v-text-field v-model="form.parent_details.additional_data.guardian_income" :error-messages="form.errors['parent_details.additional_data.guardian_income']" placeholder="PENGHASILAN WALI" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                            </v-col>

                                            <v-col cols="12" md="6">
                                                <div class="text-subtitle-2 font-weight-bold mb-2">Telepon Keluarga</div>
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NO.TELEPON RUMAH ATAU ORANG TUA <span class="text-error">*</span></div>
        <v-text-field v-model="form.parent_details.parent_phone" :error-messages="form.errors['parent_details.parent_phone']" placeholder="NO.TELEPON RUMAH ATAU ORANG TUA" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                            </v-col>

                                        </v-row>
                                    </v-card>
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Kesejahteraan & Bantuan Sosial</h3>
                                        <v-row>
                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NO.KKS</div>
        <v-text-field v-model="form.student_details.additional_data.kks_number" :error-messages="form.errors['student_details.additional_data.kks_number']" placeholder="NO.KKS" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENERIMA KKS/PKH</div>
        <v-select v-model="form.student_details.additional_data.pkh_receiver" :error-messages="form.errors['student_details.additional_data.pkh_receiver']" :items="['YA','TIDAK']" placeholder="PENERIMA KKS/PKH" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NOMOR KSS</div>
        <v-text-field v-model="form.student_details.additional_data.kps_number" :error-messages="form.errors['student_details.additional_data.kps_number']" placeholder="NOMOR KSS" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NOMOR PKH</div>
        <v-text-field v-model="form.student_details.additional_data.pkh_number" :error-messages="form.errors['student_details.additional_data.pkh_number']" placeholder="NOMOR PKH" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">USULAN LAYAK PIP</div>
        <v-select v-model="form.student_details.additional_data.pip_eligible" :error-messages="form.errors['student_details.additional_data.pip_eligible']" :items="['YA','TIDAK']" placeholder="USULAN LAYAK PIP" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENERIMA KIP</div>
        <v-select v-model="form.student_details.additional_data.kip_receiver" :error-messages="form.errors['student_details.additional_data.kip_receiver']" :items="['YA','TIDAK']" placeholder="PENERIMA KIP" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NOMOR KIP</div>
        <v-text-field v-model="form.student_details.additional_data.kip_number" :error-messages="form.errors['student_details.additional_data.kip_number']" placeholder="NOMOR KIP" prepend-inner-icon="mdi-card-account-details-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">DKIP (Nama di KIP)</div>
        <v-text-field v-model="form.student_details.additional_data.kip_name" :error-messages="form.errors['student_details.additional_data.kip_name']" placeholder="DKIP (Nama di KIP)" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TERIMA FISIK KIP</div>
        <v-select v-model="form.student_details.additional_data.kip_physical" :error-messages="form.errors['student_details.additional_data.kip_physical']" :items="['YA','TIDAK']" placeholder="TERIMA FISIK KIP" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">ALASAN LAYAK KIP / PIP</div>
        <v-select v-model="form.student_details.additional_data.pip_reason" :items="options.alasan_kip" :error-messages="form.errors['student_details.additional_data.pip_reason']" placeholder="PILIH ALASAN LAYAK KIP / PIP" prepend-inner-icon="mdi-format-list-bulleted" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                        </v-row>
                                        <div class="d-flex justify-end mt-4">
                                            <v-btn color="primary" type="submit" size="large" :loading="form.processing" class="rounded-lg">Simpan Data Keluarga</v-btn>
                                        </div>
                                    </v-card>
                                </v-form>
                            </v-window-item>

                            <!-- Akademik & Prestasi -->
                            <v-window-item value="akademik">
                                <v-form @submit.prevent="saveSection('Pendidikan & Prestasi')">
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Asal Sekolah & Pendaftaran</h3>
                                        <v-row>
                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">MINAT SISWA <span class="text-error">*</span></div>
        <v-select v-model="form.registration.additional_data.major" :error-messages="form.errors['registration.additional_data.major']" :items="['TAHFIDZ', 'MIPA', 'BAHASA', 'KUNING']" placeholder="MINAT SISWA" prepend-inner-icon="mdi-phone-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JENIS PENDAFTARAN <span class="text-error">*</span></div>
        <v-select v-model="form.registration.additional_data.registration_type" :error-messages="form.errors['registration.additional_data.registration_type']" :items="['BARU', 'PINDAHAN', 'MUTASI']" placeholder="JENIS PENDAFTARAN" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">ASAL SEKOLAH <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.origin_school_name" :error-messages="form.errors['student_details.origin_school_name']" placeholder="ASAL SEKOLAH" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JENIS SEKOLAH <span class="text-error">*</span></div>
        <v-select v-model="form.registration.additional_data.school_type" :error-messages="form.errors['registration.additional_data.school_type']" :items="['SD', 'MI']" placeholder="JENIS SEKOLAH" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">STATUS SEKOLAH <span class="text-error">*</span></div>
        <v-select v-model="form.registration.additional_data.school_status" :error-messages="form.errors['registration.additional_data.school_status']" :items="['NEGERI','SWASTA']" placeholder="STATUS SEKOLAH" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">KABUPATEN ASAL SEKOLAH <span class="text-error">*</span></div>
        <v-text-field v-model="form.registration.additional_data.school_city" :error-messages="form.errors['registration.additional_data.school_city']" placeholder="KABUPATEN ASAL SEKOLAH" prepend-inner-icon="mdi-school-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="12"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">SUMBER INFORMASI <span class="text-error">*</span></div>
        <v-text-field v-model="form.registration.additional_data.information_source" :error-messages="form.errors['registration.additional_data.information_source']" placeholder="SUMBER INFORMASI" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                        </v-row>
                                    </v-card>
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Data Prestasi</h3>
                                        <v-row>
                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JENIS PRESTASI</div>
        <v-text-field v-model="form.student_details.additional_data.achievement_type" :error-messages="form.errors['student_details.additional_data.achievement_type']" placeholder="JENIS PRESTASI" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">NAMA PRESTASI</div>
        <v-text-field v-model="form.student_details.additional_data.achievement_name" :error-messages="form.errors['student_details.additional_data.achievement_name']" placeholder="NAMA PRESTASI" prepend-inner-icon="mdi-account-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TAHUN PRESTASI</div>
        <v-text-field v-model="form.student_details.additional_data.achievement_year" :error-messages="form.errors['student_details.additional_data.achievement_year']" placeholder="TAHUN PRESTASI" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">PENYELENGGARA</div>
        <v-text-field v-model="form.student_details.additional_data.achievement_organizer" :error-messages="form.errors['student_details.additional_data.achievement_organizer']" placeholder="PENYELENGGARA" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                        </v-row>
                                        <div class="d-flex justify-end mt-4">
                                            <v-btn color="primary" type="submit" size="large" :loading="form.processing" class="rounded-lg">Simpan Akademik</v-btn>
                                        </div>
                                    </v-card>
                                </v-form>

                                <!-- Nilai Rapor is separate -->
                                <v-card class="rounded-xl elevation-2 pa-6 mb-4 border-t-lg border-indigo">
                                    <h3 class="text-h6 font-weight-bold mb-4 text-indigo">Nilai Rapor Terakhir</h3>
                                    <v-form @submit.prevent="saveGrades">
                                        <v-row>
                                            <v-col cols="12" md="4" v-for="s in subjectsRequired" :key="s.key">
                                                <div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">{{ s.label }} <span class="text-error">*</span></div>
        <v-text-field v-model="gradeForm[s.key]" type="number" :placeholder="s.label" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-file-input v-model="gradeForm.proof_file" label="Upload Foto / Scan Rapor Asli" accept="image/*,.pdf" variant="outlined" density="compact" prepend-icon="mdi-camera"></v-file-input>
                                                <div v-if="registration.grade?.proof_file_path" class="text-caption mt-1">
                                                    <v-icon color="success" size="small">mdi-check-circle</v-icon> File rapor sudah diunggah.
                                                </div>
                                            </v-col>

                                        </v-row>
                                        <div class="d-flex justify-end mt-4">
                                            <v-btn color="indigo" type="submit" size="large" :loading="gradeForm.processing" class="rounded-lg">Simpan Nilai Rapor</v-btn>
                                        </div>
                                    </v-form>
                                </v-card>
                            </v-window-item>

                            <!-- Tambahan -->
                            <v-window-item value="tambahan">
                                <v-form @submit.prevent="saveSection('Data Tambahan')">
                                    <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                        <h3 class="text-h6 font-weight-bold mb-4 text-primary">Fisik & Tambahan</h3>
                                        <v-row>
                                            <v-col cols="12" md="4"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">TINGGI BADAN <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.height" :error-messages="form.errors['student_details.additional_data.height']" type="number" placeholder="TINGGI BADAN" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="4"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">BERAT BADAN <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.weight" :error-messages="form.errors['student_details.additional_data.weight']" type="number" placeholder="BERAT BADAN" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="4"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">LINGKAR KEPALA <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.head_circumference" :error-messages="form.errors['student_details.additional_data.head_circumference']" type="number" placeholder="LINGKAR KEPALA" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">JARAK KE SEKOLAH <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.additional_data.distance_to_school" :items="['Kurang dari 1 km', 'Lebih dari 1 km']" :error-messages="form.errors['student_details.additional_data.distance_to_school']" placeholder="PILIH JARAK KE SEKOLAH" prepend-inner-icon="mdi-map-marker-distance" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">SEBUTKAN DALAM KM <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.distance_km" :error-messages="form.errors['student_details.additional_data.distance_km']" type="number" placeholder="SEBUTKAN DALAM KM" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">MODA TRANSPORTASI <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.additional_data.transportation" :items="options.moda_transportasi" :error-messages="form.errors['student_details.additional_data.transportation']" placeholder="PILIH TRANSPORTASI" prepend-inner-icon="mdi-car" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">MINAT EKSTRAKURIKULER <span class="text-error">*</span></div>
        <v-select v-model="form.student_details.additional_data.extracurricular_interest" :items="options.ekstrakurikuler" :error-messages="form.errors['student_details.additional_data.extracurricular_interest']" placeholder="PILIH EKSTRAKURIKULER" prepend-inner-icon="mdi-basketball" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-select>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">HOBI <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.hobby" :error-messages="form.errors['student_details.additional_data.hobby']" placeholder="HOBI" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                            <v-col cols="12" md="6"><div class="mb-2">
        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-3">CITA - CITA <span class="text-error">*</span></div>
        <v-text-field v-model="form.student_details.additional_data.ambition" :error-messages="form.errors['student_details.additional_data.ambition']" placeholder="CITA - CITA" prepend-inner-icon="mdi-pencil-outline" variant="outlined" bg-color="grey-lighten-5" rounded="lg" hide-details="auto"></v-text-field>
    </div></v-col>

                                        </v-row>
                                        <div class="d-flex justify-end mt-4">
                                            <v-btn color="primary" type="submit" size="large" :loading="form.processing" class="rounded-lg">Simpan Tambahan</v-btn>
                                        </div>
                                    </v-card>
                                </v-form>
                            </v-window-item>

                            <!-- Berkas -->
                            <v-window-item value="berkas">
                                <v-card class="rounded-xl elevation-2 pa-6 mb-4">
                                    <h3 class="text-h6 font-weight-bold mb-4">Upload Berkas Pendukung</h3>
                                    <v-row>
                                        <v-col v-for="doc in docTypes" :key="doc.key" cols="12" md="4">
                                            <!-- Berkas Belum Diunggah -->
                                            <v-card v-if="!getDoc(doc.key)" variant="outlined" class="pa-4 text-center border-dashed">
                                                <v-icon size="30" class="mb-2 text-teal">{{ doc.icon }}</v-icon>
                                                <div class="text-subtitle-2 font-weight-bold mb-2">{{ doc.label }}</div>
                                                <v-btn size="small" color="teal" variant="tonal" class="w-100 position-relative overflow-hidden font-weight-bold" :loading="docForm.processing && docForm.type === doc.key">
                                                    Upload File
                                                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" class="position-absolute" style="left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer" @change="e => e.target.files[0] && uploadDoc(doc.key, e.target.files[0])">
                                                </v-btn>
                                            </v-card>
                                            
                                            <!-- Berkas Sudah Diunggah -->
                                            <v-card v-else class="pa-4 text-center bg-teal-lighten-5 border border-teal-lighten-3">
                                                <v-icon size="30" class="mb-2 text-teal-darken-3">mdi-check-circle</v-icon>
                                                <div class="text-subtitle-2 font-weight-bold mb-2 text-teal-darken-4">{{ doc.label }}</div>
                                                <div class="d-flex ga-2">
                                                    <v-btn size="small" color="teal-darken-3" variant="tonal" class="flex-grow-1 font-weight-bold" :href="'/storage/' + getDoc(doc.key).file_path" target="_blank">
                                                        Lihat
                                                    </v-btn>
                                                    <v-btn size="small" color="error" variant="tonal" class="font-weight-bold" @click="deleteDoc(getDoc(doc.key).id)">
                                                        Hapus
                                                    </v-btn>
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-window-item>

                            <!-- Finalisasi -->
                            <v-window-item value="finalisasi">
                                <v-card class="rounded-xl elevation-2 pa-10 text-center mb-4">
                                    <v-icon size="80" color="warning" class="mb-4">mdi-shield-check-outline</v-icon>
                                    <h3 class="text-h5 font-weight-black mb-3">Konfirmasi Final Data</h3>
                                    <p class="text-body-2 text-grey mb-6 mx-auto" style="max-width:500px">
                                        Setelah finalisasi, data Anda akan dikunci dan dikirim ke panitia untuk proses verifikasi. Pastikan semua data, nilai, dan berkas sudah benar.
                                    </p>
                                    <v-btn color="success" size="x-large" @click="finalize" :loading="finalizeForm.processing" class="rounded-xl font-weight-black elevation-4 px-10">FINALISASI PENDAFTARAN</v-btn>
                                </v-card>
                            </v-window-item>
                        </v-window>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <v-snackbar v-model="snackbar" :timeout="3000" color="success" location="top right" rounded="lg">
            <v-icon start>mdi-check-circle</v-icon>
            {{ snackbarMsg }}
        </v-snackbar>
    </v-app>
</template>

<style scoped>
:deep(.v-tab) {
    text-transform: none;
    font-weight: 600;
    letter-spacing: 0.3px;
    padding-top: 8px;
    padding-bottom: 8px;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e0e0e0; 
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #bdbdbd; 
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
</style>
