<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const user = usePage().props.auth?.user || {};
const logout = () => router.post(route('logout'));

const props = defineProps({
    registration: Object,
    reportSemester: { type: String, default: 'Kelas 6 Semester 2' },
    subjectsRequired: { type: Array, default: () => [] },
    availableSubjects: { type: Array, default: () => [] },
    options: { type: Object, default: () => ({}) },
});

const activeTab = ref('dashboard');
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);

// ---- STATUS ----
const statusConfig = {
    incomplete: { label: 'Belum Lengkap',      bg: 'bg-amber-50',   text: 'text-amber-700',   border: 'border-amber-200',  dot: 'bg-amber-400'  },
    pending:    { label: 'Menunggu Verifikasi', bg: 'bg-blue-50',    text: 'text-blue-700',    border: 'border-blue-200',   dot: 'bg-blue-500'   },
    revision:   { label: 'Perlu Perbaikan',     bg: 'bg-orange-50',  text: 'text-orange-700',  border: 'border-orange-200', dot: 'bg-orange-500' },
    verified:   { label: 'Terverifikasi',       bg: 'bg-violet-50',  text: 'text-violet-700',  border: 'border-violet-200', dot: 'bg-violet-500' },
    passed:     { label: 'DITERIMA ✓',          bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200',dot: 'bg-emerald-500'},
    failed:     { label: 'Tidak Diterima',      bg: 'bg-red-50',     text: 'text-red-700',     border: 'border-red-200',   dot: 'bg-red-500'    },
};
const statusInfo = computed(() => statusConfig[props.registration?.status] || { label: 'Belum Diketahui', bg: 'bg-slate-50', text: 'text-slate-600', border: 'border-slate-200', dot: 'bg-slate-400' });
const isLocked = computed(() => !['incomplete', 'revision'].includes(props.registration?.status));

// ---- DATA SHORTCUTS ----
const sd    = props.registration?.student_detail || {};
const pd    = props.registration?.parent_detail  || {};
const s_add = sd.additional_data  || {};
const p_add = pd.additional_data  || {};
const r_add = props.registration?.additional_data || {};

// ---- REGION API ----
const regions = ref({ provinces: [], cities: [], districts: [], villages: [] });
const loadProvinces = async () => {
    try {
        const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
        regions.value.provinces = await res.json();
        if (form.student_details.province) {
            const p = regions.value.provinces.find(x => x.name === form.student_details.province);
            if (p) {
                const resC = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`);
                regions.value.cities = await resC.json();
                if (form.student_details.city) {
                    const c = regions.value.cities.find(x => x.name === form.student_details.city);
                    if (c) {
                        const resD = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${c.id}.json`);
                        regions.value.districts = await resD.json();
                        if (form.student_details.district) {
                            const d = regions.value.districts.find(x => x.name === form.student_details.district);
                            if (d) {
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
    if (p) { const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`); regions.value.cities = await res.json(); }
};
const onCityChange = async (val) => {
    form.student_details.district = ''; form.student_details.village = '';
    const c = regions.value.cities.find(x => x.name === val);
    if (c) { const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${c.id}.json`); regions.value.districts = await res.json(); }
};
const onDistChange = async (val) => {
    form.student_details.village = '';
    const d = regions.value.districts.find(x => x.name === val);
    if (d) { const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${d.id}.json`); regions.value.villages = await res.json(); }
};
onMounted(() => loadProvinces());

// ---- MAIN FORM ----
const form = useForm({
    registration_id: props.registration?.id,
    student_details: {
        full_name:            sd.full_name || '',
        gender:               sd.gender || 'L',
        nisn:                 sd.nisn || '',
        nik:                  sd.nik || '',
        place_of_birth:       sd.place_of_birth || '',
        date_of_birth:        sd.date_of_birth ? sd.date_of_birth.split('T')[0] : '',
        religion:             sd.religion || 'Islam',
        address:              sd.address || '',
        village:              sd.village || '',
        district:             sd.district || '',
        city:                 sd.city || '',
        province:             sd.province || '',
        postal_code:          sd.postal_code || '',
        phone:                sd.phone || '',
        email:                sd.email || '',
        origin_school_name:   sd.origin_school_name || '',
        origin_school_npsn:   sd.origin_school_npsn || '',
        origin_school_address: sd.origin_school_address || '',
        additional_data: {
            kk_number:             s_add.kk_number || '',
            akta_number:           s_add.akta_number || '',
            citizenship:           s_add.citizenship || 'WNI',
            country_name:          s_add.country_name || 'Indonesia',
            child_order:           s_add.child_order || '',
            siblings_count:        s_add.siblings_count || '',
            special_needs:         s_add.special_needs || 'Tidak Ada',
            rt:                    s_add.rt || '',
            rw:                    s_add.rw || '',
            residence_type:        s_add.residence_type || '',
            height:                s_add.height || '',
            weight:                s_add.weight || '',
            head_circumference:    s_add.head_circumference || '',
            distance_to_school:    s_add.distance_to_school || '',
            distance_km:           s_add.distance_km || '',
            travel_time:           s_add.travel_time || '',
            transportation:        s_add.transportation || '',
            extracurricular_interest: s_add.extracurricular_interest || '',
            hobby:                 s_add.hobby || '',
            ambition:              s_add.ambition || '',
            kks_number:            s_add.kks_number || '',
            pkh_receiver:          s_add.pkh_receiver || 'Tidak',
            pkh_number:            s_add.pkh_number || '',
            pip_eligible:          s_add.pip_eligible || 'Tidak',
            pip_reason:            s_add.pip_reason || '',
            kip_receiver:          s_add.kip_receiver || 'Tidak',
            kip_number:            s_add.kip_number || '',
            kip_name:              s_add.kip_name || '',
            kip_physical:          s_add.kip_physical || 'Tidak',
        },
    },
    registration: {
        additional_data: {
            major:               r_add.major || '',
            registration_type:   r_add.registration_type || 'BARU',
            school_type:         r_add.school_type || 'SD',
            school_status:       r_add.school_status || 'NEGERI',
            school_city:         r_add.school_city || '',
            information_source:  r_add.information_source || '',
        }
    },
    parent_details: {
        father_name:       pd.father_name || '',
        father_occupation: pd.father_occupation || '',
        mother_name:       pd.mother_name || '',
        mother_occupation: pd.mother_occupation || '',
        parent_phone:      pd.parent_phone || '',
        parent_address:    pd.parent_address || '',
        additional_data: {
            father_nik:             p_add.father_nik || '',
            father_birth_year:      p_add.father_birth_year || '',
            father_education:       p_add.father_education || '',
            father_income:          p_add.father_income || '',
            father_special_needs:   p_add.father_special_needs || 'TIDAK ADA',
            mother_nik:             p_add.mother_nik || '',
            mother_birth_year:      p_add.mother_birth_year || '',
            mother_education:       p_add.mother_education || '',
            mother_income:          p_add.mother_income || '',
            mother_special_needs:   p_add.mother_special_needs || 'TIDAK ADA',
            guardian_name:          p_add.guardian_name || '',
            guardian_birth_year:    p_add.guardian_birth_year || '',
            guardian_education:     p_add.guardian_education || '',
            guardian_occupation:    p_add.guardian_occupation || '',
            guardian_income:        p_add.guardian_income || '',
            mother_phone:           p_add.mother_phone || '',
        }
    }
});

// ---- GRADE FORM ----
const gradeFormData = { registration_id: props.registration?.id, proof_file: null };
const coreCols = ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'];
props.subjectsRequired.forEach(s => {
    if (coreCols.includes(s.key)) {
        gradeFormData[s.key] = props.registration?.grade?.[s.key] || '';
    } else {
        gradeFormData[s.key] = props.registration?.grade?.additional_data?.[s.key] || '';
    }
});
const gradeForm = useForm(gradeFormData);

// ---- PRESTASI ----
const prestasiList = ref(props.registration?.grade?.additional_data?.prestasiList || []);
const currentPrestasi = ref({ category: 'Non-Akademik', level: 'Tingkat Kabupaten / Kota', type: 'Perorangan / Individu', rank: 'Juara 1', name: '', organizer: '', year: new Date().getFullYear().toString(), certificate_number: '', file: null, score: 0 });

const achievementScores = computed(() => props.options?.achievement_scores || {});
const calculateScore = (level, rank) => {
    const levelData = achievementScores.value[level];
    if (levelData && levelData[rank]) return levelData[rank];
    let score = 0;
    if (level.includes('Kecamatan')) score += 5;
    else if (level.includes('Kabupaten')) score += 15;
    else if (level.includes('Provinsi')) score += 30;
    else if (level.includes('Nasional')) score += 50;
    else if (level.includes('Internasional')) score += 100;
    if (rank.includes('1')) score += 10; else if (rank.includes('2')) score += 7; else if (rank.includes('3')) score += 5; else score += 2;
    return score;
};
const updatePrestasiScore = () => {
    currentPrestasi.value.score = calculateScore(currentPrestasi.value.level, currentPrestasi.value.rank);
};
const addPrestasi = () => {
    if (!currentPrestasi.value.name || !currentPrestasi.value.organizer) {
        Swal.fire({ title: 'Data Tidak Lengkap', text: 'Nama Prestasi dan Penyelenggara wajib diisi!', icon: 'warning' });
        return;
    }
    prestasiList.value.push({ ...currentPrestasi.value });
    currentPrestasi.value = { category: 'Non-Akademik', level: 'Tingkat Kabupaten / Kota', type: 'Perorangan / Individu', rank: 'Juara 1', name: '', organizer: '', year: new Date().getFullYear().toString(), certificate_number: '', file: null, score: calculateScore('Tingkat Kabupaten / Kota', 'Juara 1') };
};
const removePrestasi = (idx) => prestasiList.value.splice(idx, 1);
const totalPrestasiScore = computed(() => prestasiList.value.reduce((s, p) => s + (Number(p.score) || 0), 0));

// ---- SAVE SECTION ----
const showSuccess = (msg) => Swal.fire({ title: 'Berhasil!', text: msg, icon: 'success', timer: 2000, showConfirmButton: false });

const saveSection = (sectionName, nextTab) => {
    form.post(route('register.saveSection'), {
        preserveScroll: true,
        onSuccess: () => { showSuccess(`Data ${sectionName} berhasil disimpan!`); if (nextTab) activeTab.value = nextTab; },
        onError: (errors) => Swal.fire({ title: 'Gagal Menyimpan!', text: Object.values(errors).join('\n') || 'Pastikan semua data wajib terisi.', icon: 'error', confirmButtonColor: '#d33' }),
    });
};

const saveGrades = (nextTab) => {
    const fd = new FormData();
    fd.append('registration_id', props.registration?.id);
    props.subjectsRequired.forEach(s => fd.append(s.key, gradeForm[s.key] || 0));
    fd.append('prestasiList', JSON.stringify(prestasiList.value));
    if (gradeForm.proof_file) fd.append('proof_file', gradeForm.proof_file);
    gradeForm.post(route('register.saveGrades'), {
        preserveScroll: true,
        onSuccess: () => { showSuccess('Nilai Rapor & Prestasi berhasil disimpan!'); if (nextTab) activeTab.value = nextTab; },
        onError: (errors) => Swal.fire({ title: 'Gagal!', text: Object.values(errors).join('\n'), icon: 'error' }),
    });
};

// ---- DOC UPLOAD ----
const docTypes = [
    { key: 'kk',      label: 'Kartu Keluarga (KK)',          info: 'Format JPG/PNG/PDF, max 5MB' },
    { key: 'akta',    label: 'Akta Kelahiran',                info: 'Format JPG/PNG/PDF, max 5MB' },
    { key: 'ijazah',  label: 'Ijazah / SKL SD/MI',           info: 'Format JPG/PNG/PDF, max 5MB' },
    { key: 'ktp_ayah',label: 'KTP Ayah',                     info: 'Format JPG/PNG/PDF, max 5MB' },
    { key: 'ktp_ibu', label: 'KTP Ibu',                      info: 'Format JPG/PNG/PDF, max 5MB' },
    { key: 'foto',    label: 'Pas Foto Terbaru (3x4)',        info: 'Format JPG/PNG, background merah/biru' },
    { key: 'kip',     label: 'Kartu KIP (jika ada)',         info: 'Opsional' },
    { key: 'prestasi',label: 'Sertifikat Prestasi (jika ada)', info: 'Opsional, format JPG/PNG/PDF' },
];
const docForm = useForm({ registration_id: props.registration?.id, type: '', file: null });
const getDoc = (type) => props.registration?.documents?.find(d => d.type === type);
const uploadDoc = (type) => {
    docForm.type = type;
    docForm.post(route('register.uploadDocument'), {
        preserveScroll: true,
        onSuccess: () => showSuccess('Dokumen berhasil diunggah!'),
        onError: (e) => Swal.fire({ title: 'Gagal Unggah!', text: Object.values(e).join('\n') || 'Pastikan file JPG/PNG/PDF dan maks 5MB.', icon: 'error' }),
    });
};
const deleteDoc = (docId) => {
    Swal.fire({ title: 'Hapus Dokumen?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Hapus', cancelButtonText: 'Batal' }).then(r => {
        if (r.isConfirmed) router.delete(route('register.deleteDocument', docId), { preserveScroll: true, onSuccess: () => showSuccess('Dokumen dihapus.') });
    });
};

// ---- FINALIZE ----
const finalizeForm = useForm({ registration_id: props.registration?.id });
const finalize = () => {
    if (completionPercentage.value < 100) {
        Swal.fire({
            title: 'Data Belum Lengkap!',
            text: 'Silakan lengkapi semua form (Identitas, Orang Tua, Nilai, Berkas) hingga progress mencapai 100% sebelum melakukan finalisasi.',
            icon: 'error',
            confirmButtonText: 'Kembali Melengkapi'
        });
        return;
    }
    Swal.fire({ title: 'Konfirmasi Finalisasi', text: 'Data akan dikunci dan dikirim ke panitia. Pastikan semua data sudah benar!', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Finalisasi', cancelButtonText: 'Periksa Kembali' })
        .then(r => { if (r.isConfirmed) finalizeForm.post(route('register.finalize'), { onSuccess: () => showSuccess('Pendaftaran berhasil difinalisasi!') }); });
};

// ---- COMPLETION ----
const completionPercentage = computed(() => {
    let total = 0, filled = 0;
    const chk = (v) => { total++; if (v !== null && v !== undefined && v !== '' && v !== 0 && v !== '0') filled++; };
    chk(form.student_details.full_name); chk(form.student_details.nisn); chk(form.student_details.nik);
    chk(form.student_details.place_of_birth); chk(form.student_details.date_of_birth);
    chk(form.student_details.province); chk(form.student_details.city); chk(form.student_details.address);
    chk(form.student_details.phone); chk(form.student_details.origin_school_name);
    chk(form.parent_details.father_name); chk(form.parent_details.mother_name); chk(form.parent_details.parent_phone);
    chk(form.student_details.additional_data.height); chk(form.student_details.additional_data.weight); chk(form.student_details.additional_data.transportation);
    props.subjectsRequired.forEach(s => chk(gradeForm[s.key]));
    total += 3; if (getDoc('kk')) filled++; if (getDoc('akta')) filled++; if (getDoc('foto')) filled++;
    return Math.round((filled / total) * 100);
});

// ---- OPTIONS (from admin) ----
const mapOpt = (arr) => arr.map(i => (typeof i === 'object' && i !== null ? (i.name || i.label || i.value || '') : i));
const optPendidikan    = computed(() => mapOpt(props.options?.pendidikan    || []));
const optPekerjaan     = computed(() => mapOpt(props.options?.pekerjaan     || []));
const optPenghasilan   = computed(() => mapOpt(props.options?.penghasilan   || []));
const optTempatTinggal = computed(() => mapOpt(props.options?.tempat_tinggal || []));
const optEkstra        = computed(() => mapOpt(props.options?.ekstrakurikuler || []));
const optPeminatan     = computed(() => mapOpt(props.options?.peminatan || []));
const optTransportasi  = computed(() => mapOpt(props.options?.moda_transportasi || []));
const optAlasanKip     = computed(() => mapOpt(props.options?.alasan_kip    || []));
const achievementLevels = computed(() => Object.keys(achievementScores.value));
const achievementRanks  = computed(() => {
    const lvl = currentPrestasi.value.level;
    return achievementScores.value[lvl] ? Object.keys(achievementScores.value[lvl]) : ['Juara 1', 'Juara 2', 'Juara 3'];
});
</script>

<template>
    <Head title="Portal Siswa - Dashboard" />

    <div class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800 flex overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden transition-opacity"></div>

        <!-- 🚀 SIDEBAR 🚀 -->
        <aside :class="[
            'fixed md:sticky top-0 inset-y-0 left-0 z-50 flex flex-col h-screen bg-white border-r border-slate-200 transition-all duration-300 ease-in-out',
            isSidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0',
            isSidebarCollapsed ? 'md:w-20' : 'w-72'
        ]">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100">
                <div class="flex items-center gap-3 overflow-hidden" v-if="!isSidebarCollapsed">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex shrink-0 items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div>
                        <div class="text-slate-800 font-bold text-sm tracking-tight">SPMB Digital</div>
                        <div class="text-slate-500 text-[10px] uppercase tracking-wider font-semibold">Portal Siswa</div>
                    </div>
                </div>
                <div class="w-full flex justify-center mt-2 mb-2" v-else>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex shrink-0 items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Status Pendaftaran (Hidden if collapsed) -->
            <div class="px-5 py-4 border-b border-slate-100" v-if="!isSidebarCollapsed">
                <div class="rounded-xl p-3 border bg-slate-50 border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full shrink-0" :class="statusInfo.dot"></span>
                        <span class="text-xs font-bold text-slate-700">{{ statusInfo.label }}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 mt-1 font-mono font-medium">{{ registration?.registration_number }}</div>
                </div>
            </div>

            <!-- Nav Menu -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                <button v-for="item in [
                    { tab: 'dashboard',   label: 'Dashboard',           icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                    { tab: 'data_pribadi',label: 'Identitas Diri',       icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
                    { tab: 'data_ortu',   label: 'Data Orang Tua',       icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
                    { tab: 'akademik',    label: 'Nilai & Prestasi',     icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
                    { tab: 'berkas',      label: 'Upload Berkas',        icon: 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13' },
                    { tab: 'finalisasi',  label: 'Finalisasi',           icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                ]" :key="item.tab"
                    @click="activeTab = item.tab; isSidebarOpen = false"
                    class="w-full flex items-center px-3 py-3 rounded-xl transition-all group"
                    :class="[
                        activeTab === item.tab ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                        isSidebarCollapsed ? 'justify-center' : 'gap-3'
                    ]"
                >
                    <svg class="w-5 h-5 shrink-0 transition-colors" :class="activeTab === item.tab ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                    </svg>
                    <span v-if="!isSidebarCollapsed" class="text-[13px] font-semibold">{{ item.label }}</span>
                </button>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-100">
                <button @click="logout" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-[13px] font-bold text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span v-if="!isSidebarCollapsed">Keluar</span>
                </button>
            </div>
        </aside>

        <!-- ═══════ MAIN CONTENT ═══════ -->
        <div class="flex-1 flex flex-col min-w-0 h-screen transition-all duration-300">
            <!-- Top Navbar -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 z-10 sticky top-0">
                <div class="flex items-center gap-4">
                    <!-- Desktop Toggle (Garis 3) -->
                    <button @click="isSidebarCollapsed = !isSidebarCollapsed" class="hidden md:flex p-2 -ml-2 rounded-xl hover:bg-slate-100 text-slate-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!-- Mobile Toggle -->
                    <button @click="isSidebarOpen = true" class="md:hidden p-2 -ml-2 rounded-xl hover:bg-slate-100 text-slate-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-bold text-slate-800 text-lg hidden sm:block">Portal Pendaftaran Siswa</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-800">{{ user.name || 'Siswa' }}</div>
                        <div class="text-xs text-slate-500">{{ user.email }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-white shadow-sm flex items-center justify-center text-blue-700 font-bold">
                        {{ (user.name || 'S').charAt(0).toUpperCase() }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                
                <!-- PROGRESS WIDGET / INFO -->
                <div class="max-w-5xl mx-auto mb-8 bg-blue-50/50 border border-blue-100 rounded-2xl p-4 flex gap-3 items-start">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-sm text-blue-800 leading-relaxed pt-1">
                        <span class="font-bold">Informasi Pendaftaran:</span> Silakan lengkapi seluruh tahapan pengisian formulir di bawah ini dengan data yang sebenarnya dan dapat dipertanggungjawabkan. Pastikan seluruh dokumen yang diunggah valid dan terbaca dengan jelas sebelum Anda melakukan proses Finalisasi.
                    </div>
                </div>

                <!-- ── TAB: DASHBOARD ── -->
            <div v-if="activeTab === 'dashboard'" class="p-4 md:p-8 md:py-10 max-w-5xl mx-auto space-y-6">

                <!-- Welcome Hero -->
                <div class="relative rounded-2xl overflow-hidden text-white p-8" style="background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 50%, #7c3aed 100%)">
                    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=none fill-rule=evenodd%3E%3Cg fill=%23fff fill-opacity=1%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
                    <div class="relative">
                        <p class="text-blue-200 text-sm font-medium mb-1">Selamat Datang 👋</p>
                        <h1 class="text-3xl font-black tracking-tight mb-2">{{ registration?.student_detail?.full_name || user?.name || 'Peserta PPDB' }}</h1>
                        <div class="flex flex-wrap gap-4 text-sm mt-4">
                            <div class="bg-white/10 rounded-xl px-4 py-2 backdrop-blur-sm">
                                <div class="text-blue-200 text-xs">No. Pendaftaran</div>
                                <div class="font-bold font-mono">{{ registration?.registration_number }}</div>
                            </div>
                            <div class="bg-white/10 rounded-xl px-4 py-2 backdrop-blur-sm">
                                <div class="text-blue-200 text-xs">Tahun Ajaran</div>
                                <div class="font-bold">{{ registration?.academic_year }}</div>
                            </div>
                            <div class="bg-white/10 rounded-xl px-4 py-2 backdrop-blur-sm">
                                <div class="text-blue-200 text-xs">Status</div>
                                <div class="font-bold">{{ statusInfo.label }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress & Steps -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800">Progres Pendaftaran</h3>
                        <span class="text-2xl font-black text-blue-600">{{ completionPercentage }}%</span>
                    </div>
                    <div class="h-3 bg-slate-100 rounded-full overflow-hidden mb-4">
                        <div class="h-full rounded-full transition-all duration-700" :class="completionPercentage === 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500'" :style="{ width: completionPercentage + '%' }"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div v-for="step in [
                            { tab: 'data_pribadi', label: 'Identitas', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
                            { tab: 'data_ortu',    label: 'Ortu',      icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
                            { tab: 'akademik',     label: 'Akademik',  icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
                            { tab: 'berkas',       label: 'Berkas',    icon: 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13' },
                            { tab: 'finalisasi',   label: 'Finalisasi',icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                        ]" :key="step.tab"
                            @click="activeTab = step.tab"
                            class="flex flex-col items-center gap-2 cursor-pointer p-3 rounded-xl hover:bg-blue-50 transition-all group">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm transition-colors" :class="activeTab === step.tab ? 'bg-blue-600 text-white' : 'bg-slate-50 border border-slate-200 text-slate-400 group-hover:text-blue-500'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon"/></svg>
                            </div>
                            <span class="text-[11px] font-bold tracking-wide uppercase transition-colors" :class="activeTab === step.tab ? 'text-blue-700' : 'text-slate-500 group-hover:text-blue-600'">{{ step.label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Rata-rata Nilai</div>
                            <div class="text-3xl font-black text-blue-700">{{ registration?.average_score || '-' }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Peminatan</div>
                            <div class="text-lg font-black text-slate-800">{{ registration?.additional_data?.major || '-' }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dokumen</div>
                            <div class="text-3xl font-black text-emerald-600">{{ registration?.documents?.length || 0 }}<span class="text-base font-normal text-slate-400"> file</span></div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-3">
                    <button @click="activeTab = 'data_pribadi'" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-md shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Lengkapi Data
                    </button>
                    <button @click="activeTab = 'finalisasi'" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-md shadow-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Finalisasi Pendaftaran
                    </button>
                </div>
            </div>

            <!-- ── HELPER: Field wrapper ── -->
            <!-- (defined inline below for clarity) -->

            <!-- ── TAB: IDENTITAS DIRI ── -->
            <div v-if="activeTab === 'data_pribadi'" class="p-4 md:p-8 md:py-10 max-w-4xl mx-auto">
                <form @submit.prevent="saveSection('Identitas Diri', 'data_ortu')">
                    <fieldset :disabled="isLocked">
                    <!-- Section header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-800">Identitas Diri</h2>
                        <p class="text-slate-500 text-sm">Isi data pribadi Anda dengan lengkap dan benar sesuai dokumen resmi.</p>
                    </div>

                    <!-- Card: Biodata Utama -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Biodata Utama</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="field-label">Nama Lengkap <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.full_name" @input="form.student_details.full_name = $event.target.value.replace(/[^a-zA-Z\s'.,]/g, '')" class="field-input" placeholder="Sesuai Akta Kelahiran">
                            </div>
                            <div>
                                <label class="field-label">NISN <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.nisn" @input="form.student_details.nisn = $event.target.value.replace(/\D/g, '')" maxlength="10" minlength="10" class="field-input" placeholder="10 digit NISN">
                            </div>
                            <div>
                                <label class="field-label">NIK <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.nik" @input="form.student_details.nik = $event.target.value.replace(/\D/g, '')" maxlength="16" minlength="16" class="field-input" placeholder="16 digit NIK sesuai KTP/KK">
                            </div>
                            <div>
                                <label class="field-label">No. Kartu Keluarga <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.additional_data.kk_number" @input="form.student_details.additional_data.kk_number = $event.target.value.replace(/\D/g, '')" maxlength="16" minlength="16" class="field-input" placeholder="16 digit No. KK">
                            </div>
                            <div>
                                <label class="field-label">No. Akta Kelahiran</label>
                                <input type="text" v-model="form.student_details.additional_data.akta_number" class="field-input" placeholder="Nomor Akta Kelahiran">
                            </div>
                            <div>
                                <label class="field-label">Jenis Kelamin <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.gender" class="field-input">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Agama <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.religion" class="field-input">
                                    <option v-for="r in ['Islam','Kristen Protestan','Katolik','Hindu','Buddha','Konghucu']" :key="r" :value="r">{{ r }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Tempat Lahir <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.place_of_birth" class="field-input" placeholder="Kota tempat lahir">
                            </div>
                            <div>
                                <label class="field-label">Tanggal Lahir <span class="req">*wajib</span></label>
                                <input type="date" v-model="form.student_details.date_of_birth" class="field-input">
                            </div>
                            <div>
                                <label class="field-label">Anak ke-</label>
                                <input type="number" v-model="form.student_details.additional_data.child_order" class="field-input" placeholder="Anak ke berapa" min="1">
                            </div>
                            <div>
                                <label class="field-label">Jumlah Saudara Kandung</label>
                                <input type="number" v-model="form.student_details.additional_data.siblings_count" class="field-input" placeholder="Jumlah saudara kandung" min="0">
                            </div>
                            <div>
                                <label class="field-label">No. HP / WhatsApp <span class="req">*wajib</span></label>
                                <input type="tel" v-model="form.student_details.phone" @input="form.student_details.phone = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="Nomor aktif yang bisa dihubungi">
                            </div>

                        </div>
                        <div class="mt-4 flex justify-end pt-4 border-t border-slate-100">
                            <button v-if="!isLocked" type="button" @click="saveSection('Identitas Diri')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Bagian Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card: Alamat -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-teal-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Alamat Tempat Tinggal</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Provinsi <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.province" @change="onProvChange($event.target.value)" class="field-input">
                                    <option value="">-- Pilih Provinsi --</option>
                                    <option v-for="p in regions.provinces" :key="p.id" :value="p.name">{{ p.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Kota / Kabupaten <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.city" @change="onCityChange($event.target.value)" class="field-input">
                                    <option value="">-- Pilih Kota/Kab --</option>
                                    <option v-for="c in regions.cities" :key="c.id" :value="c.name">{{ c.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Kecamatan <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.district" @change="onDistChange($event.target.value)" class="field-input">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <option v-for="d in regions.districts" :key="d.id" :value="d.name">{{ d.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Desa / Kelurahan <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.village" class="field-input">
                                    <option value="">-- Pilih Desa/Kel --</option>
                                    <option v-for="v in regions.villages" :key="v.id" :value="v.name">{{ v.name }}</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="field-label">Alamat Jalan <span class="req">*wajib</span></label>
                                <textarea v-model="form.student_details.address" class="field-input" rows="2" placeholder="Nama jalan, nomor rumah, dll"></textarea>
                            </div>
                            <div>
                                <label class="field-label">RT</label>
                                <input type="text" v-model="form.student_details.additional_data.rt" @input="form.student_details.additional_data.rt = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="RT">
                            </div>
                            <div>
                                <label class="field-label">RW</label>
                                <input type="text" v-model="form.student_details.additional_data.rw" @input="form.student_details.additional_data.rw = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="RW">
                            </div>
                            <div>
                                <label class="field-label">Kode Pos</label>
                                <input type="text" v-model="form.student_details.postal_code" @input="form.student_details.postal_code = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="5 digit kode pos">
                            </div>
                            <div>
                                <label class="field-label">Tempat Tinggal</label>
                                <select v-model="form.student_details.additional_data.residence_type" class="field-input">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="t in optTempatTinggal" :key="t" :value="t">{{ t }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end pt-4 border-t border-slate-100">
                            <button v-if="!isLocked" type="button" @click="saveSection('Identitas Diri')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Bagian Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card: Sekolah Asal -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-violet-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Sekolah Asal</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="field-label">Nama Sekolah Asal <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.origin_school_name" class="field-input" placeholder="Nama lengkap sekolah asal">
                            </div>
                            <div>
                                <label class="field-label">Jenis Sekolah</label>
                                <select v-model="form.registration.additional_data.school_type" class="field-input">
                                    <option value="SD">SD</option><option value="MI">MI</option><option value="SDLB">SDLB</option><option value="Paket A">Paket A</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Status Sekolah</label>
                                <select v-model="form.registration.additional_data.school_status" class="field-input">
                                    <option value="NEGERI">Negeri</option><option value="SWASTA">Swasta</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Kabupaten Sekolah</label>
                                <input type="text" v-model="form.registration.additional_data.school_city" class="field-input" placeholder="Kabupaten asal sekolah">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end pt-4 border-t border-slate-100">
                            <button v-if="!isLocked" type="button" @click="saveSection('Identitas Diri')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Bagian Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card: Data Fisik & Lainnya -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Data Fisik & Transportasi</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="field-label">Tinggi Badan (cm) <span class="req">*wajib</span></label>
                                <input type="number" v-model="form.student_details.additional_data.height" class="field-input" placeholder="Misal: 155">
                            </div>
                            <div>
                                <label class="field-label">Berat Badan (kg) <span class="req">*wajib</span></label>
                                <input type="number" v-model="form.student_details.additional_data.weight" class="field-input" placeholder="Misal: 45">
                            </div>

                            <div>
                                <label class="field-label">Jarak ke Sekolah <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.additional_data.distance_to_school" class="field-input">
                                    <option value="">-- Pilih Jarak --</option>
                                    <option v-for="j in ['Kurang dari 1 km','1 - 3 km','3 - 5 km','5 - 10 km','Lebih dari 10 km']" :key="j" :value="j">{{ j }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Jarak (km)</label>
                                <input type="number" v-model="form.student_details.additional_data.distance_km" class="field-input" placeholder="Jarak dalam KM">
                            </div>
                            <div>
                                <label class="field-label">Waktu Tempuh (menit)</label>
                                <input type="number" v-model="form.student_details.additional_data.travel_time" class="field-input" placeholder="Estimasi menit">
                            </div>
                            <div>
                                <label class="field-label">Moda Transportasi <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.additional_data.transportation" class="field-input">
                                    <option value="">-- Pilih Transportasi --</option>
                                    <option v-for="t in optTransportasi" :key="t" :value="t">{{ t }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Minat Ekstrakurikuler <span class="req">*wajib</span></label>
                                <select v-model="form.student_details.additional_data.extracurricular_interest" class="field-input">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="e in optEkstra" :key="e" :value="e">{{ e }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Hobi <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.additional_data.hobby" class="field-input" placeholder="Misal: Membaca, Olahraga">
                            </div>
                            <div>
                                <label class="field-label">Cita-cita <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.student_details.additional_data.ambition" class="field-input" placeholder="Misal: Dokter, Insinyur">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end pt-4 border-t border-slate-100">
                            <button v-if="!isLocked" type="button" @click="saveSection('Identitas Diri')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Bagian Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card: Peminatan -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-pink-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Peminatan & Jalur</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Peminatan / Jurusan <span class="text-emerald-500 font-normal normal-case ml-1 not-italic">(Opsional)</span></label>
                                <select v-model="form.registration.additional_data.major" class="field-input">
                                    <option value="">-- Pilih Peminatan --</option>
                                    <option v-for="p in optPeminatan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Jalur Pendaftaran</label>
                                <select v-model="form.registration.additional_data.registration_type" class="field-input">
                                    <option value="BARU">Peserta Didik Baru</option>
                                    <option value="PINDAH">Pindahan</option>
                                    <option value="NAIK">Naik Kelas</option>
                                    <option value="MUTASI">Mutasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Info dari</label>
                                <input type="text" v-model="form.registration.additional_data.information_source" class="field-input" placeholder="Tau dari mana tentang sekolah ini?">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end pt-4 border-t border-slate-100">
                            <button v-if="!isLocked" type="button" @click="saveSection('Identitas Diri')" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-medium text-sm transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Simpan Bagian Ini
                            </button>
                        </div>
                    </div>

                    <!-- Card: KIP/PIP -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Data Bantuan (KIP / PIP)</h3>
                        </div>
                        <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3 mb-5">
                            <p class="text-xs text-amber-700 flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong>Catatan:</strong> Bagian ini bersifat opsional. Silakan isi data berikut hanya jika calon peserta didik merupakan penerima program bantuan pendidikan bersubsidi terkait.</span>
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">No. KIP <span class="text-emerald-500 font-normal normal-case ml-1 not-italic">(Opsional)</span></label>
                                <input type="text" v-model="form.student_details.additional_data.kip_number" class="field-input" placeholder="Isi jika punya Kartu Indonesia Pintar">
                            </div>
                            <div>
                                <label class="field-label">No. PKH <span class="text-emerald-500 font-normal normal-case ml-1 not-italic">(Opsional)</span></label>
                                <input type="text" v-model="form.student_details.additional_data.pkh_number" class="field-input" placeholder="Isi jika punya Program Keluarga Harapan">
                            </div>
                            <div>
                                <label class="field-label">No. KKS <span class="text-emerald-500 font-normal normal-case ml-1 not-italic">(Opsional)</span></label>
                                <input type="text" v-model="form.student_details.additional_data.kks_number" class="field-input" placeholder="Isi jika punya Kartu Keluarga Sejahtera">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-400">Data tersimpan otomatis saat klik Simpan</p>
                        <button v-if="!isLocked" type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 font-semibold px-5 py-2.5 rounded-xl transition-all">
                            {{ form.processing ? 'Menyimpan...' : '💾 Simpan & Lanjut ➔' }}
                        </button>
                    </div>
                    </fieldset>
                </form>
            </div>

            <!-- ── TAB: DATA ORANG TUA ── -->
            <div v-if="activeTab === 'data_ortu'" class="p-4 md:p-8 md:py-10 max-w-4xl mx-auto">
                <form @submit.prevent="saveSection('Data Orang Tua', 'akademik')">
                    <fieldset :disabled="isLocked">
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-800">Data Orang Tua / Wali</h2>
                        <p class="text-slate-500 text-sm">Isi data orang tua kandung atau wali yang bertanggung jawab.</p>
                    </div>

                    <!-- Ayah -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center"><span class="text-blue-600 font-bold text-sm">♂</span></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Data Ayah Kandung</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="field-label">Nama Ayah <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.parent_details.father_name" @input="form.parent_details.father_name = $event.target.value.replace(/[^a-zA-Z\s'.,]/g, '')" class="field-input" placeholder="Nama lengkap ayah kandung">
                            </div>
                            <div>
                                <label class="field-label">NIK Ayah</label>
                                <input type="text" v-model="form.parent_details.additional_data.father_nik" @input="form.parent_details.additional_data.father_nik = $event.target.value.replace(/\D/g, '')" maxlength="16" minlength="16" class="field-input" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="field-label">Pendidikan Terakhir Ayah</label>
                                <select v-model="form.parent_details.additional_data.father_education" class="field-input">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <option v-for="p in optPendidikan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Pekerjaan Ayah</label>
                                <select v-model="form.parent_details.father_occupation" class="field-input">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <option v-for="p in optPekerjaan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Penghasilan Ayah / Bulan</label>
                                <select v-model="form.parent_details.additional_data.father_income" class="field-input">
                                    <option value="">-- Pilih Penghasilan --</option>
                                    <option v-for="p in optPenghasilan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-pink-100 rounded-lg flex items-center justify-center"><span class="text-pink-500 font-bold text-sm">♀</span></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Data Ibu Kandung</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="field-label">Nama Ibu <span class="req">*wajib</span></label>
                                <input type="text" v-model="form.parent_details.mother_name" @input="form.parent_details.mother_name = $event.target.value.replace(/[^a-zA-Z\s'.,]/g, '')" class="field-input" placeholder="Nama lengkap ibu kandung">
                            </div>
                            <div>
                                <label class="field-label">NIK Ibu</label>
                                <input type="text" v-model="form.parent_details.additional_data.mother_nik" @input="form.parent_details.additional_data.mother_nik = $event.target.value.replace(/\D/g, '')" maxlength="16" minlength="16" class="field-input" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="field-label">Pendidikan Terakhir Ibu</label>
                                <select v-model="form.parent_details.additional_data.mother_education" class="field-input">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <option v-for="p in optPendidikan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Pekerjaan Ibu</label>
                                <select v-model="form.parent_details.mother_occupation" class="field-input">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <option v-for="p in optPekerjaan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">No. HP Ibu</label>
                                <input type="tel" v-model="form.parent_details.additional_data.mother_phone" @input="form.parent_details.additional_data.mother_phone = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="Nomor WA aktif ibu">
                            </div>
                            <div>
                                <label class="field-label">No. HP Orang Tua (utama) <span class="req">*wajib</span></label>
                                <input type="tel" v-model="form.parent_details.parent_phone" @input="form.parent_details.parent_phone = $event.target.value.replace(/\D/g, '')" class="field-input" placeholder="Nomor yang aktif untuk dihubungi">
                            </div>
                            <div class="md:col-span-2">
                                <label class="field-label">Alamat Orang Tua (jika berbeda)</label>
                                <textarea v-model="form.parent_details.parent_address" class="field-input" rows="2" placeholder="Kosongkan jika sama dengan alamat siswa"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Wali -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Data Wali (jika ada)</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="field-label">Nama Wali</label>
                                <input type="text" v-model="form.parent_details.additional_data.guardian_name" class="field-input" placeholder="Kosongkan jika tidak ada wali">
                            </div>
                            <div>
                                <label class="field-label">Pendidikan Wali</label>
                                <select v-model="form.parent_details.additional_data.guardian_education" class="field-input">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="p in optPendidikan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Pekerjaan Wali</label>
                                <select v-model="form.parent_details.additional_data.guardian_occupation" class="field-input">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="p in optPekerjaan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Penghasilan Wali</label>
                                <select v-model="form.parent_details.additional_data.guardian_income" class="field-input">
                                    <option value="">-- Pilih --</option>
                                    <option v-for="p in optPenghasilan" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" @click="activeTab = 'data_pribadi'" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-xl transition-all">← Kembali</button>
                        <button v-if="!isLocked" type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 font-semibold px-5 py-2.5 rounded-xl transition-all">{{ form.processing ? 'Menyimpan...' : '💾 Simpan & Lanjut ➔' }}</button>
                    </div>
                    </fieldset>
                </form>
            </div>

            <!-- ── TAB: AKADEMIK & NILAI ── -->
            <div v-if="activeTab === 'akademik'" class="p-4 md:p-8 md:py-10 max-w-4xl mx-auto">
                <form @submit.prevent="saveGrades('berkas')">
                    <fieldset :disabled="isLocked">
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-800">Nilai Rapor & Prestasi</h2>
                        <p class="text-slate-500 text-sm">Masukkan nilai rapor {{ reportSemester }} sesuai dokumen asli.</p>
                    </div>

                    <!-- Nilai Rapor -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Nilai Rapor</h3>
                        </div>
                        <div class="mb-4 bg-blue-50 rounded-xl p-3 border border-blue-100">
                            <p class="text-xs text-blue-700 font-medium">📋 Semester yang digunakan: <strong>{{ reportSemester }}</strong></p>
                            <p class="text-xs text-blue-600 mt-1">Masukkan nilai sesuai rapor asli. Nilai harus dalam rentang 0–100. Rata-rata nilai akan dihitung otomatis oleh sistem.</p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="s in subjectsRequired" :key="s.key">
                                <label class="field-label">{{ s.label }}</label>
                                <div class="relative">
                                    <input type="number" v-model="gradeForm[s.key]" min="0" max="100" step="0.01" class="field-input pr-12" placeholder="0–100">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">/ 100</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bukti rapor -->
                        <div class="mt-5 pt-5 border-t border-slate-100">
                            <label class="field-label">Upload Bukti Rapor (PDF/JPG)</label>
                            <p class="text-xs text-slate-400 mb-2">Scan atau foto rapor halaman nilai. Max 5MB.</p>
                            <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="e => gradeForm.proof_file = e.target.files[0]" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-bold hover:file:bg-blue-100">
                        </div>
                    </div>

                    <!-- Prestasi -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></div>
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Prestasi (Opsional)</h3>
                            <span v-if="prestasiList.length > 0" class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ prestasiList.length }} prestasi • +{{ totalPrestasiScore }} poin</span>
                        </div>
                        <div class="mb-4 bg-amber-50 rounded-xl p-3 border border-amber-100">
                            <p class="text-xs text-amber-700 font-medium">🏆 Skor dihitung berdasarkan tingkat & peringkat sesuai kebijakan admin</p>
                            <p class="text-xs text-amber-600 mt-1">Tambahkan prestasi akademik maupun non-akademik. Prestasi akan menambah poin pada total skor seleksi Anda.</p>
                        </div>

                        <!-- Form Tambah Prestasi -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 mb-4">
                            <h4 class="text-xs font-bold text-slate-700 uppercase mb-3">Tambah Prestasi Baru</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="field-label-sm">Nama Prestasi</label>
                                    <input type="text" v-model="currentPrestasi.name" class="field-input-sm" placeholder="Misal: Juara Olimpiade Matematika">
                                </div>
                                <div>
                                    <label class="field-label-sm">Penyelenggara</label>
                                    <input type="text" v-model="currentPrestasi.organizer" class="field-input-sm" placeholder="Misal: Dinas Pendidikan Kab. X">
                                </div>
                                <div>
                                    <label class="field-label-sm">Tingkat</label>
                                    <select v-model="currentPrestasi.level" @change="updatePrestasiScore" class="field-input-sm">
                                        <option v-for="lvl in achievementLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label-sm">Peringkat / Juara</label>
                                    <select v-model="currentPrestasi.rank" @change="updatePrestasiScore" class="field-input-sm">
                                        <option v-for="r in achievementRanks" :key="r" :value="r">{{ r }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label-sm">Kategori</label>
                                    <select v-model="currentPrestasi.category" class="field-input-sm">
                                        <option value="Akademik">Akademik</option>
                                        <option value="Non-Akademik">Non-Akademik</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label-sm">Jenis</label>
                                    <select v-model="currentPrestasi.type" class="field-input-sm">
                                        <option value="Perorangan / Individu">Perorangan / Individu</option>
                                        <option value="Tim / Kelompok">Tim / Kelompok</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label-sm">Tahun</label>
                                    <input type="number" v-model="currentPrestasi.year" class="field-input-sm" placeholder="Tahun">
                                </div>
                                <div>
                                    <label class="field-label-sm">No. Sertifikat (opsional)</label>
                                    <input type="text" v-model="currentPrestasi.certificate_number" class="field-input-sm" placeholder="Nomor sertifikat">
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500">Estimasi skor:</span>
                                    <span class="bg-amber-100 text-amber-700 font-black text-sm px-3 py-1 rounded-lg">+{{ currentPrestasi.score }}</span>
                                </div>
                                <button type="button" @click="addPrestasi" class="flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah Prestasi
                                </button>
                            </div>
                        </div>

                        <!-- List Prestasi -->
                        <div v-if="prestasiList.length > 0" class="space-y-2">
                            <div v-for="(p, idx) in prestasiList" :key="idx" class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl border border-amber-100">
                                <div class="w-8 h-8 bg-amber-200 rounded-lg flex items-center justify-center shrink-0 font-black text-amber-800 text-xs">{{ idx + 1 }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-slate-800 truncate">{{ p.name }}</div>
                                    <div class="text-xs text-slate-500">{{ p.level }} • {{ p.rank }} • {{ p.year }}</div>
                                </div>
                                <span class="text-xs font-black text-amber-700 bg-amber-100 px-2 py-0.5 rounded-lg shrink-0">+{{ p.score }}</span>
                                <button type="button" @click="removePrestasi(idx)" class="text-red-400 hover:text-red-600 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                            Belum ada prestasi ditambahkan
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" @click="activeTab = 'data_ortu'" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-xl transition-all">← Kembali</button>
                        <button v-if="!isLocked" type="submit" :disabled="gradeForm.processing" class="bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 font-semibold px-5 py-2.5 rounded-xl transition-all">{{ gradeForm.processing ? 'Menyimpan...' : '💾 Simpan & Lanjut ➔' }}</button>
                    </div>
                    </fieldset>
                </form>
            </div>

            <!-- ── TAB: UPLOAD BERKAS ── -->
            <div v-if="activeTab === 'berkas'" class="p-4 md:p-8 md:py-10 max-w-4xl mx-auto">
                <fieldset :disabled="isLocked">
                <div class="mb-6">
                    <h2 class="text-2xl font-black text-slate-800">Unggah Berkas Persyaratan</h2>
                    <p class="text-slate-500 text-sm">Upload dokumen wajib dalam format JPG, PNG, atau PDF. Maksimum 5MB per file.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div v-for="dt in docTypes" :key="dt.key" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 transition-all" :class="getDoc(dt.key) ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-100'">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="font-bold text-sm text-slate-800">{{ dt.label }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ dt.info }}</div>
                            </div>
                            <span v-if="getDoc(dt.key)" class="shrink-0 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full ml-2">✓ Terunggah</span>
                            <span v-else class="shrink-0 bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded-full ml-2">Belum</span>
                        </div>
                        <div v-if="getDoc(dt.key)" class="mt-3 flex items-center gap-3">
                            <a :href="'/storage/' + getDoc(dt.key).file_path" target="_blank" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-xs font-bold transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat Berkas
                            </a>
                            <button v-if="!isLocked" type="button" @click="deleteDoc(getDoc(dt.key).id)" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus File
                            </button>
                        </div>
                        <form v-else @submit.prevent="uploadDoc(dt.key)" class="mt-3">
                            <div class="flex gap-2 items-center">
                                <input type="file" accept=".jpg,.jpeg,.png,.pdf" @change="e => docForm.file = e.target.files[0]" class="flex-1 text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-bold hover:file:bg-slate-200">
                                <button type="submit" :disabled="docForm.processing" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                </fieldset>
                <div class="flex items-center justify-between">
                    <button @click="activeTab = 'akademik'" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-5 py-2.5 rounded-xl transition-all">← Kembali</button>
                    <!-- Status banner -->
                    <div class="p-6 md:p-8 text-center border-b border-slate-100" :class="statusInfo.bg">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl" :class="statusInfo.bg">
                            <span v-if="registration?.status === 'passed'">🎉</span>
                            <span v-else-if="registration?.status === 'verified'">✅</span>
                            <span v-else-if="registration?.status === 'failed'">❌</span>
                            <span v-else-if="registration?.status === 'pending'">⏳</span>
                            <span v-else-if="registration?.status === 'revision'">✏️</span>
                            <span v-else>📋</span>
                        </div>
                        <h2 class="text-2xl font-black mb-1" :class="statusInfo.text">{{ statusInfo.label }}</h2>
                        <p class="text-slate-500 text-sm">No. Pendaftaran: <strong class="font-mono">{{ registration?.registration_number }}</strong></p>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Info -->
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-slate-50 rounded-xl p-3"><div class="text-xs text-slate-400 mb-1">Nama</div><div class="font-bold text-slate-800">{{ registration?.student_detail?.full_name || '-' }}</div></div>
                            <div class="bg-slate-50 rounded-xl p-3"><div class="text-xs text-slate-400 mb-1">NISN</div><div class="font-bold text-slate-800">{{ registration?.student_detail?.nisn || '-' }}</div></div>
                            <div class="bg-slate-50 rounded-xl p-3"><div class="text-xs text-slate-400 mb-1">Asal Sekolah</div><div class="font-bold text-slate-800 truncate">{{ registration?.student_detail?.origin_school_name || '-' }}</div></div>
                            <div class="bg-slate-50 rounded-xl p-3"><div class="text-xs text-slate-400 mb-1">Rata-rata Nilai</div><div class="font-bold text-blue-600 text-xl">{{ registration?.average_score || '-' }}</div></div>
                        </div>

                        <!-- Checklist -->
                        <div class="bg-slate-50 rounded-xl p-4">
                            <h4 class="font-bold text-slate-700 text-sm mb-3">Kelengkapan Berkas</h4>
                            <div class="space-y-2">
                                <div v-for="dt in docTypes.slice(0,6)" :key="dt.key" class="flex items-center gap-2">
                                    <span :class="getDoc(dt.key) ? 'text-emerald-500' : 'text-slate-300'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getDoc(dt.key) ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'"/></svg>
                                    </span>
                                    <span class="text-xs" :class="getDoc(dt.key) ? 'text-slate-700 font-medium' : 'text-slate-400'">{{ dt.label }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Finalize Button -->
                        <div v-if="registration?.status === 'incomplete'" class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                            <p class="text-xs text-amber-700 font-medium mb-3">⚠️ Setelah finalisasi, data tidak dapat diubah. Pastikan semua informasi sudah benar!</p>
                            <button @click="finalize" :disabled="finalizeForm.processing" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-emerald-200">
                                {{ finalizeForm.processing ? 'Memproses...' : '✅ Finalisasi Pendaftaran' }}
                            </button>
                        </div>
                        <div v-else class="bg-blue-50 rounded-xl p-4 border border-blue-200 text-sm text-blue-700 font-medium text-center">
                            Pendaftaran Anda sudah dikirim ke panitia. Pantau status di dashboard.
                        </div>
                    </div>
                </div>
            </div>

        </main>
        </div>
    </div>
</template>

<style scoped lang="postcss">
.field-label {
    @apply block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5;
}
.field-label-sm {
    @apply block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1;
}
.req {
    @apply text-red-400 text-[10px] font-normal normal-case ml-1 not-italic;
}
.field-input {
    @apply w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm text-slate-800 placeholder:text-slate-300;
}
.field-input-sm {
    @apply w-full px-3 py-2 rounded-lg border border-slate-200 bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-xs text-slate-800 placeholder:text-slate-300;
}
.btn-primary {
    @apply flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold py-2.5 px-6 rounded-xl shadow-md shadow-blue-200 transition-all text-sm disabled:opacity-50 disabled:cursor-not-allowed;
}
.btn-secondary {
    @apply flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-6 rounded-xl transition-all text-sm;
}
</style>
