<script setup>
import { ref } from 'vue'

const props = defineProps({
	tooltip: String,
	touch: Boolean,
	hoverable: Boolean,
	width: {
		type: String,
		default: 'normal'
	}
})

const tooltipCont = ref(null)
const tooltipEl = ref(null)
const show = ref(false)
let wrapper = null
const destination = ref(null)

const coords = ref({})

function setDestination() {
	wrapper = tooltipCont.value.closest('.overflowCont') ?? document.documentElement
	destination.value = wrapper == document.documentElement ? document.body : wrapper
}
function setCoords() {
	let { left, top, width, height } = tooltipCont.value.getBoundingClientRect()

	coords.value = {
		w: `${width}px`,
		h: `${height}px`,
		t: `${top}px`,
		l: `${left}px`,
		ww: `${wrapper.clientWidth}px`,
		ofTop: `${top + wrapper.scrollTop}px`
	}
}

function handleEnter(e) {
	const isTouch = e.pointerType == 'touch'
	if (!props.touch && isTouch) return

	if (isTouch && show.value) return show.value = false

	if (!destination.value) setDestination()

	setCoords()
	show.value = true
}
function handleCancel(e) {
	if (props.touch && e.pointerType == 'touch' && show.value) return show.value = false
}
function handleLeave(e) {
	if ( !show.value || (e.relatedTarget?.contains(tooltipEl.value) || tooltipCont.value.contains(e.relatedTarget)) ) return
	show.value = false
}
</script>

<template>
	<div class="tooltip-cont" ref="tooltipCont" @pointerenter.passive="handleEnter" @pointercancel.passive="handleCancel" @mouseleave.passive="handleLeave" v-bind="$attrs">
		<slot />
	</div>
	<Teleport v-if="destination" :to="destination">
		<div
			v-if="show"
			ref="tooltipEl"
			class="tooltip"
			:class="[
				`tooltip-${width}`,
				{
					isHoverable: hoverable,
				}
			]"
			:style="{
				'--tt-w': coords.w,
				'--tt-h': coords.h,
				'--tt-l': coords.l,
				'--tt-t': coords.t,
				'--tt-ww': coords.ww,
				'--tt-ofTop': coords.ofTop
			}"
			@pointerleave.passive="handleLeave"
		>
			<slot name="tooltip">
				<div v-html="tooltip"></div>
			</slot>
		</div>
	</Teleport>
</template>