<script setup>
import { getUUID } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'
import Editor from '@tinymce/tinymce-vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	error: String,
	placeholder: String,
	height: String,
	simple: Boolean
})

const inputID = getUUID('text')

function imageUploadHandler(blobInfo, progress) {
	return new Promise((resolve, reject) => {
		const blob = blobInfo.blob()

		if (blob.size > 5242880) reject('Maximum file size is 5MB')

		const form = new FormData()
		form.append('file', blob)
		axios.post('/admin/upimage', form).then(data => {
			if (data.data?.location) resolve(data.data.location)
			else {
				// console.log(data)
				reject('Upload failed')
			}
		}).catch(err => {
			reject(err?.message || 'Upload failed')
		})
	})
}

const defaultHeight = props.simple ? '177px' : '400px'

const editorInit = {
	simple: {
		statusbar: false,
		plugins: 'code link autolink',
		toolbar: 'styles | bold italic underline | link | code',
		style_formats: [
			{ title: 'Paragraph', block: 'p' },
			{ title: 'Heading 2', format: 'h2' },
			{ title: 'Heading 3', format: 'h3' },
			{ title: 'Heading 4', format: 'h4' },
			{ title: 'Heading 5', format: 'h5' }
		]
	},
	full: {
		plugins: 'lists code advlist link image fullscreen media autolink table',
		toolbar: 'styles | bold italic underline forecolor | link image media | alignleft aligncenter alignright alignjustify | table hr | bullist numlist | fullscreen code',
		image_caption: true,
		image_title: true,
		image_class_list: [
			{title: 'None', value: ''},
			{title: 'Border', value: 'img_border'},
			{title: 'Shadow', value: 'img_shadow'}
		],
		style_formats: [
			{ title: 'Paragraph', block: 'p' },
			{ title: 'Heading' },
			{ title: 'Heading 2', format: 'h2' },
			{ title: 'Heading 3', format: 'h3' },
			{ title: 'Heading 4', format: 'h4' },
			{ title: 'Heading 5', format: 'h5' },
			{ title: 'Blocks' },
			{ title: 'Blockquote', format: 'blockquote' },
			{ title: 'Code', format: 'pre' },
			{ title: 'Warning', block: 'div', wrapper: true, exact: true, attributes: {'class': 'line line-alert line-warning'} },
			{ title: 'Info', block: 'div', wrapper: true, exact: true, attributes: {'class': 'line line-alert line-info'} },
			{ title: 'Success', block: 'div', wrapper: true, exact: true, attributes: {'class': 'line line-alert line-success'} }
		],
		images_upload_handler: imageUploadHandler
	}
}
function getEditorInit() {
	return Object.assign({}, props.simple ? editorInit.simple : editorInit.full, {
		license_key: 'gpl',
		placeholder: props.placeholder,
		height: props.height || defaultHeight,
		menubar: false,
		toolbar_mode: 'sliding',
		entity_encoding: 'raw',
		content_css: '/assets/css/editor.css',
		end_container_on_empty_block: true,
		link_context_toolbar: true,
		link_class_list: [
			{title: 'None', value: ''},
			{title: 'Button primary', value: 'button'},
			{title: 'Button primary outline', value: 'button button-outline'}
		]
	})
}
</script>

<template>
	<InputWrapper type="texteditor" :error="error" full :style="[`--editor-height: ${height ? height : defaultHeight}`]">
		<Editor
			:id="inputID"
			:name="name"
			class="input-el"
			:init="getEditorInit()"
			:disabled="disabled"
			v-model="model"
			tinymce-script-src="/assets/editor/tinymce/tinymce.min.js"
		/>
		<input v-if="required" type="text" required :name="`rq-${name || inputID}`" :value="model.length ? 'y' : ''" class="input-fake input-fake-absolute" />
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>