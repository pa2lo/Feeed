<script setup>
import { computed, useSlots } from 'vue'
import { txt } from '@/Utils/helpers'

import DataTableHeaders from './DataTableHeaders.vue'
import DataTableBody from './DataTableBody.vue'
import Icon from '@/Components/Elements/Icon.vue'

const model = defineModel()

const props = defineProps({
	items: [Boolean, Array],
	rowClick: Function,
	itemWord: {
		type: String,
		default: txt('data')
	},
	modelField: String,
	modelDisabled: Boolean
})

const slots = useSlots()

let columns = computed(() => {
	if (!props.items.length || props.loading || !slots.default) return

	let reducedArr = slots.default().reduce((arr, item) => {
		if (item.type.__name == 'Column') arr.push(item)
		return arr
	}, [])

	return reducedArr
})
</script>

<template>
	<div v-if="!items.length" class="no-records line ta-c">
		<Icon name="circle-info" />
		<slot name="emptyCont">
			<h3>{{ txt('No') }} {{ itemWord }}</h3>
			<div v-if="$slots.empty" class="line">
				<slot name="empty" />
			</div>
		</slot>
	</div>
	<div v-else-if="columns" class="table-outer line">
		<div class="dataTable-wrapper">
			<table class="dataTable">
				<DataTableHeaders :items :columns v-model="model" :modelField :modelDisabled />
				<DataTableBody :items :columns :rowClick v-model="model" :modelField :modelDisabled />
			</table>
		</div>
	</div>
</template>