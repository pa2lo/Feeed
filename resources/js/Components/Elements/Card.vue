<script setup>
defineProps({
	header: String,
	headerNote: String,
	as: String,
	width: String,
	stickyFooter: Boolean
})
</script>

<template>
	<component :is="as || 'div'" class="card" :class="[width ? `card-${width}` : '']">
		<div v-if="$slots.header || header" class="card-header flex">
			<div class="card-header-cont">
				<slot name="header">
					<h3>{{ header }}</h3>
					<p v-if="headerNote" class="card-subtitle text-light">{{ headerNote }}</p>
				</slot>
			</div>
			<div v-if="$slots.actions" class="card-actions flex">
				<slot name="actions" />
			</div>
		</div>
		<div v-if="$slots.default" class="card-body"><slot></slot></div>
		<div v-if="$slots.footer || $slots.buttons" class="card-footer flex ai-c" :class="{isSticky: stickyFooter}">
			<div v-if="$slots.footer" class="card-footer-text">
				<slot name="footer" />
			</div>
			<div v-if="$slots.buttons" class="card-footer-buttons flex ai-c">
				<slot name="buttons" />
			</div>
		</div>
	</component>
</template>