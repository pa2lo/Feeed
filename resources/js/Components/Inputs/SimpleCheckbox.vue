<script setup>
import { ref } from 'vue'
import { getUUID } from '@/Utils/helpers'

import Icon from '../Elements/Icon.vue'

const model = defineModel()

const props = defineProps({
	label: String,
	name: String,
	required: Boolean,
	disabled: Boolean,
	value: {
		default: null,
	},
	horizontal: Boolean,
	full: Boolean,
	tooltip: String
})

const inputID = getUUID('cb')

const checkbox = ref(null)

defineExpose({
	setIndeterminate: (v) => {checkbox.value.indeterminate = v}
})
</script>

<template>
	<div class="line input-inline flex" :class="{'input-full': full, 'input-horizontal-simple': horizontal}">
		<input
			:id="inputID"
			class="input input-cb aj-c"
			:class="{
				isDisabled: disabled
			}"
			:name="name"
			type="checkbox"
			:value="value"
			:required="required"
			:disabled="disabled"
			v-model="model"
			ref="checkbox"
		/>
		<label :for="inputID" v-if="$slots.default || label" class="input-title" :class="{'input-slot': $slots.default}"><slot>{{ label }}</slot></label>
		<Icon v-if="tooltip" v-tooltip.touch="tooltip" class="input-tooltip" name="circle-info" />
	</div>
</template>