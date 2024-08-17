<script setup>
import { ref, onMounted } from 'vue'
import { getUUID, txt } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'
import Icon from '@/Components/Elements/Icon.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	readOnly: Boolean,
	error: String,
	placeholder: {
		type: String,
		default: txt('Select...')
	},
	autofocus: Boolean,
	autocomplete: String,
	icon: String,
	options: Array,
	allowEmpty: Boolean
})

let inputID = getUUID('select')

const inputEl = ref(null)

onMounted(() => {
	if (props.autofocus) inputEl.focus()
})

defineExpose({
	focus: () => inputEl.value.focus()
})
</script>

<template>
	<InputWrapper type="select" :id="inputID" :error="error">
		<span v-if="icon" class="input-icon"><Icon :name="icon" /></span>
		<select
			:id="inputID"
			class="input input-select"
			:class="{
				wError: error,
				wIcon: icon,
				isDisabled: disabled,
				isReadOnly: readOnly,
				hasPlaceholder: model === '' || model === null
			}"
			:name="name"
			:autocomplete="autocomplete"
			:required="required"
			:disabled="disabled || readOnly"
			ref="inputEl"
			v-model="model"
		>
			<option value='' :hidden="!allowEmpty" selected>{{ placeholder }}</option>
			<option v-for="option in options" :value="option.value ?? option" :disabled="option.disabled">{{ option.title ?? option }}</option>
		</select>
		<span class="input-select-toggle"></span>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>
