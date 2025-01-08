<script setup>
import { getUUID } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'
import Icon from '../Elements/Icon.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	readOnly: Boolean,
	error: String,
	options: Array,
	solid: Boolean,
	sameWidth: Boolean
})

const inputName = props.name || getUUID('radio-buttons')
</script>

<template>
	<InputWrapper type="radio-buttons" :error="error">
		<div class="input-el input-radio-buttons-el flex" :class="{isDisabled: disabled, wError: error, isReadOnly: readOnly, buttonsSolid: solid, isSameWidth: sameWidth}">
			<label v-for="item in options" class="input-radio-button isLabel flex" :class="[`input-radio-${item.color ?? 'link'}`, {isSelected: item?.value != undefined ? item.value == model : item == model, isDisabled: !disabled && item.disabled, isReadOnly: disabled || readOnly}]" v-tooltip="!item.disabled && !disabled ? item.tooltip : ''">
				<input
					class="sr-only"
					:class="{
						isDisabled: disabled || item.disabled
					}"
					:name="inputName"
					type="radio"
					:value="item.value ?? item"
					v-model="model"
					:required="required"
					:disabled="readOnly || disabled || item.disabled"
				/>
				<Icon v-if="item.icon" :name="item.icon" class="input-radio-button-icon" />
				<span v-if="!item.icon || item.title" class="input-radio-button-title" v-html="item.title ?? item"></span>
			</label>
		</div>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>