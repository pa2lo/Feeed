<script setup>
import { ref, onMounted } from 'vue'
import { getUUID } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'
import IcoButton from '../Elements/IcoButton.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	disabled: Boolean,
	required: Boolean,
	readOnly: Boolean,
	error: String,
	placeholder: String,
	autofocus: Boolean,
	autocomplete: String,
	min: Number,
	max: Number,
	step: {
		type: Number,
		default: 1
	},
	inc: Number,
	chars: Number,
	suffix: String,
	hideButtons: Boolean
})

const inputID = getUUID('number')

const inputEl = ref(null)

onMounted(() => {
	if (props.autofocus) inputEl.value.focus()
})

function inc() {
	let oldValue = isNaN(model.value) || model.value === null || model.value === '' ? props.min ? props.min : 0 : parseFloat(model.value)
	let changeStep = props.inc || props.step
	let newValue = parseFloat((oldValue + changeStep).toFixed(4))
	if (props.max && newValue > props.max) newValue = props.max
	if (props.min && newValue < props.min) newValue = props.min
	model.value = newValue
}
function dec() {
	let oldValue = isNaN(model.value) || model.value === null || model.value === '' ? props.min ? props.min : 0 : parseFloat(model.value)
	let changeStep = props.inc || props.step
	let newValue = parseFloat((oldValue - changeStep).toFixed(4))
	if (props.min && newValue < props.min) newValue = props.min
	if (props.max && newValue > props.max) newValue = props.max
	model.value = newValue
}

defineExpose({
	focus: () => inputEl.value.focus()
})
</script>

<template>
	<InputWrapper type="number" :id="inputID" :error="error">
		<label class="input-el input-number-el ai-c flex" :for="inputID" :class="{isDisabled: disabled, wError: error, isReadOnly: readOnly}">
			<IcoButton v-if="!hideButtons" tabindex="-1" icon="down" class="input-number-button input-number-buttonDown" :disabled="disabled || readOnly || (typeof min == 'number' && model <= min)" @click.prevent="dec" />
			<input
				:id="inputID"
				class="input-number"
				:class="{
					isDisabled: disabled,
					isReadOnly: readOnly,
					hasSuffix: suffix
				}"
				:min="min"
				:max="max"
				:step="step"
				:name="name"
				type="number"
				:placeholder="placeholder"
				:autocomplete="autocomplete"
				:readonly="readOnly"
				:required="required"
				:disabled="disabled"
				:style="{'--chars': chars}"
				ref="inputEl"
				v-model="model"
			/>
			<span v-if="suffix" class="input-number-suffix">{{ suffix }}</span>
			<IcoButton v-if="!hideButtons" tabindex="-1" icon="up" class="input-number-button input-number-buttonUp" :disabled="disabled || readOnly || (typeof max == 'number' && model >= max)" @click.prevent="inc" />
		</label>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>