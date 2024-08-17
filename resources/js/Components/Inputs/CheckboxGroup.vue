<script setup>
import { getUUID } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	error: String,
	options: Array,
	vertical: Boolean
})

const inputName = props.name || getUUID('cb-group')
</script>

<template>
	<InputWrapper type="cb-group" :error="error">
		<label v-for="item in options" class="input-inline flex" :class="{isFull: vertical}">
			<input
				class="input input-cb aj-c"
				:class="{
					isDisabled: disabled || item.disabled
				}"
				:name="inputName"
				type="checkbox"
				:value="item.value"
				:required="required"
				:disabled="disabled || item.disabled"
				v-model="model"
			/>
			<span class="input-title" v-html="item.title"></span>
		</label>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>