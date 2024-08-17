<script setup>
defineProps({
	show: Boolean
})
</script>

<template>
	<Transition name="slide-toggle" @after-leave="$emit('dismissed')">
		<div v-if="show" class="slide-toggle">
			<div class="slide-toggle-inner">
				<slot></slot>
			</div>
		</div>
	</Transition>
</template>