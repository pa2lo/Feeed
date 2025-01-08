<script setup>
import { ref } from 'vue'
import { getUUID, txt } from '@/Utils/helpers'

import { dialog } from '@/Utils/dialog'

import InputWrapper from './InputWrapper.vue'
import Icon from '../Elements/Icon.vue'

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	readOnly: Boolean,
	error: String,
	fileName: String,
	fileUrl: String,
	previewImage: String,
	maxSize: Number,
	placeholder: {
		type: String,
		default: txt('Select file')
	},
	accept: {
		type: String,
		default: ''
	}
})

const inputID = getUUID('text')

const inputEl = ref(null)

const previewFileName = ref(props.fileName)
const previewFileUrl = ref(props.fileUrl)
const previewFileImage = ref(props.previewImage ? props.previewImage : props.fileUrl && ['.jpg', '.png'].some(e => props.fileUrl.includes(e)) ? props.fileUrl : '')
const fileError = ref(null)

const emit = defineEmits(['setFile'])

function onFileChange(e) {
	const [file] = e.target.files

	if (!file) return

	if (props.maxSize && (file.size > props.maxSize)) {
		e.preventDefault()
		return fileError.value = `Maximum file size is ${parseFloat((props.maxSize / 1000000).toFixed(2))}MB`
	}

	if (file) {
		fileError.value = null
		previewFileUrl.value = URL.createObjectURL(file)
		if (file.type.includes('image')) previewFileImage.value = URL.createObjectURL(file)
		else if (!file.type.includes('image')) previewFileImage.value = ''
		previewFileName.value = file.name
		emit('setFile', file)
	}
}

function handlePlaceholderClick() {
	if (inputEl.value) inputEl.value.click()
}

function showImagePreview() {
	dialog.image(previewFileUrl, previewFileName)
}
</script>

<template>
	<InputWrapper type="file" :error="fileError || error">
		<div class="input-el input-file-el flex" :class="{isDisabled: disabled, wError: fileError || error, isReadOnly: readOnly}">
			<a v-if="previewFileName" :href="previewFileUrl" target="_blank" class="file-preview flex ai-c">
				<img v-if="previewFileImage" :src="previewFileImage" class="file-preview-img" title="Preview" alt="" @click.prevent="showImagePreview" />
				<span class="file-preview-name">{{ previewFileName }}</span>
			</a>
			<button type="button" class="button button-outline button-link button-compact file-placeholder-button nowrap" :class="{isDisabled: disabled || readOnly}" @click.prevent="handlePlaceholderClick" :disabled="disabled || readOnly">
				<Icon name="folder-open" class="file-button-ico" />
				<span class="file-placeholder-text" :class="{'sr-only': previewFileName}">{{ placeholder }}</span>
			</button>
		</div>
		<input ref="inputEl" class="input-fake input-fake-absolute" tabindex="-1" :id="inputID" :name="name" type="file" :disabled="disabled" @change="onFileChange" :accept="accept" :required="required" />
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>