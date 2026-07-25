<script setup>
import { ref, onMounted, defineExpose, defineEmits } from 'vue';

const props = defineProps({
    error: String,
});
const emit = defineEmits(['update:modelValue']);

const canvas = ref(null);
const code = ref('');
const userInput = ref('');

const generateCode = () => {
    code.value = Math.floor(10000 + Math.random() * 90000).toString(); // 5 digits
    drawCaptcha();
};

const drawCaptcha = () => {
    if (!canvas.value) return;
    const ctx = canvas.value.getContext('2d');
    const width = 160;
    const height = 50;
    
    // Clear
    ctx.clearRect(0, 0, width, height);
    
    // Background noise
    ctx.fillStyle = '#f3f4f6';
    ctx.fillRect(0, 0, width, height);
    
    // Draw dots
    for (let i = 0; i < 50; i++) {
        ctx.fillStyle = `rgba(0,0,0,${Math.random() * 0.2})`;
        ctx.beginPath();
        ctx.arc(Math.random() * width, Math.random() * height, Math.random() * 2, 0, Math.PI * 2);
        ctx.fill();
    }
    
    // Draw lines
    for (let i = 0; i < 4; i++) {
        ctx.strokeStyle = `rgba(0,0,0,${Math.random() * 0.3})`;
        ctx.beginPath();
        ctx.moveTo(Math.random() * width, Math.random() * height);
        ctx.lineTo(Math.random() * width, Math.random() * height);
        ctx.stroke();
    }
    
    // Draw text
    ctx.font = 'bold 36px "Courier New"';
    ctx.fillStyle = '#0f172a';
    ctx.textBaseline = 'middle';
    
    // Draw characters with slight rotation and offset
    for (let i = 0; i < code.value.length; i++) {
        ctx.save();
        ctx.translate(20 + (i * 28), 25);
        ctx.rotate((Math.random() - 0.5) * 0.4);
        ctx.fillText(code.value[i], -10, 0);
        ctx.restore();
    }
};

const validate = () => {
    return userInput.value === code.value;
};

const handleInput = () => {
    emit('update:modelValue', userInput.value);
};

onMounted(() => {
    generateCode();
});

defineExpose({
    validate,
    generateCode
});
</script>

<template>
    <div class="space-y-2 mb-6">
        <label class="block text-sm font-bold text-slate-700 mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">Kode Keamanan <span class="text-red-500">*</span></label>
        
        <div class="flex items-center gap-3 mb-2">
            <canvas ref="canvas" width="160" height="50" class="border border-slate-200 rounded-lg cursor-pointer bg-slate-50" @click="generateCode" title="Klik untuk memuat ulang"></canvas>
            <button type="button" @click="generateCode" class="p-3 text-slate-500 hover:text-blue-600 transition-colors bg-slate-100 hover:bg-blue-50 rounded-lg border border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
        
        <div>
            <input 
                type="text" 
                v-model="userInput" 
                @input="handleInput"
                placeholder="Masukkan kode keamanan" 
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700 outline-none"
                style="height: 48px; font-family: 'Plus Jakarta Sans', sans-serif;"
            >
            <p v-if="error" class="text-sm text-red-500 mt-1 font-medium">{{ error }}</p>
        </div>
    </div>
</template>
