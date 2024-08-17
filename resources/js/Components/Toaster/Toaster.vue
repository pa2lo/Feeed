<script setup>
import Toast from './Toast.vue'
defineProps({
	toastStore: Array
})
</script>

<template>
	<TransitionGroup name="toast">
		<Toast v-for="toast in toastStore" :key="toast.id" v-bind="toast" />
	</TransitionGroup>
</template>