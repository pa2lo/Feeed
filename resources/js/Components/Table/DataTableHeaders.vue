<script setup>
import { ref, computed } from 'vue'

import SimpleCheckbox from '../Inputs/SimpleCheckbox.vue'

const model = defineModel()

const props = defineProps({
	columns: Array,
	items: Array,
	modelField: String,
	modelDisabled: Boolean
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

const proxyModel = computed({
	get() {
		if (props.items.every(i => model.value.includes(i[props.modelField]))) {
			tableCb.value.setIndeterminate(false)
			return true
		}
		if (tableCb.value) tableCb.value.setIndeterminate(props.items.some(i => model.value.includes(i[props.modelField])))
		return false
	},
	set(val) {
		if (val) model.value = props.items.reduce((acc, i) => {
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
			<th
				v-if="modelField"
				class="dataTable-th dataTable-th-cb"
			>
				<SimpleCheckbox v-model="proxyModel" ref="tableCb" :disabled="modelDisabled" />
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