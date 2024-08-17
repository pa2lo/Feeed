<script setup>
import { ref } from 'vue'

import Icon from '@/Components/Elements/Icon.vue'
import SlideToggle from '@/Components/Elements/SlideToggle.vue'

defineProps({
	type: {
		type: String,
		default: 'info'
	},
	solid: Boolean,
	dismissible: {
		type: Boolean,
		default: false
	}
})

const isOpen = ref(true)

const iconMap = {
	'info': 'circle-info',
	'error': 'circle-x',
	'success': 'circle-check',
	'warning': 'circle-alert'
}
</script>

<template>
	<component :is="dismissible ? SlideToggle : 'div'" class="line" :show="dismissible ? isOpen : null">
		<div class="message flex ai-c" :class="[`message-${type}`, `message-${solid ? 'solid' : 'light'}`]">
			<Icon class="message-ico" :name="iconMap[type]" />
			<div class="message-text-outer flex ai-c">
				<div class="message-text">
					<slot />
				</div>
				<div v-if="$slots.buttons" class="message-text-buttons">
					<slot name="buttons" />
				</div>
			</div>
			<button v-if="dismissible" type="button" class="message-x" @click.prevent="isOpen = false" title="Dismiss">
				<Icon name="x" />
			</button>
		</div>
	</component>
</template>