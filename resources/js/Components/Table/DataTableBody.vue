<script setup>
import { formatDate, getHref } from '@/Utils/helpers'
import { Link } from '@inertiajs/vue3'

import SimpleCheckbox from '../Inputs/SimpleCheckbox.vue';

const model = defineModel()

defineProps({
	columns: Array,
	items: Array,
	rowClick: Function,
	modelField: String,
	modelDisabled: Boolean
})

function getAlignClass(col) {
	if (col.props?.type == 'date' && !col.props.align) return 'ta-c'

	return {
		left: 'ta-l',
		center: 'ta-c',
		right: 'ta-r'
	}[col.props?.align]
}
</script>

<template>
	<tbody>
		<tr
			v-for="item in items"
			class="dataTable-row"
			:class="{
				isClickable: rowClick
			}"
			@click="rowClick && rowClick(item)"
		>
			<td
				v-if="modelField"
				class="dataTable-col dataTable-col-cb"
			>
				<SimpleCheckbox v-model="model" :value="item[modelField]" :disabled="modelDisabled" />
			</td>
			<td
				v-for="col in columns"
				class="dataTable-col"
				:class="[
					getAlignClass(col),
					`dataTable-col-${col.props?.type}`,
					col.props?.cellClass,
					{
						isClickable: col.props?.colClick || col.props?.link,
					}
				]"
				:style="col.props?.colStyle"
				@click="col.props?.colClick && col.props?.colClick(item)"
			>
				<template v-if="col.props?.type == 'date' && col.props?.field">
					<span :class="{'basic-link': col.props?.colClick || col.props?.link || col?.props?.cellClass?.includes('isClickable')}">
						{{ item[col.props.field] ? formatDate(item[col.props.field]) : '-' }}
					</span>
				</template>
				<template v-else-if="col.props?.field">
					<span :class="{'basic-link': col.props?.colClick || col.props?.link || col?.props?.cellClass?.includes('isClickable')}">
						{{ item[col.props.field] }}
					</span>
				</template>
				<component v-else-if="col.children?.default" :is="col.children.default" :data="item" />
				<Link v-if="col.props?.link" class="dataTable-col-link" :href="getHref(col.props?.link, col.props?.linkParam ? item[col.props?.linkParam] : null)"></Link>
			</td>
		</tr>
	</tbody>
</template>