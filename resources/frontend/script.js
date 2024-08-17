"use strict";

// vars
let activeFeeds = localStorage.getItem('feeds') ? JSON.parse(localStorage.getItem('feeds')) : [];
let activeCategories = localStorage.getItem('categories') ? JSON.parse(localStorage.getItem('categories')) : [];
let categories = {};
let feeds = {};
let time = parseInt(Date.now() / 1000);
let renderedPosts = [];
let nextPage = 1;

// el references
let docEl = document.documentElement;
let fullPageLoader = document.querySelector('.loader');
let paginator = document.querySelector('.paginator');
let postsContainer = document.querySelector('.posts');
let feedFilterContainer = document.querySelector('.filterItems-feed');
let categoryFilterContainer = document.querySelector('.filterItems-category');
let settingsCont = document.querySelector('.settings');
let settingsToggle = document.querySelectorAll('.settings-toggle');
let filterButton = document.querySelector('.filterButton');
let themeSwitcher = document.querySelector('.themeSwitcher');
let noPosts = document.querySelector('.noPosts');
let clearFilter = document.querySelectorAll('.clearFilter');
let settingsToggler = document.querySelector('.settingsToggler');
let clearFilterInSettings = document.querySelector('.clearFilterInSettings');
let lastUpdateTime = document.querySelector('.lastUpdate-time');

// observers
let paginatorObserver = new IntersectionObserver((entries) => {
	if (entries[0].isIntersecting) {
		getPosts(parseInt(paginator.dataset.next));
		paginatorObserver.unobserve(paginator);
	}
});

// helpers
function formatDate(date) {
	if (!date) return;
	return new Date(date * 1000).toLocaleString( 'sk-SK', {	day: 'numeric',	month: 'numeric', year: 'numeric', hour: '2-digit',	minute: 'numeric' });
}
function makeElement(el, className, innerHTML = '', data = {}) {
	return Object.assign(document.createElement(el), {className, innerHTML, ...data});
}

// ajax
async function getCategories() {
	const data = await fetch(`/getCategories`);
	const jsonData = await data.json();
	if (jsonData.success && jsonData.categories?.length) {
		categories = jsonData.categories.reduce((acc, c) => {
			acc[c.id] = {...c};
			return acc;
		}, {});
		jsonData.categories.forEach(item => categoryFilterContainer.append(makeFilterLabel(item.id, `${item.name} (${item.feeds?.length})`, activeCategories.includes(item.id))));
	}
	return categories;
}

async function getFeeds() {
	const data = await fetch(`/getFeeds`);
	const jsonData = await data.json();
	if (jsonData.success && jsonData.feeds?.length) {
		feeds = jsonData.feeds.reduce((acc, f) => {
			acc[f.id] = {...f};
			return acc;
		}, {});
		jsonData.feeds.forEach(item => feedFilterContainer.append(makeFilterLabel(item.id, item.name, activeFeeds.includes(item.id), item.network, item.url)));
		feedFilterContainer.append(makeElement('div', 'filterItem filterItem-ckheckAll', 'Select all', {
			onclick: () => {
				Array.from(feedFilterContainer.querySelectorAll('.filterItem-cb')).forEach(el => {
					if (el.disabled) el.checked = false;
					else el.checked = true;
					feedFilterContainer.dispatchEvent(new Event('change'));
				})
			}
		}));
	}
	return feeds;
}

async function getPosts(page = 1) {
	let params = new URLSearchParams({ page, time });
	if (activeFeeds.length) activeFeeds.forEach(f => params.append('feeds[]', f))
	if (activeCategories.length) activeCategories.forEach(c => params.append('categories[]', c));

	if (!paginator.classList.contains('invisible')) paginator.classList.add('loading');

	const postsData = await fetch(`/getPosts?${params.toString()}`);
	const jsonData = await postsData.json();

	// render posts
	if (jsonData.posts.length) jsonData.posts.forEach(post => renderPost(post));

	// handle pagination
	if (jsonData.nextPage) {
		paginator.classList.remove('loading', 'invisible');
		paginator.dataset.next = jsonData.nextPage;
		paginatorObserver.observe(paginator);
	} else paginator.classList.add('invisible');

	noPosts.classList.toggle('invisible', jsonData.posts.length > 0);
	if (!fullPageLoader.classList.contains('invisible')) fullPageLoader.classList.add('invisible');

	return jsonData;
}

async function getLastUpdate() {
	const data = await fetch(`/getLastUpdate`);
	const jsonData = await data.json();

	if (jsonData?.date) lastUpdateTime.textContent = new Date(jsonData.date).toLocaleString();

	return jsonData;
}

// renderer
function makeFilterLabel(id, name, active, icon, link) {
	let label = makeElement('label', 'filterItem', `
		<input class="filterItem-cb" type="checkbox" name="feeds" value="${id}" ${active ? 'checked' : ''} />
		${icon ? `<svg class="ico filterItem-network"><use href="#i-${icon}" /></svg>` : ''}
		<span class="filterItem-name">${name}</span>
		${link ? renderLink('filterItem-link', link, '<svg class="ico"><use href="#i-link" /></svg>') : ''}`
	);
	label.dataset.id = id;

	return label;
}
function renderLink(linkClass = '', link = '', content) {
	if (!link) return;

	return `<a class="${linkClass}" href="${link}" target="_blank" rel="noopener noreferrer">${content}</a>`;
}
function renderImage(image, ratio, imgClass = 'postImage-img', alt = '') {
	if (!image) return;

	let width = 500;
	let height = ratio ? 500 / eval(ratio) : 500;

	return `<img class="${imgClass}" src="${image}" width="${width}" height="${height}" loading="lazy" alt="${alt}" />`;
}
function renderPost(post) {
	if (renderedPosts.includes(post.network_id)) return;

	let $post = makeElement('div', 'post', `
		<div class="postHeader">
			${renderLink('postHeader-imgLink', feeds[post.feed_id]?.url, `<img class="postHeader-img" src="${feeds[post.feed_id]?.thumbnail}" />`)}
			<div class="postHeader-info">
				${renderLink('postHeader-feed', feeds[post.feed_id]?.url, feeds[post.feed_id]?.name)}
				<div class="postHeader-time">${formatDate(post?.time)}</div>
			</div>
			<svg class="ico postHeader-ico"><use href="#i-${feeds[post.feed_id]?.network}" /></svg>
		</div>
		${post.content?.text ? `<div class="postText">${post.content.text}</div>` : ''}
		${post.type == 'image' ? renderLink('postImage-link', post?.content?.network_link, renderImage(post?.content?.image, post?.content?.['aspect-ratio'] ?? null)) : ''}
		${post.type == 'video' ? `<div class="postImage-link loadInlineVideo" data-url="${post?.content?.video}">
			${renderImage(post?.content?.thumbnail, post?.content?.['aspect-ratio'] ?? null)}
			<svg class="ico postImage-videoIco"><use href="#i-play" /></svg>
		</div>` : ''}
		${post.type == 'link' && post.content?.link ? renderLink('postLink', post.content.link, `
			${post.content?.meta?.['twitter:image'] ? renderImage(post.content.meta['twitter:image']) : ''}
			<div class="postLink-info">
				${post.content?.web ? `<div class="postLink-web">${post.content.web}</div>` : ''}
				${post.content?.title || post.content?.meta?.title || post.content?.meta?.['twitter:title'] ? `<div class="postLink-title">${post?.content?.meta?.title || post.content?.meta?.['twitter:title'] || post.content?.title}</div>` : ''}
				${post.content?.meta?.description ? `<div class="postLink-description">${post.content.meta.description}</div>` : ''}
			</div>
		`) : ''}
		${post.type == 'gallery' ? renderLink('postImage-link postGallery', post?.content?.network_link, `
			${post.content?.gallery?.map(g => `<div class="postGallery-item">
				${renderImage(g.image, 1, 'postGallery-img', g.alt)}
				${g.hasMore ? `<span class="postGallery-plus">${g.hasMore}</span>` : ''}
			</div>`).join('')}
		`) : ''}
		<div class="postMeta">
			${renderLink('postMeta-link', post?.content?.network_link, `
				${post?.content?.likes ? `<span class="postMeta-linkInner"><svg class="ico"><use href="#i-like" /></svg> ${post.content.likes}</span>` : ''}
				${post?.content?.comments ? `<span class="postMeta-linkInner"><svg class="ico"><use href="#i-comment" /></svg> ${post.content.comments}</span>` : ''}
			`)}
			${renderLink('postMeta-link', post?.content?.network_link, `
				<svg class="ico"><use href="#i-link" /></svg>
			`)}
		</div>`, { id: post.network_id }
	);
	postsContainer.append($post);

	renderedPosts.push(post.network_id);
}

// actions
async function initLoad() {
	await Promise.all([getCategories(), getFeeds()]);
	await getPosts();
	setActiveFilterClass();
	setFilterState();
	getLastUpdate();
}

function applyFilter(oFeeds, oCategories) {
	paginator.classList.add('invisible')
	activeFeeds = oFeeds || getCheckedFeeds();
	activeCategories = oCategories || getCheckedCategories();
	time = parseInt(Date.now() / 1000);
	renderedPosts = [];
	localStorage.setItem('feeds', JSON.stringify(activeFeeds));
	localStorage.setItem('categories', JSON.stringify(activeCategories));

	if (oFeeds) Array.from(feedFilterContainer.querySelectorAll('.filterItem-cb')).forEach(i => { i.checked = oFeeds.includes(parseInt(i.value)) });
	if (oCategories) Array.from(categoryFilterContainer.querySelectorAll('.filterItem-cb')).forEach(i => { i.checked = oCategories.includes(parseInt(i.value)) });

	postsContainer.innerHTML = '';
	settingsCont.classList.remove('active');
	filterButton.classList.add('disabled');
	fullPageLoader.classList.remove('invisible');

	if (Array.from(feedFilterContainer.querySelectorAll('.labelDisabled .filterItem-cb:checked')).length) Array.from(feedFilterContainer.querySelectorAll('.labelDisabled .filterItem-cb:checked')).forEach(el => {
		el.checked = false;
	})

	getPosts();
	setActiveFilterClass();
}

function setFilterState() {
	let checkedCategories = getCheckedCategories();

	if (checkedCategories.length) {
		let availableFeeds = checkedCategories.reduce((acc, cid) => {
			acc.push(...categories[cid].feeds);
			return acc;
		}, []);

		checkedCategories.forEach(el => {
			Array.from(feedFilterContainer.querySelectorAll('.filterItem:not(.filterItem-ckheckAll)')).forEach(el => {
				if (!availableFeeds.includes(parseInt(el.dataset.id))) {
					el.classList.add('labelDisabled');
					el.querySelector('.filterItem-cb').disabled = true;
				} else if (el.classList.contains('labelDisabled')) {
					el.classList.remove('labelDisabled');
					el.querySelector('.filterItem-cb').disabled = false;
				}
			});
		});
	} else if (Array.from(feedFilterContainer.querySelectorAll('.labelDisabled')).length) {
		Array.from(feedFilterContainer.querySelectorAll('.labelDisabled')).forEach(el => {
			el.classList.remove('labelDisabled');
			el.querySelector('.filterItem-cb').disabled = false;
		});
	}

	let checkedFeeds = getCheckedFeeds();

	if (checkedFeeds.toString() == activeFeeds.toString() && checkedCategories.toString() == activeCategories.toString()) filterButton.classList.add('disabled');
	else filterButton.classList.remove('disabled');
}

function setActiveFilterClass() {
	let hasActiveFilter = activeCategories.length || activeFeeds.length;
	settingsToggler.classList.toggle('filterSet', hasActiveFilter);
	clearFilterInSettings.classList.toggle('invisible', !hasActiveFilter);
}

// getters
function getCheckedFeeds() {
	return Array.from(feedFilterContainer.querySelectorAll(':checked:not(:disabled)')).map(i => parseInt(i.value));
}

function getCheckedCategories() {
	return Array.from(categoryFilterContainer.querySelectorAll(':checked')).map(i => parseInt(i.value));
}

// events
paginator.addEventListener('click', function(e) {
	e.preventDefault();
	if (['invisible', 'loading'].some(c => paginator.classList.contains(c)) || !paginator.dataset.next) return;

	getPosts(parseInt(paginator.dataset.next));
});

settingsToggle.forEach(el => {
	el.addEventListener('click', function(e) {
		e.preventDefault();
		settingsCont.classList.toggle('active');
	});
});

[feedFilterContainer, categoryFilterContainer].forEach(f => f.addEventListener('change', setFilterState));

filterButton.addEventListener('click', function() {
	if (filterButton.classList.contains('disabled')) return;

	applyFilter();
});

clearFilter.forEach(el => {
	el.addEventListener('click', function() {
		applyFilter([], []);
	});
});

document.addEventListener('click', function(e) {
	if (e.target.classList.contains('loadInlineVideo') && !e.target.classList.contains('videoLoading') && !e.target.classList.contains('videoLoaded')) {
		let target = e.target;
		let img = target.querySelector('.postImage-img');

		let video = makeElement('video', 'postImage-video', `<source src="${target.dataset.url}" />`, {
			controls: true,
			width: img.getAttribute('width') ?? '',
			height: img.getAttribute('height') ?? '',
			autoplay: true,
			playsInline: true,
			webkitPlaysinline: true
		});
		video.addEventListener('play', () => {
			img.replaceWith(video);
			videoIntersectionObserver.observe(video);
		}, {once: true});

		video.addEventListener('loadstart', () => {
			e.target.classList.add('videoLoading');
		})
		video.addEventListener('playing', () => {
			e.target.classList.remove('videoLoading');
			e.target.classList.add('videoLoaded');
		})
	}
})

// videoIntersectionObserver
let videoIntersectionObserver = new IntersectionObserver((entries) => {
	entries.forEach((entry) => {
		if (!entry.isIntersecting && !entry.target.paused && !entry.target.ended && entry.target.readyState > 2) entry.target.pause();
	});
});

// theme
if (localStorage.getItem('theme')) docEl.classList.add(`theme-${localStorage.getItem('theme')}`);
else if (window.matchMedia('(prefers-color-scheme: dark)').matches) docEl.classList.add('theme-dark');
else docEl.classList.add('theme-light');

let currentThemeDark = docEl.classList.contains('theme-dark');
document.querySelector('meta[name="theme-color"]').setAttribute('content', currentThemeDark ? '#000000' : '#ffffff');

themeSwitcher.addEventListener('click', function(e) {
	e.preventDefault();
	docEl.classList.toggle('theme-dark');
	docEl.classList.toggle('theme-light');

	currentThemeDark = !currentThemeDark;

	document.querySelector('meta[name="theme-color"]').setAttribute('content', currentThemeDark ? '#000000' : '#ffffff');

	localStorage.removeItem('theme');
	if (currentThemeDark && !window.matchMedia('(prefers-color-scheme: dark)').matches) localStorage.setItem('theme', 'dark');
	else if (!currentThemeDark && window.matchMedia('(prefers-color-scheme: dark)').matches) localStorage.setItem('theme', 'light');
});

// init
initLoad();

// PWA install
const isStandalone = window.matchMedia('(display-mode: standalone)').matches;

if (!isStandalone) {
	const isIOS = /iPad|iPhone|iPod/.test(navigator?.platform || navigator?.userAgentData?.platform) || (navigator?.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
	const isSafari = /Apple/i.test(navigator.vendor) && /Safari/i.test(navigator.userAgent);

	let installPromptCancelled = localStorage.getItem('installClosed') ?? false;
	let installPrompt = null;
	const iOsInstallPrompt = isIOS && isSafari && !isStandalone;

	let installModal = null;
	let installButton = null;
	let iOsInstallModal = null;

	window.cancelInstallation = function() {
		localStorage.setItem('installClosed', 1);
		installModal.classList.remove('installPrompt-show');
		if (installButton) installButton.classList.remove('installButton-invisible');
	}
	window.installApp = function() {
		if (iOsInstallPrompt) return showIOsInstall();

		if (!installPrompt) return;

		installPrompt.prompt();
		installPrompt.userChoice.then(({outcome}) => {
			if (outcome == 'accepted') {
				if (installModal) installModal.classList.remove('installPrompt-show');
				if (installButton) installButton.classList.add('installButton-invisible');
			}
		});
	}

	function showIOsInstall() {
		if (!iOsInstallModal) {
			iOsInstallModal = makeElement('div', 'iOsInstallModal');
			let iOsModalBackdrop = makeElement('div', 'iOsInstallModal-backdrop', { onclick: () => iOsInstallModal.classList.remove('active') });
			let iOsModelContent = makeElement('div', 'iOsInstallModal-cont', `
				<h3 class="iOsInstallModal-title">Download Feeed</h3>
				<div class="iOsInstallModal-line">1. Stlač tlačidlo <strong>Zdieľať</strong></div>
				<img class="iOsInstallModal-img" src="/img/step1.png" />
				<div class="iOsInstallModal-line">2. V kontextovom okne vyber možnosť <strong>Pridať na plochu</strong></div>
				<img class="iOsInstallModal-img" src="/img/step2.png" />
				<div class="iOsInstallModal-line">3. Klepni na položku <strong>Pridať</strong> v pravom hornom rohu</div>
				<img class="iOsInstallModal-img" src="/img/step3.png" />`
			);
			let iOsModalClose = makeElement('button', 'floatingButton iOsInstallModal-x', '<svg class="ico"><use href="#i-x" /></svg>', {
				onclick: () => iOsInstallModal.classList.remove('active')
			});

			iOsModelContent.append(iOsModalClose);
			iOsInstallModal.append(iOsModalBackdrop, iOsModelContent);
			document.body.append(iOsInstallModal);
		}

		if (installModal) installModal.classList.remove('installPrompt-show');
		setTimeout(() => {
			iOsInstallModal.classList.add('active');
		}, 10);
	}

	function showInstallActions() {
		installButton = makeElement('button', 'button installButton installButton-invisible', '<svg class="ico installButton-ico"><use href="#i-download" /></svg> Download Feeed', { onclick: () => installApp() });
		document.body.append(installButton);

		if (installPromptCancelled) {
			setTimeout(() => {
				installButton.classList.remove('installButton-invisible');
			}, 500);
			return;
		}

		installModal = makeElement('div', 'installPrompt', `
			<svg class="ico installPrompt-ico"><use href="#i-download" /></svg>
			<div class="installPrompt-text">
				<h3 class="installPrompt-title">Download Feeed</h3>
				<div class="installPrompt-line">Want to download <strong>Feeed</strong> as an app to your device??</div>
			</div>
			<div class="installPrompt-buttons">
				<button class="button" onclick="installApp()">Download</button>
				<button class="button outline" onclick="cancelInstallation()">Cancel</button>
			</div>`
		);

		document.body.append(installModal);
		setTimeout(() => {
			installModal.classList.add('installPrompt-show');
		}, 500);
	}


	if (iOsInstallPrompt) showInstallActions();
	else {
		window.addEventListener('beforeinstallprompt', (e) => {
			e.preventDefault();
			if (!installPrompt) showInstallActions();
			installPrompt = e;
		});
	}
}