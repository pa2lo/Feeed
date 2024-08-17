<script setup>
import { Comment } from 'vue'

import SlideToggle from '../Elements/SlideToggle.vue'
</script>

<template>
	<SlideToggle :show="$slots.default().some(node => node.type != Comment)" class="filter-tags-cont">
		<div class="filter-tags">
			<TransitionGroup name="checked-options">
				<slot />
			</TransitionGroup>
		</div>
	</SlideToggle>
</template>