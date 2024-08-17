import { ref, onMounted } from "vue"

export const installIsInit = ref(false)
export const installPrompt = ref(null)

function installApp() {
	if (!installPrompt.value) return
	installPrompt.value.prompt()
	installPrompt.value.userChoice.then(res => {
		if (res?.outcome === 'accepted') installPrompt.value = null
	})
}

export function useAppInstaller() {
	if (!window.matchMedia('(display-mode: standalone)').matches) {
		onMounted(() => {
			if (installIsInit.value) return

			installIsInit.value = true
			window.addEventListener('beforeinstallprompt', (e) => {
				e.preventDefault()
				installPrompt.value = e
			})
		})
	}

	return {
		installPrompt, installApp
	}
}