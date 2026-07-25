<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden font-sans">
        
        <!-- Background Decoration (Official Government Style) -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-y-0 left-0 w-1/2 bg-blue-900 rounded-r-[100px] shadow-2xl transform -translate-x-1/3"></div>
            <!-- Pattern overlay -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#1e3a8a 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="text-center">
                <div class="mx-auto w-20 h-20 bg-white rounded-2xl shadow-xl flex items-center justify-center p-2 mb-4 border border-slate-100">
                    <img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" class="w-full h-full object-contain" />
                    <svg v-else class="w-12 h-12 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $page.props.app_settings?.app_name || 'Portal Resmi SPMB' }}
                </h2>
                <p class="mt-2 text-sm text-slate-600 uppercase tracking-widest font-bold">
                    {{ $page.props.app_settings?.school_name || 'Bustanul Ulum' }}
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-white py-8 px-4 shadow-2xl shadow-blue-900/10 sm:rounded-2xl sm:px-10 border border-slate-100">
                <slot />
            </div>
            
            <div class="mt-6 text-center text-xs text-slate-500 font-medium">
                &copy; {{ new Date().getFullYear() }} {{ $page.props.app_settings?.school_name || 'Bustanul Ulum' }}.<br> Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </div>
</template>
