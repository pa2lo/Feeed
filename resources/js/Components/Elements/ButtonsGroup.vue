<script setup>
const props = defineProps({
	align: {
		type: String,
		default: 'left'
	},
	full: Boolean
})
</script>

<template>
	<div class="line buttons-group flex" :class="[`buttons-group-${align}`, {isFull: full}]">
		<slot />
	</div>
</template>