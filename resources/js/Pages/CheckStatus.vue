<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const isRecoverMode = ref(false);

const loginForm = useForm({
    registration_number: '',
    access_code: '',
});

const recoverForm = useForm({
    nisn: '',
    phone: '',
});

const submitLogin = () => {
    loginForm.post(route('check-status.process'));
};

const submitRecover = () => {
    recoverForm.post(route('recover-access'));
};

const resetForms = () => {
    loginForm.reset();
    recoverForm.reset();
    isRecoverMode.value = false;
};
</script>

<template>
    <Head title="Portal Siswa" />
    <v-app class="app-refined">
        <v-main class="auth-split-layout">
            <v-row no-gutters class="fill-height">
                
                <!-- Left Side: Branding & Visuals (Hidden on small screens or shown as header) -->
                <v-col cols="12" md="5" lg="6" class="brand-panel d-flex flex-column justify-center align-center position-relative overflow-hidden pa-10">
                    <div class="brand-overlay"></div>
                    <div class="blob-shape shape-1"></div>
                    <div class="blob-shape shape-2"></div>
                    
                    <div class="brand-content position-relative text-center text-md-left w-100 max-w-lg mx-auto">
                        <div class="d-flex align-center justify-center justify-md-start mb-8">
                            <div class="logo-box elevation-8 mr-4 bg-white overflow-hidden" style="padding: 2px;">
                                <v-img v-if="$page.props.app_settings?.school_logo_path" :src="$page.props.app_settings.school_logo_path" cover class="w-100 h-100 rounded-lg"></v-img>
                                <v-icon v-else icon="mdi-school" color="primary" size="36"></v-icon>
                            </div>
                            <div class="text-white">
                                <h1 class="text-h4 font-weight-black line-height-tight mb-0">{{ $page.props.app_settings?.app_name || 'SPMB' }}</h1>
                                <div class="text-subtitle-2 font-weight-bold letter-spacing-wide opacity-80">DIGITAL PORTAL</div>
                            </div>
                        </div>

                        <h2 class="text-h3 font-weight-black text-white line-height-tight mb-6 d-none d-md-block position-relative z-index-2">
                            Pantau Hasil & <br>Lengkapi Berkas Anda.
                        </h2>
                        <p class="text-h6 text-white opacity-90 font-weight-regular d-none d-md-block position-relative z-index-2 mb-8">
                            Sistem penerimaan siswa baru yang transparan, terpadu, dan sepenuhnya digital.
                        </p>

                        <!-- Illustration Image -->
                        <div class="d-none d-md-block position-relative z-index-2">
                            <v-btn
                                variant="outlined"
                                color="white"
                                class="rounded-pill font-weight-bold px-8 border-opacity-100 btn-hover-grow"
                                size="large"
                                @click="router.get(route('home'))"
                                prepend-icon="mdi-arrow-left"
                            >
                                Kembali ke Beranda
                            </v-btn>
                        </div>
                    </div>
                </v-col>

                <!-- Right Side: Auth Form -->
                <v-col cols="12" md="7" lg="6" class="d-flex align-center justify-center bg-white pa-6 pa-md-12">
                    <div class="auth-form-container w-100 max-w-sm">
                        
                        <transition name="fade-slide" mode="out-in">
                            <!-- FORM LOGIN -->
                            <div v-if="!isRecoverMode" key="login">
                                <div class="mb-10 text-center text-md-left">
                                    <v-chip color="primary" variant="tonal" class="font-weight-bold mb-4" size="small">PORTAL SISWA</v-chip>
                                    <h2 class="text-h4 font-weight-black color-main mb-2">Selamat Datang</h2>
                                    <p class="text-body-1 text-grey-darken-1">Silakan masukkan detail akses Anda.</p>
                                </div>

                                <v-alert v-if="loginForm.errors.message" type="error" variant="tonal" class="mb-8 rounded-xl font-weight-bold border-s-lg border-error">
                                    {{ loginForm.errors.message }}
                                </v-alert>

                                <v-form @submit.prevent="submitLogin">
                                    <div class="mb-4">
                                        <label class="text-caption font-weight-black color-main text-uppercase letter-spacing-wide mb-2 d-block">Nomor Pendaftaran</label>
                                        <v-text-field
                                            v-model="loginForm.registration_number"
                                            placeholder="Contoh: 20260001"
                                            required
                                            variant="outlined"
                                            color="primary"
                                            prepend-inner-icon="mdi-badge-account-horizontal-outline"
                                            class="input-premium"
                                            bg-color="grey-lighten-4"
                                            hide-details="auto"
                                        ></v-text-field>
                                    </div>

                                    <div class="mb-6">
                                        <div class="d-flex justify-space-between align-center mb-2">
                                            <label class="text-caption font-weight-black color-main text-uppercase letter-spacing-wide d-block">Kode Akses</label>
                                            <a href="#" @click.prevent="isRecoverMode = true" class="text-caption text-primary font-weight-bold text-decoration-none hover-primary transition-fast">
                                                Lupa Kode?
                                            </a>
                                        </div>
                                        <v-text-field
                                            v-model="loginForm.access_code"
                                            placeholder="Masukkan kode rahasia"
                                            type="password"
                                            required
                                            variant="outlined"
                                            color="primary"
                                            prepend-inner-icon="mdi-lock-outline"
                                            class="input-premium"
                                            bg-color="grey-lighten-4"
                                            hide-details="auto"
                                        ></v-text-field>
                                    </div>

                                    <v-btn
                                        block
                                        size="x-large"
                                        color="primary"
                                        type="submit"
                                        :loading="loginForm.processing"
                                        class="rounded-xl font-weight-black text-capitalize btn-premium shadow-primary mt-2"
                                        elevation="4"
                                    >
                                        Masuk Sekarang
                                    </v-btn>

                                    <!-- Tombol Kembali Untuk Mobile -->
                                    <v-btn
                                        block
                                        variant="text"
                                        color="grey-darken-2"
                                        class="mt-4 rounded-xl font-weight-bold text-capitalize d-md-none"
                                        @click="router.get(route('home'))"
                                        prepend-icon="mdi-arrow-left"
                                    >
                                        Kembali ke Beranda
                                    </v-btn>
                                </v-form>
                            </div>

                            <!-- FORM RECOVERY -->
                            <div v-else key="recover">
                                <div class="mb-10 text-center text-md-left">
                                    <v-btn icon="mdi-arrow-left" variant="tonal" size="small" color="primary" class="mb-6 rounded-lg d-none d-md-inline-flex" @click="resetForms"></v-btn>
                                    <h2 class="text-h4 font-weight-black text-warning-darken-2 mb-2">Lupa Akses?</h2>
                                    <p class="text-body-1 text-grey-darken-1">Jangan khawatir, kami bantu temukan data Anda.</p>
                                </div>

                                <v-alert v-slot:text v-if="$page.props.errors.recover_message" type="error" variant="tonal" class="mb-8 rounded-xl font-weight-bold border-s-lg border-error">
                                    {{ $page.props.errors.recover_message }}
                                </v-alert>

                                <!-- Success State -->
                                <div v-if="$page.props.flash.recover_success" class="recovery-success-card mb-8">
                                    <div class="d-flex align-start mb-4">
                                        <v-avatar color="success-lighten-4" class="mr-4 mt-1" size="48">
                                            <v-icon color="success-darken-1" size="28">mdi-check-all</v-icon>
                                        </v-avatar>
                                        <div>
                                            <div class="text-h6 font-weight-black text-success-darken-2 line-height-tight">Akses Ditemukan!</div>
                                            <div class="text-caption text-grey-darken-1 mt-1">Halo <strong class="color-main">{{ $page.props.flash.recover_success.full_name }}</strong>, ini data Anda:</div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg pa-4 border mb-4 shadow-sm">
                                        <div class="text-caption text-grey-darken-1 font-weight-bold mb-1">No. Pendaftaran</div>
                                        <div class="font-weight-black text-h6 color-main tracking-wide mb-3">{{ $page.props.flash.recover_success.registration_number }}</div>
                                        
                                        <div class="text-caption text-grey-darken-1 font-weight-bold mb-1">Kode Akses</div>
                                        <div class="font-weight-black text-h6 text-warning-darken-2 tracking-wide">{{ $page.props.flash.recover_success.access_code }}</div>
                                    </div>

                                    <v-btn
                                        block
                                        color="success"
                                        size="x-large"
                                        class="rounded-xl font-weight-black text-capitalize btn-premium shadow-success mt-2"
                                        @click="() => {
                                            loginForm.registration_number = $page.props.flash.recover_success.registration_number;
                                            loginForm.access_code = $page.props.flash.recover_success.access_code;
                                            resetForms();
                                        }"
                                    >
                                        Gunakan untuk Login
                                    </v-btn>
                                </div>

                                <!-- Input State -->
                                <v-form v-if="!$page.props.flash.recover_success" @submit.prevent="submitRecover">
                                    <div class="mb-4">
                                        <label class="text-caption font-weight-black color-main text-uppercase letter-spacing-wide mb-2 d-block">NISN Anda</label>
                                        <v-text-field
                                            v-model="recoverForm.nisn"
                                            placeholder="Masukkan 10-digit NISN"
                                            required
                                            maxlength="10"
                                            variant="outlined"
                                            color="warning"
                                            prepend-inner-icon="mdi-card-account-details-outline"
                                            class="input-premium"
                                            bg-color="grey-lighten-4"
                                            hide-details="auto"
                                            :error-messages="recoverForm.errors.nisn"
                                        ></v-text-field>
                                    </div>

                                    <div class="mb-8">
                                        <label class="text-caption font-weight-black color-main text-uppercase letter-spacing-wide mb-2 d-block">Nomor WhatsApp</label>
                                        <v-text-field
                                            v-model="recoverForm.phone"
                                            placeholder="08xxxxxxxxxx"
                                            required
                                            variant="outlined"
                                            color="warning"
                                            prepend-inner-icon="mdi-whatsapp"
                                            class="input-premium"
                                            bg-color="grey-lighten-4"
                                            hide-details="auto"
                                            :error-messages="recoverForm.errors.phone"
                                        ></v-text-field>
                                    </div>

                                    <v-btn
                                        block
                                        size="x-large"
                                        color="warning-darken-1"
                                        type="submit"
                                        :loading="recoverForm.processing"
                                        class="rounded-xl font-weight-black text-white text-capitalize btn-premium shadow-warning mt-2"
                                    >
                                        Cari Data Akses
                                    </v-btn>

                                    <!-- Tombol Kembali Untuk Mobile & Web -->
                                    <v-btn
                                        block
                                        variant="text"
                                        color="grey-darken-2"
                                        class="mt-4 rounded-xl font-weight-bold text-capitalize"
                                        @click="resetForms"
                                        prepend-icon="mdi-arrow-left"
                                    >
                                        Kembali ke Login
                                    </v-btn>
                                </v-form>
                            </div>
                        </transition>

                    </div>
                </v-col>
            </v-row>
        </v-main>
    </v-app>
</template>

<style scoped>
/* Base Reset & Typography */
.app-refined {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}
.color-main { color: #0f172a !important; } /* Slate 900 for sharp contrast */
.line-height-tight { line-height: 1.15; }
.tracking-wide { letter-spacing: 1.5px; }
.letter-spacing-wide { letter-spacing: 1px; }
.transition-fast { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.max-w-sm { max-width: 420px; width: 100%; margin: 0 auto; }
.max-w-lg { max-width: 500px; width: 100%; }

/* Split Layout */
.auth-split-layout {
    min-height: 100vh;
    background-color: #ffffff;
}

/* Brand Panel (Left Side) */
.brand-panel {
    background: linear-gradient(135deg, #166534 0%, #15803d 100%); /* Green 800 to 700 */
}

.brand-overlay {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.5;
    z-index: 1;
}

.blob-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    z-index: 0;
}
.shape-1 {
    width: 400px; height: 400px;
    background: #4ade80; /* Green 400 */
    top: -100px; left: -100px;
}
.shape-2 {
    width: 500px; height: 500px;
    background: #047857; /* Emerald 700 */
    bottom: -150px; right: -100px;
}

.logo-box {
    width: 64px; height: 64px;
    background: #ffffff;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    transform: rotate(-5deg);
}

.brand-content { z-index: 5; }

/* Form Elements Styling */
.input-premium :deep(.v-field) {
    border-radius: 16px;
    transition: all 0.3s ease;
}
.input-premium :deep(.v-field--focused) {
    background-color: #ffffff !important;
    box-shadow: 0 4px 20px rgba(22, 101, 52, 0.08) !important;
}

/* Buttons */
.btn-premium {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-premium:hover {
    transform: translateY(-2px);
}
.btn-hover-grow:hover {
    background: rgba(255,255,255,0.1);
    transform: scale(1.02);
}

.shadow-primary { box-shadow: 0 10px 25px -5px rgba(22, 101, 52, 0.4) !important; }
.shadow-warning { box-shadow: 0 10px 25px -5px rgba(245, 124, 0, 0.4) !important; }
.shadow-success { box-shadow: 0 10px 25px -5px rgba(46, 125, 50, 0.4) !important; }
.shadow-sm { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important; }
.drop-shadow-xl { filter: drop-shadow(0 20px 13px rgba(0, 0, 0, 0.3)) drop-shadow(0 8px 5px rgba(0, 0, 0, 0.08)); }

/* Links */
.hover-primary:hover { color: #166534 !important; text-decoration: underline; }

/* Recovery Success Layout */
.recovery-success-card {
    background: #f0fdf4; /* Green 50 */
    border: 1px solid #bbf7d0; /* Green 200 */
    border-radius: 20px;
    padding: 24px;
}

/* Transitions */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

/* Mobile Specific */
@media (max-width: 960px) {
    .brand-panel {
        min-height: 250px;
        padding: 40px 20px !important;
    }
    .logo-box {
        width: 56px; height: 56px;
    }
}
</style>
