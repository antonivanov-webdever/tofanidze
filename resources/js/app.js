import './bootstrap';

import Alpine from 'alpinejs';

/**
 * Reveals elements on scroll. Usage: <div x-data="reveal()">.
 * Falls back to "visible immediately" when IntersectionObserver is missing
 * or the visitor asked for reduced motion.
 */
Alpine.data('reveal', (delay = 0) => ({
    shown: false,

    init() {
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reducedMotion || !('IntersectionObserver' in window)) {
            this.shown = true;
            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;

                setTimeout(() => (this.shown = true), delay);
                observer.disconnect();
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
        );

        observer.observe(this.$el);
    },
}));

/** Copies text to the clipboard and flashes a confirmation. */
Alpine.data('copyable', (value) => ({
    copied: false,

    async copy() {
        try {
            await navigator.clipboard.writeText(value);
        } catch {
            const input = document.createElement('textarea');
            input.value = value;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
        }

        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
    },
}));

/**
 * Client-side technology filter for the projects index.
 * `items` mirrors the rendered cards so the empty state can be derived
 * without inspecting the DOM.
 */
Alpine.data('projectFilter', (items = []) => ({
    active: 'all',
    query: '',

    matches(technologies, haystack) {
        const byTech = this.active === 'all' || technologies.split('|').includes(this.active);
        const byQuery = this.query.trim() === '' || haystack.includes(this.query.trim().toLowerCase());

        return byTech && byQuery;
    },

    get visibleCount() {
        return items.filter((item) => this.matches(item.tech, item.haystack)).length;
    },

    get isFiltered() {
        return this.active !== 'all' || this.query.trim() !== '';
    },

    reset() {
        this.active = 'all';
        this.query = '';
    },
}));

/** Tracks which section is in view to highlight the matching nav link. */
Alpine.data('scrollSpy', (ids = []) => ({
    current: ids[0] ?? '',
    scrolled: false,

    init() {
        this.onScroll();
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });

        if (!('IntersectionObserver' in window) || ids.length === 0) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries
                    .filter((entry) => entry.isIntersecting)
                    .forEach((entry) => (this.current = entry.target.id));
            },
            { rootMargin: '-45% 0px -50% 0px' },
        );

        ids.forEach((id) => {
            const el = document.getElementById(id);
            if (el) observer.observe(el);
        });
    },

    onScroll() {
        this.scrolled = window.scrollY > 24;
    },
}));

window.Alpine = Alpine;
Alpine.start();

/**
 * First-party page-view beacon — no cookies, no fingerprinting, no
 * third-party script. Fires once per page load. Uses sendBeacon so it
 * survives the page unloading before the request completes; falls back to a
 * keepalive fetch on browsers without it.
 */
(function sendPageViewBeacon() {
    const payload = JSON.stringify({
        type: 'page_view',
        path: window.location.pathname,
        referrer: document.referrer || null,
    });

    const url = '/api/v1/events';

    try {
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, new Blob([payload], { type: 'application/json' }));
            return;
        }
    } catch {
        // Fall through to fetch.
    }

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: payload,
        keepalive: true,
    }).catch(() => {});
})();
