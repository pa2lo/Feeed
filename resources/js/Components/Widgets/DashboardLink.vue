<script setup>
import { Link } from '@inertiajs/vue3'

import Icon from '../Elements/Icon.vue'

const props = defineProps({
	href: String,
	icon: String,
	title: String,
	description: String
})
</script>

<template>
	<component :is="href ? Link : 'div'" class="dashboard-link" :href="href">
		<Icon v-if="icon" class="dashboard-link-ico" :name="icon" />
		<div class="dashboard-link-text">
			<div class="dashboard-link-title">
				<slot>{{ title }}</slot>
			</div>
			<div v-if="description || $slots.description" class="dashboard-link-description">
				<slot name="description">{{ description }}</slot>
			</div>
		</div>
	</component>
</template>