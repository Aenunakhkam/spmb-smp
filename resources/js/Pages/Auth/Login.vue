<script setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
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
    
    <v-app class="app-refined">
        <v-main class="bg-white">
            <v-row no-gutters class="w-100" style="min-height: 100vh;">
                <!-- Left Side: Visual / Brand (Hidden on small screens) -->
                <v-col cols="12" md="6" lg="7" class="d-none d-md-flex position-relative bg-primary overflow-hidden align-center justify-center">
                    <div class="mesh-bg"></div>
                    <div class="floating-blobs">
                        <div class="blob blob-1"></div>
                        <div class="blob blob-2"></div>
                    </div>
                    <div class="position-relative z-index-2 pa-16 text-left max-width-600 animate-slide-in">
                        <v-chip color="white" variant="outlined" class="mb-6 font-weight-bold px-5 py-4 rounded-pill border-opacity-50 border-sm">
                            <v-icon icon="mdi-shield-account" size="small" class="mr-2"></v-icon>
                            PORTAL ADMIN {{ $page.props.app_settings?.app_name || 'SPMB' }}
                        </v-chip>
                        <h1 class="text-h2 font-weight-black text-white mb-6 leading-tight">
                            Kelola Data <br>Dengan Mudah.
                        </h1>
                        <p class="text-h6 text-white opacity-80 mb-12 font-weight-regular max-width-500">
                            Masuk ke dashboard panitia untuk mengelola pendaftaran, verifikasi berkas, dan memantau progres calon siswa.
                        </p>
                        
                        <div class="d-flex align-center ga-6 glass-panel rounded-xl pa-5">
                            <v-avatar color="white" size="56" class="elevation-4">
                                <v-icon icon="mdi-lock-check" color="primary" size="32"></v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-white font-weight-bold text-subtitle-1">Akses Terotorisasi</div>
                                <div class="text-white opacity-70 text-caption">Sistem keamanan tingkat lanjut</div>
                            </div>
                        </div>
                    </div>
                </v-col>

                <!-- Right Side: Form -->
                <v-col cols="12" md="6" lg="5" class="d-flex align-center justify-center bg-white position-relative">
                    <div class="decoration-circle position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: #f0f7f0; z-index: 0"></div>
                    
                    <div class="w-100 pa-6 pa-md-12 max-width-500 mx-auto position-relative z-index-2 animate-fade-in-up">
                        
                        <div class="d-md-none text-center mb-8">
                            <v-avatar color="primary" size="64" class="mb-4 elevation-4 bg-white">
                                <v-img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" cover></v-img>
                                <v-icon v-else icon="mdi-school" color="primary" size="32"></v-icon>
                            </v-avatar>
                            <h1 class="text-h5 font-weight-black color-main">{{ $page.props.app_settings?.app_name || 'SPMB DIGITAL' }}</h1>
                        </div>

                        <div class="mb-10 text-center text-md-left">
                            <h2 class="text-h3 font-weight-black color-main mb-3 leading-tight">Masuk <span class="text-primary">Admin</span></h2>
                            <p class="text-subtitle-1 text-grey-darken-1">Masukkan username dan kata sandi Anda.</p>
                        </div>

                        <v-alert v-if="form.errors.username" type="error" variant="tonal" class="mb-6 rounded-xl font-weight-medium">
                            <v-icon icon="mdi-alert-circle-outline" class="mr-2"></v-icon>
                            {{ form.errors.username }}
                        </v-alert>

                        <v-form @submit.prevent="submit" class="form-modern">
                            <div class="field-group mb-6">
                                <label class="text-subtitle-2 font-weight-bold color-main mb-2 d-block">Username</label>
                                <v-text-field
                                    v-model="form.username"
                                    placeholder="Masukkan username"
                                    required
                                    variant="outlined"
                                    color="primary"
                                    bg-color="white"
                                    prepend-inner-icon="mdi-account"
                                    rounded="xl"
                                    hide-details="auto"
                                    class="modern-input"
                                ></v-text-field>
                            </div>

                            <div class="field-group mb-8">
                                <label class="text-subtitle-2 font-weight-bold color-main mb-2 d-block">Password</label>
                                <v-text-field
                                    v-model="form.password"
                                    placeholder="Masukkan kata sandi"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    variant="outlined"
                                    color="primary"
                                    bg-color="white"
                                    prepend-inner-icon="mdi-lock"
                                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="showPassword = !showPassword"
                                    rounded="xl"
                                    hide-details="auto"
                                    class="modern-input"
                                ></v-text-field>
                            </div>

                            <div class="d-flex align-center justify-space-between mb-10 px-1">
                                <v-checkbox
                                    v-model="form.remember"
                                    label="Ingat Saya"
                                    color="primary"
                                    hide-details
                                    density="compact"
                                    class="font-weight-bold text-grey-darken-2"
                                ></v-checkbox>
                            </div>

                            <v-btn
                                block
                                size="x-large"
                                color="primary"
                                type="submit"
                                :loading="form.processing"
                                class="rounded-pill font-weight-black elevation-4 py-4 btn-hover-effect h-auto"
                            >
                                <span class="text-button text-white font-weight-bold tracking-wide mr-2">MASUK SEKARANG</span>
                                <v-icon icon="mdi-login-variant"></v-icon>
                            </v-btn>

                            <div class="mt-8 text-center">
                                <v-btn
                                    variant="plain"
                                    class="font-weight-bold text-grey-darken-1 text-none hover-primary"
                                    @click="router.get(route('home'))"
                                    prepend-icon="mdi-arrow-left"
                                    :ripple="false"
                                >
                                    Kembali ke Beranda
                                </v-btn>
                            </div>
                        </v-form>
                    </div>
                </v-col>
            </v-row>
        </v-main>
    </v-app>
</template>

<style scoped>
/* Base overrides */
.app-refined {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

.color-main { color: #0a2a12 !important; }
.leading-tight { line-height: 1.15; }
.z-index-2 { z-index: 2; }
.tracking-wide { letter-spacing: 1px; }
.max-width-600 { max-width: 600px; }
.max-width-500 { max-width: 500px; }
.border-sm { border-width: 1px !important; }

/* Visual Backgrounds */
.mesh-bg {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 30px 30px;
    z-index: 1;
}

.floating-blobs {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    overflow: hidden;
    z-index: 0;
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
}

.blob-1 {
    width: 400px; height: 400px;
    background: #4caf50;
    top: -100px; left: -100px;
    animation: blob-float 10s infinite alternate ease-in-out;
}

.blob-2 {
    width: 500px; height: 500px;
    background: #81c784;
    bottom: -150px; right: -150px;
    animation: blob-float 12s infinite alternate-reverse ease-in-out;
}

.glass-panel {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Modern Input Styling */
.modern-input :deep(.v-field) {
    transition: all 0.3s ease;
    border-radius: 16px;
    background: #f8faf9 !important;
    border-color: transparent;
}

.modern-input :deep(.v-field:hover) {
    background: #ffffff !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.modern-input :deep(.v-field--focused) {
    background: #ffffff !important;
    box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
}

.modern-input :deep(.v-field__outline) {
    display: none;
}

/* Animations */
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-slide-in {
    animation: slideIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-40px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes blob-float {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, 50px) scale(1.1); }
}

/* Button Hover */
.btn-hover-effect {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-hover-effect:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(27, 94, 32, 0.2) !important;
}

.hover-primary {
    transition: 0.3s;
}
.hover-primary:hover {
    color: #1B5E20 !important;
}
</style>
