<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    programs: Array,
});

const isModalOpen = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null,
    title: '',
    description: '',
    icon: 'mdi-star',
    color_theme: 'blue',
});

const themeOptions = [
    { value: 'blue', label: 'Biru' },
    { value: 'emerald', label: 'Hijau (Emerald)' },
    { value: 'amber', label: 'Kuning (Amber)' },
    { value: 'rose', label: 'Merah (Rose)' },
    { value: 'violet', label: 'Ungu (Violet)' },
    { value: 'sky', label: 'Biru Muda (Sky)' },
];

const openModal = (program = null) => {
    isEditing.value = !!program;
    if (program) {
        form.id = program.id;
        form.title = program.title;
        form.description = program.description;
        form.icon = program.icon;
        form.color_theme = program.color_theme;
    } else {
        form.reset();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const saveProgram = () => {
    if (isEditing.value) {
        form.put(route('admin.excellent-programs.update', form.id), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Program unggulan berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    } else {
        form.post(route('admin.excellent-programs.store'), {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Program unggulan berhasil ditambahkan.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
};

const deleteProgram = (program) => {
    Swal.fire({
        title: 'Hapus Program?',
        text: `Anda yakin ingin menghapus program "${program.title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.excellent-programs.destroy', program.id), {
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Program unggulan berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Program Unggulan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Program Unggulan (Excellent)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="d-flex justify-end mb-4">
                    <v-btn color="primary" prepend-icon="mdi-plus" @click="openModal()" elevation="2" class="text-none">
                        Tambah Program
                    </v-btn>
                </div>
                <v-card class="elevation-1 rounded-xl border">
                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left font-weight-bold">Ikon</th>
                                <th class="text-left font-weight-bold">Program</th>
                                <th class="text-left font-weight-bold">Deskripsi</th>
                                <th class="text-left font-weight-bold">Tema Warna</th>
                                <th class="text-right font-weight-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="programs.length === 0">
                                <td colspan="5" class="text-center py-8 text-grey">Belum ada program unggulan.</td>
                            </tr>
                            <tr v-for="program in programs" :key="program.id" class="hover:bg-slate-50 transition-colors">
                                <td>
                                    <v-icon :icon="program.icon" size="32" :color="program.color_theme"></v-icon>
                                </td>
                                <td class="font-weight-medium">{{ program.title }}</td>
                                <td class="text-body-2 text-grey-darken-1 max-w-[300px] truncate" :title="program.description">{{ program.description }}</td>
                                <td>
                                    <v-chip size="small" :color="program.color_theme" variant="tonal">{{ program.color_theme }}</v-chip>
                                </td>
                                <td class="text-right">
                                    <v-btn variant="text" icon color="blue" size="small" @click="openModal(program)" title="Edit">
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn variant="text" icon color="error" size="small" @click="deleteProgram(program)" title="Hapus">
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </div>
        </div>

        <!-- Modal Tambah/Edit -->
        <v-dialog v-model="isModalOpen" max-width="600" persistent>
            <v-card rounded="xl">
                <v-card-title class="d-flex justify-space-between align-center pa-4 border-b">
                    <span class="text-h6 font-weight-bold">{{ isEditing ? 'Edit Program' : 'Tambah Program' }}</span>
                    <v-btn icon variant="text" @click="closeModal" density="comfortable">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                
                <v-card-text class="pa-4">
                    <form @submit.prevent="saveProgram" class="d-flex flex-column ga-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 d-block">Judul Program</label>
                            <input type="text" v-model="form.title" class="w-100 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm" placeholder="Contoh: Program Tahfidz Al-Qur'an" required>
                            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 d-block">Deskripsi</label>
                            <textarea v-model="form.description" class="w-100 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm" rows="4" placeholder="Deskripsi lengkap program..." required></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                        </div>

                        <v-row>
                            <v-col cols="12" sm="6">
                                <label class="text-sm font-medium text-gray-700 mb-1 d-block">Ikon (MDI)</label>
                                <div class="d-flex ga-2 align-center">
                                    <v-icon :icon="form.icon" size="32" :color="form.color_theme"></v-icon>
                                    <input type="text" v-model="form.icon" class="w-100 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm" placeholder="Contoh: mdi-book-open" required>
                                </div>
                                <div class="text-xs text-grey mt-1">Cari ikon di <a href="https://pictogrammers.com/library/mdi/" target="_blank" class="text-primary text-decoration-underline">Material Design Icons</a></div>
                                <div v-if="form.errors.icon" class="text-red-500 text-xs mt-1">{{ form.errors.icon }}</div>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <label class="text-sm font-medium text-gray-700 mb-1 d-block">Tema Warna</label>
                                <select v-model="form.color_theme" class="w-100 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm" required>
                                    <option v-for="t in themeOptions" :key="t.value" :value="t.value">{{ t.label }}</option>
                                </select>
                                <div v-if="form.errors.color_theme" class="text-red-500 text-xs mt-1">{{ form.errors.color_theme }}</div>
                            </v-col>
                        </v-row>
                    </form>
                </v-card-text>
                
                <v-card-actions class="pa-4 border-t bg-slate-50 d-flex justify-end ga-2">
                    <v-btn variant="text" color="grey-darken-1" @click="closeModal" class="text-none">Batal</v-btn>
                    <v-btn color="primary" variant="flat" @click="saveProgram" :loading="form.processing" class="text-none px-6">
                        Simpan
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
