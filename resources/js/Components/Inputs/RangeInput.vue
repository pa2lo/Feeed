<script setup>
import { ref, computed } from 'vue'
import { getUUID } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	disabled: Boolean,
	required: Boolean,
	error: String,
	min: {
		type: Number,
		default: 0
	},
	max: {
		type: Number,
		default: 10
	},
	step: {
		type: Number,
		default: 1
	},
	suffix: String
})

const inputID = getUUID('range')

const inputEl = ref(null)

let isActive = ref(false)

const complete = computed(() => {
	return Math.max(Math.min(1, parseFloat((model.value - props.min) / (props.max - props.min))), 0)
})
</script>

<template>
	<InputWrapper type="range" :id="inputID" :error="error">
		<div class="input-range-el flex" :style="{'--complete': complete}">
			<input
				:id="inputID"
				class="input-range"
				:class="{
					isDisabled: disabled,
					isActive: isActive
				}"
				type="range"
				:min="min"
				:max="max"
				:step="step"
				:name="name"
				:required="required"
				:disabled="disabled"
				ref="inputEl"
				v-model="model"
				@touchstart="isActive = true"
				@touchend="isActive = false"
			/>
			<span v-if="!disabled" class="input-range-tooltip">
				<span class="input-range-tooltip-inner">
					<span class="input-range-tooltip-val">
						{{ modelValue }}
					</span>
					<span v-if="suffix" class="input-range-tooltip-suffix">
						{{ suffix }}
					</span>
				</span>
			</span>
		</div>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>