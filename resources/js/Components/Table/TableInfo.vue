<script setup>
import { computed } from 'vue'
import { txt } from '@/Utils/helpers'

const props = defineProps({
	count: Number,
	countWords: {
		type: Array,
		default: [txt('record'), txt('records2', 'records'), txt('records')]
	}
})

const itemsCount = computed(() => {
	if (props.count === null) return

	let l = props.count
	if (l == 0 || l > 4) return `${l} ${props.countWords?.[2]}`
	else if (l < 2) return `${l} ${props.countWords?.[0]}`
	else return `${l} ${props.countWords?.[1]}`
})
</script>

<template>
	<div v-if="$slots.buttons || $slots.info || $slots.default || count" class="table-top line flex ai-c">
		<div v-if="$slots.default || typeof count == 'number'" class="table-info flex ai-c">
			<slot />
			<h5 v-if="typeof count == 'number'" class="m0">{{ itemsCount }}</h5>
		</div>
		<div v-if="$slots.buttons" class="card-footer-buttons flex ai-c">
			<slot name="buttons" />
		</div>
	</div>
</template>