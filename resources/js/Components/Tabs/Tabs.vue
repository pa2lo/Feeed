<script setup>
import { ref, computed, useSlots, onMounted, onBeforeUnmount } from 'vue'

import Icon from '@/Components/Elements/Icon.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'

const props = defineProps({
	grow: Boolean,
	variant: {
		type: String,
		default: 'underline'
	}
})

const slots = useSlots()
const activeTab = ref(0)
const panelsCont = ref(null)

const tabs = computed(() => {
	return slots.default().reduce((arr, item) => {
		if (item.type.__name == 'Tab') arr.push(item)
		return arr
	}, [])
})
const currentTab = computed(() => {
	if (typeof activeTab.value === 'number') return tabs.value[activeTab.value]
	return tabs.value.filter(tab => tab.props?.id == activeTab.value)[0]
})

function setTabByIndex(index, tab) {
	if (tab && tab.props.disabled) return
	activeTab.value = tab?.props?.id || index

	if (isScrollable.value) {
		requestAnimationFrame(() => {
			let activeTabEl = tabsEl.value.querySelector('.tab.isActive')
			if (!activeTabEl) return

			activeTabEl.scrollIntoView({ block: 'nearest', inline: 'center' })

			/*let activeTabOffset = activeTabEl.offsetLeft,
				activeTabWidth = activeTabEl.clientWidth,
				tabsScroll = tabsEl.value.scrollLeft,
				tabsWidth = tabsEl.value.clientWidth

			if (tabsScroll > 0 && activeTabOffset < tabsScroll + 32) tabsEl.value.scrollLeft = activeTabOffset - 48
			else if (activeTabOffset + activeTabWidth + 32 > tabsScroll + tabsWidth) tabsEl.value.scrollLeft = activeTabOffset - tabsWidth + activeTabWidth + 48*/
		})
	}
}

function setTab(id) {
	activeTab.value = id
	// let newIndex = tabs.value.findIndex(tab => tab.props?.id == id)
	// if (newIndex > -1) setTabByIndex(newIndex)
}

defineExpose({
	getActiveTabIndex: activeTab.value,
	setTab: setTab
})

let resizeObserver = null
let isScrollable = ref(false)
onMounted(() => {
	resizeObserver = new ResizeObserver(entries => {
		isScrollable.value = tabsEl.value.scrollWidth > tabsEl.value.clientWidth
		setArrows()
	})
	resizeObserver.observe(tabsEl.value)
})
onBeforeUnmount(() => {
	if (resizeObserver) resizeObserver.disconnect()
})

let isMouseDown = false
const tabsEl = ref(null)
const isDragging = ref(false)
const arrowLeftVisible = ref(false)
const arrowRightVisible = ref(false)

function onMousedown() {
	if (!isScrollable.value) return
	isMouseDown = true
	document.addEventListener('mouseup', onMouseup, { once: true })
}
function onMousemove(e) {
	if (!isMouseDown || !isScrollable.value) return

	isDragging.value = true
	tabsEl.value.scrollLeft -= e.movementX
}
function onMouseup() {
	if (!isMouseDown) return

	isMouseDown = false
	if (isDragging.value) isDragging.value = false
}

function moveLeft() {
	tabsEl.value.scrollLeft -= tabsEl.value.clientWidth * 0.9
}
function moveRight() {
	tabsEl.value.scrollLeft += tabsEl.value.clientWidth * 0.9
}
function setArrows() {
	if (!isScrollable.value) return

	let sl = tabsEl.value.scrollLeft
	arrowLeftVisible.value = sl > 0
	arrowRightVisible.value = sl + 1 + tabsEl.value.clientWidth < tabsEl.value.scrollWidth
}

function onLeave(e) {
	panelsCont.value.style.height = `${e.clientHeight}px`
}
function onEnter(e) {
	panelsCont.value.style.height = `${e.clientHeight}px`
}
function afterEnter(e) {
	panelsCont.value.style.height = ``
}
</script>

<template>
	<div class="tabs-cont" :class="{isDragging: isDragging, isBordered: variant != 'pill', line: variant != 'underline'}" @mousemove="onMousemove">
		<div class="tabs flex ai-c" :class="[`tabs-${variant}`]" @mousedown="onMousedown" ref="tabsEl" @scroll="setArrows">
			<button v-for="(tab, index) in tabs" @click="setTabByIndex(index, tab)" :disabled="tab.props.disabled" type="button" class="tab flex ai-c" :class="{isActive: typeof activeTab === 'number' ? activeTab == index : tab.props?.id == activeTab, grow: grow, isDisabled: tab.props.disabled}">
				<component :is="tab.children?.trigger ?? 'span'" class="tab-inner">
					<Icon v-if="tab.props.icon" class="tab-icon" :name="tab.props.icon" />
					<span class="tab-text">
						{{ tab.props?.name }}
					</span>
				</component>
			</button>
		</div>
		<div v-if="isScrollable && arrowLeftVisible" class="tabs-arrow tabs-arrow-left">
			<IcoButton icon="left" @click.prevent="moveLeft" />
		</div>
		<div v-if="isScrollable && arrowRightVisible" class="tabs-arrow tabs-arrow-right">
			<IcoButton icon="right" @click.prevent="moveRight" />
		</div>
	</div>
	<div class="tab-panels line" ref="panelsCont">
		<Transition mode="out-in" name="tabs" @leave="onLeave" @enter="onEnter" @afterEnter="afterEnter">
			<component :is="currentTab" :key="currentTab?.props?.id ?? activeTab"></component>
		</Transition>
	</div>
</template>