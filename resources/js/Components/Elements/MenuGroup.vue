<script setup>
import { onBeforeMount, ref } from 'vue'

import Icon from './Icon.vue'

const props = defineProps({
	active: Array,
	title: String,
	icon: String
})

const open = ref(null)
const isActive = ref(null)

onBeforeMount(() => {
	let path = window.location.pathname
	open.value = isActive.value = props.active.some(i => path.startsWith(i))
})
</script>

<template>
	<div class="menu-group" :class="{isOpen: open}">
		<button class="menu-link flex ai-c" @click="open = !open" :class="{isActive: isActive}">
			<Icon v-if="icon" class="menu-link-ico menu-link-ico-left" :name="icon" />
			<span class="menu-link-text">
				{{ title }}
			</span>
			<Icon class="menu-link-ico menu-link-chevron" name="down" />
		</button>
		<div class="menu-group-submenu">
			<slot />
		</div>
	</div>
</template>