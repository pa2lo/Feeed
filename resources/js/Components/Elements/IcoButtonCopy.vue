<script setup>
import { ref } from 'vue'
import { toast } from '@/Utils/toaster'
import { txt } from '@/Utils/helpers'

import IcoButton from './IcoButton.vue'

const props = defineProps({
	copyableText: String,
	color: {
		tyoe: String,
		default: 'heading'
	}
})

const copied = ref(false)
async function copy() {
	if (copied.value) return

	await navigator.clipboard.writeText(props.copyableText).then(() => {
		toast.success(txt('Copied to clipboard'))
		copied.value = true
		setTimeout(() => {
			copied.value = false
		}, 1000)
	})
}
</script>

<template>
	<IcoButton :icon="copied ? 'check' : 'copy'" v-tooltip="copied ? txt('Copied') : txt('Copy')" :highlighted="copied" :color="copied ? 'success' : color" @click.stop="copy" />
</template>