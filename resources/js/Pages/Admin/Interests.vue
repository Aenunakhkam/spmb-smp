<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    chartData: Array,
    students: Object,
    filters: Object,
    minatOptions: Array,
});

const filterMinat = ref(props.filters.minat || 'Semua');

// Using watch to trigger inertia visits when filter changes
watch(filterMinat, (value) => {
    router.get(route('admin.interests'), { minat: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const getMinatColor = (minat) => {
    const colors = {
        'Belum Memilih': 'grey',
        'BAHASA': 'info',
        'MIPA': 'success',
        'KUNING': 'warning',
        'TAHFIDZ': 'primary',
    };
    return colors[minat] || 'secondary';
};
</script>

<template>
    <Head title="Data Peminatan Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Peminatan Siswa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Summary Cards -->
                <v-row class="mb-6">
                    <v-col v-for="stat in chartData" :key="stat.label" cols="12" sm="6" md="3">
                        <v-card class="rounded-xl elevation-2 h-100">
                            <v-card-text class="d-flex align-center justify-space-between h-100">
                                <div>
                                    <div class="text-caption font-weight-bold text-uppercase text-grey-darken-1 mb-1">{{ stat.label }}</div>
                                    <div class="text-h4 font-weight-black" :class="`text-${getMinatColor(stat.label)}`">{{ stat.count }}</div>
                                </div>
                                <v-avatar :color="`${getMinatColor(stat.label)}-lighten-4`" size="56">
                                    <v-icon :color="getMinatColor(stat.label)" icon="mdi-account-group"></v-icon>
                                </v-avatar>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Data Table Section -->
                <v-card class="rounded-xl elevation-2">
                    <v-card-title class="d-flex align-center px-6 py-4 border-b">
                        <h3 class="text-h6 font-weight-bold mb-0">Daftar Pendaftar per Minat</h3>
                        <v-spacer></v-spacer>
                        <div class="d-flex align-center gap-2">
                            <v-btn color="error" prepend-icon="mdi-file-pdf-box" class="mr-2" :href="route('admin.interests.exportPdf', { minat: filterMinat })" target="_blank" size="small">
                                Export PDF
                            </v-btn>
                            <v-btn color="success" prepend-icon="mdi-file-excel" class="mr-4" :href="route('admin.interests.exportExcel', { minat: filterMinat })" size="small">
                                Export Excel
                            </v-btn>
                            <div style="width: 250px;">
                            <v-select
                                v-model="filterMinat"
                                :items="minatOptions"
                                density="compact"
                                variant="outlined"
                                hide-details
                                prepend-inner-icon="mdi-filter"
                                label="Filter Minat"
                            ></v-select>
                        </div>
                        </div>
                    </v-card-title>
                    
                    <v-card-text class="pa-0">
                        <v-table hover>
                            <thead>
                                <tr>
                                    <th class="text-left font-weight-bold">No. Pendaftaran</th>
                                    <th class="text-left font-weight-bold">Nama Lengkap</th>
                                    <th class="text-left font-weight-bold">L/P</th>
                                    <th class="text-left font-weight-bold">No. HP / WA</th>
                                    <th class="text-left font-weight-bold">Peminatan</th>
                                    <th class="text-center font-weight-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students.data" :key="student.id">
                                    <td class="font-weight-medium">{{ student.registration_number }}</td>
                                    <td>{{ student.full_name }}</td>
                                    <td>{{ student.gender }}</td>
                                    <td>
                                        <a :href="`https://wa.me/${student.phone.replace(/^0/, '62')}`" target="_blank" class="text-success text-decoration-none">
                                            <v-icon icon="mdi-whatsapp" size="small" class="mr-1"></v-icon>
                                            {{ student.phone }}
                                        </a>
                                    </td>
                                    <td>
                                        <v-chip size="small" :color="getMinatColor(student.minat)">
                                            {{ student.minat }}
                                        </v-chip>
                                    </td>
                                    <td class="text-center">
                                        <v-chip size="x-small" :color="student.status === 'verified' ? 'success' : (student.status === 'pending' ? 'warning' : 'grey')">
                                            {{ student.status.toUpperCase() }}
                                        </v-chip>
                                    </td>
                                </tr>
                                <tr v-if="students.data.length === 0">
                                    <td colspan="6" class="text-center py-8 text-grey">Belum ada data pendaftar untuk minat ini.</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card-text>
                    
                    <v-card-actions class="px-6 py-4 border-t d-flex justify-center" v-if="students.links && students.links.length > 3">
                        <div class="d-flex ga-1 flex-wrap justify-center">
                            <v-btn 
                                v-for="(link, i) in students.links" 
                                :key="i"
                                :disabled="!link.url"
                                :variant="link.active ? 'flat' : 'text'"
                                :color="link.active ? 'primary' : 'default'"
                                size="small"
                                class="min-w-0"
                                @click="link.url ? router.get(link.url, { minat: filterMinat }, { preserveState: true, preserveScroll: true }) : null"
                                v-html="link.label"
                            ></v-btn>
                        </div>
                    </v-card-actions>
                </v-card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
