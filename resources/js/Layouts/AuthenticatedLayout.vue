<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false);
const isProfileDropdownOpen = ref(false);

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
    { title: 'Dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', route: 'admin.dashboard' },
    { title: 'Daftar Pendaftar', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', route: 'admin.registrations.index' },
    { title: 'Data Peminatan', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', route: 'admin.interests' },
    { title: 'Program Unggulan', icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', route: 'admin.excellent-programs.index' },
    { title: 'Laporan', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'admin.reports.index' },
    { title: 'Pengaturan PPDB', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', route: 'admin.admission-settings.index' },
    { title: 'Identitas Aplikasi', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', route: 'admin.settings.index' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex font-sans text-slate-800">
        
        <!-- Mobile Sidebar Overlay -->
        <div v-if="isMobileMenuOpen" @click="isMobileMenuOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="[
            isSidebarOpen ? 'w-64' : 'w-20',
            isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            'fixed lg:relative inset-y-0 left-0 z-50 bg-gradient-to-b from-[#1e3a8a] to-[#0f172a] text-white flex flex-col transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none'
        ]">
            <!-- Logo Area -->
            <div class="h-16 flex items-center px-4 border-b border-white/10 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0">
                    <img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" class="w-7 h-7 object-contain" />
                    <svg v-else class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <div v-if="isSidebarOpen" class="ml-3 overflow-hidden whitespace-nowrap">
                    <div class="font-bold text-sm leading-tight tracking-wide">{{ $page.props.app_settings?.app_name || 'SPMB Admin' }}</div>
                    <div class="text-[10px] text-blue-200 uppercase tracking-wider">{{ $page.props.app_settings?.school_name || 'Bustanul Ulum' }}</div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
                <div v-if="isSidebarOpen" class="px-3 pb-2 text-[10px] font-bold text-blue-300/50 uppercase tracking-widest mt-2">MENU UTAMA</div>
                
                <Link v-for="item in menuItems" :key="item.title" :href="route(item.route)"
                    :class="[
                        route().current(item.route) || route().current(item.route + '.*') 
                            ? 'bg-blue-600/20 text-white border-blue-400' 
                            : 'text-slate-300 hover:bg-white/5 hover:text-white border-transparent',
                        'flex items-center px-3 py-2.5 rounded-lg border-l-4 transition-all group relative'
                    ]"
                    :title="!isSidebarOpen ? item.title : ''"
                >
                    <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                    </svg>
                    <span v-if="isSidebarOpen" class="ml-3 text-sm font-medium whitespace-nowrap">{{ item.title }}</span>
                </Link>
            </div>

            <!-- Footer / Logout -->
            <div class="p-3 border-t border-white/10">
                <button @click="logout" class="w-full flex items-center px-3 py-2.5 rounded-lg text-red-300 hover:bg-red-500/10 hover:text-red-200 transition-all border-l-4 border-transparent group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span v-if="isSidebarOpen" class="ml-3 text-sm font-medium">Keluar Sistem</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/50">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-30 sticky top-0">
                <div class="flex items-center">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="lg:hidden p-2 -ml-2 mr-2 text-slate-500 hover:bg-slate-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="isSidebarOpen = !isSidebarOpen" class="hidden lg:block p-2 -ml-2 mr-4 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </button>
                    
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight hidden sm:block">
                        <slot name="header" />
                    </h2>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button @click="isProfileDropdownOpen = !isProfileDropdownOpen" class="flex items-center space-x-2 p-1.5 rounded-full hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <div class="hidden md:block text-left mr-1">
                                <div class="text-sm font-bold text-slate-700 leading-tight">{{ $page.props.auth.user.name }}</div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-wider">Administrator</div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div v-if="isProfileDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-medium">Pengaturan Profil</Link>
                            <div class="border-t border-slate-100 my-1"></div>
                            <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Keluar</button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 flex flex-col">
                <div class="max-w-7xl mx-auto w-full flex-1">
                    <slot />
                </div>
                
                <!-- Admin Footer with Copyright and WA icon -->
                <div class="max-w-7xl mx-auto w-full mt-auto pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-sm text-slate-500 pb-2">
                    <div>
                        &copy; {{ new Date().getFullYear() }} {{ $page.props.app_settings?.app_name || 'SPMB' }}. Hak cipta dilindungi.
                    </div>
                    <div class="mt-2 sm:mt-0">
                        <a v-if="$page.props.app_settings?.contact_whatsapp" :href="'https://wa.me/' + $page.props.app_settings?.contact_whatsapp.replace(/[^0-9]/g, '')" target="_blank" class="flex items-center gap-1.5 hover:text-emerald-600 transition-colors font-medium">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Hubungi Admin
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
