<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import bannerImg from '@/assets/banner-siswa.png';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    totalRegistrants: Number,
    byInterest: Array,
});

const chartData = computed(() => {
    const labels = props.byInterest.map(item => item.major || 'Belum Memilih');
    const data = props.byInterest.map(item => item.count);
    
    return {
        labels,
        datasets: [
            {
                backgroundColor: ['#1B5E20', '#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800'],
                data
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: { font: { family: 'Plus Jakarta Sans', size: 12 } }
        }
    },
    cutout: '70%',
};

const isScrolled = ref(false);
const mobileMenu = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const infographics = [
    { title: '100% Online', desc: 'Pendaftaran & pantau hasil dari mana saja.', icon: 'mdi-devices', color: '#1B5E20' },
    { title: 'Transparan', desc: 'Sistem ranking otomatis yang terbuka.', icon: 'mdi-eye-check-outline', color: '#2E7D32' },
    { title: 'Paperless', desc: 'Unggah berkas digital tanpa fotokopi.', icon: 'mdi-file-document-outline', color: '#388E3C' },
    { title: 'Bantuan CS', desc: 'Layanan informasi cepat via WhatsApp.', icon: 'mdi-whatsapp', color: '#43A047' }
];

const steps = [
    { n: '01', title: 'Registrasi', sub: 'Input NISN & Nama' },
    { n: '02', title: 'Biodata', sub: 'Data Diri & Ortu' },
    { n: '03', title: 'Nilai', sub: 'Rapor Kelas 6' },
    { n: '04', title: 'Berkas', sub: 'Upload Dokumen' },
    { n: '05', title: 'Hasil', sub: 'Pantau Pengumuman' }
];
</script>

<template>
    <Head title="Beranda" />
    <v-app class="app-refined">
        
        <!-- Highly Responsive Navbar -->
        <v-app-bar 
            :elevation="isScrolled ? 2 : 0" 
            :color="isScrolled ? 'white' : 'transparent'"
            :class="{ 'nav-transition': true, 'border-b': isScrolled }"
            height="80"
            app
        >
            <v-container class="d-flex align-center h-100 pa-2 pa-sm-4">
                <div class="brand-container d-flex align-center">
                    <div class="logo-circle-bg mr-3">
                        <v-icon icon="mdi-school" color="white" size="24"></v-icon>
                    </div>
                    <div class="brand-text" style="min-width: 0;">
                        <div class="text-subtitle-1 font-weight-black line-height-tight color-main text-truncate">SPMB <span class="text-primary">DIGITAL</span></div>
                        <div class="text-tiny font-weight-bold opacity-60 text-uppercase text-truncate" style="max-width: 220px;">SMP BUSTANUL ULUM NU JATIROKEH</div>
                    </div>
                </div>
                
                <v-spacer></v-spacer>
                
                <!-- Desktop Menu -->
                <div class="d-none d-lg-flex align-center">
                    <nav class="nav-links-desktop mr-6">
                        <a href="#info" class="mx-4 nav-item">Informasi</a>
                        <a href="#statistik" class="mx-4 nav-item">Statistik</a>
                        <a href="#alur" class="mx-4 nav-item">Alur</a>
                        <a href="#faq" class="mx-4 nav-item">Bantuan</a>
                    </nav>
                    <v-btn 
                        variant="outlined" 
                        color="primary" 
                        class="rounded-lg mr-3 font-weight-bold text-capitalize" 
                        @click="router.get(route('check-status'))"
                    >
                        Portal Login Siswa
                    </v-btn>
                    <v-btn 
                        color="primary" 
                        class="rounded-lg font-weight-black text-capitalize px-6 elevation-0" 
                        @click="router.get(route('register.start'))"
                    >
                        Daftar
                    </v-btn>
                </div>

                <!-- Mobile Toggle - always visible on small screens -->
                <v-btn
                    :icon="mobileMenu ? 'mdi-close' : 'mdi-menu'"
                    color="primary"
                    class="d-flex d-lg-none"
                    variant="text"
                    size="large"
                    @click="mobileMenu = !mobileMenu"
                    style="min-width:48px"
                ></v-btn>
            </v-container>
        </v-app-bar>

        <!-- Mobile Drawer -->
        <v-navigation-drawer v-model="mobileMenu" location="right" temporary width="300">
            <v-list class="pa-6">
                <v-list-item class="px-0 mb-4">
                    <div class="text-h6 font-weight-black color-main">Menu Navigasi</div>
                </v-list-item>
                <v-list-item link href="#info" @click="mobileMenu = false" class="rounded-lg mb-2">Informasi</v-list-item>
                <v-list-item link href="#statistik" @click="mobileMenu = false" class="rounded-lg mb-2">Statistik</v-list-item>
                <v-list-item link href="#alur" @click="mobileMenu = false" class="rounded-lg mb-2">Alur</v-list-item>
                <v-list-item link href="#faq" @click="mobileMenu = false" class="rounded-lg mb-2">Bantuan</v-list-item>
                <v-divider class="my-4"></v-divider>
                <v-btn block color="primary" variant="outlined" class="mb-3 rounded-lg font-weight-bold" @click="router.get(route('check-status'))">Cek Status</v-btn>
                <v-btn block color="primary" class="rounded-lg font-weight-black" @click="router.get(route('register.start'))">Daftar Sekarang</v-btn>
            </v-list>
        </v-navigation-drawer>

        <v-main class="pa-0">
            <!-- Fluid Hero Section -->
            <section class="hero-fluid">
                <v-container class="h-100">
                    <v-row align="center" class="min-vh-80 py-16">
                        <v-col cols="12" lg="7" class="text-center text-lg-left">
                            <v-chip color="primary" variant="tonal" class="mb-6 font-weight-bold px-4" size="small">
                                PENERIMAAN PESERTA DIDIK BARU 2026/2027
                            </v-chip>
                            <h1 class="hero-display-text mb-6">
                                Masa Depan Gemilang di <br class="hidden-sm-and-down"/>
                                <span class="text-primary">SMP BUSTANUL ULUM.</span>
                            </h1>
                            <p class="hero-subtext mb-10 mx-auto mx-lg-0">
                                Bergabunglah dengan institusi pendidikan yang mengedepankan IMTAK, IPTEK, dan karakter Ahlussunnah Wal Jamaah NU Jatirokeh.
                            </p>
                            <div class="d-flex flex-column flex-sm-row justify-center justify-lg-start ga-4">
                                <v-btn size="x-large" color="primary" class="rounded-xl px-10 font-weight-black py-4" @click="router.get(route('register.start'))">
                                    Mulai Pendaftaran
                                </v-btn>
                                <v-btn size="x-large" variant="outlined" color="primary" class="rounded-xl px-10 font-weight-black border-2" href="#alur">
                                    Lihat Alur PPDB
                                </v-btn>
                            </div>
                        </v-col>
                        <v-col cols="12" lg="5" class="d-none d-lg-block">
                            <div class="hero-visual-refined">
                                <div class="blob-bg"></div>
                                <!-- Main Hero Image: Representing SMP Students -->
                                <v-img 
                                    :src="bannerImg" 
                                    cover 
                                    class="hero-img-refined rounded-pill-custom elevation-12 bg-white"
                                ></v-img>
                                
                                <!-- Floating Accreditation Box with Student Icon -->
                                <div class="hero-stat-box elevation-8 animate-y">
                                    <div class="d-flex align-center ga-4">
                                        <div class="student-mini-illust">
                                            <v-icon icon="mdi-account-school" size="32" color="primary"></v-icon>
                                        </div>
                                        <div class="text-left">
                                            <div class="text-h4 font-weight-black text-primary line-height-1">A</div>
                                            <div class="text-tiny font-weight-bold opacity-60">AKREDITASI</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Secondary Floating Illustration/Badge -->
                                <div class="floating-badge-v2 elevation-6 animate-x">
                                    <v-avatar color="secondary" size="40" class="mr-3">
                                        <v-icon icon="mdi-star" color="primary" size="20"></v-icon>
                                    </v-avatar>
                                    <span class="text-caption font-weight-black">Sekolah Juara</span>
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Grid-based Infographics -->
            <section id="info" class="py-16 bg-white border-y">
                <v-container>
                    <v-row>
                        <v-col v-for="(info, i) in infographics" :key="i" cols="12" sm="6" lg="3">
                            <v-card class="info-card-refined pa-8 rounded-xl h-100 text-center text-sm-left" flat border>
                                <v-icon :icon="info.icon" :color="info.color" size="40" class="mb-6"></v-icon>
                                <h3 class="text-h6 font-weight-black mb-3 color-main">{{ info.title }}</h3>
                                <p class="text-body-2 text-grey-darken-1 leading-relaxed">{{ info.desc }}</p>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Statistik Pendaftar Infographic -->
            <section id="statistik" class="py-16 bg-grey-lighten-5 overflow-hidden">
                <v-container>
                    <div class="text-center mb-12">
                        <h2 class="text-h3 font-weight-black color-main">Statistik Pendaftar</h2>
                        <p class="text-grey-darken-1 mt-2">Data real-time pendaftar pada tahun ajaran ini.</p>
                    </div>

                    <v-row justify="center" align="center">
                        <v-col cols="12" md="4">
                            <v-card class="pa-8 rounded-xl elevation-4 border-t-lg border-primary bg-white text-center h-100" border>
                                <v-icon icon="mdi-account-group" size="64" color="primary" class="mb-4"></v-icon>
                                <div class="text-h2 font-weight-black color-main mb-2">{{ totalRegistrants }}</div>
                                <div class="text-subtitle-1 font-weight-bold text-grey-darken-1">Total Pendaftar</div>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="8">
                            <v-card class="pa-6 rounded-xl elevation-2 h-100 border-t-md border-secondary d-flex align-center justify-center" border>
                                <div style="height: 300px; width: 100%;">
                                    <Doughnut v-if="byInterest && byInterest.length > 0" :data="chartData" :options="chartOptions" />
                                    <div v-else class="h-100 d-flex align-center justify-center text-grey">Belum ada data pendaftar</div>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Responsive Step Flow -->
            <section id="alur" class="py-16 bg-grey-lighten-4 overflow-hidden">
                <v-container>
                    <div class="text-center mb-16">
                        <h2 class="text-h3 font-weight-black color-main">Alur Pendaftaran</h2>
                        <p class="text-grey-darken-1 mt-2">Sistematis, Mudah, dan Cepat.</p>
                    </div>

                    <div class="steps-wrapper-refined">
                        <v-row class="position-relative">
                            <div class="step-connector hidden-md-and-down"></div>
                            <v-col v-for="(step, i) in steps" :key="i" cols="12" md="" class="text-center mb-10 mb-md-0">
                                <div class="step-circle-refined mb-6 mx-auto elevation-4">
                                    <span>{{ step.n }}</span>
                                </div>
                                <h4 class="text-h6 font-weight-black mb-1 color-main">{{ step.title }}</h4>
                                <p class="text-caption font-weight-bold text-grey-darken-1">{{ step.sub }}</p>
                            </v-col>
                        </v-row>
                    </div>
                </v-container>
            </section>

            <!-- Structured FAQ & Contact -->
            <section id="faq" class="py-16 bg-white">
                <v-container>
                    <v-row align="start">
                        <v-col cols="12" lg="5" class="mb-12 mb-lg-0">
                            <h2 class="text-h3 font-weight-black color-main mb-6 leading-tight">Butuh <br class="hidden-md-and-down"/>Bantuan?</h2>
                            <p class="text-body-1 text-grey-darken-1 mb-8">Tim panitia siap membantu Anda melalui kanal komunikasi resmi kami.</p>
                            
                            <div class="ga-4 d-flex flex-column">
                                <v-card class="pa-5 rounded-xl border-s-lg border-primary elevation-2" border>
                                    <div class="d-flex align-center">
                                        <v-avatar color="green-lighten-5" class="mr-4">
                                            <v-icon icon="mdi-whatsapp" color="primary"></v-icon>
                                        </v-avatar>
                                        <div>
                                            <div class="text-caption font-weight-bold opacity-60">WHATSAPP PANITIA</div>
                                            <div class="text-h6 font-weight-black color-main">0812-3456-7890</div>
                                        </div>
                                    </div>
                                </v-card>
                                <v-card class="pa-5 rounded-xl border-s-lg border-info elevation-2" border>
                                    <div class="d-flex align-center">
                                        <v-avatar color="blue-lighten-5" class="mr-4">
                                            <v-icon icon="mdi-email-outline" color="info"></v-icon>
                                        </v-avatar>
                                        <div>
                                            <div class="text-caption font-weight-bold opacity-60">EMAIL RESMI</div>
                                            <div class="text-h6 font-weight-black color-main">info@smpbu.sch.id</div>
                                        </div>
                                    </div>
                                </v-card>
                            </div>
                        </v-col>
                        
                        <v-col cols="12" lg="7">
                            <v-expansion-panels variant="accordion" class="rounded-xl overflow-hidden elevation-0 border">
                                <v-expansion-panel
                                    v-for="n in 3"
                                    :key="n"
                                    class="py-2"
                                >
                                    <v-expansion-panel-title class="font-weight-black color-main">
                                        {{ n === 1 ? 'Bagaimana cara konfirmasi pembayaran?' : n === 2 ? 'Apakah berkas harus diantar ke sekolah?' : 'Apa yang harus dilakukan setelah mendaftar?' }}
                                    </v-expansion-panel-title>
                                    <v-expansion-panel-text class="text-grey-darken-1">
                                        {{ n === 1 ? 'Sistem pendaftaran ini gratis untuk tahap awal. Untuk biaya administrasi lainnya akan diinfokan setelah verifikasi data awal selesai.' : n === 2 ? 'Tidak perlu. Cukup unggah scan dokumen asli (format JPG/PDF) ke sistem ini. Fisik dokumen dibawa saat tes wawancara.' : 'Silakan simpan Nomor Pendaftaran dan Kode Akses Anda untuk memantau status verifikasi berkas oleh admin secara berkala.' }}
                                    </v-expansion-panel-text>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Full-Width Responsive CTA -->
            <v-container class="py-16">
                <v-card color="primary" class="rounded-pill-mobile pa-8 pa-md-16 text-center text-white elevation-12 overflow-hidden position-relative border-0">
                    <div class="cta-mesh"></div>
                    <div class="position-relative" style="z-index: 2">
                        <h2 class="text-h3 text-md-h2 font-weight-black mb-6 leading-tight">Mulai Pendaftaran Anda</h2>
                        <p class="text-h6 opacity-80 mb-12 font-weight-medium max-width-600 mx-auto">Jadilah bagian dari keluarga besar SMP BUSTANUL ULUM NU JATIROKEH hari ini.</p>
                        <v-btn color="white" size="x-large" class="rounded-xl px-12 font-weight-black color-main py-4 h-auto" @click="router.get(route('register.start'))">
                            DAFTAR ONLINE SEKARANG
                        </v-btn>
                    </div>
                </v-card>
            </v-container>

            <!-- Structured Professional Footer -->
            <footer class="footer-refined bg-white border-t">
                <v-container class="py-16">
                    <v-row>
                        <v-col cols="12" md="5" class="mb-12">
                            <div class="text-h5 font-weight-black color-main mb-6">SMP <span class="text-primary">BUSTANUL ULUM</span></div>
                            <p class="text-body-1 text-grey-darken-1 max-width-400 mb-8">Lembaga pendidikan yang berkomitmen mencetak generasi Qur'ani yang cerdas di era digital.</p>
                            <div class="d-flex ga-4">
                                <v-btn icon="mdi-facebook" variant="tonal" size="small" color="primary"></v-btn>
                                <v-btn icon="mdi-instagram" variant="tonal" size="small" color="primary"></v-btn>
                                <v-btn icon="mdi-youtube" variant="tonal" size="small" color="primary"></v-btn>
                            </div>
                        </v-col>
                        
                        <v-col cols="6" sm="4" md="2" class="mb-12">
                            <div class="footer-title-refined">Akses Cepat</div>
                            <div class="d-flex flex-column ga-4">
                                <a href="#" @click.prevent="router.get(route('register.start'))" class="footer-link-refined">Formulir Baru</a>
                                <a href="#" @click.prevent="router.get(route('check-status'))" class="footer-link-refined">Cek Status</a>
                                <a href="#" @click.prevent="router.get(route('login'))" class="footer-link-refined">Login Panitia</a>
                            </div>
                        </v-col>

                        <v-col cols="6" sm="4" md="2" class="mb-12">
                            <div class="footer-title-refined">Informasi</div>
                            <div class="d-flex flex-column ga-4">
                                <a href="#" class="footer-link-refined">Profil Sekolah</a>
                                <a href="#" class="footer-link-refined">Fasilitas</a>
                                <a href="#" class="footer-link-refined">Kontak</a>
                            </div>
                        </v-col>
                        
                        <v-col cols="12" sm="4" md="3" class="mb-12">
                            <div class="footer-title-refined">Sekretariat</div>
                            <p class="text-body-2 text-grey-darken-1 leading-relaxed">
                                Jatirokeh, Kec. Songgom, <br/>
                                Kabupaten Brebes, <br/>
                                Jawa Tengah 52266
                            </p>
                        </v-col>
                    </v-row>
                    
                    <v-divider class="my-8"></v-divider>
                    
                    <div class="d-flex flex-wrap justify-space-between align-center text-caption font-weight-bold text-grey">
                        <div>© 2026 SMP BUSTANUL ULUM NU JATIROKEH. All Rights Reserved.</div>
                        <div class="d-flex ga-6 mt-4 mt-sm-0">
                            <a href="#" class="text-grey text-decoration-none hover-primary">PRIVACY</a>
                            <a href="#" class="text-grey text-decoration-none hover-primary">TERMS</a>
                        </div>
                    </div>
                </v-container>
            </footer>
        </v-main>
    </v-app>
</template>

<style scoped>
/* Reset & Precision Typography */
.app-refined {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    color: #1a1a1a;
    background-color: #fcfcfc;
}

.color-main { color: #0a2a12 !important; }
.line-height-tight { line-height: 1.2; }
.text-tiny { font-size: 0.65rem; letter-spacing: 1px; }
.max-width-600 { max-width: 600px; }

/* Responsive Navbar */
.nav-transition {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.logo-circle-bg {
    width: 40px; height: 40px;
    background: #1B5E20;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}

.nav-item {
    text-decoration: none;
    color: #0a2a12;
    font-weight: 700;
    font-size: 0.95rem;
    transition: 0.3s;
    opacity: 0.8;
}

.nav-item:hover { color: #1B5E20; opacity: 1; }

/* Refined Hero */
.hero-fluid {
    min-height: 100vh;
    background-color: #fdfdfd;
    background-image: radial-gradient(#1B5E20 0.5px, transparent 0.5px);
    background-size: 40px 40px;
    display: flex;
    align-items: center;
}

.min-vh-80 { min-height: 80vh; }

.hero-display-text {
    font-size: clamp(2.8rem, 8vw, 4.8rem);
    line-height: 1;
    font-weight: 900;
    letter-spacing: -3px;
    color: #0a2a12;
}

.hero-subtext {
    font-size: 1.25rem;
    color: #555;
    max-width: 650px;
    line-height: 1.6;
    font-weight: 500;
}

.hero-visual-refined {
    position: relative;
    padding: 30px;
}

.rounded-pill-custom {
    border-radius: 80px 40px 160px 40px !important;
}

.hero-stat-box {
    position: absolute;
    bottom: 10%; left: -70px;
    background: white;
    padding: 16px 24px;
    border-radius: 20px;
    text-align: center;
    z-index: 5;
    border: 1px solid rgba(0,0,0,0.05);
}

.floating-badge-v2 {
    position: absolute;
    top: 5%; right: -40px;
    background: white;
    padding: 12px 20px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    z-index: 5;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.student-mini-illust {
    width: 48px;
    height: 48px;
    background: #f0f7f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.animate-y {
    animation: float-y 5s ease-in-out infinite;
}

.animate-x {
    animation: float-x 6s ease-in-out infinite;
}

@keyframes float-y {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

@keyframes float-x {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(10px); }
}

/* Info Cards */
.info-card-refined {
    transition: all 0.3s ease;
}

.info-card-refined:hover {
    transform: translateY(-8px);
    border-color: #1B5E20 !important;
    background: #f8fcf8 !important;
}

/* Steps Refined */
.step-circle-refined {
    width: 64px; height: 64px;
    background: #1B5E20;
    color: white;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    font-weight: 900;
    position: relative;
    z-index: 2;
    transition: 0.3s;
}

.step-circle-refined:hover { transform: rotate(8deg) scale(1.1); }

.step-connector {
    position: absolute;
    top: 32px; left: 10%; right: 10%;
    height: 2px;
    background: #d1d1d1;
    z-index: 1;
}

/* CTA Mesh & Responsive Shape */
.cta-mesh {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
    background-size: 25px 25px;
    opacity: 0.3;
}

.rounded-pill-mobile {
    border-radius: 40px !important;
}

@media (min-width: 960px) {
    .rounded-pill-mobile { border-radius: 100px !important; }
}

/* Footer Refined */
.footer-title-refined {
    font-weight: 900;
    font-size: 0.8rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #0a2a12;
    margin-bottom: 25px;
}

.footer-link-refined {
    text-decoration: none;
    color: #666;
    font-weight: 700;
    transition: 0.3s;
    font-size: 0.95rem;
}

.footer-link-refined:hover { color: #1B5E20; transform: translateX(5px); }

/* Mobile Specifics */
@media (max-width: 600px) {
    .hero-display-text { font-size: 2.5rem; letter-spacing: -1.5px; }
    .hero-fluid { padding-top: 100px; }
    .hero-subtext { font-size: 1.1rem; }
}
</style>
