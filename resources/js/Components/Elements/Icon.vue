<script setup>
defineProps({
	name: String
})
</script>

<template>
	<svg v-if="name == 'x'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M18 6l-12 12"></path>
		<path d="M6 6l12 12"></path>
	</svg>
	<svg v-else-if="name == 'menu'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 6l16 0"></path>
		<path d="M4 12l16 0"></path>
		<path d="M4 18l16 0"></path>
	</svg>
	<svg v-else-if="name == 'user-edit'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
		<path d="M6 21v-2a4 4 0 0 1 4 -4h3.5"></path>
		<path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z"></path>
	</svg>
	<svg v-else-if="name == 'dashboard'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
		<path d="M13.45 11.55l2.05 -2.05"></path>
		<path d="M6.4 20a9 9 0 1 1 11.2 0z"></path>
	</svg>
	<svg v-else-if="name == 'info2'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<circle r="9" cx="12" cy="12" />
		<line x1="12" y1="8" x2="12.01" y2="8"></line>
		<line x1="12" y1="16" x2="12" y2="12"></line>
	</svg>
	<svg v-else-if="name == 'moon'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-moon-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 1.992a10 10 0 1 0 9.236 13.838c.341 -.82 -.476 -1.644 -1.298 -1.31a6.5 6.5 0 0 1 -6.864 -10.787l.077 -.08c.551 -.63 .113 -1.653 -.758 -1.653h-.266l-.068 -.006l-.06 -.002z" stroke-width="0" fill="currentColor"></path>
	</svg>
	<svg v-else-if="name == 'sun'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sun-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 19a1 1 0 0 1 .993 .883l.007 .117v1a1 1 0 0 1 -1.993 .117l-.007 -.117v-1a1 1 0 0 1 1 -1z" stroke-width="0" fill="currentColor"></path>
		<path d="M18.313 16.91l.094 .083l.7 .7a1 1 0 0 1 -1.32 1.497l-.094 -.083l-.7 -.7a1 1 0 0 1 1.218 -1.567l.102 .07z" stroke-width="0" fill="currentColor"></path>
		<path d="M7.007 16.993a1 1 0 0 1 .083 1.32l-.083 .094l-.7 .7a1 1 0 0 1 -1.497 -1.32l.083 -.094l.7 -.7a1 1 0 0 1 1.414 0z" stroke-width="0" fill="currentColor"></path>
		<path d="M4 11a1 1 0 0 1 .117 1.993l-.117 .007h-1a1 1 0 0 1 -.117 -1.993l.117 -.007h1z" stroke-width="0" fill="currentColor"></path>
		<path d="M21 11a1 1 0 0 1 .117 1.993l-.117 .007h-1a1 1 0 0 1 -.117 -1.993l.117 -.007h1z" stroke-width="0" fill="currentColor"></path>
		<path d="M6.213 4.81l.094 .083l.7 .7a1 1 0 0 1 -1.32 1.497l-.094 -.083l-.7 -.7a1 1 0 0 1 1.217 -1.567l.102 .07z" stroke-width="0" fill="currentColor"></path>
		<path d="M19.107 4.893a1 1 0 0 1 .083 1.32l-.083 .094l-.7 .7a1 1 0 0 1 -1.497 -1.32l.083 -.094l.7 -.7a1 1 0 0 1 1.414 0z" stroke-width="0" fill="currentColor"></path>
		<path d="M12 2a1 1 0 0 1 .993 .883l.007 .117v1a1 1 0 0 1 -1.993 .117l-.007 -.117v-1a1 1 0 0 1 1 -1z" stroke-width="0" fill="currentColor"></path>
		<path d="M12 7a5 5 0 1 1 -4.995 5.217l-.005 -.217l.005 -.217a5 5 0 0 1 4.995 -4.783z" stroke-width="0" fill="currentColor"></path>
	</svg>
	<svg v-else-if="name == 'logout'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
		<path d="M7 12h14l-3 -3m0 6l3 -3"></path>
	</svg>
	<svg v-else-if="name == 'eye'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
		<path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
	</svg>
	<svg v-else-if="name == 'eye-off'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"></path>
		<path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"></path>
		<path d="M3 3l18 18"></path>
	</svg>
	<svg v-else-if="name == 'copy'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
		<path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
	</svg>
	<svg v-else-if="name == 'check'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M5 12l5 5l10 -10"></path>
	</svg>
	<svg v-else-if="name == 'circle-check'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<circle r="11" cx="12" cy="12" />
		<path d="M8 12L11 15L16 9"></path>
	</svg>
	<svg v-else-if="name == 'circle-x'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<circle r="11" cx="12" cy="12" />
		<line x1="15" y1="9" x2="9" y2="15"></line>
		<line x1="9" y1="9" x2="15" y2="15"></line>
	</svg>
	<svg v-else-if="name == 'circle-info'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<circle r="11" cx="12" cy="12" />
		<line x1="12" y1="7" x2="12.01" y2="7"></line>
		<line x1="12" y1="17" x2="12" y2="11"></line>
	</svg>
	<svg v-else-if="name == 'circle-alert'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-exclamation-circle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<circle r="11" cx="12" cy="12" />
		<line x1="12" y1="7" x2="12" y2="13"></line>
		<line x1="12" y1="17" x2="12.01" y2="17"></line>
	</svg>
	<svg v-else-if="name == 'right'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M9 6l6 6l-6 6"></path>
	</svg>
	<svg v-else-if="name == 'double-right'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M6 6l6 6l-6 6"></path>
		<path d="M12 6l6 6l-6 6"></path>
	</svg>
	<svg v-else-if="name == 'left'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M15 6l-6 6l6 6"></path>
	</svg>
	<svg v-else-if="name == 'double-left'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 6l-6 6l6 6"></path>
		<path d="M18 6l-6 6l6 6"></path>
	</svg>
	<svg v-else-if="name == 'down'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M6 9l6 6l6 -6"></path>
	</svg>
	<svg v-else-if="name == 'up'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M6 15l6 -6l6 6" />
	</svg>
	<svg v-else-if="name == 'back'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M5 12l14 0"></path>
		<path d="M5 12l6 6"></path>
		<path d="M5 12l6 -6"></path>
	</svg>
	<svg v-else-if="name == 'edit'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4"></path>
		<path d="M13.5 6.5l4 4"></path>
	</svg>
	<svg v-else-if="name == 'trash'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 7h16"></path>
		<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
		<path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
		<path d="M10 12l4 4m0 -4l-4 4"></path>
	</svg>
	<svg v-else-if="name == 'circle-check-animated'" class="icon icon-success-animated color-success" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
		<g transform="rotate(-90 12 12)">
			<circle stroke-dasharray="69.5" stroke-dashoffset="69.5" cx="12" cy="12" r="11"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.25s" dur="0.5s" values="70;0"/></circle>
		</g>
		<path stroke-dasharray="14" stroke-dashoffset="14" d="M8 12L11 15L16 9"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.25s" values="14;0"/></path>
	</svg>
	<svg v-else-if="name == 'circle-info-animated'" class="icon icon-info-animated color-info" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2">
			<g transform="rotate(-90 12 12)">
				<circle stroke-dasharray="69.5" stroke-dashoffset="69.5" cx="12" cy="12" r="11"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.25s" dur="0.5s" values="70;0"/></circle>
			</g>
			<g transform="rotate(-180 12 12)">
				<path stroke-dasharray="8" stroke-dashoffset="8" d="M12 7V13"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.25s" values="8;0"/></path>
			</g>
		</g>
		<circle cx="12" cy="7" r="1" fill="currentColor" fill-opacity="0"><animate fill="freeze" attributeName="fill-opacity" begin="0.7s" dur="0.1s" values="0;1"/></circle>
	</svg>
	<svg v-else-if="name == 'circle-alert-animated'" class="icon icon-error-animated color-warn" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2">
			<g transform="rotate(-90 12 12)">
				<circle stroke-dasharray="69.5" stroke-dashoffset="69.5" cx="12" cy="12" r="11"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.25s" dur="0.5s" values="70;0"/></circle>
			</g>
			<path stroke-dasharray="8" stroke-dashoffset="8" d="M12 7V13"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.25s" values="8;0"/></path>
		</g>
		<circle cx="12" cy="17" r="1" fill="currentColor" fill-opacity="0"><animate fill="freeze" attributeName="fill-opacity" begin="0.7s" dur="0.1s" values="0;1"/></circle>
	</svg>
	<svg v-else-if="name == 'circle-x-animated'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x color-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<g transform="rotate(-90 12 12)">
			<circle stroke-dasharray="69.5" stroke-dashoffset="69.5" cx="12" cy="12" r="11"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.25s" dur="0.5s" values="70;0"/></circle>
		</g>
		<line stroke-dasharray="9" stroke-dashoffset="9" x1="15" y1="9" x2="9" y2="15"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.15s" values="8;0"/></line>
		<line stroke-dasharray="9" stroke-dashoffset="9" x1="9" y1="9" x2="15" y2="15"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.65s" dur="0.15s" values="8;0"/></line>
	</svg>
	<svg v-else-if="name == 'circle-question-animated'" class="icon icon-question-animated color-link" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2">
			<g transform="rotate(-90 12 12)">
				<circle stroke-dasharray="69.5" stroke-dashoffset="69.5" cx="12" cy="12" r="11"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.25s" dur="0.5s" values="70;0"/></circle>
			</g>
			<path stroke-dasharray="20" stroke-dashoffset="20" d="M8.99999 10C8.99999 8.34315 10.3431 7 12 7C13.6569 7 15 8.34315 15 10C15 10.9814 14.5288 11.8527 13.8003 12.4C13.0718 12.9473 12.5 13 12 14"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.35s" values="20;0"/></path>
		</g>
		<circle cx="12" cy="17" r="1" fill="currentColor" fill-opacity="0"><animate fill="freeze" attributeName="fill-opacity" begin=".8s" dur="0.1s" values="0;1"/></circle>
	</svg>
	<svg v-else-if="name == 'plus'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 5l0 14"></path>
		<path d="M5 12l14 0"></path>
	</svg>
	<svg v-else-if="name == 'minus'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-minus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M5 12l14 0" />
	</svg>
	<svg v-else-if="name == 'archive'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-archive" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
		<path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"></path>
		<path d="M10 12l4 0"></path>
	</svg>
	<svg v-else-if="name == 'external-link'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
		<path d="M11 13l9 -9"></path>
		<path d="M15 4h5v5"></path>
	</svg>
	<svg v-else-if="name == 'mail'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
		<path d="M3 7l9 6l9 -6" />
	</svg>
	<svg v-else-if="name == 'stack'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-stack-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 4l-8 4l8 4l8 -4l-8 -4" />
		<path d="M4 12l8 4l8 -4" />
		<path d="M4 16l8 4l8 -4" />
	</svg>
	<svg v-else-if="name == 'history'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-history" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 8l0 4l2 2" />
		<path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
	</svg>
	<svg v-else-if="name == 'plusminus'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus-minus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 7h6" /><path d="M7 4v6" />
		<path d="M20 18h-6" />
		<path d="M5 19l14 -14" />
	</svg>
	<svg v-else-if="name == 'users'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
		<path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
		<path d="M16 3.13a4 4 0 0 1 0 7.75" />
		<path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
	</svg>
	<svg v-else-if="name == 'save'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
		<path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
		<path d="M14 4l0 4l-6 0l0 -4" />
	</svg>
	<svg v-else-if="name == 'print'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
		<path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
		<path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
	</svg>
	<svg v-else-if="name == 'download'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
		<path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" />
	</svg>
	<svg v-else-if="name == 'github'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-github" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5" />
	</svg>
	<svg v-else-if="name == 'components'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-components" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 12l3 3l3 -3l-3 -3z" />
		<path d="M15 12l3 3l3 -3l-3 -3z" />
		<path d="M9 6l3 3l3 -3l-3 -3z" />
		<path d="M9 18l3 3l3 -3l-3 -3z" />
	</svg>
	<svg v-else-if="name == 'boxes'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
		<path d="M2 13.5v5.5l5 3" />
		<path d="M7 16.545l5 -3.03" />
		<path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
		<path d="M12 19l5 3" />
		<path d="M17 16.5l5 -3" />
		<path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" />
		<path d="M7 5.03v5.455" />
		<path d="M12 8l5 -3" />
	</svg>
	<svg v-else-if="name == 'code'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-code" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
		<path d="M7 8l-4 4l4 4" />
		<path d="M17 8l4 4l-4 4" />
		<path d="M14 4l-4 16" />
	</svg>
	<svg v-else-if="name == 'search'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
		<path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
		<path d="M21 21l-6 -6" />
	</svg>
	<svg v-else-if="name == 'dots'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dots">
		<path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
		<path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
		<path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
	</svg>
	<svg v-else-if="name == 'calendar'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-due">
		<path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
		<path d="M16 3v4" />
		<path d="M8 3v4" />
		<path d="M4 11h16" />
		<path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
	</svg>
	<svg v-else-if="name == 'filter'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter">
		<path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" />
	</svg>
	<svg v-else-if="name == 'clock'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
		<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
		<path d="M12 7v5l3 3" />
	</svg>
	<svg v-else-if="name == 'category'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category">
		<path d="M4 4h6v6h-6z" />
		<path d="M14 4h6v6h-6z" />
		<path d="M4 14h6v6h-6z" />
		<path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
	</svg>
	<svg v-else-if="name == 'rss'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rss">
		<path d="M5 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
		<path d="M4 4a16 16 0 0 1 16 16" />
		<path d="M4 11a9 9 0 0 1 9 9" />
	</svg>
	<svg v-else-if="name == 'article'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-article">
		<path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
		<path d="M7 8h10" />
		<path d="M7 12h10" />
		<path d="M7 16h10" />
	</svg>
	<svg v-else-if="name == 'refresh'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
		<path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
		<path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
	</svg>
	<svg v-else-if="name == 'type-text'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-typography"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20l3 0" /><path d="M14 20l7 0" /><path d="M6.9 15l6.9 0" /><path d="M10.2 6.3l5.8 13.7" /><path d="M5 20l6 -16l2 0l7 16" /></svg>
	<svg v-else-if="name == 'type-image'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
	<svg v-else-if="name == 'type-gallery'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M12 4v7l2 -2l2 2v-7" /></svg>
	<svg v-else-if="name == 'type-video'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-video"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>
	<svg v-else-if="name == 'type-link'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
	<svg v-else-if="name == 'facebook'" class="icon icon-tabler icons-tabler-filled icon-tabler-brand-facebook" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>
	<svg v-else-if="name == 'instagram'" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-instagram" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
	<svg v-else-if="name == 'settings'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
</template>