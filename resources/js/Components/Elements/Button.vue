<script setup>
import { Link } from '@inertiajs/vue3'
import { getHref } from '@/Utils/helpers'

import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	label: String,
	icon: String,
	iconRight: String,
	link: String,
	linkParam: [String, Number],
	color: {
		type: String,
		default: 'primary'
	},
	variant: {
		type: String,
		default: 'solid'
	},
	size: {
		type: String,
		default: 'normal'
	},
	type: {
		type: String,
		default: 'button'
	},
	disabled: Boolean,
	loading: Boolean,
	bigIcon: Boolean,
	full: Boolean,
	download: Boolean
})

const isBasicLink = typeof props.link == "string" && ['https://', 'http://', '#'].some(l => props.link?.startsWith(l))
</script>

<template>
	<component
		:is="link ? (isBasicLink || download) ? 'a' : Link : 'button'"
		:href="link ? getHref(link, linkParam) : null"
		:type="link ? null : type"
		class="button"
		:class="[
			`button-${color}`,
			`button-${size}`,
			`button-${variant}`,
			{
				isDisabled: disabled,
				isLoading: loading,
				isFull: full,
				iconOnly: (icon || iconRight) && !($slots.default || label)
			}
		]"
		:disabled="disabled || loading"
		:download="download"
	>
		<Icon v-if="icon" class="button-ico" :class="{isBig: bigIcon}" :name="icon" />
		<span v-if="$slots.default || label" class="button-text">
			<slot>
				{{ label }}
			</slot>
		</span>
		<Icon v-if="iconRight" class="button-ico" :name="iconRight" />
	</component>
</template>