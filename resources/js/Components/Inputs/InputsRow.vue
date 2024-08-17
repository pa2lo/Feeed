<script setup>
import InputWrapper from './InputWrapper.vue'

const props = defineProps({
	wrap: Boolean
})
</script>

<template>
	<InputWrapper type="row" :class="{'input-row-wrap': wrap}">
		<slot />
		<template v-if="$slots.note" #note><slot name="note"></slot></template>
	</InputWrapper>
</template>