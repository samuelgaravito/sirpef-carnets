<script lang="ts" setup>
import CardDashboard from './cardDashboard.vue';


const props = defineProps<{
  items: any
}>()

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4 w-full md:h-full mx-auto xl:grid-cols-5 sm:px-8">
    <CardDashboard :title="item[0]" :value="item[1]" v-for="item in items"/>
  </div>
</template>