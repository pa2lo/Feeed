<script setup>
import { ref } from 'vue'

import Icon from './Icon.vue'

const props = defineProps({
	title: String,
	open: Boolean,
	pre: Boolean
})

const isOpen = ref(props.open ? true : false)
</script>

<template>
	<div class="details" :class="{open: isOpen}">
		<div class="details-title flex ai-c isClickable" @click="isOpen = !isOpen">
			<slot name="trigger">
				<span class="details-title-text">{{ title }}</span>
			</slot>
			<Icon name="down" class="details-title-chevron ml-a" />
		</div>
		<div class="details-content">
			<component :is="pre ? 'pre' : 'div'" class="details-inner">
				<slot />
			</component>
		</div>
	</div>
</template>