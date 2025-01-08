<script setup>
import { ref, onMounted } from 'vue'
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
	clearable: Boolean,
	copyable: Boolean,
	rows: {
		type: [String, Number],
		default: 4
	}
})

const inputID = getUUID('text')

const inputEl = ref(null)

onMounted(() => {
	if (props.autofocus) inputEl.value.focus()
})

const copied = ref(false)
async function copy() {
	if (!model.value || copied.value) return

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
</script>

<template>
	<InputWrapper type="textarea" :id="inputID" :error="error">
		<textarea
			:id="inputID"
			class="input input-textarea"
			:class="{
				wError: error,
				wButton: copyable || clearable,
				isDisabled: disabled,
				isReadOnly: readOnly
			}"
			:name="name"
			:placeholder="placeholder"
			:readonly="readOnly"
			:required="required"
			:disabled="disabled"
			:rows="rows"
			ref="inputEl"
			v-model="model"
		/>
		<div v-if="copyable || clearable" class="input-buttons flex">
			<IcoButton v-if="clearable" transparent icon="x" v-tooltip="'Clear'" :invisible="!model?.length" @click.prevent="model = ''" />
			<IcoButton v-if="copyable" transparent :icon="copied ? 'check' : 'copy'" :invisible="!model?.length" v-tooltip="copied ? txt('Copied') : txt('Copy')" :highlighted="copied" :color="copied ? 'success' : 'heading'" @click.prevent="copy" />
		</div>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>