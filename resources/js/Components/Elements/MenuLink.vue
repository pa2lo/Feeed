<script setup>
import { Link } from '@inertiajs/vue3'

import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	link: {
		type: String,
		default: ''
	},
	icon: String,
	activeRoute: {
		type: String,
		default: null
	},
	external: Boolean
})

let currentPath = window.location.pathname
</script>

<template>
	<component
		:is="link ? external ? 'a' : Link : 'button'"
		:href="link ? link : null"
		class="menu-link flex ai-c"
		:class="{
			isActive: !external && link && (currentPath.startsWith(activeRoute) || currentPath == link)
		}"
		:target="link && external ? '_blank' : null"
	>
		<Icon v-if="icon" class="menu-link-ico menu-link-ico-left" :name="icon" />
		<span class="menu-link-text">
			<slot />
		</span>
		<Icon v-if="external" class="menu-link-ico" name="external-link" />
	</component>
</template>