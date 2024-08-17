<script setup>
import { ref } from 'vue'

const props = defineProps({
	horizontal: Boolean
})

const x = ref(0)
const y = ref(0)
function onMousemove(e) {
	x.value = e.x
	y.value = e.y
}
</script>

<template>
	<div class="dashboard-links line" :class="{'dashboard-links-horizontal': horizontal}" @mousemove="onMousemove" :style="{ '--x': `${x}px`, '--y': `${y}px` }">
		<slot />
	</div>
</template>