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
		class="mobile-nav-link flex ai-c"
		:class="{
			isActive: !external && link && (currentPath.startsWith(activeRoute) || currentPath == link)
		}"
	>
		<span v-if="icon" class="mobile-nav-link-ico">
			<Icon :name="icon" />
		</span>
		<span class="mobile-nav-link-text">
			<slot />
		</span>
	</component>
</template>