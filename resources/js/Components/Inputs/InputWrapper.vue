<script setup>
import Icon from '@/Components/Elements/Icon.vue'

defineProps({
	type: String,
	id: String,
	label: String,
	labelNote: String,
	error: String,
	horizontal: Boolean,
	full: Boolean,
	tooltip: [Object, String]
})
</script>

<template>
	<div class="line input-wrapper" :class="[`input-${type}-wrapper`, {'input-full': full, 'input-horizontal': horizontal}]">
		<component :is="id ? 'label' : 'div'" v-if="label" class="input-label" :for="id || null">{{ label }}<span v-if="labelNote" class="text-light input-label-note">{{ labelNote }}</span></component>
		<div class="input-outer flex ai-c" :class="`input-${type}-outer`">
			<div class="input-inner" :class="[`input-${type}-inner`]">
				<slot></slot>
			</div>
			<Icon v-if="tooltip" v-tooltip.touch="tooltip" class="input-tooltip" name="circle-info" />
		</div>
		<div v-if="error && error.trim()" class="input-note input-note-error color-error">{{ error }}</div>
		<div v-if="$slots.note" class="input-note input-note-info"><slot name="note"></slot></div>
	</div>
</template>