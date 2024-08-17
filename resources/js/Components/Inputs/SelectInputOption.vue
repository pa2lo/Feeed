<script setup>
import Icon from '../Elements/Icon.vue'

defineProps({
	checked: Boolean,
	disabled: Boolean,
	current: Boolean,
	value: [String, Number]
})
</script>

<template>
	<li
		class="dropdown-link flex ai-c clickable"
		:class="{
			isChecked: checked,
			isDisabled: disabled,
			current: current
		}"
		role="option"
		:aria-selected="checked.toString()"
	>
		<span class="dropdown-link-text grow">
			<slot />
		</span>
		<Icon v-if="checked" class="dropdown-link-icon" name="check" />
	</li>
</template>