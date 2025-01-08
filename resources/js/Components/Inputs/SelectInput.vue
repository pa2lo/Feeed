<script setup>
import { ref, watch, computed } from 'vue'
import { getUUID, txt } from '@/Utils/helpers'

import InputWrapper from './InputWrapper.vue'
import SelectInputOption from './SelectInputOption.vue'
import Tag from '../Elements/Tag.vue'
import Icon from '../Elements/Icon.vue'

const model = defineModel()

const props = defineProps({
	name: String,
	required: Boolean,
	disabled: Boolean,
	readOnly: Boolean,
	error: String,
	placeholder: {
		type: String,
		default: txt('Select...')
	},
	options: Array,
	allowEmpty: Boolean,
	showCount: Boolean,
	searchable: Boolean,
	searchableFields: {
		type: Array,
		default: []
	},
	searchPlaceholder: {
		type: String,
		default: txt('Search...')
	},
	loading: Boolean,
	onSearch: {
		type: Function,
		default: null
	},
	noItemsText: {
		type: String,
		default: txt('no items')
	},
	searchThrottle: {
		tyoe: Number,
		default: 350
	}
})

const emit = defineEmits(['change'])

let inputID = getUUID('select')

const inputEl = ref(null)
const listboxEl = ref(null)
const filterInputEl = ref(null)
const searchInputEl = ref(null)
const resetFilterEl = ref(null)

const isMulti = typeof model.value == 'object'

const isOpen = ref(false)
let wrapper = null
const destination = ref(null)
const coords = ref({})

function setDestination() {
	wrapper = inputEl.value.closest('.overflowCont') ?? document.documentElement
	destination.value = wrapper == document.documentElement ? document.body : wrapper
}
function setCoords() {
	let { left, top, width, height } = inputEl.value.getBoundingClientRect()

	coords.value = {
		w: `${width}px`,
		h: `${height}px`,
		t: `${top}px`,
		l: `${left}px`,
		ww: `${wrapper.clientWidth}px`,
		wh: `${wrapper.clientHeight}px`,
		ofTop: `${top + wrapper.scrollTop}px`
	}
}

function toggleList() {
	if (props.disabled || props.readOnly) return

	if (isOpen.value) isOpen.value = false
	else {
		if (!destination.value) setDestination()

		setCoords()

		filter.value = ''

		if (availableOptions.value.length && (!model.value || (isMulti && !model.value.length))) focusedOption.value = availableOptions.value[0].value
		else focusedOption.value = isMulti ? model.value[0] : model.value

		requestAnimationFrame(() => {
			isOpen.value = true
			scrollCurrentIntoView()
		})
	}
}

function selectOption(value) {
	if (props.disabled || props.readOnly) return

	if (isMulti) {
		if (model.value.some(v => v == value)) deselectOption(value)
		else {
			model.value.push(value)
			requestAnimationFrame(() => setCoords())
			emit('change', value)
		}
	} else {
		model.value = value
		focusInputEl(true)
		emit('change', value)
	}
}
function deselectOption(value) {
	if (props.disabled || props.readOnly) return

	model.value = model.value.filter(i => i != value)
	emit('change')
	if (!model.value.length && (document.activeElement != inputEl.value)) focusInputEl()
	if (isOpen.value) {
		requestAnimationFrame(() => setCoords())
	}
}

const filter = ref('')
const filteredOptions = computed(() => {
	if (!filter.value && !isMulti && props.allowEmpty) return [{title: props.placeholder, value: ''}, ...props.options]
	let fvLowercase = filter.value.toLocaleLowerCase()
	return props.options.filter(o => o.title.toLowerCase().includes(fvLowercase) || props.searchableFields.some(key => o[key] && String(o[key]).toLocaleLowerCase().includes(fvLowercase)))
})

const hasSearchEvent = props.onSearch ? true : false
const searchModel = ref('')
const searchPrevValue = ref('')
let searchTimeout = null
function onSearchInput() {
	clearTimeout(searchTimeout)
	searchTimeout = setTimeout(() => {
		if (searchPrevValue.value == searchModel.value) return

		searchPrevValue.value = searchModel.value
		emit('search', searchModel.value)
	}, props.searchThrottle)
}
function resetSearch() {
	searchModel.value = ''
	searchPrevValue.value = ''
	emit('search', '')
}

const availableOptions = computed(() => {
	if (props.loading || !filteredOptions.value.length) return []
	return filteredOptions.value.filter(o => !o.disabled)
})

watch(() => isOpen.value && availableOptions.value, (newOptions) => {
	if (!availableOptions.value.some(o => o.value == focusedOption.value)) focusedOption.value = availableOptions.value[0]?.value
}, { flush: 'post' })

const checkedOptions = computed(() => {
	if (!isMulti) return props.options.find(o => o.value == model.value) || {title: props.placeholder}

	return model.value.reduce((arr, value) => {
		let option = props.options.find(o => o.value == value)
		if (option) arr.push(option)
		return arr
	}, [])
})

const focusedOption = ref('')
function scrollCurrentIntoView() {
	requestAnimationFrame(() => {
		listboxEl.value.querySelector('.current')?.scrollIntoView({
			block: 'nearest',
			behavior: 'smooth'
		})
	})
}

function focusNextOption() {
	if (props.disabled || props.readOnly || focusedOption.value == undefined || !availableOptions.value.length) return

	let index = availableOptions.value.findIndex(o => o.value == focusedOption.value)
	if (index < availableOptions.value.length - 1) {
		focusedOption.value = availableOptions.value[index + 1].value
		scrollCurrentIntoView()
	}
}
function focusPrevOption() {
	if (props.disabled || props.readOnly || focusedOption.value == undefined || !availableOptions.value.length) return

	let index = availableOptions.value.findIndex(o => o.value == focusedOption.value)
	if (index > 0) {
		focusedOption.value = availableOptions.value[index - 1].value
		scrollCurrentIntoView()
	}
}
function selectFocusedOption() {
	if ((focusedOption.value && filteredOptions.value.some(o => o.value == focusedOption.value)) || (focusedOption.value == '' && props.allowEmpty)) selectOption(focusedOption.value)
}

function focusInputEl(closeList) {
	inputEl.value.focus({ preventScroll: true })
	if (closeList == true) isOpen.value = false
}

function onKeySpace() {
	isOpen.value ? selectFocusedOption() : toggleList()
}
function onKeyDown() {
	isOpen.value ? focusNextOption() : toggleList()
}
function onKeyUp() {
	isOpen.value ? focusPrevOption() : toggleList()
}

function onFocusout(e, isDropdown) {
	if (!isOpen.value || (e.relatedTarget && [inputEl.value, listboxEl.value].some(el => el.contains(e.relatedTarget))) || (isDropdown && document.activeElement == inputEl.value)) return
	isOpen.value = false
}
function onInputBlur(e) {
	if (e.relatedTarget && resetFilterEl.value == e.relatedTarget) focusInputEl()
}

function afterEnter() {
	if (hasSearchEvent && searchInputEl.value) searchInputEl.value.focus()
	else if (props.searchable && filterInputEl.value) filterInputEl.value.focus()
}
</script>

<template>
	<InputWrapper type="select" :error="error">
		<div
			ref="inputEl"
			:id="inputID"
			role="combobox"
			class="input-el input-el-focusable input-select-el"
			:class="{
				isDisabled: disabled,
				isReadOnly: readOnly,
				wError: error,
				isFocused: isOpen,
				isOpen: isOpen
			}"
			:tabindex="!readOnly && !disabled ? 0 : null"
			:aria-controls="`list-${inputID}`"
			aria-haspopup="listbox"
			:aria-expanded="isOpen.toString()"
			:aria-activedescendant="isOpen && focusedOption != undefined ? `listitem-${inputID}-${focusedOption}` : null"
			@click="toggleList"
			@keydown.down.prevent="onKeyDown"
			@keydown.up.prevent="onKeyUp"
			@keydown.space.prevent="onKeySpace"
			@keydown.enter.prevent="onKeySpace"
			@keydown.esc.prevent="isOpen = false"
			@focusout="onFocusout($event, false)"
		>
			<Transition name="fade" mode="out-in">
				<span v-if="(!model && !options.some(o => o.value == model)) || (isMulti && !model.length)" class="input-placeholder">{{ placeholder }}</span>
				<template v-else>
					<span v-if="isMulti && !showCount" class="input-checked-options">
						<TransitionGroup name="checked-options">
							<Tag v-for="option in checkedOptions" :key="option.value" color="link" :clearable="!disabled && !readOnly" @keydown.enter.stop="deselectOption(option.value)" @click.stop="deselectOption(option.value)" :title="`Deselect option - ${option.title}`">{{ option.title }}</Tag>
						</TransitionGroup>
					</span>
					<span v-else-if="isMulti && showCount">{{ checkedOptions.length }} selected</span>
					<span v-else>{{ checkedOptions.title }}</span>
				</template>
			</Transition>
			<span class="input-select-toggle"></span>
		</div>
		<select class="input-fake input-fake-select" :value="model" :required="required" :id="`fake-${inputID}`" tabindex="-1">
			<option>{{ placeholder }}</option>
			<option v-for="o in options" :value="o.value">{{ o.title }}</option>
		</select>
		<Teleport v-if="destination" :to="destination">
			<Transition name="fade" @afterEnter="afterEnter">
				<ul
					v-if="isOpen"
					ref="listboxEl"
					:id="`list-${inputID}`"
					role="listbox"
					class="dropdown-select"
					:style="{
						'--dd-w': coords.w,
						'--dd-h': coords.h,
						'--dd-l': coords.l,
						'--dd-t': coords.t,
						'--dd-ww': coords.ww,
						'--dd-wh': coords.wh,
						'--dd-ofTop': coords.ofTop
					}"
					tabindex="-1"
					@focusout="onFocusout($event, true)"
					@click.self="focusInputEl()"
				>
					<li v-if="hasSearchEvent" class="input-select-search-cont">
						<input
							ref="searchInputEl"
							class="input-select-search"
							type="text"
							:placeholder="searchPlaceholder"
							v-model="searchModel"
							@keydown.tab.prevent="focusInputEl()"
							@keydown.esc.prevent="focusInputEl(true)"
							@keydown.down.prevent="onKeyDown"
							@keydown.up.prevent="onKeyUp"
							@keydown.enter.prevent="onKeySpace"
							@blur="onInputBlur"
							@input="onSearchInput"
						/>
						<button v-if="searchModel.length" ref="resetFilterEl" class="input-select-search-x" type="button" @click.prevent="resetSearch"><Icon name="x" /></button>
					</li>
					<li v-else-if="searchable" class="input-select-search-cont">
						<input
							ref="filterInputEl"
							class="input-select-search"
							type="text"
							:placeholder="searchPlaceholder"
							v-model="filter"
							@keydown.tab.prevent="focusInputEl()"
							@keydown.esc.prevent="focusInputEl(true)"
							@keydown.down.prevent="onKeyDown"
							@keydown.up.prevent="onKeyUp"
							@keydown.enter.prevent="onKeySpace"
							@blur="onInputBlur"
						/>
						<button v-if="filter.length" ref="resetFilterEl" class="input-select-search-x" type="button" @click.prevent="filter = ''"><Icon name="x" /></button>
					</li>
					<li v-if="loading" class="dropdown-search flex aj-c"></li>
					<template v-else>
						<SelectInputOption
							v-for="option in filteredOptions"
							:id="`listitem-${inputID}-${option.value}`"
							:checked="isMulti ? model.some(v => v == option.value) : model == option.value"
							:disabled="option.disabled"
							:current="focusedOption == option.value"
							:value="option.value"
							@mouseenter="() => focusedOption = option.value"
							@mousedown.prevent="selectOption(option.value)"
						>
							<slot name="option" :option="option">{{ option.title }}</slot>
						</SelectInputOption>
						<li v-if="!options.length && !searchPrevValue && !filter" class="dropdown-text-empty">{{ noItemsText }}</li>
						<li v-else-if="!filteredOptions.length && searchPrevValue.length" class="dropdown-text-empty">No results for "{{ searchPrevValue }}"</li>
						<li v-else-if="!filteredOptions.length && filter.length" class="dropdown-text-empty">No results for "{{ filter }}"</li>
					</template>
				</ul>
			</Transition>
		</Teleport>
		<template v-if="$slots.default" #note><slot></slot></template>
	</InputWrapper>
</template>