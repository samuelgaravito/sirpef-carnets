<script lang="ts" setup>


const props = defineProps<{
    title: string,
    value: string,
}>()


const checkIcon = () => {
    if(props.title == 'Aceptados Externos') return 'person-circle-check'
    else if(props.title == 'Rechazados Externos') return "person-circle-xmark"
    else if(props.title == 'Pendientes Externos') return 'person-circle-exclamation'
    else if(props.title == 'Aceptados Presencial') return 'person-cane'
    else if(props.title == 'Aceptados Correo') return 'envelope-circle-check'
}

const setBackground = () => {
    if(props.title == 'Aceptados Externos') return 'bg-[#283B8F]'
    else if(props.title == 'Rechazados Externos') return "bg-[#C80036]"
    else if(props.title == 'Pendientes Externos') return 'bg-[#EC8A0A]'
    else if(props.title == 'Aceptados Presencial') return 'bg-[#088408]'
    else if(props.title == 'Aceptados Correo') return 'bg-[#4E85C8]'
}


</script>

<template>
    <div class="flex items-center justify-center bg-white border rounded-xl overflow-hidden shadow">
        <div :class="setBackground()"  class="p-4 h-full flex items-center w-[50%]">
          <font-awesome-icon :icon="checkIcon()" class="block scale-[1.4] mx-auto text-[#FFFF] h-[60px]" />
        </div>
        <div class="px-4 text-gray-700 text-center w-[50%] capitalize">
            <h3 class="text-xl font-bold tracking-wider">{{ title }}</h3>
            <p class="text-3xl mt-2">{{value}}</p>
        </div>
    </div>
</template>