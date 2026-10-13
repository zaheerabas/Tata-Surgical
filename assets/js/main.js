/**
 * Tata Surgical theme scripts.
 * Header menus, mobile drawer, live search, quote list, WhatsApp chat and small helpers.
 * No dependencies.
 */
(function () {
	'use strict';

	var D = window.tataData || { i18n: {} };
	var T = D.i18n || {};
	var $ = function (s, c) { return (c || document).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
	var body = document.body;
	var desktop = window.matchMedia('(min-width: 1101px)');
	var lastFocus = null;

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}
	function fmt(str, val) { return String(str || '').replace('%s', val).replace('%d', val); }
	function icon(id) { return '<svg class="icon" aria-hidden="true" focusable="false"><use href="#' + id + '"></use></svg>'; }
	function lockScroll(on) { body.style.overflow = on ? 'hidden' : ''; }

	/* Sticky header ----------------------------------------------------- */
	var header = $('[data-header]');
	if (header) {
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 30); };
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* Menus: mega + dropdowns ------------------------------------------- */
	var menus = $$('[data-menu]');
	function panelOf(li) { var b = $('[data-menu-toggle]', li); return b ? document.getElementById(b.getAttribute('aria-controls')) : null; }
	function closeMenu(li) {
		var b = $('[data-menu-toggle]', li), p = panelOf(li);
		if (!b || !p) { return; }
		b.setAttribute('aria-expanded', 'false'); p.hidden = true; li.classList.remove('is-open');
	}
	function openMenu(li) {
		menus.forEach(function (o) { if (o !== li) { closeMenu(o); } });
		var b = $('[data-menu-toggle]', li), p = panelOf(li);
		if (!b || !p) { return; }
		b.setAttribute('aria-expanded', 'true'); p.hidden = false; li.classList.add('is-open');
	}
	menus.forEach(function (li) {
		var b = $('[data-menu-toggle]', li), t;
		if (!b) { return; }
		b.addEventListener('click', function () { b.getAttribute('aria-expanded') === 'true' ? closeMenu(li) : openMenu(li); });
		li.addEventListener('mouseenter', function () { if (!desktop.matches) { return; } clearTimeout(t); t = setTimeout(function () { openMenu(li); }, 110); });
		li.addEventListener('mouseleave', function () { if (!desktop.matches) { return; } clearTimeout(t); t = setTimeout(function () { closeMenu(li); }, 220); });
	});

	/* Top bar pop-ups (WeChat, language) -------------------------------- */
	var pops = $$('[data-pop]');
	function closePops(except) {
		pops.forEach(function (b) {
			if (b === except) { return; }
			b.setAttribute('aria-expanded', 'false');
			var p = document.getElementById(b.getAttribute('data-pop')); if (p) { p.hidden = true; }
		});
	}
	pops.forEach(function (b) {
		b.addEventListener('click', function (e) {
			e.stopPropagation();
			var open = b.getAttribute('aria-expanded') !== 'true';
			closePops(b);
			b.setAttribute('aria-expanded', String(open));
			var p = document.getElementById(b.getAttribute('data-pop')); if (p) { p.hidden = !open; }
		});
	});

	/* Mobile drawer ------------------------------------------------------ */
	var navToggle = $('[data-nav-toggle]'), backdrop = $('[data-backdrop]');
	function setNav(open) {
		if (!navToggle) { return; }
		body.classList.toggle('nav-open', open);
		navToggle.setAttribute('aria-expanded', String(open));
		navToggle.setAttribute('aria-label', open ? (T.closeMenu || 'Close menu') : (T.openMenu || 'Open menu'));
		var o = $('[data-ico-open]', navToggle), c = $('[data-ico-close]', navToggle);
		if (o) { o.hidden = open; }
		if (c) { c.hidden = !open; }
		if (backdrop) { backdrop.hidden = !open; }
	}
	if (navToggle) { navToggle.addEventListener('click', function () { setNav(!body.classList.contains('nav-open')); }); }
	if (backdrop) { backdrop.addEventListener('click', function () { setNav(false); }); }

	/* Quote list (kept in the visitor's browser) ------------------------ */
	var KEY = 'tataQuote';
	var store = {
		get: function () { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } },
		set: function (list) { try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {} document.dispatchEvent(new CustomEvent('tata:quote')); }
	};
	function hasItem(id) { return store.get().some(function (x) { return String(x.id) === String(id); }); }
	function addItem(data, qty) {
		var list = store.get(), found = null;
		list.forEach(function (x) { if (String(x.id) === String(data.id)) { found = x; } });
		qty = Math.max(1, parseInt(qty, 10) || 100);
		if (found) { found.qty = qty; } else { list.push({ id: data.id, sku: data.sku || '', name: data.name || '', url: data.url || '', cat: data.cat || '', qty: qty }); }
		store.set(list);
	}

	var badge = $('[data-quote-badge]'), qItems = $('[data-quote-items]'), qWa = $('[data-quote-wa]');
	function qtyControl(i, x) {
		return '<div class="qty"><button type="button" data-dec="' + i + '" aria-label="' + esc(T.decrease) + '">−</button>' +
			'<input type="number" min="1" step="1" value="' + esc(x.qty) + '" data-qty="' + i + '" aria-label="' + esc(fmt(T.qtyFor, x.name)) + '">' +
			'<button type="button" data-inc="' + i + '" aria-label="' + esc(T.increase) + '">+</button></div>';
	}
	function renderQuote(bump) {
		var list = store.get();
		if (badge) {
			badge.textContent = list.length; badge.hidden = !list.length;
			if (bump) { badge.classList.remove('bump'); void badge.offsetWidth; badge.classList.add('bump'); }
		}
		if (qItems) {
			qItems.innerHTML = list.length ? list.map(function (x, i) {
				return '<div class="q-item"><div><div class="q-item__name">' + (x.url ? '<a href="' + esc(x.url) + '">' + esc(x.name) + '</a>' : esc(x.name)) + '</div>' +
					(x.sku ? '<div class="q-item__code">' + esc(T.itemCode) + ' <span class="notranslate">' + esc(x.sku) + '</span></div>' : '') + '</div>' +
					qtyControl(i, x) + '<button class="q-item__remove" type="button" data-remove="' + i + '">' + esc(T.remove) + '</button></div>';
			}).join('') : '<p class="side__empty">' + esc(T.emptyQuote) + '</p>';
		}
		if (qWa && D.whatsapp) {
			var lines = list.map(function (x) { return '- ' + (x.sku ? x.sku + ', ' : '') + x.name + ': ' + x.qty + ' ' + (T.pcs || 'pcs'); });
			qWa.href = 'https://wa.me/' + D.whatsapp + '?text=' + encodeURIComponent((T.waQuoteIntro || '') + '\n' + lines.join('\n'));
		}
		$$('[data-add-quote]').forEach(function (b) {
			var inList = hasItem(b.getAttribute('data-id'));
			b.classList.toggle('is-added', inList);
			var l = $('[data-label]', b) || b;
			if (!b.hasAttribute('data-default-label')) { b.setAttribute('data-default-label', l.textContent); }
			l.textContent = inList ? (T.added || 'Added') : b.getAttribute('data-default-label');
		});
		renderForm();
	}
	function editList(container) {
		if (!container) { return; }
		container.addEventListener('click', function (e) {
			var t = e.target.closest('button'); if (!t) { return; }
			var list = store.get();
			if (t.hasAttribute('data-inc')) { list[+t.getAttribute('data-inc')].qty += 50; }
			else if (t.hasAttribute('data-dec')) { var x = list[+t.getAttribute('data-dec')]; x.qty = Math.max(1, x.qty - 50); }
			else if (t.hasAttribute('data-remove')) { list.splice(+t.getAttribute('data-remove'), 1); }
			else { return; }
			store.set(list);
		});
		container.addEventListener('change', function (e) {
			var t = e.target; if (!t.hasAttribute('data-qty')) { return; }
			var list = store.get(); list[+t.getAttribute('data-qty')].qty = Math.max(1, parseInt(t.value, 10) || 1); store.set(list);
		});
	}
	editList(qItems);

	/* Quote request form: show the list and send it as JSON */
	var formItems = $('[data-form-items]'), formInput = $('[data-form-items-input]');
	function renderForm() {
		if (!formItems || !formInput) { return; }
		var list = store.get();
		formInput.value = JSON.stringify(list.map(function (x) { return { id: x.id, sku: x.sku, name: x.name, qty: x.qty }; }));
		formItems.innerHTML = list.length ? list.map(function (x, i) {
			return '<div class="ef-item"><div><span class="ef-item__name">' + esc(x.name) + '</span>' + (x.sku ? '<span class="ef-item__code">' + esc(T.itemCode) + ' ' + esc(x.sku) + '</span>' : '') + '</div>' +
				qtyControl(i, x) + '<button class="q-item__remove" type="button" data-remove="' + i + '">' + esc(T.remove) + '</button></div>';
		}).join('') : '<p class="ef-empty">' + esc(T.listEmptyForm) + '</p>';
	}
	editList(formItems);
	if ($('[data-clear-quote]')) { store.set([]); }

	/* Add to quote buttons anywhere on the page */
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-add-quote]'); if (!b) { return; }
		var qty = 100, from = b.getAttribute('data-qty-from');
		if (from && $(from)) { qty = $(from).value; }
		addItem({ id: b.getAttribute('data-id'), sku: b.getAttribute('data-sku'), name: b.getAttribute('data-name'), url: b.getAttribute('data-url'), cat: b.getAttribute('data-cat') }, qty);
		renderQuote(true);
		flyToQuote(b);
	});

	/* A small dot flies from the button to the quote list icon */
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	function flyToQuote(from) {
		var target = $('[data-open-quote]');
		if (!target || reduceMotion.matches || !document.body.animate) { return; }
		var a = from.getBoundingClientRect(), t = target.getBoundingClientRect();
		if (!t.width) { return; }
		var x0 = a.left + a.width / 2, y0 = a.top + a.height / 2, x1 = t.left + t.width / 2, y1 = t.top + t.height / 2;
		var dot = document.createElement('span');
		dot.className = 'fly-dot';
		dot.style.left = x0 + 'px'; dot.style.top = y0 + 'px';
		document.body.appendChild(dot);
		var dx = x1 - x0, dy = y1 - y0, lift = Math.min(-60, dy / 2 - 80);
		dot.animate([
			{ transform: 'translate(0,0) scale(1)', opacity: 1 },
			{ transform: 'translate(' + dx * 0.5 + 'px,' + (dy * 0.5 + lift) + 'px) scale(1.1)', opacity: 1, offset: 0.55 },
			{ transform: 'translate(' + dx + 'px,' + dy + 'px) scale(.4)', opacity: 0.2 }
		], { duration: 650, easing: 'cubic-bezier(.3,.6,.3,1)' }).onfinish = function () {
			dot.remove();
			target.classList.remove('got-item'); void target.offsetWidth; target.classList.add('got-item');
		};
	}
	document.addEventListener('tata:quote', function () { renderQuote(false); });
	window.addEventListener('storage', function (e) { if (e.key === KEY) { renderQuote(false); } });

	/* Quote drawer */
	var quote = $('#quote');
	function openQuote() { if (!quote) { return; } lastFocus = document.activeElement; quote.hidden = false; lockScroll(true); setTimeout(function () { var c = $('[data-close-quote]'); if (c) { c.focus(); } }, 20); }
	function closeQuote() { if (!quote) { return; } quote.hidden = true; lockScroll(false); if (lastFocus) { lastFocus.focus(); } }
	$$('[data-open-quote]').forEach(function (b) { b.addEventListener('click', openQuote); });
	$$('[data-close-quote]').forEach(function (b) { b.addEventListener('click', closeQuote); });
	if (quote) { quote.addEventListener('click', function (e) { if (e.target === quote) { closeQuote(); } }); }

	/* Single product quantity steppers */
	$$('[data-qty-step]').forEach(function (b) {
		b.addEventListener('click', function () {
			var input = b.parentNode.querySelector('[data-qty-input]'); if (!input) { return; }
			input.value = Math.max(1, (parseInt(input.value, 10) || 0) + parseInt(b.getAttribute('data-qty-step'), 10));
		});
	});

	/* Live search -------------------------------------------------------- */
	var search = $('#search'), input = $('#search-input'), results = $('[data-results]'), start = $('[data-search-start]'), rLabel = $('[data-results-label]');
	var timer, ctrl, cache = {};

	function highlight(text, q) {
		var i = text.toLowerCase().indexOf(q.toLowerCase());
		if (!q || i < 0) { return esc(text); }
		return esc(text.slice(0, i)) + '<mark>' + esc(text.slice(i, i + q.length)) + '</mark>' + esc(text.slice(i + q.length));
	}
	function showResults(q, data) {
		if (start) { start.hidden = true; }
		rLabel.hidden = false;
		var items = data.items || [];
		rLabel.textContent = data.total === 1 ? T.oneResult : fmt(T.results, data.total || 0);
		if (!items.length) {
			results.innerHTML = '<p class="no-results">' + esc(fmt(T.noResults, q)) + ' <a href="' + esc(D.customUrl) + '">' + esc(T.sendDrawing) + '</a>.</p>';
			return;
		}
		results.innerHTML = items.map(function (p) {
			var inList = hasItem(p.id);
			return '<div class="result"><span class="result__ico">' + (p.image ? '<img src="' + esc(p.image) + '" alt="" loading="lazy">' : icon('c-forceps')) + '</span>' +
				'<div><div class="result__name"><a href="' + esc(p.url) + '">' + highlight(p.name, q) + '</a></div>' +
				'<div class="result__meta">' + (p.sku ? esc(T.itemCode) + ' <b class="notranslate">' + highlight(p.sku, q) + '</b>' : '') + (p.cat ? (p.sku ? ', ' : '') + esc(p.cat) : '') + '</div></div>' +
				'<button class="btn btn--outline add-btn' + (inList ? ' is-added' : '') + '" type="button" data-add-quote data-id="' + esc(p.id) + '" data-sku="' + esc(p.sku) + '" data-name="' + esc(p.name) + '" data-url="' + esc(p.url) + '" data-cat="' + esc(p.cat) + '"><span data-label>' + esc(inList ? T.added : T.addToQuote) + '</span></button></div>';
		}).join('') + (data.total > items.length ? '<a class="results__all" href="' + esc(data.allUrl) + '">' + esc(T.viewAll) + '</a>' : '');
	}
	function runSearch(q) {
		q = q.trim();
		if (q.length < 2) { if (start) { start.hidden = false; } rLabel.hidden = true; results.innerHTML = ''; return; }
		if (cache[q]) { showResults(q, cache[q]); return; }
		rLabel.hidden = false; rLabel.textContent = T.searching;
		if (ctrl && ctrl.abort) { ctrl.abort(); }
		ctrl = window.AbortController ? new AbortController() : null;
		fetch(D.searchUrl + (D.searchUrl.indexOf('?') > -1 ? '&' : '?') + 'q=' + encodeURIComponent(q), ctrl ? { signal: ctrl.signal } : {})
			.then(function (r) { return r.json(); })
			.then(function (data) { cache[q] = data; if (input.value.trim() === q) { showResults(q, data); } })
			.catch(function () {});
	}
	function openSearch() {
		if (!search) { return; }
		lastFocus = document.activeElement;
		menus.forEach(closeMenu); setNav(false);
		search.hidden = false; lockScroll(true);
		setTimeout(function () { input.focus(); input.select(); }, 20);
	}
	function closeSearch() { if (!search) { return; } search.hidden = true; lockScroll(false); if (lastFocus) { lastFocus.focus(); } }
	$$('[data-open-search]').forEach(function (b) { b.addEventListener('click', openSearch); });
	$$('[data-close-search]').forEach(function (b) { b.addEventListener('click', closeSearch); });
	if (search) {
		search.addEventListener('click', function (e) { if (e.target === search) { closeSearch(); } });
		input.addEventListener('input', function () { clearTimeout(timer); var v = input.value; timer = setTimeout(function () { runSearch(v); }, 220); });
		$$('.chip', search).forEach(function (c) { c.addEventListener('click', function () { input.value = c.textContent; runSearch(input.value); input.focus(); }); });
	}

	/* Custom order form: prefill from "Request a variant" links */
	var params = new URLSearchParams(window.location.search);
	var customMsg = $('[data-enquiry-form="custom"] textarea');
	if (customMsg && params.get('variant') && !customMsg.value) {
		customMsg.value = 'Variant of ' + params.get('variant') + ': ';
	}

	/* Collapsible category description */
	$$('[data-collapse]').forEach(function (box) {
		var inner = box.firstElementChild, btn = $('[data-collapse-toggle]', box);
		if (!inner || !btn || inner.scrollHeight < 140) { return; }
		box.classList.add('is-collapsed'); btn.hidden = false;
		btn.addEventListener('click', function () { var c = box.classList.toggle('is-collapsed'); btn.textContent = c ? btn.getAttribute('data-more') || btn.textContent : btn.getAttribute('data-less') || btn.textContent; btn.hidden = !c; });
	});

	/* WhatsApp chat -------------------------------------------------------- */
	var waBtn = $('[data-wa]'), waCard = $('#wa-card');
	if (waBtn && waCard) {
		waBtn.addEventListener('click', function (e) { e.stopPropagation(); var o = waCard.hidden; waCard.hidden = !o; waBtn.setAttribute('aria-expanded', String(o)); });
	}

	/* Back to top */
	$$('[data-top]').forEach(function (b) { b.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); }); });

	/* Keyboard and outside clicks ---------------------------------------- */
	document.addEventListener('keydown', function (e) {
		var tag = (document.activeElement && document.activeElement.tagName) || '';
		if (e.key === '/' && search && search.hidden && !/input|textarea|select/i.test(tag) && !document.activeElement.isContentEditable) { e.preventDefault(); openSearch(); return; }
		if (e.key !== 'Escape') { return; }
		if (search && !search.hidden) { closeSearch(); return; }
		if (quote && !quote.hidden) { closeQuote(); return; }
		var open = menus.filter(function (m) { return m.classList.contains('is-open'); })[0];
		if (open) { closeMenu(open); var b = $('[data-menu-toggle]', open); if (b) { b.focus(); } return; }
		closePops();
		if (waCard && !waCard.hidden) { waCard.hidden = true; waBtn.setAttribute('aria-expanded', 'false'); return; }
		if (body.classList.contains('nav-open')) { setNav(false); navToggle.focus(); }
	});
	document.addEventListener('click', function (e) {
		menus.forEach(function (m) { if (m.classList.contains('is-open') && !m.contains(e.target)) { closeMenu(m); } });
		if (!e.target.closest('.pop')) { closePops(); }
		if (waCard && !waCard.hidden && !e.target.closest('.wa')) { waCard.hidden = true; waBtn.setAttribute('aria-expanded', 'false'); }
	});
	var onBp = function () { if (desktop.matches) { setNav(false); } menus.forEach(closeMenu); };
	if (desktop.addEventListener) { desktop.addEventListener('change', onBp); } else if (desktop.addListener) { desktop.addListener(onBp); }

	/* Shop: layout switch, products per page, categories (sidebar dropdowns and mobile toggle) */
	var setPref = function (name, value) {
		document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=31536000; SameSite=Lax';
	};
	var shopMain = $('.shop-main');
	$$('[data-layout-btn]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var layout = btn.getAttribute('data-layout-btn');
			if (shopMain) { shopMain.setAttribute('data-layout', layout); }
			$$('[data-layout-btn]').forEach(function (b) { b.setAttribute('aria-pressed', String(b === btn)); });
			setPref('tata_layout', layout);
		});
	});
	$$('[data-per-page]').forEach(function (sel) {
		sel.addEventListener('change', function () { setPref('tata_per_page', sel.value); sel.form.submit(); });
	});
	$$('[data-cat-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var li = btn.closest('li'), open = !li.classList.contains('is-open');
			li.classList.toggle('is-open', open);
			btn.setAttribute('aria-expanded', String(open));
		});
	});
	var filterBtn = $('[data-filter-toggle]'), shopSide = $('#shop-side'), shopBackdrop = $('[data-filter-backdrop]');
	var setFilters = function (open) {
		if (!filterBtn || !shopSide) { return; }
		shopSide.classList.toggle('is-open', open);
		filterBtn.setAttribute('aria-expanded', String(open));
		if (shopBackdrop) { shopBackdrop.hidden = !open; }
		lockScroll(open);
		if (open) { var c = $('[data-filter-close]', shopSide); if (c) { c.focus(); } } else { filterBtn.focus(); }
	};
	if (filterBtn && shopSide) {
		filterBtn.addEventListener('click', function () { setFilters(!shopSide.classList.contains('is-open')); });
		$$('[data-filter-close]').forEach(function (b) { b.addEventListener('click', function () { setFilters(false); }); });
		if (shopBackdrop) { shopBackdrop.addEventListener('click', function () { setFilters(false); }); }
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && shopSide.classList.contains('is-open')) { setFilters(false); } });
	}

	/* Translator (Google Translate), loaded only when a language other than English is chosen */
	var translator = $('[data-translator]');
	if (translator) {
		var tLangs = translator.getAttribute('data-langs');
		var getLang = function () {
			var m = document.cookie.match(/(?:^|;\s*)googtrans=\/[^\/;]*\/([^;]+)/);
			return m ? decodeURIComponent(m[1]) : 'en';
		};
		var setLangCookie = function (value) {
			var expire = value ? '' : '; expires=Thu, 01 Jan 1970 00:00:00 GMT';
			var cookie = 'googtrans=' + value + expire + '; path=/';
			var host = window.location.hostname;
			document.cookie = cookie;
			if (host.indexOf('.') > -1 && !/^[0-9.]+$/.test(host)) {
				document.cookie = cookie + '; domain=' + host;
				document.cookie = cookie + '; domain=.' + host.replace(/^www\./, '');
			}
		};
		var loadTranslate = function () {
			if (window.google && window.google.translate) { return; }
			window.tataTranslateInit = function () {
				new window.google.translate.TranslateElement({ pageLanguage: 'en', includedLanguages: tLangs, autoDisplay: false }, 'google_translate_element');
			};
			var sc = document.createElement('script');
			sc.src = 'https://translate.google.com/translate_a/element.js?cb=tataTranslateInit';
			sc.async = true;
			document.head.appendChild(sc);
		};
		var current = getLang();
		/* Keep the label right even when the page comes from a cache */
		var opt = $('[data-lang="' + current + '"]', translator);
		if (opt) {
			$$('[data-lang]', translator).forEach(function (b) { b.removeAttribute('aria-current'); });
			opt.setAttribute('aria-current', 'true');
			var label = $('[data-lang-label]', translator);
			if (label) { label.textContent = $('small', opt).textContent; }
		}
		if (current !== 'en') {
			document.documentElement.setAttribute('data-translated', current);
			loadTranslate();
		}
		translator.addEventListener('click', function (e) {
			var b = e.target.closest('[data-lang]');
			if (!b) { return; }
			var code = b.getAttribute('data-lang');
			if (code === getLang()) { closePops(); return; }
			setLangCookie(code === 'en' ? '' : '/en/' + code);
			document.documentElement.classList.add('is-switching-lang');
			window.location.reload();
		});
	}

	/* Reveal on scroll */
	window.tataRevealReady = true;
	var revealEls = $$('[data-reveal]');
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		revealEls.forEach(function (el) { io.observe(el); });
	} else {
		revealEls.forEach(function (el) { el.classList.add('is-in'); });
	}

	renderQuote(false);
})();
