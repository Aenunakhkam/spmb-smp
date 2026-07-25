<template>
    <div class="mockup-section">
        <div class="mockup-title">{{ title }}</div>
        
        <div v-if="showInput" class="mockup-input-container">
            <div class="mockup-tags-wrapper">
                <div class="mockup-tags-box" @click="$refs.inputField.focus()">
                    <span class="mockup-tag" v-for="(item, i) in modelValue" :key="i">
                        {{ item.name }} <span class="mockup-tag-close" @click.stop="removeItem(i)">x</span>
                    </span>
                    <input 
                        ref="inputField"
                        type="text" 
                        class="mockup-tag-input" 
                        placeholder="Ketik lalu Enter untuk menambah..." 
                        v-model="inputValue"
                        @keydown.enter.prevent="addItem"
                    >
                </div>
                <button class="mockup-btn-blue" @click.prevent="addItem">Ubah Opsi</button>
            </div>
            <div class="mockup-hint">Ketik lalu Enter untuk menambah. Klik (x) untuk menghapus.</div>
        </div>

        <table class="mockup-table" v-if="modelValue.length > 0">
            <thead v-if="hasHeader">
                <tr>
                    <th>Kondisi</th>
                    <th></th>
                    <th style="width: 150px;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in modelValue" :key="i">
                    <td style="width: 35%; font-weight: 500;">{{ item.name }}</td>
                    <td>
                        <input v-if="editIndex === i" v-model="editItemData.description" class="mockup-edit-input" placeholder="Tulis keterangan...">
                        <span v-else>{{ item.description }}</span>
                    </td>
                    <td style="width: 200px; text-align: right; white-space: nowrap;">
                        <button v-if="editIndex === i" class="mockup-btn-outline-green mr-1" @click.prevent="saveEdit(i)">
                            <v-icon size="14" class="mr-1">mdi-check</v-icon> Simpan
                        </button>
                        <button v-else class="mockup-btn-outline-blue mr-1" @click.prevent="startEdit(i)">
                            <v-icon size="14" class="mr-1">mdi-pencil</v-icon> Ubah
                        </button>
                        <button class="mockup-btn-outline-red" @click.prevent="removeItem(i)">
                            <v-icon size="14" class="mr-1">mdi-delete</v-icon> Hapus
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    showInput: {
        type: Boolean,
        default: false,
    },
    hasHeader: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['update:modelValue']);

const inputValue = ref('');
const editIndex = ref(-1);
const editItemData = ref({ name: '', description: '' });

const addItem = () => {
    if (inputValue.value.trim()) {
        const newValue = [...props.modelValue, { name: inputValue.value.trim(), description: '' }];
        emit('update:modelValue', newValue);
        inputValue.value = '';
    }
};

const removeItem = (index) => {
    const newValue = [...props.modelValue];
    newValue.splice(index, 1);
    emit('update:modelValue', newValue);
};

const startEdit = (index) => {
    editIndex.value = index;
    editItemData.value = { ...props.modelValue[index] };
};

const saveEdit = (index) => {
    const newValue = [...props.modelValue];
    newValue[index] = { ...editItemData.value };
    emit('update:modelValue', newValue);
    editIndex.value = -1;
};
</script>

<style scoped>
.mockup-section {
    border-bottom: 1px solid #d1d5db;
}
.mockup-section:last-child {
    border-bottom: none;
}
.mockup-title {
    font-size: 16px;
    font-weight: 700;
    color: #212529;
    padding: 16px 16px 12px;
}
.mockup-input-container {
    padding: 0 16px 12px;
}
.mockup-tags-wrapper {
    display: flex;
    gap: 8px;
    align-items: flex-start;
}
.mockup-tags-box {
    flex-grow: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 6px 8px;
    background: white;
    cursor: text;
}
.mockup-tag {
    background-color: #e9ecef;
    color: #495057;
    font-size: 13px;
    padding: 2px 8px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mockup-tag-close {
    cursor: pointer;
    font-weight: bold;
    color: #6c757d;
}
.mockup-tag-close:hover {
    color: #dc3545;
}
.mockup-tag-input {
    border: none;
    outline: none;
    font-size: 13px;
    flex-grow: 1;
    min-width: 100px;
    background: transparent;
}
.mockup-btn-blue {
    background-color: #0d6efd;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 13px;
    cursor: pointer;
    font-weight: 500;
    white-space: nowrap;
    align-self: flex-start;
}
.mockup-btn-blue:hover {
    background-color: #0b5ed7;
}
.mockup-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 6px;
}
.mockup-table {
    width: 100%;
    border-collapse: collapse;
}
.mockup-table th {
    text-align: left;
    font-size: 14px;
    color: #212529;
    padding: 8px 16px;
    border-bottom: 1px solid #dee2e6;
    font-weight: 700;
}
.mockup-table td {
    padding: 12px 16px;
    border-top: 1px solid #dee2e6;
    font-size: 14px;
    color: #212529;
}
.mockup-edit-input {
    width: 100%;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
    outline: none;
}
.mockup-edit-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
}
.mockup-btn-outline-blue {
    border: 1px solid #0d6efd;
    color: #0d6efd;
    background: white;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
}
.mockup-btn-outline-blue:hover {
    background: #0d6efd;
    color: white;
}
.mockup-btn-outline-red {
    border: 1px solid #dc3545;
    color: #dc3545;
    background: white;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
}
.mockup-btn-outline-red:hover {
    background: #dc3545;
    color: white;
}
.mockup-btn-outline-green {
    border: 1px solid #198754;
    color: #198754;
    background: white;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
}
.mockup-btn-outline-green:hover {
    background: #198754;
    color: white;
}
</style>
