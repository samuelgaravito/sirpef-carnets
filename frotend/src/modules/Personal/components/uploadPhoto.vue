<script lang="ts" setup>
import { ref } from 'vue';
import { storePhotoPersona } from '../services';
import { alerta } from '@/utils/alert';

const fileInput = ref(null);
const selectedFile = ref(null);
const previewUrl = ref<any>('');
const dragOver = ref(false);
const isUploading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const props = defineProps<{
    persona: any
}>()

const emit = defineEmits(["clear"])

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    validateAndSetFile(file);
};

const handleDrop = (event) => {
    dragOver.value = false;
    const file = event.dataTransfer.files[0];
    validateAndSetFile(file);
};

const validateAndSetFile = (file) => {
    errorMessage.value = '';
    successMessage.value = '';

    if (!file) return;

    if (!file.type.match('image.*')) {
        errorMessage.value = 'Solo se permiten archivos de imagen';
        return;
    }

    if (file.size > 5 * 1024 * 1024) { // 5MB
        errorMessage.value = 'El archivo es demasiado grande (máx. 5MB)';
        return;
    }

    selectedFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        previewUrl.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const removeImage = () => {
    selectedFile.value = null;
    previewUrl.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const uploadFile = async () => {
    if (!selectedFile.value) return;

    isUploading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const formData = new FormData();
        formData.append('foto', selectedFile.value);
        const response = await storePhotoPersona(formData, props.persona)
        alerta('Info', '¡Foto subida correctamente!', 'success')
        emit('clear')
    } catch (error) {
        console.error('Error al subir:', error);
        errorMessage.value = 'Error al subir la foto. Por favor, inténtalo de nuevo.';
    } finally {
        isUploading.value = false;
    }
};

const hidden = (e?: any) => {
    const target = e.target as HTMLElement
    if (e && target.tagName === "SECTION") emit('clear')
}

</script>

<template>
    <section @click="(e) => hidden(e as PointerEvent)"
        class="fixed bg-black bg-opacity-30 w-full h-full z-50 grid place-content-center top-0 left-0 cursor-pointer">
        <div class="max-w-md mx-auto p-6 bg-white cursor-default rounded-lg shadow-md" @click.stop>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Subir Foto</h2>


            <div v-if="previewUrl" class="mb-4">
                <h3 class="font-medium text-gray-700 mb-2">Vista previa:</h3>
                <div class="relative">
                    <img :src="previewUrl" alt="Preview"
                        class="rounded-lg w-full h-64 object-cover border border-gray-200">
                    <button @click="removeImage"
                        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-else @dragover.prevent="dragOver = true" @dragleave="dragOver = false" @drop.prevent="handleDrop"
                :class="{ 'border-blue-500 bg-blue-50': dragOver, 'border-gray-300': !dragOver }"
                class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer mb-4 transition-colors relative">
                <input type="file" ref="fileInput" @change="handleFileSelect" accept="image/*" class="absolute inset-0 opacity-0">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-gray-600">
                        <span class="text-blue-600 font-medium">Haz clic para subir</span> o arrastra y suelta
                    </p>
                    <p class="text-xs text-gray-500">Formatos soportados: JPEG, PNG (Max. 5MB)</p>
                </div>
            </div>

            <div v-if="selectedFile" class="bg-gray-50 p-3 rounded-lg mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-700 truncate">{{ selectedFile.name }}</p>
                        <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                        {{ selectedFile.type.split('/')[1].toUpperCase() }}
                    </span>
                </div>
            </div>

            <button @click="uploadFile" :disabled="!selectedFile || isUploading"
                :class="{ 'bg-blue-600 hover:bg-blue-700': selectedFile, 'bg-blue-300 cursor-not-allowed': !selectedFile }"
                class="w-full py-2 px-4 text-white rounded-md transition-colors flex items-center justify-center">
                <svg v-if="isUploading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ isUploading ? 'Subiendo...' : 'Subir Foto' }}
            </button>

            <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                {{ errorMessage }}
            </div>

            <div v-if="successMessage" class="mt-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                {{ successMessage }}
            </div>
        </div>
    </section>
</template>