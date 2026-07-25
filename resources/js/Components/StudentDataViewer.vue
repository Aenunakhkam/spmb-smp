<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    registration: Object
});

const activeTab = ref('data_pribadi');

const sd = props.registration?.student_detail || {};
const pd = props.registration?.parent_detail || {};
const s_add = sd.additional_data || {};
const p_add = pd.additional_data || {};
const r_add = props.registration?.additional_data || {};
const prestasiList = computed(() => props.registration?.grade?.additional_data?.prestasiList || []);
const totalPrestasiScore = computed(() => prestasiList.value.reduce((sum, item) => sum + (Number(item.score) || 0), 0));

const docMap = {
    'kk': 'Kartu Keluarga',
    'akta': 'Akta Kelahiran',
    'ijazah': 'Ijazah / SKL',
    'ktp_ayah': 'KTP Ayah',
    'ktp_ibu': 'KTP Ibu',
    'foto': 'Pas Foto',
    'kip': 'KIP',
    'pkh': 'PKH',
    'kks': 'KKS',
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'long', year: 'numeric'
    });
};

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

const allGrades = computed(() => {
    const grade = props.registration?.grade;
    if (!grade) return [];
    
    let list = [];
    const subjectsRequired = usePage().props.app_settings?.subjects_required || [];
    const allowedKeys = subjectsRequired.map(s => s.key);

    const coreCols = ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'];
    coreCols.forEach(col => {
        if (allowedKeys.includes(col) && grade[col] !== null && grade[col] !== undefined) {
            list.push({ key: col, val: grade[col] });
        }
    });

    if (grade.additional_data) {
        for (const [k, v] of Object.entries(grade.additional_data)) {
            if (k !== 'prestasiList' && allowedKeys.includes(k)) {
                list.push({ key: k, val: v });
            }
        }
    }
    return list;
});
</script>

<template>
    <div class="student-data-viewer">
        <v-tabs v-model="activeTab" bg-color="primary" class="rounded-t-lg">
            <v-tab value="data_pribadi"><v-icon start>mdi-account-details</v-icon> Data Pribadi</v-tab>
            <v-tab value="keluarga"><v-icon start>mdi-account-group</v-icon> Keluarga</v-tab>
            <v-tab value="akademik"><v-icon start>mdi-school</v-icon> Akademik</v-tab>
            <v-tab value="prestasi"><v-icon start>mdi-trophy</v-icon> Prestasi</v-tab>
            <v-tab value="tambahan"><v-icon start>mdi-star-shooting</v-icon> Tambahan</v-tab>
            <v-tab value="berkas"><v-icon start>mdi-folder-upload</v-icon> Berkas</v-tab>
        </v-tabs>

        <v-window v-model="activeTab" class="pa-4 bg-white border border-t-0 rounded-b-lg">
            
            <!-- Data Pribadi -->
            <v-window-item value="data_pribadi">
                <v-row dense>
                    <v-col cols="12"><div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Biodata Siswa</div></v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Nama Lengkap</v-col>
                    <v-col cols="12" md="8">: <strong class="text-black">{{ sd.full_name || '-' }}</strong></v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">NISN</v-col>
                    <v-col cols="12" md="8">: {{ sd.nisn || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">NIK</v-col>
                    <v-col cols="12" md="8">: {{ sd.nik || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. Kartu Keluarga</v-col>
                    <v-col cols="12" md="8">: {{ s_add.kk_number || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. Akte Kelahiran</v-col>
                    <v-col cols="12" md="8">: {{ s_add.akta_number || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Jenis Kelamin</v-col>
                    <v-col cols="12" md="8">: {{ sd.gender === 'L' ? 'Laki-laki' : (sd.gender === 'P' ? 'Perempuan' : '-') }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Tempat, Tgl Lahir</v-col>
                    <v-col cols="12" md="8">: {{ sd.place_of_birth || '-' }}, {{ formatDate(sd.date_of_birth) }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Agama</v-col>
                    <v-col cols="12" md="8">: {{ sd.religion || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Kewarganegaraan</v-col>
                    <v-col cols="12" md="8">: {{ s_add.citizenship || '-' }} ({{ s_add.country_name || '-' }})</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Anak Ke / Jml Sdr</v-col>
                    <v-col cols="12" md="8">: Anak ke-{{ s_add.child_order || '-' }} dari {{ s_add.siblings_count || '-' }} bersaudara</v-col>

                    <v-col cols="12" class="mt-4"><div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Alamat & Kontak</div></v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Alamat Jalan</v-col>
                    <v-col cols="12" md="8">: {{ sd.address || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">RT / RW</v-col>
                    <v-col cols="12" md="8">: {{ s_add.rt || '-' }} / {{ s_add.rw || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Kelurahan/Desa</v-col>
                    <v-col cols="12" md="8">: {{ sd.village || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Kecamatan</v-col>
                    <v-col cols="12" md="8">: {{ sd.district || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Kabupaten/Kota</v-col>
                    <v-col cols="12" md="8">: {{ sd.city || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Provinsi</v-col>
                    <v-col cols="12" md="8">: {{ sd.province || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Kode Pos</v-col>
                    <v-col cols="12" md="8">: {{ sd.postal_code || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Tempat Tinggal</v-col>
                    <v-col cols="12" md="8">: {{ s_add.residence_type || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. HP / Telepon</v-col>
                    <v-col cols="12" md="8">: {{ sd.phone || '-' }}</v-col>
                    <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Email</v-col>
                    <v-col cols="12" md="8">: {{ sd.email || '-' }}</v-col>
                </v-row>
            </v-window-item>

            <!-- Keluarga -->
            <v-window-item value="keluarga">
                <v-row dense>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Data Ayah</div>
                        <v-row dense>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Nama Ayah</v-col><v-col cols="7">: {{ pd.father_name || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">NIK Ayah</v-col><v-col cols="7">: {{ p_add.father_nik || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pendidikan</v-col><v-col cols="7">: {{ p_add.father_education || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pekerjaan</v-col><v-col cols="7">: {{ pd.father_occupation || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Data Ibu</div>
                        <v-row dense>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Nama Ibu</v-col><v-col cols="7">: {{ pd.mother_name || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">NIK Ibu</v-col><v-col cols="7">: {{ p_add.mother_nik || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pendidikan</v-col><v-col cols="7">: {{ p_add.mother_education || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pekerjaan</v-col><v-col cols="7">: {{ pd.mother_occupation || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6" class="mt-4">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Data Wali (Jika Ada)</div>
                        <v-row dense>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Nama Wali</v-col><v-col cols="7">: {{ p_add.guardian_name || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pendidikan</v-col><v-col cols="7">: {{ p_add.guardian_education || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Pekerjaan</v-col><v-col cols="7">: {{ p_add.guardian_occupation || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6" class="mt-4">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Kontak Keluarga</div>
                        <v-row dense>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">No. HP Ayah</v-col><v-col cols="7">: {{ pd.parent_phone || '-' }}</v-col>
<v-col cols="5" class="text-grey-darken-1 font-weight-medium">No. HP Ibu</v-col><v-col cols="7">: {{ pd.additional_data?.mother_phone || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    
                    <v-col cols="12" class="mt-4">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Kesejahteraan & Bantuan Sosial</div>
                        <v-row dense>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. KKS</v-col>
                            <v-col cols="12" md="8">: {{ s_add.kks_number || '-' }}</v-col>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. KIP</v-col>
                            <v-col cols="12" md="8">: {{ s_add.kip_number || '-' }}</v-col>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">No. PKH</v-col>
                            <v-col cols="12" md="8">: {{ s_add.pkh_number || '-' }}</v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Akademik -->
            <v-window-item value="akademik">
                <v-row dense>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Asal Sekolah & Pendaftaran</div>
                        <v-row dense>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Asal Sekolah</v-col><v-col cols="7">: {{ sd.origin_school_name || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Jenis Sekolah</v-col><v-col cols="7">: {{ r_add.school_type || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Status Sekolah</v-col><v-col cols="7">: {{ r_add.school_status || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Kabupaten Sekolah</v-col><v-col cols="7">: {{ r_add.school_city || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Minat Siswa</v-col><v-col cols="7">: {{ r_add.major || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Jenis Pendaftaran</v-col><v-col cols="7">: {{ r_add.registration_type || '-' }}</v-col>
                            <v-col cols="5" class="text-grey-darken-1 font-weight-medium">Sumber Informasi</v-col><v-col cols="7">: {{ r_add.information_source || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Sekolah Sebelumnya</div>
                        <v-row dense>
                            <!-- Space filler or other academic info if needed -->
                        </v-row>
                    </v-col>
                    
                    <v-col cols="12" class="mt-4">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Nilai Rapor</div>
                        <v-row dense v-if="registration.grade">
                            <v-col cols="3" class="mb-4" v-for="g in allGrades" :key="g.key">
                                <div class="text-caption text-grey">{{ formatSubject(g.key) }}</div>
                                <div class="text-subtitle-1 font-weight-bold">{{ g.val }}</div>
                            </v-col>
                            <v-col cols="3" class="mb-4">
                                <div class="text-caption text-orange-darken-2 font-weight-bold">Total Poin Prestasi</div>
                                <div class="text-subtitle-1 font-weight-black text-orange-darken-3">+{{ totalPrestasiScore }}</div>
                            </v-col>
                            <v-col v-if="registration.grade.proof_file_path" cols="12">
                                <v-btn color="info" size="small" :href="'/storage/' + registration.grade.proof_file_path" target="_blank" prepend-icon="mdi-file-eye" variant="tonal">
                                    Lihat Bukti Scan Rapor
                                </v-btn>
                            </v-col>
                        </v-row>
                        <v-alert v-else type="warning" variant="tonal" density="compact">Nilai Rapor belum diisi.</v-alert>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Prestasi -->
            <v-window-item value="prestasi">
                <v-row dense>
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Daftar Prestasi Siswa</div>
                        <v-table v-if="prestasiList.length > 0" density="compact" class="border rounded">
                            <thead>
                                <tr>
                                    <th class="text-left font-weight-bold bg-grey-lighten-4">Nama Prestasi</th>
                                    <th class="text-left font-weight-bold bg-grey-lighten-4">Penyelenggara / Tahun</th>
                                    <th class="text-left font-weight-bold bg-grey-lighten-4">Kategori & Tingkat</th>
                                    <th class="text-left font-weight-bold bg-grey-lighten-4">Peringkat</th>
                                    <th class="text-right font-weight-bold text-orange-darken-3 bg-orange-lighten-5">Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(ach, idx) in prestasiList" :key="idx">
                                    <td class="py-2">
                                        <div class="font-weight-bold text-subtitle-2">{{ ach.name || '-' }}</div>
                                        <div class="text-caption text-grey">Sertifikat: {{ ach.certificate_number || '-' }}</div>
                                    </td>
                                    <td class="py-2">
                                        <div>{{ ach.organizer || '-' }}</div>
                                        <div class="text-caption text-grey">Tahun {{ ach.year || '-' }}</div>
                                    </td>
                                    <td class="py-2">
                                        <div>{{ ach.category || '-' }} ({{ ach.type || '-' }})</div>
                                        <div class="text-caption text-grey">{{ ach.level || '-' }}</div>
                                    </td>
                                    <td class="py-2">
                                        <div class="font-weight-medium">{{ ach.rank || '-' }}</div>
                                    </td>
                                    <td class="py-2 text-right">
                                        <v-chip size="small" color="orange-darken-3" variant="elevated" class="font-weight-black">+{{ ach.score || 0 }}</v-chip>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                        <v-alert v-else type="info" variant="tonal" density="compact" class="mt-2">Peserta belum menambahkan data prestasi.</v-alert>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Tambahan -->
            <v-window-item value="tambahan">
                <v-row dense>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Data Fisik</div>
                        <v-row dense>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Tinggi Badan</v-col><v-col cols="6">: {{ s_add.height ? s_add.height + ' cm' : '-' }}</v-col>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Berat Badan</v-col><v-col cols="6">: {{ s_add.weight ? s_add.weight + ' kg' : '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Transportasi & Jarak</div>
                        <v-row dense>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Jarak ke Sekolah</v-col><v-col cols="6">: {{ s_add.distance_to_school || '-' }}</v-col>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Dalam KM</v-col><v-col cols="6">: {{ s_add.distance_km ? s_add.distance_km + ' km' : '-' }}</v-col>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Moda Transportasi</v-col><v-col cols="6">: {{ s_add.transportation || '-' }}</v-col>
                            <v-col cols="6" class="text-grey-darken-1 font-weight-medium">Waktu Tempuh</v-col><v-col cols="6">: {{ s_add.travel_time || '-' }}</v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" class="mt-4">
                        <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Minat & Hobi</div>
                        <v-row dense>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Minat Ekstrakurikuler</v-col><v-col cols="12" md="8">: {{ s_add.extracurricular_interest || '-' }}</v-col>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Hobi</v-col><v-col cols="12" md="8">: {{ s_add.hobby || '-' }}</v-col>
                            <v-col cols="12" md="4" class="text-grey-darken-1 font-weight-medium">Cita-Cita</v-col><v-col cols="12" md="8">: {{ s_add.ambition || '-' }}</v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Berkas -->
            <v-window-item value="berkas">
                <div class="text-subtitle-2 font-weight-bold text-primary mb-2 border-b pb-1">Dokumen Berkas Terunggah</div>
                <v-row dense v-if="registration.documents && registration.documents.length > 0">
                    <v-col v-for="doc in registration.documents" :key="doc.id" cols="12" sm="6" md="4">
                        <v-card variant="outlined" class="pa-3 text-center h-100 d-flex flex-column justify-center bg-grey-lighten-5">
                            <div class="font-weight-bold mb-2">{{ docMap[doc.type] || doc.type }}</div>
                            <v-btn color="primary" size="small" :href="'/storage/' + doc.file_path" target="_blank" prepend-icon="mdi-eye" variant="elevated">Lihat File</v-btn>
                        </v-card>
                    </v-col>
                </v-row>
                <v-alert v-else type="info" variant="tonal" density="compact" class="mt-2">Siswa belum mengunggah berkas dokumen apapun.</v-alert>
            </v-window-item>

        </v-window>
    </div>
</template>

<style scoped>
.student-data-viewer .border-b {
    border-bottom: 2px solid #e0e0e0;
}
</style>
