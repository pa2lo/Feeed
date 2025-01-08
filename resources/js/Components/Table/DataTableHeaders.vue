<script setup>
import { ref, computed } from 'vue'

import SimpleCheckbox from '../Inputs/SimpleCheckbox.vue'

const model = defineModel()

const props = defineProps({
	columns: Array,
	items: Array,
	modelField: String,
	modelDisabled: Boolean,
	hasModel: Boolean,
	disabledRows: Array,
	loadingRows: Array
})

function getAlignClass(col) {
	if (col.props?.type == 'date' && !col.props.align) return 'ta-c'

	if (col.props?.align) return {
		left: 'ta-l',
		center: 'ta-c',
		right: 'ta-r'
	}[col.props.align]
}

const tableCb = ref(null)

const availableItems = computed(() => props.items.filter(i => !props.disabledRows.includes(i[props.modelField])).filter(i => !props.loadingRows.includes(i[props.modelField])))

const proxyModel = computed({
	get() {
		if (availableItems.value.every(i => model.value.includes(i[props.modelField]))) {
			tableCb.value?.setIndeterminate(false)
			return availableItems.value.length ? true : false
		}
		if (tableCb.value) tableCb.value.setIndeterminate(props.items.some(i => model.value.includes(i[props.modelField])))
		return false
	},
	set(val) {
		if (val) model.value = availableItems.value.reduce((acc, i) => {
			acc.push(i[props.modelField])
			return acc
		}, [])
		else model.value = []
	}
})
</script>

<template>
	<thead>
		<tr class="dataTable-header-row">
			<th v-if="modelField" class="dataTable-th-placeholder"></th>
			<th
				v-if="hasModel && modelField"
				class="dataTable-th dataTable-th-cb"
			>
				<SimpleCheckbox v-model="proxyModel" ref="tableCb" :disabled="modelDisabled || !availableItems.length" />
			</th>
			<th
				v-for="col in columns"
				class="dataTable-th"
				:class="[
					getAlignClass(col),
					{
						isEmpty: !col.props?.header,
					}
				]"
				:style="{'--min-width': col.props?.minWidth}"
			>
				<template v-if="col.props?.header">
					{{ col.props.header }}
				</template>
			</th>
		</tr>
	</thead>
</template>