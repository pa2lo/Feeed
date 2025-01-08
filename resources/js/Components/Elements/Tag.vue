<script setup>
import Icon from './Icon.vue'

defineProps({
	icon: String,
	clearable: Boolean,
	color: {
		type: String,
		default: props => props.clearable ? 'link' : 'heading'
	},
	solid: Boolean
})
</script>

<template>
	<span class="tag" :class="[`tag-${color}`, `tag-${solid ? 'solid' : 'light'}`, { isClickable: clearable }]">
		<Icon v-if="icon" class="tag-icon" :name="icon" />
		<span class="tag-text">
			<slot />
		</span>
		<Icon v-if="clearable" class="tag-icon tag-x" name="x" />
	</span>
</template>