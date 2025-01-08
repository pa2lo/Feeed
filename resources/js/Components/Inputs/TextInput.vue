<script setup>
import { ref, computed, onMounted } from 'vue'
import { getUUID, txt } from '@/Utils/helpers'

import { toast } from '@/Utils/toaster'

import InputWrapper from './InputWrapper.vue'
import Icon from '@/Components/Elements/Icon.vue'
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
	autocomplete: String,
	clearable: Boolean,
	copyable: Boolean,
	icon: String,
	type: {
		type: String,
		default: 'text'
	},
	chars: Number,
	min: [String, Number],
	max: [String, Number],
	trim: Boolean
})

const inputID = getUUID('text')

const inputEl = ref(null)
const showPassword = ref(false)

const inputType = computed(() => {
	if (props.type != 'password') return props.type
	if (showPassword.value == true) return 'text'
	else return 'password'
})

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

function getPlaceholderByType() {
	if (props.type == 'time') return '12:30'
	if (props.type == 'date') return '30.03.2024'
	if (props.type == 'datetime-local') return '30.03.2024, 12:30'
}
const placeholder = computed(() => !props.placeholder && ['date', 'time', 'datetime-local'].includes(props.type) ? getPlaceholderByType() : props.placeholder)

defineExpose({
	focus: () => inputEl.value.focus()
})

function onBlur() {
	if (props.trim && model.value.trim() != model.value) model.value = model.value.trim()
}
</script>

<template>
	<InputWrapper type="text" :id="inputID" :error="error">
		<span v-if="icon" class="input-icon"><Icon :name="icon" /></span>
		<input
			:id="inputID"
			class="input input-text"
			:class="{
				wError: error,
				wButton: copyable || clearable || ['password', 'date', 'datetime-local', 'time'].includes(type),
				wIcon: icon,
				isDisabled: disabled,
				isReadOnly: readOnly
			}"
			:name="name"
			:type="inputType"
			:min="min"
			:max="max"
			:placeholder="placeholder"
			:autocomplete="autocomplete"
			:readonly="readOnly"
			:required="required"
			:disabled="disabled"
			:size="chars"
			ref="inputEl"
			v-model="model"
			@blur="onBlur"
		/>
		<div v-if="clearable || copyable || ['password', 'date', 'datetime-local', 'time'].includes(type)" class="input-buttons flex">
			<IcoButton v-if="clearable" transparent icon="x" v-tooltip="'Clear'" :invisible="!model?.length || disabled || readOnly" @click.prevent="model = ''" />
			<IcoButton v-if="type == 'password'" tabindex="-1" transparent :icon="!showPassword ? 'eye' : 'eye-off'" v-tooltip="showPassword ? txt('Hide password') : txt('Show password')" :invisible="!model?.length" @click.prevent="showPassword = !showPassword" />
			<IcoButton v-if="copyable && model?.length" transparent :icon="copied ? 'check' : 'copy'" :invisible="!model?.length" v-tooltip="copied ? txt('Copied') : txt('Copy')" :highlighted="copied" :color="copied ? 'success' : 'heading'" @click.prevent="copy" />
			<IcoButton v-if="type == 'date' || type == 'datetime-local'" class="ico-date-picker" transparent icon="calendar" v-tooltip="txt('Select date')" @click.prevent="$refs.inputEl.showPicker()" :disabled="disabled || readOnly" />
			<IcoButton v-if="type == 'time'" transparent icon="clock" class="ico-clock-picker" v-tooltip="txt('Select time')" @click.prevent="$refs.inputEl.showPicker()" :disabled="disabled || readOnly" />
		</div>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>