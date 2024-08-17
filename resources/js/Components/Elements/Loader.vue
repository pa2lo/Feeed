<script setup>
defineProps({
	loading: Boolean,
	text: String
})
</script>

<template>
	<div class="loader-cont" :class="{isLoading: loading}">
		<slot />
		<Transition name="fade">
			<div v-if="loading" class="loader flex aj-c">
				<div class="loader-spinner likeH1"></div>
				<h3 v-if="text" class="loader-text">{{ text }}</h3>
			</div>
		</Transition>
	</div>
</template>