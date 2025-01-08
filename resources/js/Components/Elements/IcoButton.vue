<script setup>
import { Link } from '@inertiajs/vue3'
import { getHref } from '@/Utils/helpers'

import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	icon: String,
	link: String,
	linkParam: [String, Number],
	color: {
		type: String,
		default: 'heading'
	},
	variant: {
		type: String,
		default: 'plain'
	},
	type: {
		type: String,
		default: 'button'
	},
	disabled: Boolean,
	loading: Boolean,
	highlighted: Boolean,
	invisible: Boolean,
	transparent: Boolean,
	download: {
		type: Boolean,
		default: null
	},
	circle: Boolean
})

const isBasicLink = typeof props.link == "string" && ['https://', 'http://', '#'].some(l => props.link?.startsWith(l))
</script>

<template>
	<component
		:is="link ? (isBasicLink || download) ? 'a' : Link : 'button'"
		:href="link ? getHref(link, linkParam) : null"
		:type="link ? null : type"
		class="ico-button"
		:class="[
			`button-${color}`,
			`button-${variant}`,
			{
				isDisabled: disabled,
				isLoading: loading,
				isInvisible: invisible,
				isTransparent: transparent,
				isHighlighted: highlighted,
				isCircle: circle
			}
		]"
		:disabled="disabled || loading ? 'disabled' : null"
		:download="download ? '' : null"
	>
		<Icon class="ico-button-ico" :name="icon" />
	</component>
</template>