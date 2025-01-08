<script setup>
import { ref, computed, onMounted } from 'vue'
import { getUUID, txt } from '@/Utils/helpers'

import { toast } from '@/Utils/toaster'

import InputWrapper from './InputWrapper.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	readOnly: Boolean,
	error: String,
	placeholder: String,
	autofocus: Boolean,
	copyable: Boolean
})

const inputID = getUUID('color')

const inputEl = ref(null)

onMounted(() => {
	if (props.autofocus) inputEl.value.focus()
})

const copied = ref(false)
async function copy() {
	if (!model.value.length || copied.value) return

	inputEl.value.setSelectionRange(0, 99999)
	await navigator.clipboard.writeText(model.value).then(() => {
		toast.success(txt('Copied to clipboard'))
		copied.value = true
		setTimeout(() => {
			copied.value = false
		}, 1000)
	})
}

defineExpose({
	focus: () => inputEl.value.focus()
})

let lastColor = model.value

function onBlur() {
	let newModelValue = model.value.trim()
	if (newModelValue.length && !newModelValue.startsWith('#')) newModelValue = `#${newModelValue}`
	if (newModelValue.length > 7) newModelValue = newModelValue.slice(0, 7)
	if (newModelValue.length == 4) newModelValue = `#${newModelValue[1].repeat(2)}${newModelValue[2].repeat(2)}${newModelValue[3].repeat(2)}`
	if (newModelValue != model.value) model.value = newModelValue
	if (newModelValue.length == 7) lastColor = newModelValue
}

const proxyColorModel = computed({
	get() {
		if (model.value) {
			let trimModel = model.value.trim()
			if (trimModel.startsWith('#')) {
				if (trimModel.length == 7) {
					if (lastColor != trimModel) lastColor = trimModel
					return trimModel
				} else if (trimModel.length == 4) {
					let newValue = `#${trimModel[1].repeat(2)}${trimModel[2].repeat(2)}${trimModel[3].repeat(2)}`
					lastColor = newValue
					return newValue
				}
			}
		}
		if (lastColor) return lastColor
		else return '#000000'
	},
	set(val) {
		model.value = val
	}
})
</script>

<template>
	<InputWrapper type="color" :id="inputID" :error="error">
		<input
			class="input-el-trigger input-color-trigger"
			:class="{
				isDisabled: disabled,
				isReadOnly: readOnly
			}"
			type="color"
			:disabled="disabled || readOnly"
			v-model="proxyColorModel"
			tabindex="-1"
		/>
		<input
			:id="inputID"
			class="input input-color"
			:class="{
				wError: error,
				wButton: copyable,
				isDisabled: disabled,
				isReadOnly: readOnly
			}"
			:name="name"
			type="text"
			:placeholder="placeholder"
			:readonly="readOnly"
			:required="required"
			:disabled="disabled"
			size="8"
			ref="inputEl"
			v-model="model"
			@blur="onBlur"
		/>
		<div v-if="copyable" class="input-buttons flex">
			<IcoButton v-if="copyable && model?.length" transparent :icon="copied ? 'check' : 'copy'" :invisible="!model?.length" v-tooltip="copied ? txt('Copied') : txt('Copy')" :highlighted="copied" :color="copied ? 'success' : 'heading'" @click.prevent="copy" />
		</div>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>