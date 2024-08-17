<script setup>
import { Link } from '@inertiajs/vue3'
import { getHref } from '@/Utils/helpers'

import Icon from '../Elements/Icon.vue'

const props = defineProps({
	label: String,
	icon: String,
	link: String,
	linkParam: [String, Number],
	disabled: Boolean,
	checked: Boolean,
	closeable: Boolean,
	color: {
		tyoe: String,
		default: 'heading'
	}
})

const isBasicLink = typeof props.link == "string" && ['https://', 'http://', '#'].some(l => props.link?.startsWith(l))

function onEnter(e) {
	if (!props.disabled && e.target != document.activeElement) e.target.focus({ preventScroll: true })	//e.target.closest('.dropdown').focus({ preventScroll: true })
}
function onLeave(e) {
	if (!props.disabled && e.target == document.activeElement) e.target.closest('.dropdown-menu').focus({ preventScroll: true })
}
</script>

<template>
	<component
		:is="link ? isBasicLink ? 'a' : Link : 'button'"
		:href="link ? getHref(link, linkParam) : null"
		:type="link ? null : 'button'"
		class="dropdown-link flex ai-c clickable"
		:class="[
			`dropdown-link-${color}`,
			{
				isChecked: checked,
				isDisabled: disabled,
				isCloseableLink: closeable
			}
		]"
		@mouseenter="onEnter"
		@mouseleave="onLeave"
		:disabled="disabled"
		:tabindex="disabled ? -1 : null"
	>
		<Icon v-if="icon" class="dropdown-link-icon" :name="icon" />
		<span class="dropdown-link-text grow">
			<slot>{{ label }}</slot>
		</span>
		<Icon v-if="checked" class="dropdown-link-icon" name="check" />
	</component>
</template>