import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { tooltip } from './Utils/tooltip';

// localization
// import { sk } from './Lang/sk';
// window.textLabels = sk;

// set dark theme color if no cookie for theme
if (!document.cookie.includes('theme=') && window.matchMedia('(prefers-color-scheme: dark)').matches) document.querySelector('meta[name="theme-color"]').setAttribute('content', 'hsl(215, 71%, 6%)');

const appName = import.meta.env.VITE_APP_NAME || 'Feeed';

// reload on popstate
let stale = false
window.addEventListener('popstate', (event) => {
	stale = true
});

router.on('navigate', (event) => {
	if (stale) {
		router.reload({ preserveScroll: true })
		stale = false;
	}
});

// update when tab becomes active - open from background in PWA
document.addEventListener('visibilitychange', () => {
	if (document.visibilityState === "visible") {
		router.reload({ preserveScroll: true })
	}
});

// create app
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
			.directive('tooltip', tooltip)
            .mount(el);
    },
    progress: {
        // color: '#6a6ce7',
        color: '#2670EA',
    },
});

if ("serviceWorker" in navigator) {
	navigator.serviceWorker.register('/sw.js');
}