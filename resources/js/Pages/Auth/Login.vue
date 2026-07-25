<script setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import Captcha from '@/Components/Captcha.vue';

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const captchaRef = ref(null);
const captchaError = ref('');

const submit = () => {
    if (captchaRef.value && !captchaRef.value.validate()) {
        captchaError.value = 'Kode keamanan salah. Silakan coba lagi.';
        captchaRef.value.generateCode();
        return;
    }
    captchaError.value = '';
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil Login!',
                text: 'Selamat datang di panel admin.',
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
</script>

<template>
    <Head title="Admin Login" />
    
    <div class="min-h-screen flex font-sans">
        <!-- Left Side: Visual / Brand -->
        <div class="hidden md:flex md:w-1/2 lg:w-7/12 bg-blue-900 relative overflow-hidden items-center justify-center">
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 32px 32px;"></div>
            
            <div class="relative z-10 p-12 lg:p-20 text-white max-w-2xl">
                <div class="inline-flex items-center px-4 py-2 rounded-full border border-white/20 bg-white/5 backdrop-blur-sm mb-8">
                    <svg class="w-4 h-4 mr-2 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-sm font-bold tracking-wider text-blue-100 uppercase">PORTAL ADMIN {{ $page.props.app_settings?.app_name || 'SPMB' }}</span>
                </div>
                
                <h1 class="text-4xl lg:text-5xl font-black mb-6 leading-tight">
                    Kelola Data <br><span class="text-blue-300">Dengan Mudah.</span>
                </h1>
                
                <p class="text-lg text-blue-100/80 mb-12 max-w-md leading-relaxed">
                    Masuk ke dashboard panitia untuk mengelola pendaftaran, verifikasi berkas, dan memantau progres calon siswa.
                </p>
                
                <div class="flex items-center gap-6 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shrink-0 shadow-lg">
                        <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-white text-lg">Akses Terotorisasi</div>
                        <div class="text-blue-200 text-sm">Sistem keamanan tingkat lanjut</div>
                    </div>
                </div>
            </div>
            
            <!-- Abstract decorative shapes -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-800 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-1/2 lg:w-5/12 bg-white flex items-center justify-center relative">
            <div class="w-full max-w-md p-8 sm:p-12 z-10">
                
                <div class="md:hidden text-center mb-8">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-sm border border-blue-100 p-2">
                        <img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" class="w-full h-full object-contain" />
                        <svg v-else class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <h1 class="text-xl font-black text-slate-800">{{ $page.props.app_settings?.app_name || 'SPMB DIGITAL' }}</h1>
                </div>

                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-black text-slate-800 mb-2">Masuk <span class="text-blue-600">Admin</span></h2>
                    <p class="text-slate-500 font-medium">Masukkan username dan kata sandi Anda.</p>
                </div>

                <div v-if="form.errors.username" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-bold text-red-700">{{ form.errors.username }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input v-model="form.username" type="text" required class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-medium" placeholder="Masukkan username">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-medium" placeholder="Masukkan kata sandi">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29"/></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <Captcha ref="captchaRef" :error="captchaError" />

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 mr-2">
                                <input v-model="form.remember" type="checkbox" class="peer appearance-none w-5 h-5 border-2 border-slate-300 rounded focus:ring-0 checked:bg-blue-600 checked:border-blue-600 transition-colors cursor-pointer">
                                <svg class="absolute w-3 h-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">Ingat Saya</span>
                        </label>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span class="tracking-wide mr-2 uppercase text-sm">{{ form.processing ? 'Memproses...' : 'Masuk Sekarang' }}</span>
                        <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </button>
                    
                    <div class="mt-8 text-center">
                        <button type="button" @click="router.get(route('home'))" class="text-sm font-bold text-slate-500 hover:text-blue-600 inline-flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Beranda
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="absolute top-0 right-0 p-8 hidden md:block">
                <div class="w-32 h-32 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
            </div>
        </div>
    </div>
</template>
