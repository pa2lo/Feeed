<script setup>
import { ref } from 'vue'
import { txt } from '@/Utils/helpers'

import IcoButton from '@/Components/Elements/IcoButton.vue'
import Button from '../Elements/Button.vue'

defineOptions({
	inheritAttrs: false
})

const props = defineProps({
	header: String,
	headerNote: String,
	as: String,
	open: Boolean,
	closeable: {
		type: Boolean,
		default: true
	},
	showCloseButton: Boolean,
	closeButtonText: {
		type: String,
		default: txt('Close')
	},
	width: {
		type: String,
		default: 'normal'
	},
	beforeClose: {
		type: Function,
		default: null
	}
})

const modal = ref(null)
const emit = defineEmits(['update:open', 'modalOpen', 'modalClosed'])
let focusedEl = null

function beforeEnter() {
	focusedEl = document.activeElement
}

function afterEnter() {
	if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName)) return
	let autoFocusEl = modal.value.querySelector('.autofocus input, .autofocus select, .autofocus button, input.autofocus, select.autofocus, a.autofocus, button.autofocus')
	if (autoFocusEl) autoFocusEl.focus()
	else modal.value.focus({ preventScroll: true })
}

function afterLeave() {
	if (focusedEl && document.documentElement.contains(focusedEl)) focusedEl.focus({ preventScroll: true })
	emit('modalClosed')
}

async function close() {
	if (!props.closeable) return

	if (props.beforeClose) {
		const shouldClose = await props.beforeClose()
		if (!shouldClose) return
	}

	emit('update:open', false)
}

function handleEscape(e) {
	if (['INPUT', 'SELECT'].includes(e.target.tagName) || e.target.matches('.input-el-focusable')) return
	e.stopPropagation()
	close()
}
</script>

<template>
	<teleport to="body">
        <Transition name="modal" @before-enter="beforeEnter" @enter="$emit('modalOpen')" @after-enter="afterEnter" @after-leave="afterLeave">
			<div v-if="open" class="modal-backdrop flex overflowCont" @click.self="close">
				<component :is="as || 'div'" ref="modal" class="card modal-card" :class="[`card-${width}`]" @keydown.esc.stop="handleEscape" tabindex="-1" v-bind="$attrs">
					<div v-if="header || $slots.header" class="card-header">
						<slot name="header">
							<h3 class="modal-header">{{ header }}</h3>
							<p v-if="headerNote" class="card-subtitle text-light">{{ headerNote }}</p>
						</slot>
					</div>
					<div v-if="$slots.default" class="card-body"><slot></slot></div>
					<div v-if="$slots.footer || $slots.buttons || showCloseButton" class="card-footer flex ai-c">
						<div v-if="$slots.footer" class="card-footer-text">
							<slot name="footer" />
						</div>
						<div v-if="$slots.buttons || showCloseButton" class="card-footer-buttons flex ai-c">
							<slot name="buttons" />
							<Button v-if="showCloseButton" variant="outline" color="link" @click.prevent="close">{{ closeButtonText }}</Button>
						</div>
					</div>
					<IcoButton v-if="closeable" class="modal-button" transparent icon="x" title="Close" @click.prevent="close" />
				</component>
			</div>
		</Transition>
	</teleport>
</template>