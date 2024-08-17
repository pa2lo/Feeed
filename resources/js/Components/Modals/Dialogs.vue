<script setup>
import Dialog from './Dialog.vue'

defineProps({
	dialogStore: Array
})
</script>

<template>
	<Dialog v-for="dialog in dialogStore" :key="dialog.id" v-bind="dialog" />
</template>