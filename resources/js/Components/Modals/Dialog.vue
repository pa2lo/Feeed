<script setup>
import { ref } from 'vue'
import { destroyDialog } from '@/Utils/dialog'
import { txt } from '@/Utils/helpers'

import Button from '@/Components/Elements/Button.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'
import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	id: String,
	title: String,
	type: String,
	message: String,
	image: String,
	closeable: {
		type: Boolean,
		default: true
	},
	width: String,

	// cancel
	onCancel: Function,
	showCancel: Boolean,
	cancelColor: {
		type: String,
		default: 'link'
	},
	cancelText: {
		type: String,
		default: txt('Cancel')
	},

	// confirm
	onConfirm: Function,
	confirmColor: {
		type: String,
		default: 'primary'
	},
	confirmText: {
		type: String,
		default: 'Ok'
	}
})

const open = ref(true)

function closeModal() {
	open.value = false
}
function afterLeave() {
	destroyDialog(props.id)
}

function confirm() {
	closeModal()
	if (typeof props.onConfirm == 'function') props.onConfirm()
}

function cancel() {
	if (props.showCancel || props.closeable) {
		closeModal()
		if (typeof props.onCancel == 'function') props.onCancel()
	}
}

function backdropClick() {
	if (!props.closeable) return
	cancel()
}

const modal = ref(null)
const modalWidth = props.width ? props.width : props.image ? 'image-preview' : 'narrow'
let focusedEl = null

function afterEnter() {
	focusedEl = document.activeElement
	modal.value.focus({ preventScroll: true })
}

function beforeLeave() {
	if (focusedEl && document.documentElement.contains(focusedEl)) focusedEl.focus({ preventScroll: true })
}

const iconMap = {
	'info': 'circle-info-animated',
	'error': 'circle-x-animated',
	'success': 'circle-check-animated',
	'warning': 'circle-alert-animated',
	'question': 'circle-question-animated'
}
</script>

<template>
	<teleport to="body">
        <Transition name="modal" appear @after-enter="afterEnter" @before-leave="beforeLeave" @after-leave="afterLeave">
			<div v-if="open" class="modal-backdrop flex" @click.self="backdropClick" tabindex="-1" @keyup.enter="confirm" @keyup.esc="cancel">
				<div ref="modal" class="card modal-card ta-c" :class="[`card-${modalWidth}`]" tabindex="-1">
					<template v-if="image">
						<img class="modal-image-preview-img" :src="image" alt="" />
						<div v-if="title" class="modal-image-preview-info flex ai-c">
							<span class="modal-image-preview-title">{{ title }}</span>
							<IcoButton icon="external-link" title="Open in new tab" target="_blank" :link="image" />
						</div>
					</template>
					<div v-else class="card-body">
						<div class="modal-dialog-icon">
							<Icon :name="iconMap[type]" />
						</div>
						<h3 class="modal-dialog-title">{{ title }}</h3>
						<div v-if="message" class="line modal-dialog-text" v-html="message"></div>
						<div class="line modal-dialog-buttons flex" :class="[message ? 'line-bigger' : 'line']">
							<Button class="modal-dialog-button" v-if="showCancel" :color="cancelColor" variant="outline" @click.prevent="cancel">{{ cancelText }}</Button>
							<Button class="modal-dialog-button" :color="confirmColor" @click.prevent="confirm">{{ confirmText }}</Button>
						</div>
					</div>
					<IcoButton v-if="closeable" class="modal-button" :variant="image ? 'outline' : 'plain'" transparent icon="x" title="Close" @click.prevent="cancel" />
				</div>
			</div>
		</Transition>
	</teleport>
</template>