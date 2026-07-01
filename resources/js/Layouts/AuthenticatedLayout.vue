<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const drawer = ref(true);

const logout = () => {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil Logout!',
                text: 'Anda telah keluar dari sistem.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    });
};

const menuItems = [
    { title: 'Dashboard', icon: 'mdi-view-dashboard', route: 'admin.dashboard' },
    { title: 'Data Master', icon: 'mdi-database', route: 'admin.master.index' },
    { title: 'Daftar Pendaftar', icon: 'mdi-account-group', route: 'admin.registrations.index' },
    { title: 'Data Peminatan', icon: 'mdi-star-face', route: 'admin.interests' },
    { title: 'Laporan Pendaftaran', icon: 'mdi-file-document-multiple', route: 'admin.reports.index' },
    { title: 'Pengaturan PPDB', icon: 'mdi-clipboard-list-outline', route: 'admin.admission-settings.index' },
    { title: 'Identitas Aplikasi', icon: 'mdi-application-cog', route: 'admin.settings.index' },
    { title: 'Log Aktivitas', icon: 'mdi-history', route: 'admin.logs.index' },
    { title: 'Kelola Akun', icon: 'mdi-shield-account', route: 'admin.users.index' },
];
</script>

<template>
    <v-app style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
        <v-navigation-drawer v-model="drawer" color="white" elevation="0" style="border-right: 1px solid rgba(0,0,0,0.05);">
            <v-list class="pa-4">
                <div class="d-flex align-center px-2 mb-6 mt-2">
                    <v-avatar color="primary-lighten-1" size="40" class="mr-3 elevation-2 bg-white">
                        <v-img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" cover></v-img>
                        <v-icon v-else color="primary" size="20">mdi-school</v-icon>
                    </v-avatar>
                    <div>
                        <div class="text-subtitle-1 font-weight-black text-grey-darken-4" style="line-height: 1.2;">{{ $page.props.app_settings?.app_name || 'SPMB Admin' }}</div>
                        <div class="text-caption font-weight-medium text-primary text-truncate" style="max-width: 150px;">{{ $page.props.app_settings?.school_name || 'Bustanul Ulum' }}</div>
                    </div>
                </div>
                <v-divider class="mb-4 opacity-10"></v-divider>
                
                <v-list-item
                    v-for="item in menuItems"
                    :key="item.title"
                    :prepend-icon="item.icon"
                    :title="item.title"
                    @click="item.route === 'coming_soon' ? null : router.get(route(item.route))"
                    :active="route().current(item.route) || route().current(item.route + '.*')"
                    active-color="primary"
                    variant="text"
                    rounded="lg"
                    class="mb-2 font-weight-medium text-grey-darken-2"
                    style="cursor:pointer"
                >
                    <template v-slot:append v-if="item.route === 'coming_soon'">
                        <v-chip size="x-small" color="grey-lighten-1" text-color="white" class="font-weight-bold">Segera</v-chip>
                    </template>
                </v-list-item>
            </v-list>

            <template v-slot:append>
                <v-divider class="opacity-10"></v-divider>
                <v-list class="pa-4">
                    <div @click="logout" class="w-100" style="text-decoration:none; color:inherit;">
                        <v-list-item
                            prepend-icon="mdi-logout"
                            title="Keluar"
                            variant="text"
                            rounded="lg"
                            class="text-error font-weight-medium hover-lift"
                            style="cursor: pointer"
                        ></v-list-item>
                    </div>
                </v-list>
            </template>
        </v-navigation-drawer>

        <v-app-bar class="glass-header px-md-4" elevation="0" height="75" app style="background: rgba(255, 255, 255, 0.85);">
            <v-app-bar-nav-icon @click="drawer = !drawer" color="grey-darken-3"></v-app-bar-nav-icon>
            <v-toolbar-title class="font-weight-bold text-grey-darken-4">
                <slot name="header" />
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <div class="px-4">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <div v-bind="props" class="d-flex align-center pa-1 pr-3 rounded-pill" style="cursor: pointer; background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05);">
                            <v-avatar size="36" color="primary-lighten-4" class="mr-2">
                                <v-icon icon="mdi-shield-account" color="primary" size="20"></v-icon>
                            </v-avatar>
                            <span class="text-caption font-weight-bold text-grey-darken-3">{{ $page.props.auth.user.name }}</span>
                            <v-icon size="small" color="grey-darken-1" class="ml-1">mdi-chevron-down</v-icon>
                        </div>
                    </template>
                    <v-list elevation="3" class="mt-2 rounded-lg pa-2 border" min-width="180">
                        <v-list-item @click="router.get(route('profile.edit'))" prepend-icon="mdi-account-circle-outline" title="Profil Admin" class="rounded-lg mb-1 font-weight-medium"></v-list-item>
                        <v-divider class="my-1 opacity-20"></v-divider>
                        <div @click="logout" style="text-decoration:none; color:inherit; cursor: pointer;">
                            <v-list-item prepend-icon="mdi-logout" title="Keluar Sistem" class="text-error rounded-lg font-weight-medium"></v-list-item>
                        </div>
                    </v-list>
                </v-menu>
            </div>
        </v-app-bar>

        <v-main style="position: relative; z-index: 1;">
            <v-container fluid class="pa-md-8 pa-4" style="max-width: 1400px;">
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>
