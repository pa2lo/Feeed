let tooltipIDCounter = 1
const tooltipsMap = {}

export const tooltip = {
	mounted(el, binding) {
		if (!binding.value?.text && !binding.value) return

		let tooltipId = `tooltip-${tooltipIDCounter++}`
		el.dataset.tooltipId = tooltipId

		tooltipsMap[tooltipId] = {
			touch: binding.value?.touch || binding.modifiers.touch,
			hoverable: binding.value?.hoverable || binding.modifiers.hoverable,
			width: binding.value?.width || 'normal',
			wrapper: null,
			destination: null,
			show: false,
			text: binding.value?.text || binding.value
		}
		addEvents(el, tooltipId)
	},
	beforeUnmount(el) {
		if (!el.dataset.tooltipId) return

		let tooltipId = el.dataset.tooltipId
		if (tooltipsMap[tooltipId]?.show) destroyTooltip(tooltipId)
		delete tooltipsMap[tooltipId]
	},
	beforeUpdate(el, binding) {
		if (!el.dataset.tooltipId || binding.oldValue == binding.value) return

		let tooltipId = el.dataset.tooltipId
		tooltipsMap[tooltipId].text = binding.value?.text || binding.value

		if (tooltipsMap[tooltipId].show) updateTooltipContent(tooltipId)
	}
}

function setDestination(el, tooltipId) {
	let wrapper = el.closest('.overflowCont') ?? document.documentElement
	tooltipsMap[tooltipId].wrapper = wrapper
	tooltipsMap[tooltipId].destination = wrapper == document.documentElement ? document.body : wrapper
}

function addEvents(el, tooltipId) {
	const handleEnterFunction = (e) => handleEnter(e, el, tooltipId)
	const handleCancelFunction = (e) => handleCancel(e, el, tooltipId)
	const handleLeaveFunction = (e) => handleLeave(e, el, tooltipId)

	el.addEventListener('pointerenter', handleEnterFunction, {passive: true})
	el.addEventListener('pointercancel', handleCancelFunction, {passive: true})
	el.addEventListener('mouseleave', handleLeaveFunction, {passive: true})
}

function createTooltipEl(el, tooltipId) {
	if (!tooltipsMap[tooltipId]?.text) return

	let element = document.createElement("div")
	element.id = tooltipId
	element.className = `tooltip tooltip-${tooltipsMap[tooltipId].width}`
	element.innerHTML = tooltipsMap[tooltipId].text || ''

	if (tooltipsMap[tooltipId].hoverable) {
		element.classList.add('isHoverable')
		const handleLeaveFunction = (e) => handleLeave(e, el, tooltipId)
		element.addEventListener('pointerleave', handleLeaveFunction)
	}

	return element
}

function updateTooltipContent(tooltipId) {
	if (!tooltipsMap[tooltipId].tooltipEl) return

	if (!tooltipsMap[tooltipId].text) return destroyTooltip(tooltipId)

	tooltipsMap[tooltipId].tooltipEl.innerHTML = tooltipsMap[tooltipId].text
}

function handleEnter(e, el, tooltipId) {
	if (!tooltipsMap[tooltipId]?.text) return

	const isTouch = e.pointerType == 'touch'
	if (!tooltipsMap[tooltipId].touch && isTouch) return

	if (isTouch && tooltipsMap[tooltipId].show) return destroyTooltip(tooltipId)

	if (tooltipsMap[tooltipId].show) return

	if (!tooltipsMap[tooltipId].destination) setDestination(el, tooltipId)

	let tooltipEl = createTooltipEl(el, tooltipId)

	setCoords(tooltipEl, el, tooltipsMap[tooltipId].wrapper)

	tooltipsMap[tooltipId].tooltipEl = tooltipEl
	tooltipsMap[tooltipId].destination.append(tooltipEl)
	tooltipsMap[tooltipId].show = true
}

function handleCancel(e, el, tooltipId) {
	if (!tooltipsMap[tooltipId].text) return

	if (tooltipsMap[tooltipId].touch && e.pointerType == 'touch' && tooltipsMap[tooltipId].show) return destroyTooltip(tooltipId)
}

function handleLeave(e, el, tooltipId) {
	if ( !tooltipsMap[tooltipId]?.show || (e.relatedTarget?.contains(tooltipsMap[tooltipId].tooltipEl) || el.contains(e.relatedTarget)) ) return
	destroyTooltip(tooltipId)
}

function destroyTooltip(tooltipId) {
	if (!tooltipsMap[tooltipId].tooltipEl) return

	tooltipsMap[tooltipId].tooltipEl.remove()
	tooltipsMap[tooltipId].tooltipEl = null
	tooltipsMap[tooltipId].show = false
}

function setCoords(tooltipEl, tooltipCont, tooltipWrapper) {
	let { left, top, width, height } = tooltipCont.getBoundingClientRect()

	tooltipEl.style.setProperty('--tt-w', `${width}px`)
	tooltipEl.style.setProperty('--tt-h', `${height}px`)
	tooltipEl.style.setProperty('--tt-l', `${left}px`)
	tooltipEl.style.setProperty('--tt-t', `${top}px`)
	tooltipEl.style.setProperty('--tt-ww', `${tooltipWrapper.clientWidth}px`)
	tooltipEl.style.setProperty('--tt-ofTop', `${top + tooltipWrapper.scrollTop}px`)
}