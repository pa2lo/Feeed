<script setup>
import { onMounted } from 'vue'

defineProps({
	name: String,
	id: String,
	icon: String,
	disabled: Boolean
})

const emit = defineEmits(['mounted'])

onMounted(() => {
	emit('mounted')
})
</script>

<template>
	<div class="tab-content">
		<slot />
	</div>
</template>