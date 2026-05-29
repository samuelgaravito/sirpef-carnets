<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const videoElement = ref(null);
const isCameraActive = ref(false);
const photoTaken = ref(false);
const photoDataUrl = ref('');
let stream = null;

const startCamera = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });

        videoElement.value.srcObject = stream;
        isCameraActive.value = true;
        photoTaken.value = false;
    } catch (error) {
        console.error('Error al acceder a la cámara:', error);
        alert('No se pudo acceder a la cámara. Asegúrate de dar los permisos necesarios.');
        emit('clear')
    }
};

const takePhoto = () => {
    if (!isCameraActive.value) return;

    const canvas = document.createElement('canvas');
    const video = videoElement.value;

    // Ajustar el canvas al tamaño del video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Dibujar el frame actual del video en el canvas
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convertir a Data URL (formato JPEG)
    photoDataUrl.value = canvas.toDataURL('image/jpeg');
    photoTaken.value = true;
};

const stopCamera = () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        isCameraActive.value = false;

        if (videoElement.value) {
            videoElement.value.srcObject = null;
        }
    }
};

const downloadPhoto = () => {
    if (!photoDataUrl.value) return;

    const link = document.createElement('a');
    link.href = photoDataUrl.value;
    link.download = `foto-${new Date().toISOString().slice(0, 10)}.jpg`;
    link.click();
};

const emit = defineEmits(["clear"])

const hidden = (e?: any) => {
    const target = e.target as HTMLElement
    if (e && target.tagName === "SECTION") emit('clear')
}

onMounted(() => {
    startCamera()
})

onBeforeUnmount(() => {
    stopCamera();
});
</script>

<template>
    <section @click="(e) => hidden(e as PointerEvent)"
        class="fixed bg-black bg-opacity-30 w-full h-full z-50 grid place-content-center top-0 left-0 cursor-pointer">
        <div class="camera-container bg-white cursor-default" @click.stop>
            <h1>Cámara</h1>

            <div class="video-container">
                <video ref="videoElement" autoplay playsinline class="video-preview"></video>
            </div>

            <div class="controls">
                <button @click="takePhoto" :disabled="!isCameraActive">Tomar Foto</button>
            </div>

            <div v-if="photoTaken" class="photo-preview">
                <h2>Foto tomada:</h2>
                <img :src="photoDataUrl" alt="Foto capturada" class="captured-photo">
                <button @click="downloadPhoto" class="download-btn">Descargar Foto</button>
            </div>
        </div>
    </section>

</template>


<style scoped>
.camera-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
    font-family: Arial, sans-serif;
}

.video-container {
    margin: 20px 0;
    background-color: #000;
    border-radius: 8px;
    overflow: hidden;
}

.video-preview {
    width: 100%;
    max-height: 500px;
    display: block;
}

.controls {
    margin: 20px 0;
    display: flex;
    justify-content: center;
    gap: 10px;
}

button {
    padding: 10px 15px;
    background-color: #42b983;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #369f6b;
}

button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.photo-preview {
    margin-top: 30px;
    padding: 20px;
    border-top: 1px solid #eee;
}

.captured-photo {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 15px 0;
}

.download-btn {
    background-color: #2196F3;
}

.download-btn:hover {
    background-color: #0b7dda;
}
</style>