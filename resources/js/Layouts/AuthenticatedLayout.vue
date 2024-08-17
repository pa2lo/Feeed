<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'

import ApplicationLogo from '@/Components/Elements/ApplicationLogo.vue'
import Icon from '@/Components/Elements/Icon.vue'
import MenuGroup from '@/Components/Elements/MenuGroup.vue'
import MenuLink from '@/Components/Elements/MenuLink.vue'
import MobileMenuLink from '@/Components/Elements/MobileMenuLink.vue'
import Button from '@/Components/Elements/Button.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'

defineProps({
	header: String,
	headerMeta: String,
	backLink: String,
	width: {
		type: String,
		default: 'normal'
	}
})

let menuOpen = ref(false)
let isDark = ref(document.cookie.includes('theme=dark') || (window.matchMedia('(prefers-color-scheme: dark)').matches && !document.cookie.includes('theme=light')))

function switchTheme() {
	isDark.value = !isDark.value

	document.cookie = `theme=${isDark.value ? 'dark' : 'light'}; path=/`
	document.querySelector('meta[name="theme-color"]').setAttribute('content', isDark.value ? 'hsl(215, 71%, 6%)' : 'hsl(213, 39%, 95%)')
	window.darkTheme = isDark.value
	document.documentElement.classList.toggle('theme-dark', isDark.value)
	document.documentElement.classList.toggle('theme-light', !isDark.value)
}

const isiOS = /iPhone|iPad|iPod/.test(window.navigator?.userAgentData?.platform || window.navigator.platform)
</script>

<template>
	<Head :title="headerMeta ?? header" />
	<aside class="sidemenu-cont" :class="{menuOpen: menuOpen}">
		<div class="sidemenu-backdrop l-hide clickable" @click="menuOpen = !menuOpen"></div>
		<nav class="sidemenu-menu flex">
			<div class="sidemenu-logo flex ai-c">
				<ApplicationLogo />
				<Icon class="sidemenu-hide-x clickable ml-a l-hide" name="x" @click="menuOpen = !menuOpen" />
			</div>
			<div class="divided">
				<MenuLink link="/feeds" icon="rss">Feeds</MenuLink>
				<MenuLink link="/posts" icon="article">Posts</MenuLink>
				<MenuLink link="/categories" icon="category">Categories</MenuLink>
				<MenuLink link="/logs" icon="archive">Logs</MenuLink>
				<MenuLink link="/settings" icon="settings">Settings</MenuLink>
			</div>
			<div class="line divided mb-a">
				<MenuLink v-if="$page.props?.auth?.user?.role == 'admin'" activeRoute="/users" link="/users" icon="users">User accounts</MenuLink>
				<MenuLink link="/profile" icon="user-edit">Profile</MenuLink>
			</div>
			<div class="line divided sidemenu-footer flex ai-c">
				<Button color="heading" size="compact" variant="outline" :icon="isDark ? 'sun' : 'moon'" bigIcon :title="isDark ? 'Light mode' : 'Dark mode'" @click="switchTheme" />
				<Button color="link" size="compact" variant="outline" class="grow" link="/logout" icon="logout" method="post" as="button">Log Out</Button>
			</div>
		</nav>
	</aside>
	<div class="authenticated-layout" :class="[`authenticated-layout-${width}`]" :tabindex="isiOS ? '-1' : null">
		<header class="authenticated-header flex ai-c">
			<IcoButton v-if="backLink" icon="left" class="authenticated-header-back" :link="backLink" transparent />
			<h1 class="page-title">{{ header ?? $page.component }}</h1>
			<button class="menu-toggler flex aj-c clickable l-hide" @click="menuOpen = !menuOpen"><Icon name="menu" /></button>
		</header>
		<main class="page-main section">
			<div class="page-content">
				<slot />
			</div>
		</main>
		<aside class="mobile-nav flex l-hide">
			<MobileMenuLink link="/feeds" icon="rss">Feeds</MobileMenuLink>
			<MobileMenuLink link="/posts" icon="article">Posts</MobileMenuLink>
			<MobileMenuLink link="/categories" icon="category">Categories</MobileMenuLink>
			<MobileMenuLink link="/logs" icon="archive">Logs</MobileMenuLink>
		</aside>
	</div>
</template>