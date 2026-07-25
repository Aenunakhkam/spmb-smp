<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import OptionListEditor from '@/Components/OptionListEditor.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    registration_status: props.settings.registration_status,
    registration_start_date: props.settings.registration_start_date,
    registration_end_date: props.settings.registration_end_date,
    ppdb_agenda: props.settings.ppdb_agenda ? [...props.settings.ppdb_agenda] : [],
    frontend_faqs: props.settings.frontend_faqs ? [...props.settings.frontend_faqs] : [],
    popup_banner_file: null,
    quota: props.settings.quota,
    report_semester: props.settings.report_semester,
    available_subjects: [...props.settings.available_subjects],
    subjects_required: [...props.settings.subjects_required],
    achievement_scores: props.settings.achievement_scores ? { ...props.settings.achievement_scores } : {},
    
    opt_pendidikan: [...props.settings.opt_pendidikan],
    opt_pekerjaan: [...props.settings.opt_pekerjaan],
    opt_penghasilan: [...props.settings.opt_penghasilan],
    opt_kebutuhan_khusus: [...props.settings.opt_kebutuhan_khusus],
    opt_tempat_tinggal: [...props.settings.opt_tempat_tinggal],
    opt_ekstrakurikuler: [...props.settings.opt_ekstrakurikuler],
    opt_peminatan: [...props.settings.opt_peminatan],
    opt_moda_transportasi: [...props.settings.opt_moda_transportasi],
    opt_alasan_kip: [...props.settings.opt_alasan_kip],
});

const activeTab = ref('umum');
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

const deleteBanner = () => {
    Swal.fire({
        title: 'Hapus Banner?',
        text: 'Apakah Anda yakin ingin menghapus banner pop-up ini? File tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.admission-settings.deleteBanner'), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Banner berhasil dihapus.', 'success');
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Pengaturan PPDB" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-800">Pengaturan PPDB</h2>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 md:col-span-10">
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                    {{ $page.props.flash.success }}
                </div>

                <v-form @submit.prevent="submit">
                    <v-tabs v-model="activeTab" color="primary" align-tabs="start" class="mb-6">
                        <v-tab value="umum" class="font-weight-bold"><v-icon start>mdi-school</v-icon> Umum & Akademik</v-tab>
                        <v-tab value="jadwal" class="font-weight-bold"><v-icon start>mdi-calendar-clock</v-icon> Jadwal & Info</v-tab>
                        <v-tab value="skor" class="font-weight-bold"><v-icon start>mdi-medal-outline</v-icon> Skor Prestasi</v-tab>
                        <v-tab value="referensi" class="font-weight-bold"><v-icon start>mdi-database-outline</v-icon> Data Referensi</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- TAB UMUM & AKADEMIK -->
                        <v-window-item value="umum">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                                <!-- Parameter Penerimaan Murid Baru -->
                                <div class="col-span-12 md:col-span-6">
                            <div class="neo-card p-6 h-full mb-0">
                                <div class="d-flex align-center mb-6 border-b pb-3">
                                    <v-icon color="primary" class="mr-2" size="28">mdi-school</v-icon>
                                    <h3 class="text-lg font-bold text-slate-800">Parameter Penerimaan Murid Baru</h3>
                                </div>

                                <!-- Kuota Penerimaan -->
                                <div class="mb-4">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Kuota Penerimaan (Siswa)</div>
                                    <TextInput
                                        v-model="form.quota"
                                        type="number"
                                        class="w-full mt-2"
                                    />
                                </div>

                                <!-- Tanggal Pendaftaran -->
                                <div class="mb-4 d-flex ga-4">
                                    <div class="w-50">
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Tanggal Buka</div>
                                        <TextInput
                                            v-model="form.registration_start_date"
                                            type="date"
                                            class="w-full mt-2"
                                        />
                                    </div>
                                    <div class="w-50">
                                        <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Tanggal Tutup</div>
                                        <TextInput
                                            v-model="form.registration_end_date"
                                            type="date"
                                            class="w-full mt-2"
                                        />
                                    </div>
                                </div>

                                <!-- Status Pendaftaran -->
                                <div>
                                    <div class="text-subtitle-2 font-weight-bold mb-2 text-grey-darken-2">Status Penerimaan</div>
                                    <v-radio-group v-model="form.registration_status" inline color="primary" hide-details="auto">
                                        <v-radio label="Buka Pendaftaran" value="open" class="mr-4"></v-radio>
                                        <v-radio label="Tutup Pendaftaran" value="closed"></v-radio>
                                    </v-radio-group>
                                    
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                                        <strong>Penting:</strong> Calon siswa tidak dapat registrasi baru saat status ditutup.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Konfigurasi Nilai Rapor -->
                        <div class="col-span-12 md:col-span-6">
                            <div class="neo-card p-6 h-full mb-0">
                                <div class="d-flex align-center mb-6 border-b pb-3">
                                    <v-icon color="success" class="mr-2" size="28">mdi-book-education</v-icon>
                                    <h3 class="text-lg font-bold text-slate-800">Konfigurasi Nilai Rapor</h3>
                                </div>

                                <!-- Semester Rapor -->
                                <div class="mb-6">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Keterangan Rapor yang Digunakan</div>
                                    <div class="text-caption text-grey mb-2">Akan ditampilkan sebagai judul di form input nilai siswa.</div>
                                    <TextInput
                                        v-model="form.report_semester"
                                        placeholder="Contoh: Kelas 6 Semester 2"
                                        class="w-full mt-2"
                                    />
                                </div>

                                <!-- Master Mata Pelajaran -->
                                <div class="mb-6">
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Mata Pelajaran (Master Data)</div>
                                    <div class="text-caption text-grey mb-3">Kelola mata pelajaran yang tersedia.</div>
                                    
                                    <div class="d-flex align-center ga-2 mb-3">
                                        <div class="mb-4"><label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode (cth: mtk)</label><input type="text" v-model="newSubject.key" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-slate-50 hover:bg-white focus:bg-white"></div>
                                        <div class="mb-4"><label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Mata Pelajaran</label><input type="text" v-model="newSubject.label" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-slate-50 hover:bg-white focus:bg-white"></div>
                                        <button @click="addSubject" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                                    </div>
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                                        {{ form.errors.available_subjects }}
                                    </div>
                                </div>

                                <!-- Mata Pelajaran Wajib -->
                                <div>
                                    <div class="text-subtitle-2 font-weight-bold mb-1 text-grey-darken-2">Mata Pelajaran Wajib Diinput</div>
                                    <div class="text-caption text-grey mb-3">Centang mata pelajaran yang harus diisi. Klik tombol (Hapus) untuk menghapus dari Master Data.</div>
                                    
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
                                        {{ form.errors.subjects_required }}
                                    </div>

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
                                                <button @click="removeSubject(index, subj.key)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                                            </template>
                                        </v-list-item>
                                    </v-list>
                                </div>
                            </div>
                        </div>
                    </div>
                    </v-window-item>

                    <!-- TAB JADWAL & INFO -->
                    <v-window-item value="jadwal">
                        
                        <!-- Pop-up Banner PPDB -->
                        <div class="neo-card p-6 mb-6">
                            <div class="d-flex align-center mb-4 border-b pb-3">
                                <v-icon color="indigo" class="mr-2" size="28">mdi-image-multiple-outline</v-icon>
                                <h3 class="text-lg font-bold text-slate-800">Pop-up Banner Pendaftaran</h3>
                            </div>
                            <p class="text-body-2 text-grey-darken-1 mb-4">Unggah poster atau banner informasi PPDB yang akan otomatis muncul (pop up) di halaman utama pendaftaran.</p>
                            
                            <div v-if="settings.popup_banner" class="mb-4">
                                <div class="relative inline-block group">
                                    <img src="'/storage/' + settings.popup_banner" class="w-full h-full object-cover">
                                    <button @click="deleteBanner" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                                        Hapus Banner
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-4 border border-slate-200 rounded-lg p-3 bg-slate-50">
                                <input type="file" @change="e => form.popup_banner_file = e.target.files[0]" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <div v-if="form.errors.popup_banner_file" class="text-error text-caption mt-1">{{ form.errors.popup_banner_file }}</div>
                            </div>
                        </div>

                        <!-- Agenda / Timeline PPDB -->
                        <div class="neo-card p-6 mb-6">
                        <div class="d-flex align-center mb-6 border-b pb-3">
                            <v-icon color="warning" class="mr-2" size="28">mdi-calendar-clock</v-icon>
                            <h3 class="text-lg font-bold text-slate-800">Agenda / Timeline PPDB</h3>
                        </div>
                        <p class="text-body-2 text-grey-darken-1 mb-4">Tambahkan jadwal agenda PPDB seperti Ujian Masuk, Daftar Ulang, Pengumuman, dll.</p>

                        <div class="overflow-x-auto"><table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-grey-lighten-4">
                                    <th class="font-weight-bold" style="width: 200px;">Tanggal / Waktu</th>
                                    <th class="font-weight-bold">Nama Agenda</th>
                                    <th class="font-weight-bold">Deskripsi</th>
                                    <th class="font-weight-bold text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(agenda, i) in form.ppdb_agenda" :key="i">
                                    <td>
                                        <TextInput type="date" v-model="agenda.date" class="w-full" density="compact" />
                                    </td>
                                    <td>
                                        <v-text-field v-model="agenda.title" variant="underlined" density="compact" hide-details placeholder="Nama Agenda"></v-text-field>
                                    </td>
                                    <td>
                                        <v-text-field v-model="agenda.description" variant="underlined" density="compact" hide-details placeholder="Keterangan singkat"></v-text-field>
                                    </td>
                                    <td class="text-center">
                                        <button @click="form.ppdb_agenda.splice(i, 1)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table></div>
                        <button @click="form.ppdb_agenda.push({title: '', description: '', date: ''})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            Tambah Agenda
                        </button>
                    </div>

                    <!-- FAQ (Pertanyaan yang sering diajukan) -->
                    <div class="neo-card p-6 mt-6 mb-6">
                        <div class="d-flex align-center mb-6 border-b pb-3">
                            <v-icon color="info" class="mr-2" size="28">mdi-frequently-asked-questions</v-icon>
                            <h3 class="text-lg font-bold text-slate-800">Pertanyaan yang Sering Diajukan (FAQ)</h3>
                        </div>
                        <p class="text-body-2 text-grey-darken-1 mb-4">Tambahkan pertanyaan dan jawaban yang akan ditampilkan di halaman utama pendaftaran (Welcome).</p>

                        <div class="overflow-x-auto"><table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-grey-lighten-4">
                                    <th class="font-weight-bold" style="width: 40%;">Pertanyaan</th>
                                    <th class="font-weight-bold">Jawaban</th>
                                    <th class="font-weight-bold text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(faq, i) in form.frontend_faqs" :key="i">
                                    <td>
                                        <v-text-field v-model="faq.question" variant="underlined" density="compact" hide-details placeholder="Pertanyaan"></v-text-field>
                                    </td>
                                    <td>
                                        <v-textarea v-model="faq.answer" rows="2" auto-grow variant="underlined" density="compact" hide-details placeholder="Jawaban lengkap"></v-textarea>
                                    </td>
                                    <td class="text-center">
                                        <button @click="form.frontend_faqs.splice(i, 1)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table></div>
                        <button @click="form.frontend_faqs.push({question: '', answer: ''})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            Tambah Pertanyaan
                        </button>
                    </div>
                    </v-window-item>

                    <!-- TAB SKOR PRESTASI -->
                    <v-window-item value="skor">
                        <!-- Pengaturan Skor Prestasi -->
                        <div class="neo-card p-6 mb-6">
                        <div class="d-flex align-center mb-6 border-b pb-3">
                            <v-icon color="orange" class="mr-2" size="28">mdi-medal-outline</v-icon>
                            <h3 class="text-lg font-bold text-slate-800">Pengaturan Skor Prestasi</h3>
                        </div>
                        <p class="text-body-2 text-grey-darken-1 mb-6">Atur bobot poin prestasi berdasarkan Tingkat Kejuaraan dan Peringkat Juara.</p>
                        
                        <div v-for="(ranks, level) in form.achievement_scores" :key="level" class="mb-6 p-4 border rounded-xl bg-slate-50">
                            <div class="text-subtitle-1 font-weight-bold text-primary mb-4">{{ level }}</div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="(score, rank) in ranks" :key="rank">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">{{ rank }}</label>
                                    <TextInput 
                                        type="number"
                                        v-model.number="form.achievement_scores[level][rank]"
                                        class="w-full bg-white"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    </v-window-item>

                    <!-- TAB DATA REFERENSI -->
                    <v-window-item value="referensi">
                        <!-- Master Data Referensi Card (Mockup Style) -->
                        <div class="mb-6">
                        <div class="mockup-container" style="background-color: #f8f9fa; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                            <OptionListEditor
                                v-model="form.opt_tempat_tinggal"
                                title="Opsi Tempat Tinggal"
                                :showInput="true"
                            />
                            
                            <OptionListEditor
                                v-model="form.opt_moda_transportasi"
                                title="Opsi Moda Transportasi"
                                :showInput="true"
                            />

                            <OptionListEditor
                                v-model="form.opt_kebutuhan_khusus"
                                title="Opsi Kebutuhan Khusus"
                                :showInput="true"
                                :hasHeader="true"
                            />
                        </div>
                    </div>
                    
                    <!-- Latar Belakang & Ekonomi -->
                    <div class="mt-8 mb-6">
                        <div class="mockup-container" style="background-color: #f8f9fa; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                            <OptionListEditor
                                v-model="form.opt_pendidikan"
                                title="Opsi Pendidikan Orang Tua"
                                :showInput="true"
                            />
                            
                            <OptionListEditor
                                v-model="form.opt_pekerjaan"
                                title="Opsi Pekerjaan Orang Tua"
                                :showInput="true"
                            />

                            <OptionListEditor
                                v-model="form.opt_penghasilan"
                                title="Opsi Penghasilan Orang Tua"
                                :showInput="true"
                            />
                        </div>
                    </div>

                    <!-- Opsi Tambahan -->
                    <div class="mt-8 mb-6">
                        <div class="mockup-container" style="background-color: #f8f9fa; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden;">
                            <OptionListEditor
                                v-model="form.opt_ekstrakurikuler"
                                title="Opsi Ekstrakurikuler"
                                :showInput="true"
                            />
                            <OptionListEditor
                                v-model="form.opt_peminatan"
                                title="Opsi Peminatan / Jurusan"
                                :showInput="true"
                            />
                            <OptionListEditor
                                v-model="form.opt_alasan_kip"
                                title="Opsi Alasan Layak KIP / PIP"
                                :showInput="true"
                            />
                        </div>
                    </div>
                    </v-window-item>
                    </v-window>

                    <!-- Save Button -->
                    <div class="d-flex justify-end mt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                            Simpan Semua Pengaturan
                        </button>
                    </div>
                </v-form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
