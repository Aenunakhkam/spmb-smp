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


// --- PRESTASI STATE ---
const prestasiList = ref(props.registration.grade?.additional_data?.prestasiList || []);
const currentPrestasi = useForm({
    category: 'Non-Akademik',
    level: 'Tingkat Kabupaten / Kota',
    type: 'Perorangan / Individu',
    rank: 'Juara 1',
    name: '',
    organizer: '',
    year: new Date().getFullYear().toString(),
    certificate_number: '',
    file: null,
    score: 0
});

const calculateScore = (level, rank) => {
    let base = 0;
    if (level.includes('Nasional') || level.includes('Internasional')) base = 100;
    else if (level.includes('Provinsi')) base = 75;
    else if (level.includes('Kabupaten') || level.includes('Kota')) base = 50;
    else if (level.includes('Kecamatan')) base = 25;
    else base = 10;
    
    let mult = 1;
    if (rank.includes('1')) mult = 1;
    else if (rank.includes('2')) mult = 0.8;
    else if (rank.includes('3')) mult = 0.6;
    else mult = 0.4;
    
    return Math.round(base * mult);
};

const addPrestasi = () => {
    if (!currentPrestasi.name || !currentPrestasi.organizer) {
        alert('Harap lengkapi Nama Prestasi dan Instansi Penyelenggara terlebih dahulu!');
        return;
    }
    currentPrestasi.score = calculateScore(currentPrestasi.level, currentPrestasi.rank);
    
    prestasiList.value.push({
        ...currentPrestasi.data(),
        id: Date.now()
    });
    
    currentPrestasi.reset();
    currentPrestasi.category = 'Non-Akademik';
    currentPrestasi.level = 'Tingkat Kabupaten / Kota';
    currentPrestasi.type = 'Perorangan / Individu';
    currentPrestasi.rank = 'Juara 1';
    currentPrestasi.year = new Date().getFullYear().toString();
};

const removePrestasi = (index) => {
    prestasiList.value.splice(index, 1);
};


const saveGrades = () => {
    gradeForm.transform((data) => ({
        ...data,
        prestasiList: prestasiList.value
    })).post(route('register.saveGrades'), { 
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => step.value = 4
    });
};

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

const getStepTitle = (s) => {
    const titles = { 1: 'Biodata', 2: 'Orang Tua', 3: 'Nilai', 4: 'Berkas', 5: 'Selesai' };
    return titles[s] || '';
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans text-slate-800 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header -->
            <div class="mb-10 text-center">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pendaftaran Siswa Baru</h1>
                <p class="text-slate-500 mt-2">Isi data dengan lengkap dan benar.</p>
            </div>

            <!-- Modern Stepper -->
            <div class="mb-12">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full z-0"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-600 rounded-full z-0 transition-all duration-500" :style="'width: ' + ((step - 1) / 3 * 100) + '%'"></div>
                    
                    <div v-for="s in [1, 2, 3, 4]" :key="s" class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-sm transition-all duration-300" 
                             :class="step >= s ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white text-slate-400 border-2 border-slate-200'">
                            <span v-if="step > s">✓</span>
                            <span v-else>{{ s }}</span>
                        </div>
                        <div class="mt-3 text-xs font-semibold tracking-wide uppercase" :class="step >= s ? 'text-blue-700' : 'text-slate-400'">
                            {{ getStepTitle(s) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Cards -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                
                <!-- STEP 1: BIODATA -->
                <div v-if="step === 1" class="p-8 sm:p-10 transition-all">
                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h2 class="text-xl font-bold text-slate-800">1. Identitas Diri (Biodata)</h2>
                        <p class="text-sm text-slate-500 mt-1">Masukkan data diri Anda sesuai dengan dokumen resmi.</p>
                    </div>

                    <form @submit.prevent="saveBio">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- NIK -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.nik" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="16 Digit NIK">
                                <p v-if="bioForm.errors.nik" class="text-red-500 text-xs mt-1">{{ bioForm.errors.nik }}</p>
                            </div>
                            
                            <!-- No KK -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No. Kartu Keluarga <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.no_kk" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="16 Digit No. KK">
                                <p v-if="bioForm.errors.no_kk" class="text-red-500 text-xs mt-1">{{ bioForm.errors.no_kk }}</p>
                            </div>
                            
                            <!-- Nama Lengkap -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.full_name" @keypress="$event.key.length === 1 && !/^[a-zA-Z\s]*$/.test($event.key) && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Sesuai Ijazah">
                                <p v-if="bioForm.errors.full_name" class="text-red-500 text-xs mt-1">{{ bioForm.errors.full_name }}</p>
                            </div>

                            <!-- NISN -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NISN <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.nisn" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="10 Digit NISN">
                                <p v-if="bioForm.errors.nisn" class="text-red-500 text-xs mt-1">{{ bioForm.errors.nisn }}</p>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kelamin <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.gender" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <p v-if="bioForm.errors.gender" class="text-red-500 text-xs mt-1">{{ bioForm.errors.gender }}</p>
                            </div>

                            <!-- Tempat Lahir -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tempat Lahir <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.place_of_birth" @keypress="$event.key.length === 1 && !/^[a-zA-Z\s]*$/.test($event.key) && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Kota/Kabupaten Lahir">
                                <p v-if="bioForm.errors.place_of_birth" class="text-red-500 text-xs mt-1">{{ bioForm.errors.place_of_birth }}</p>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Lahir <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="date" v-model="bioForm.date_of_birth" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white">
                                <p v-if="bioForm.errors.date_of_birth" class="text-red-500 text-xs mt-1">{{ bioForm.errors.date_of_birth }}</p>
                            </div>

                            <!-- Agama -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Agama <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.religion" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="r in ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']" :key="r" :value="r">{{ r }}</option>
                                </select>
                                <p v-if="bioForm.errors.religion" class="text-red-500 text-xs mt-1">{{ bioForm.errors.religion }}</p>
                            </div>

                            <!-- Berkebutuhan Khusus -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Berkebutuhan Khusus</label>
                                <select v-model="bioForm.special_needs" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="r in ['TIDAK ADA', 'Tunanetra', 'Tunarungu', 'Tunagrahita', 'Tunadaksa', 'Lainnya']" :key="r" :value="r">{{ r }}</option>
                                </select>
                            </div>

                            <!-- Asal Sekolah -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Asal Sekolah <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.origin_school_name" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Nama Sekolah Dasar / MI Asal">
                                <p v-if="bioForm.errors.origin_school_name" class="text-red-500 text-xs mt-1">{{ bioForm.errors.origin_school_name }}</p>
                            </div>

                            <div class="md:col-span-2 border-t border-slate-100 my-4"></div>

                            <div class="md:col-span-2 mb-2">
                                <h3 class="text-lg font-bold text-slate-800">Alamat Lengkap</h3>
                            </div>

                            <!-- Nama Jalan -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Jalan <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <textarea v-model="bioForm.address" rows="2" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Jalan, Gang, Blok, No. Rumah"></textarea>
                                <p v-if="bioForm.errors.address" class="text-red-500 text-xs mt-1">{{ bioForm.errors.address }}</p>
                            </div>

                            <!-- RT & RW -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">RT</label>
                                    <input type="text" v-model="bioForm.rt" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="RT">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">RW</label>
                                    <input type="text" v-model="bioForm.rw" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="RW">
                                </div>
                            </div>
                            
                            <!-- Provinsi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Provinsi <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.province" @change="onProvChange" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="p in regions.provinces" :key="p.name" :value="p.name">{{ p.name }}</option>
                                </select>
                                <p v-if="bioForm.errors.province" class="text-red-500 text-xs mt-1">{{ bioForm.errors.province }}</p>
                            </div>

                            <!-- Kota/Kab -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kabupaten / Kota <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.city" @change="onCityChange" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="c in regions.cities" :key="c.name" :value="c.name">{{ c.name }}</option>
                                </select>
                                <p v-if="bioForm.errors.city" class="text-red-500 text-xs mt-1">{{ bioForm.errors.city }}</p>
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kecamatan <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.district" @change="onDistrictChange" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="d in regions.districts" :key="d.name" :value="d.name">{{ d.name }}</option>
                                </select>
                                <p v-if="bioForm.errors.district" class="text-red-500 text-xs mt-1">{{ bioForm.errors.district }}</p>
                            </div>

                            <!-- Desa/Kelurahan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Desa / Kelurahan <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <select v-model="bioForm.village" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="v in regions.villages" :key="v.name" :value="v.name">{{ v.name }}</option>
                                </select>
                                <p v-if="bioForm.errors.village" class="text-red-500 text-xs mt-1">{{ bioForm.errors.village }}</p>
                            </div>
                            
                            <!-- Kode Pos -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Pos <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.postal_code" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Kode Pos">
                                <p v-if="bioForm.errors.postal_code" class="text-red-500 text-xs mt-1">{{ bioForm.errors.postal_code }}</p>
                            </div>
                            
                            <!-- Tempat Tinggal & Transportasi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tempat Tinggal</label>
                                <select v-model="bioForm.residence" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="r in ['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan']" :key="r" :value="r">{{ r }}</option>
                                </select>
                            </div>
                            
                            <!-- No HP -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor HP / WA <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="bioForm.phone" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="08xxxxxxxxxx">
                                <p v-if="bioForm.errors.phone" class="text-red-500 text-xs mt-1">{{ bioForm.errors.phone }}</p>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <button type="submit" :disabled="bioForm.processing" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center">
                                <span v-if="bioForm.processing">Menyimpan...</span>
                                <span v-else>Simpan & Lanjutkan <span class="ml-2">→</span></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- STEP 2: ORANG TUA -->
                <div v-if="step === 2" class="p-8 sm:p-10 transition-all">
                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h2 class="text-xl font-bold text-slate-800">2. Data Orang Tua / Wali</h2>
                        <p class="text-sm text-slate-500 mt-1">Lengkapi informasi data orang tua Anda.</p>
                    </div>

                    <form @submit.prevent="saveParent">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <h3 class="font-bold text-slate-800 bg-blue-50 p-3 rounded-lg border border-blue-100 text-sm">DATA AYAH KANDUNG</h3>
                            </div>

                            <!-- NIK Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK Ayah <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="parentForm.father_nik" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="16 Digit NIK">
                            </div>
                            
                            <!-- Nama Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Ayah <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="parentForm.father_name" @keypress="$event.key.length === 1 && !/^[a-zA-Z\s]*$/.test($event.key) && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Nama Sesuai KTP">
                            </div>
                            
                            <!-- Tahun Lahir Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Lahir Ayah</label>
                                <input type="text" v-model="parentForm.father_birth_year" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="YYYY">
                            </div>

                            <!-- Pendidikan Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pendidikan Ayah</label>
                                <select v-model="parentForm.father_education" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="e in ['Tidak Sekolah', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'D1-D3', 'D4/S1', 'S2', 'S3']" :key="e" :value="e">{{ e }}</option>
                                </select>
                            </div>

                            <!-- Pekerjaan Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pekerjaan Ayah</label>
                                <input type="text" v-model="parentForm.father_occupation" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Pekerjaan">
                            </div>

                            <!-- Penghasilan Ayah -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penghasilan Ayah</label>
                                <select v-model="parentForm.father_income" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="i in ['Kurang dari Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 5.000.000', 'Lebih dari Rp 5.000.000']" :key="i" :value="i">{{ i }}</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 mt-4">
                                <h3 class="font-bold text-slate-800 bg-pink-50 p-3 rounded-lg border border-pink-100 text-sm">DATA IBU KANDUNG</h3>
                            </div>

                            <!-- NIK Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK Ibu <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="parentForm.mother_nik" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="16 Digit NIK">
                            </div>

                            <!-- Nama Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Ibu <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="parentForm.mother_name" @keypress="$event.key.length === 1 && !/^[a-zA-Z\s]*$/.test($event.key) && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Nama Sesuai KTP">
                            </div>
                            
                            <!-- Tahun Lahir Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Lahir Ibu</label>
                                <input type="text" v-model="parentForm.mother_birth_year" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="YYYY">
                            </div>

                            <!-- Pendidikan Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pendidikan Ibu</label>
                                <select v-model="parentForm.mother_education" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="e in ['Tidak Sekolah', 'SD Sederajat', 'SMP Sederajat', 'SMA Sederajat', 'D1-D3', 'D4/S1', 'S2', 'S3']" :key="e" :value="e">{{ e }}</option>
                                </select>
                            </div>

                            <!-- Pekerjaan Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pekerjaan Ibu</label>
                                <input type="text" v-model="parentForm.mother_occupation" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="Pekerjaan">
                            </div>

                            <!-- Penghasilan Ibu -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penghasilan Ibu</label>
                                <select v-model="parentForm.mother_income" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white appearance-none">
                                    <option v-for="i in ['Kurang dari Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 5.000.000', 'Lebih dari Rp 5.000.000']" :key="i" :value="i">{{ i }}</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 mt-4 border-t border-slate-100 pt-6"></div>

                            <!-- No HP Ortu -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor HP / WA Orang Tua <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="text" v-model="parentForm.parent_phone" @keypress="$event.key.length === 1 && ($event.key < '0' || $event.key > '9') && $event.preventDefault()" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="08xxxxxxxxxx">
                                <p v-if="parentForm.errors.parent_phone" class="text-red-500 text-xs mt-1">{{ parentForm.errors.parent_phone }}</p>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-between">
                            <button type="button" @click="step = 1" class="border-2 border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-3 px-8 rounded-xl transition-all">
                                <span class="mr-2">←</span> Kembali
                            </button>
                            <button type="submit" :disabled="parentForm.processing" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all">
                                <span v-if="parentForm.processing">Menyimpan...</span>
                                <span v-else>Simpan & Lanjutkan <span class="ml-2">→</span></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- STEP 3: NILAI & PRESTASI -->
                <div v-if="step === 3" class="p-8 sm:p-10 transition-all">
                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h2 class="text-xl font-bold text-slate-800">3. Nilai Rapor & Prestasi</h2>
                        <p class="text-sm text-slate-500 mt-1">Masukkan nilai rapor dan tambahkan prestasi jika ada.</p>
                    </div>

                    <form @submit.prevent="saveGrades">
                        
                        <!-- Nilai -->
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 bg-slate-50 p-3 rounded-lg border border-slate-100">Nilai Rapor Terakhir</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div v-for="s in subjectsRequired" :key="s.key">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ s.label }} <span class="text-red-500 text-[10px] lowercase font-normal italic ml-1">(*wajib diisi)</span></label>
                                <input type="number" v-model="gradeForm[s.key]" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-slate-50/50 hover:bg-white focus:bg-white" placeholder="0 - 100">
                                <p v-if="gradeForm.errors[s.key]" class="text-red-500 text-xs mt-1">{{ gradeForm.errors[s.key] }}</p>
                            </div>
                        </div>

                        <!-- Prestasi UI Rebuilt in Tailwind -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm mb-10">
                            <!-- Alert PANDUAN -->
                            <div class="bg-blue-50 p-4 border-b border-blue-100 flex items-start m-4 rounded-lg">
                                <div class="text-blue-500 mr-3 mt-1">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 mb-1">PANDUAN PENGISIAN :</div>
                                    <div class="text-xs text-slate-600 leading-tight">
                                        * Anda dapat memasukkan maksimal 3 prestasi terbaik.<br>
                                        * Sistem akan menghitung bobot nilai secara otomatis.
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-tight">TAMBAH DATA PRESTASI</h4>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="text-[11px] font-black uppercase tracking-wider text-slate-700 mb-2">KATEGORI & TINGKAT PRESTASI</div>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <!-- Kategori -->
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Kategori Prestasi <span class="text-red-500">*</span></label>
                                            <select v-model="currentPrestasi.category" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white">
                                                <option v-for="i in ['Akademik', 'Non-Akademik', 'Keagamaan']" :key="i" :value="i">{{ i }}</option>
                                            </select>
                                        </div>
                                        <!-- Tingkat -->
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Tingkat Kejuaraan <span class="text-red-500">*</span></label>
                                            <select v-model="currentPrestasi.level" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white">
                                                <option v-for="i in ['Tingkat Kecamatan', 'Tingkat Kabupaten / Kota', 'Tingkat Provinsi', 'Tingkat Nasional', 'Tingkat Internasional']" :key="i" :value="i">{{ i }}</option>
                                            </select>
                                        </div>
                                        <!-- Jenis -->
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Jenis Kejuaraan <span class="text-red-500">*</span></label>
                                            <div class="border rounded-lg px-3 py-2 flex items-center bg-white space-x-4">
                                                <label class="flex items-center text-xs"><input type="radio" v-model="currentPrestasi.type" value="Perorangan / Individu" class="mr-1 text-blue-600"> Perorangan</label>
                                                <label class="flex items-center text-xs"><input type="radio" v-model="currentPrestasi.type" value="Beregu / Kelompok" class="mr-1 text-blue-600"> Beregu</label>
                                            </div>
                                        </div>
                                        <!-- Juara -->
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Peringkat / Juara <span class="text-red-500">*</span></label>
                                            <select v-model="currentPrestasi.rank" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white">
                                                <option v-for="i in ['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Lainnya']" :key="i" :value="i">{{ i }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-[11px] font-black uppercase tracking-wider text-slate-700 mb-2 mt-4">INFORMASI DETAIL</div>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Nama Perlombaan / Kejuaraan <span class="text-red-500">*</span></label>
                                            <input type="text" v-model="currentPrestasi.name" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white" placeholder="Contoh: FLS2N Tari">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Instansi Penyelenggara <span class="text-red-500">*</span></label>
                                            <input type="text" v-model="currentPrestasi.organizer" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white" placeholder="Contoh: Dinas Pendidikan">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">Tahun Perolehan <span class="text-red-500">*</span></label>
                                            <select v-model="currentPrestasi.year" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white">
                                                <option v-for="i in ['2021', '2022', '2023', '2024', '2025']" :key="i" :value="i">{{ i }}</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700 mb-1">No Sertifikat (Optional)</label>
                                            <input type="text" v-model="currentPrestasi.certificate_number" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-xs bg-white" placeholder="(optional)">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex justify-end items-center">
                                    <div class="mr-4 text-sm font-bold text-slate-800">
                                        PREDIKSI SKOR: [ <span class="text-blue-600">{{ calculateScore(currentPrestasi.level, currentPrestasi.rank) }}</span> Poin ]
                                    </div>
                                    <button type="button" @click="addPrestasi" class="bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold py-2 px-6 rounded-lg transition-all text-sm border border-blue-200">
                                        + Tambah ke Daftar
                                    </button>
                                </div>
                            </div>

                            <!-- DAFTAR PRESTASI -->
                            <div class="m-4 border rounded-lg overflow-hidden bg-white shadow-sm">
                                <div class="bg-slate-100 p-3 border-b border-slate-200">
                                    <span class="font-bold text-xs text-slate-700 uppercase tracking-wider">DAFTAR PRESTASI TERSIMPAN</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 text-slate-600 border-b">
                                            <tr>
                                                <th class="p-3 font-bold">NO</th>
                                                <th class="p-3 font-bold">KATEGORI</th>
                                                <th class="p-3 font-bold">NAMA PRESTASI</th>
                                                <th class="p-3 font-bold">TINGKAT</th>
                                                <th class="p-3 font-bold">JUARA</th>
                                                <th class="p-3 font-bold">SKOR</th>
                                                <th class="p-3 font-bold">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="prestasiList.length === 0">
                                                <td colspan="7" class="p-6 text-center text-slate-400">Belum ada data prestasi.</td>
                                            </tr>
                                            <tr v-for="(p, idx) in prestasiList" :key="p.id || idx" class="border-b hover:bg-slate-50">
                                                <td class="p-3">{{ idx + 1 }}</td>
                                                <td class="p-3">{{ p.category }}</td>
                                                <td class="p-3 font-medium">{{ p.name }}</td>
                                                <td class="p-3">{{ p.level }}</td>
                                                <td class="p-3">{{ p.rank }}</td>
                                                <td class="p-3 font-bold text-blue-600">{{ p.score }}</td>
                                                <td class="p-3">
                                                    <button type="button" @click="removePrestasi(idx)" class="text-red-500 hover:bg-red-50 p-1 rounded transition-colors">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bg-slate-50 p-3 text-right border-t font-black text-xs text-slate-800">
                                    TOTAL TAMBAHAN POIN | {{ prestasiList.reduce((acc, curr) => acc + (curr.score || 0), 0) }} Poin
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-between">
                            <button type="button" @click="step = 2" class="border-2 border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-3 px-8 rounded-xl transition-all">
                                <span class="mr-2">←</span> Kembali
                            </button>
                            <button type="submit" :disabled="gradeForm.processing" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all">
                                <span v-if="gradeForm.processing">Menyimpan...</span>
                                <span v-else>Simpan & Lanjutkan <span class="ml-2">→</span></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- STEP 4: BERKAS -->
                <div v-if="step === 4" class="p-8 sm:p-10 transition-all">
                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h2 class="text-xl font-bold text-slate-800">4. Unggah Berkas Persyaratan</h2>
                        <p class="text-sm text-slate-500 mt-1">Unggah dokumen yang diminta dalam format gambar (JPG/PNG) atau PDF (Maks 2MB).</p>
                    </div>

                    <div class="space-y-6">
                        <div v-for="dt in docTypes" :key="dt.key" class="border border-slate-200 rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between bg-white hover:border-blue-200 transition-colors">
                            <div class="mb-4 md:mb-0">
                                <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wide">{{ dt.label }}</h4>
                                <p v-if="getDoc(dt.key)" class="text-xs text-green-600 font-bold mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Sudah diunggah
                                </p>
                                <p v-else class="text-xs text-red-500 mt-1">Belum diunggah</p>
                            </div>
                            <form @submit.prevent="uploadDoc(dt.key)" class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3">
                                <input type="file" @change="e => docForm.file = e.target.files[0]" accept="image/*,.pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 outline-none">
                                <button type="submit" :disabled="docForm.processing && docForm.type === dt.key" class="w-full md:w-auto bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2.5 px-6 rounded-lg transition-colors whitespace-nowrap">
                                    {{ docForm.processing && docForm.type === dt.key ? 'Mengunggah...' : 'Unggah File' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-between">
                        <button type="button" @click="step = 3" class="border-2 border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-3 px-8 rounded-xl transition-all">
                            <span class="mr-2">←</span> Kembali
                        </button>
                        <button type="button" @click="step = 5" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-blue-500/30 transition-all">
                            Lanjut ke Finalisasi <span class="ml-2">→</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 5: SELESAI -->
                <div v-if="step === 5" class="p-8 sm:p-10 transition-all text-center py-16">
                    
                    <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <h2 class="text-3xl font-extrabold text-slate-900 mb-4">Hampir Selesai!</h2>
                    <p class="text-slate-600 max-w-md mx-auto mb-8">Pastikan semua data dan berkas yang Anda masukkan sudah benar. Setelah difinalisasi, data tidak dapat diubah kembali.</p>

                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <button type="button" @click="step = 4" class="w-full sm:w-auto border-2 border-slate-200 text-slate-600 hover:bg-slate-50 font-bold py-3 px-8 rounded-xl transition-all">
                            Cek Ulang Data
                        </button>
                        <button type="button" @click="finalize" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-green-500/30 transition-all">
                            Finalisasi Pendaftaran
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>


<style scoped>
/* Modern Pill-Shaped Input Overrides */
:deep(.v-field--variant-outlined) {
    border-radius: 9999px !important;
    background-color: #f8fafc !important; 
}
:deep(.v-field__outline) {
    --v-field-border-opacity: 1 !important;
}
:deep(.v-field__outline__start) { 
    border-color: #e2e8f0 !important; 
    border-radius: 9999px 0 0 9999px !important; 
}
:deep(.v-field__outline__end) { 
    border-color: #e2e8f0 !important; 
    border-radius: 0 9999px 9999px 0 !important; 
}
:deep(.v-field__outline__notch) { 
    border-color: #e2e8f0 !important; 
}

:deep(.v-field--focused .v-field__outline__start),
:deep(.v-field--focused .v-field__outline__end),
:deep(.v-field--focused .v-field__outline__notch) { 
    border-color: #cbd5e1 !important; 
}

:deep(.v-field--focused) {
    box-shadow: 0 0 15px rgba(226, 232, 240, 0.5) !important;
    background-color: #ffffff !important;
}

:deep(.v-text-field input) {
    font-size: 0.875rem !important; 
    color: #475569 !important;
    padding-left: 0.5rem !important;
}

:deep(.v-text-field .v-icon) {
    color: #94a3b8 !important; 
}

:deep(.v-btn) {
    text-transform: none !important;
    letter-spacing: normal !important;
}
</style>

