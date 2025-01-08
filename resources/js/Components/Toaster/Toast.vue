<script setup>
import { onMounted } from 'vue'
import { destroyToast } from '@/Utils/toaster'

import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	id: String,
	type: String,
	title: String,
	message: String,
	timeout: {
		type: Number,
		default: 7000
	},
	onClick: Function
})

const isCountdown = props.timeout > 0
let toastTimeout, startTime, remaining

onMounted(() => {
	if (isCountdown) startCountdown(props.timeout)
})

function startCountdown(time) {
	startTime = Date.now()
	remaining = time
	toastTimeout = setTimeout(closeToast, time)
}

function pauseCountdown() {
	if (!isCountdown) return
	remaining = remaining - (Date.now() - startTime)
	clearTimeout(toastTimeout)
}

function resumeCountdown() {
	if (!isCountdown || !remaining) return
	startCountdown(remaining)
}

function handleClick() {
	if (!props.onClick) return
	props.onClick()
	closeToast()
}

function closeToast() {
	if (isCountdown && toastTimeout) clearTimeout(toastTimeout)
	destroyToast(props.id)
}

const iconMap = {
	'info': 'circle-info',
	'error': 'circle-x',
	'success': 'circle-check',
	'warning': 'circle-alert'
}
</script>

<template>
	<div class="toast flex ai-c" :class="[`toast-${type}`, {isClickable: onClick}]" @click.prevent="handleClick" @mouseenter="pauseCountdown" @mouseleave="resumeCountdown">
		<Icon class="toast-ico" :name="iconMap[type]" />
		<div class="toast-text">
			<div class="toast-text-title" v-html="title"></div>
			<div v-if="message" class="toast-text-message" v-html="message"></div>
		</div>
		<div class="toast-close isClickable flex aj-c" @click.stop="closeToast">
			<Icon name="x" />
		</div>
		<div v-if="isCountdown" :style="{'--countdown': `${timeout}ms`}" class="toast-countdown"></div>
	</div>
</template>