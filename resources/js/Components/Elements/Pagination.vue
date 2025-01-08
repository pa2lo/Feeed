<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	links: Array,
	pages: Number,
	currentPage: Number,
	prevPage: String,
	nextPage: String,
	firstPage: String,
	lastPage: String,
	from: Number,
	to: Number,
	total: Number
})

const paginationLinks = computed(() => {
	let bottomLimit = 3
	let topLimit = 3
	if (props.currentPage == 1) bottomLimit = 5
	if (props.currentPage == 2) bottomLimit = 4
	if (props.currentPage == props.pages) topLimit = 5
	if (props.currentPage == props.pages - 1) topLimit = 4

	return props.links.filter(link => !link.label.includes('Previous') && !link.label.includes('Next') && parseInt(link.label) > props.currentPage - topLimit && parseInt(link.label) < props.currentPage + bottomLimit)
})
</script>

<template>
	<div class="line pagination-container flex aj-c" v-if="pages > 1">
		<div class="pagination flex aj-c">
			<Link class="pagination-link" :href="firstPage" title="First" :class="{noAction: currentPage == 1}"><Icon name="double-left" /></Link>
			<Link class="pagination-link" :href="prevPage || '#'" title="Previous" :class="{noAction: currentPage == 1}"><Icon name="left" /></Link>
			<template v-for="(link, key) in paginationLinks">
				<div v-if="link.active || link.url == null" class="pagination-link" :class="{active: link.active, disabled: link.url == null}">{{ link.label }}</div>
				<Link v-else class="pagination-link" :href="link.url">{{ link.label }}</Link>
			</template>
			<Link class="pagination-link" :href="nextPage || '#'" title="Next" :class="{noAction: currentPage >= pages}"><Icon name="right" /></Link>
			<Link class="pagination-link" :href="lastPage" title="Last" :class="{noAction: currentPage >= pages}"><Icon name="double-right" /></Link>
		</div>
		<div v-if="from && to && total" class="pagination-info lh1 text-light ml-a rm-hide">{{ from }} - {{ to }} / {{ total }}</div>
	</div>
</template>