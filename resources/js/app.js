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

/**
 * Contact form submission. A plain <form method="POST"> can't send OPTIONS —
 * HTML forms only support GET/POST — so this is fetch-driven instead. See the
 * route definition (routes/web.php) for why OPTIONS is required here at all.
 */
Alpine.data('contactForm', () => ({
    submitting: false,
    succeeded: false,
    successMessage: '',
    generalError: '',
    errors: {},
    renderedAt: Math.floor(Date.now() / 1000),

    async submit(event) {
        this.submitting = true;
        this.succeeded = false;
        this.generalError = '';
        this.errors = {};

        const form = event.target;
        const data = Object.fromEntries(new FormData(form).entries());
        data.rendered_at = this.renderedAt;

        try {
            const response = await fetch(form.action, {
                method: 'OPTIONS',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(data),
            });

            if (response.status === 422) {
                const body = await response.json();
                const fieldErrors = { ...(body.errors ?? {}) };

                // The honeypot has no visible field to attach an error to —
                // surface it as a generic failure instead.
                if (fieldErrors.website) {
                    this.generalError = 'Your submission looked automated. Please try again.';
                    delete fieldErrors.website;
                }

                this.errors = Object.fromEntries(
                    Object.entries(fieldErrors).map(([field, messages]) => [field, messages[0]]),
                );
                return;
            }

            if (response.status === 429) {
                this.generalError = "You've sent a few of these already — please wait a bit before trying again.";
                return;
            }

            if (!response.ok) {
                this.generalError = 'Something went wrong sending this — please try again or email me directly.';
                return;
            }

            const body = await response.json();
            this.succeeded = true;
            this.successMessage = body.message;
            form.reset();
        } catch {
            this.generalError = 'Could not reach the server — check your connection and try again.';
        } finally {
            this.submitting = false;
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();

/**
 * First-party page-view beacon — no cookies, no fingerprinting, no
 * third-party script. Fires once per page load.
 *
 * Method is OPTIONS, not POST: Yandex Cloud CDN disables POST/PUT/PATCH/DELETE
 * by default (a support request to enable them is routinely declined per
 * public reports), while OPTIONS already passes through — it's what the
 * VPN's own XHTTP path relies on. This also rules out navigator.sendBeacon(),
 * which only ever sends POST — keepalive fetch is the closest equivalent for
 * surviving a page unload with an arbitrary method.
 */
(function sendPageViewBeacon() {
    const payload = JSON.stringify({
        type: 'page_view',
        path: window.location.pathname,
        referrer: document.referrer || null,
    });

    fetch('/api/v1/events', {
        method: 'OPTIONS',
        headers: { 'Content-Type': 'application/json' },
        body: payload,
        keepalive: true,
    }).catch(() => {});
})();
