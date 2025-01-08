<script setup>
import { ref } from 'vue'

import Button from '../Elements/Button.vue'
import IcoButton from '../Elements/IcoButton.vue'

const props = defineProps({
	element: {
		type: String,
		default: 'button'
	},
	icon: {
		type: String,
		default: 'down'
	},
	togglerIcon: String,
	align: {
		type: String,
		default: 'left'
	},
	closeOnClick: Boolean,
	focusFirst: Boolean,
	disabled: Boolean
})

const togglerEl = ref(null)
const dropdownEl = ref(null)

const isOpen = ref(false)
let wrapper = null
const destination = ref(null)
const coords = ref({})

function setDestination() {
	wrapper = togglerEl.value.closest('.overflowCont') ?? document.documentElement
	destination.value = wrapper == document.documentElement ? document.body : wrapper
}
function setCoords() {
	let { left, top, width, height } = togglerEl.value.getBoundingClientRect()

	coords.value = {
		w: `${width}px`,
		h: `${height}px`,
		t: `${top}px`,
		l: `${left}px`,
		ww: `${wrapper.clientWidth}px`,
		wh: `${wrapper.clientHeight}px`,
		ofTop: `${top + wrapper.scrollTop}px`
	}
}

function toggleDropdown(e) {
	if (isOpen.value || props.disabled) return closeDropdown()

	togglerEl.value = e.target.closest('.dropdown-toggler')

	if (!destination.value) setDestination()

	setCoords()

	requestAnimationFrame(() => {
		isOpen.value = true
		if (props.closeOnClick) document.addEventListener('click', closeDropdown, {once: true})
	})
}

function closeDropdown(focusBack) {
	isOpen.value = false
	if (focusBack == true && document.documentElement.contains(togglerEl.value)) togglerEl.value.focus({ preventScroll: true })
}

function focusDropdown() {
	props.focusFirst ? focusFirstEl() : dropdownEl.value.focus({ preventScroll: true })
}

function focusPrevEl() {
	if (!dropdownEl.value.querySelector('.dropdown-link')) return

	let el = dropdownEl.value.querySelector('.dropdown-link:focus')
	if (!el) return focusLastEl()

	let prev = el.previousElementSibling
	while (prev) {
		if (prev.matches('.dropdown-link:not(.isDisabled)')) return prev.focus()
		prev = prev.previousElementSibling
	}
	focusLastEl()
}
function focusNextEl() {
	if (!dropdownEl.value.querySelector('.dropdown-link')) return

	let el = dropdownEl.value.querySelector('.dropdown-link:focus')
	if (!el) return focusFirstEl()

	let next = el.nextElementSibling
	while (next) {
		if (next.matches('.dropdown-link:not(.isDisabled)')) return next.focus()
		next = next.nextElementSibling
	}
	focusFirstEl()
}

function focusFirstEl() {
	let el = dropdownEl.value.querySelector('.dropdown-link:not(.isDisabled)')
	if (el) el.focus({ preventScroll: true })
	else dropdownEl.value.focus({ preventScroll: true })
}
function focusLastEl() {
	let els = dropdownEl.value.querySelectorAll('.dropdown-link:not(.isDisabled)')
	if (els.length) els[els.length - 1].focus({ preventScroll: true })
}

function onFocusout(e) {
	if (!isOpen.value || (e.relatedTarget && (dropdownEl.value.contains(e.relatedTarget) || e.relatedTarget == togglerEl.value))) return
	closeDropdown()
}
function onTabKey(e) {
	if (!e.altKey && !e.ctrlKey) e.preventDefault()
	e.shiftKey ? focusPrevEl() : focusNextEl()
}

function onConfirmHandler(e) {
	if (e.target && (e.target.classList.contains('isCloseableLink') || e.target.closest('.isCloseableLink'))) closeDropdown(true)
}
</script>

<template>
	<div v-if="$slots.toggler" class="dropdown-toggler isClickable" :class="{isOpen: isOpen, isDisabled: disabled}" @mousedown.prevent="toggleDropdown" v-bind="$attrs" tabindex="0">
		<slot name="toggler" />
	</div>
	<component v-else :is="element == 'button' ? Button : IcoButton" :class="{isOpen: isOpen, isDisabled: disabled}" class="dropdown-toggler" :icon="element == 'button' ? togglerIcon : icon" :iconRight="element == 'button' ? icon : null" @click.prevent="toggleDropdown" :disabled="disabled" v-bind="$attrs" tabindex="0" />
	<Teleport v-if="destination" :to="destination">
		<Transition name="fade" @enter="focusDropdown">
			<div
				v-if="isOpen"
				ref="dropdownEl"
				class="dropdown-menu"
				:class="[
					`dropdown-${align}`
				]"
				:style="{
					'--dd-w': coords.w,
					'--dd-h': coords.h,
					'--dd-l': coords.l,
					'--dd-t': coords.t,
					'--dd-ww': coords.ww,
					'--dd-wh': coords.wh,
					'--dd-ofTop': coords.ofTop
				}"
				tabindex="-1"
				@keydown.tab="onTabKey"
				@keydown.esc.prevent="() => closeDropdown(true)"
				@keydown.up.prevent="focusPrevEl"
				@keydown.down.prevent="focusNextEl"
				@focusout="onFocusout"
				@click="onConfirmHandler"
			>
				<slot />
			</div>
		</Transition>
	</Teleport>
</template>