<script setup>
import { ref } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    users: Array,
});

const page = usePage();

const search = ref('');
const dialog = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const headers = [
    { title: 'Nama Admin', key: 'name', align: 'start' },
    { title: 'Email', key: 'email', align: 'start' },
    { title: 'Tanggal Dibuat', key: 'created_at', align: 'start' },
    { title: 'Aksi', key: 'actions', align: 'end', sortable: false },
];

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    dialog.value = true;
};

const openEditModal = (user) => {
    isEditing.value = true;
    form.reset();
    form.clearErrors();
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.password_confirmation = '';
    dialog.value = true;
};

const saveUser = () => {
    if (isEditing.value) {
        form.put(route('admin.users.update', form.id), {
            onSuccess: () => {
                dialog.value = false;
                Swal.fire('Berhasil!', 'Data admin berhasil diperbarui.', 'success');
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                dialog.value = false;
                Swal.fire('Berhasil!', 'Admin baru berhasil ditambahkan.', 'success');
            },
        });
    }
};

const confirmDelete = (id, name) => {
    if (id === page.props.auth.user.id) {
        Swal.fire('Gagal!', 'Anda tidak dapat menghapus akun Anda sendiri.', 'error');
        return;
    }

    Swal.fire({
        title: 'Hapus Admin?',
        text: `Apakah Anda yakin ingin menghapus akses admin untuk ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.users.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Akun admin telah dihapus.', 'success');
                }
            });
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).replace(' pukul ', ', ');
};
</script>

<template>
    <Head title="Kelola Akun Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Kelola Akun Admin</h2>
                <span class="text-caption text-grey-darken-1">Manajemen akses dan daftar admin sistem SPMB</span>
            </div>
        </template>

        <!-- Flash Messages -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
            {{ $page.props.flash.success }}
        </div>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
            {{ $page.props.flash.error }}
        </div>

        <div class="d-flex justify-end mb-4">
            <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                Tambah Admin
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="d-flex justify-space-between align-center mb-6 wrap-sm">
                <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-4">Daftar Admin</h3>
                <div class="w-25 mt-sm-2" style="min-width: 250px;">
                    <v-text-field
                        v-model="search"
                        prepend-inner-icon="mdi-magnify"
                        placeholder="Cari admin..."
                        hide-details
                        density="compact"
                        class="rounded-lg"
                    ></v-text-field>
                </div>
            </div>

            <v-data-table
                :headers="headers"
                :items="users"
                :search="search"
                class="bg-transparent"
                hover
            >
                <template v-slot:item.name="{ item }">
                    <div class="d-flex align-center py-2">
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-700 overflow-hidden">
                            <span class="text-caption font-weight-bold text-primary">{{ item.name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <span class="font-weight-medium text-grey-darken-3">{{ item.name }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border bg-slate-100 text-slate-700 border-slate-200">Anda</span>
                    </div>
                </template>
                <template v-slot:item.email="{ item }">
                    <span class="text-body-2 text-grey-darken-1">{{ item.email }}</span>
                </template>
                <template v-slot:item.created_at="{ item }">
                    <span class="text-caption text-grey-darken-1">{{ formatDate(item.created_at) }}</span>
                </template>
                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-end ga-2">
                        <button @click="openEditModal(item)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                        <button @click="confirmDelete(item.id, item.name)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                    </div>
                </template>
            </v-data-table>
        </div>

        <!-- Add/Edit Modal -->
        <v-dialog v-model="dialog" max-width="500">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <span>{{ isEditing ? 'Edit Akun Admin' : 'Tambah Admin Baru' }}</span>
                    <button @click="dialog = false" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"></button>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <v-form @submit.prevent="saveUser">
                        <div class="mb-4">
                            <div class="text-caption font-weight-bold mb-1 text-grey-darken-3">Nama Lengkap <span class="text-error">*</span></div>
                            <v-text-field
                                v-model="form.name"
                                :error-messages="form.errors.name"
                                placeholder="Masukkan nama"
                                prepend-inner-icon="mdi-account-outline"
                                hide-details="auto"
                            ></v-text-field>
                        </div>

                        <div class="mb-4">
                            <div class="text-caption font-weight-bold mb-1 text-grey-darken-3">Alamat Email <span class="text-error">*</span></div>
                            <v-text-field
                                v-model="form.email"
                                type="email"
                                :error-messages="form.errors.email"
                                placeholder="admin@domain.com"
                                prepend-inner-icon="mdi-email-outline"
                                hide-details="auto"
                            ></v-text-field>
                        </div>

                        <div class="mb-4">
                            <div class="text-caption font-weight-bold mb-1 text-grey-darken-3">Password {{ isEditing ? '(Opsional)' : '*' }}</div>
                            <v-text-field
                                v-model="form.password"
                                type="password"
                                :error-messages="form.errors.password"
                                :placeholder="isEditing ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password baru'"
                                prepend-inner-icon="mdi-lock-outline"
                                hide-details="auto"
                            ></v-text-field>
                        </div>

                        <div class="mb-6">
                            <div class="text-caption font-weight-bold mb-1 text-grey-darken-3">Konfirmasi Password {{ !isEditing ? '*' : '' }}</div>
                            <v-text-field
                                v-model="form.password_confirmation"
                                type="password"
                                :error-messages="form.errors.password_confirmation"
                                placeholder="Ketik ulang password"
                                prepend-inner-icon="mdi-lock-check-outline"
                                hide-details="auto"
                            ></v-text-field>
                        </div>

                        <div class="d-flex justify-end ga-3 pb-2">
                            <button @click="dialog = false" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">Batal</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all">
                                {{ isEditing ? 'Simpan Perubahan' : 'Tambahkan Admin' }}
                            </button>
                        </div>
                    </v-form>
                </div>
            </div>
        </v-dialog>
    </AuthenticatedLayout>
</template>

<style scoped>
@media (max-width: 600px) {
    .wrap-sm {
        flex-direction: column;
        align-items: flex-start !important;
    }
    .mt-sm-2 {
        margin-top: 12px;
        width: 100%;
    }
}
</style>
