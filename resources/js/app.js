import './bootstrap';

const on = (selector, event, handler, options = {}) => {
    document.querySelectorAll(selector).forEach((node) => node.addEventListener(event, handler, options));
};

const toggle = (id, force) => {
    const node = document.getElementById(id);

    if (!node) {
        return;
    }

    const show = force ?? node.classList.contains('hidden');
    node.classList.toggle('hidden', !show);
    document.body.classList.toggle('overflow-hidden', show && node.dataset.overlayLock === 'true');

    // Keep the control that opened the panel in step with what it now controls.
    document.querySelectorAll(`[data-toggle="${id}"]`).forEach((trigger) => {
        trigger.setAttribute('aria-expanded', show ? 'true' : 'false');
    });
};

const closeTogglesOnOutsideClick = () => {
    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-toggle], #mobile-menu')) {
            return;
        }

        toggle('mobile-menu', false);
    });

    // Growing past the phone breakpoint swaps the drawer for the nav bar, so
    // the drawer must not stay open behind it.
    window.addEventListener('resize', () => {
        if (window.matchMedia('(min-width: 768px)').matches) {
            toggle('mobile-menu', false);
        }
    }, { passive: true });
};

/**
 * The breadcrumb back arrow. It is a real link, so it works with JavaScript
 * off and crawlers follow it; when there is somewhere to go back to it pops the
 * history instead, which is what a back button is expected to do.
 */
const setupBackLinks = () => {
    on('[data-back]', 'click', (event) => {
        const referrer = document.referrer;

        if (!referrer) {
            return;
        }

        try {
            if (new URL(referrer).origin !== window.location.origin) {
                return;
            }
        } catch {
            return;
        }

        event.preventDefault();
        window.history.back();
    });
};

on('[data-toggle]', 'click', (event) => {
    event.preventDefault();
    const id = event.currentTarget.dataset.toggle;
    const targets = id.endsWith('!') ? [id.slice(0, -1)] : id.split(',');
    targets.forEach((target) => toggle(target));
});

on('[data-dismiss]', 'click', (event) => {
    event.preventDefault();
    const targets = event.currentTarget.dataset.dismiss.split(',');
    targets.forEach((target) => {
        const node = document.getElementById(target);
        if (!node) {
            return;
        }
        if (event.currentTarget.dataset.fade !== 'false') {
            node.classList.add('opacity-0');
            setTimeout(() => {
                node.classList.add('hidden');
                node.classList.remove('opacity-0');
            }, 150);
            return;
        }
        node.classList.add('hidden');
    });
});

on('[data-confirm]', 'click', (event) => {
    if (!window.confirm(event.currentTarget.dataset.confirm)) {
        event.preventDefault();
    }
});

on('[data-autosubmit]', 'change', (event) => event.currentTarget.form?.requestSubmit());

on('[data-search-reset]', 'click', (event) => {
    event.preventDefault();
    const form = event.currentTarget.closest('form');
    form?.querySelectorAll('input[type="search"], input[name="q"]').forEach((input) => (input.value = ''));
    form?.querySelectorAll('select').forEach((select) => (select.selectedIndex = 0));
    form?.requestSubmit();
});

on('[data-copy]', 'click', async (event) => {
    const value = event.currentTarget.dataset.copy;

    try {
        await navigator.clipboard.writeText(value);
        const label = event.currentTarget.querySelector('[data-copy-label]');
        if (label) {
            const original = label.textContent;
            label.textContent = 'Copied';
            setTimeout(() => (label.textContent = original), 1200);
        }
    } catch {
        const input = document.createElement('input');
        input.value = value;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
    }
});

const setupDropdowns = () => {
    const triggers = document.querySelectorAll('[data-dropdown]');

    triggers.forEach((trigger) => {
        const target = document.getElementById(trigger.dataset.dropdown);
        if (!target) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const willOpen = target.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => menu.classList.add('hidden'));
            target.classList.toggle('hidden', !willOpen);
            trigger.setAttribute('aria-expanded', String(willOpen));
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => menu.classList.add('hidden'));
        document.querySelectorAll('[data-dropdown]').forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => menu.classList.add('hidden'));
        }
    });
};

const setupTreeToggles = () => {
    document.querySelectorAll('[data-tree-toggle]').forEach((button) => {
        const branch = document.getElementById(button.dataset.treeToggle);
        if (!branch) {
            return;
        }

        const sync = () => {
            const open = !branch.classList.contains('hidden');
            button.setAttribute('aria-expanded', String(open));
            const chevron = button.querySelector('[data-tree-chevron]');
            chevron?.classList.toggle('rotate-90', open);
        };

        button.addEventListener('click', () => {
            branch.classList.toggle('hidden');
            sync();
        });

        sync();
    });
};

const setupFlash = () => {
    document.querySelectorAll('[data-flash]').forEach((node) => {
        setTimeout(() => {
            node.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => node.remove(), 250);
        }, 4500);
    });
};

const setupDebouncedSearch = () => {
    document.querySelectorAll('[data-live-search]').forEach((input) => {
        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => input.form?.requestSubmit(), 450);
        });
    });
};

const setupPreview = () => {
    const input = document.querySelector('[data-image-input]');
    const preview = document.querySelector('[data-image-preview]');
    const placeholder = document.querySelector('[data-image-placeholder]');

    input?.addEventListener('change', () => {
        const [file] = input.files;

        if (!file) {
            return;
        }

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }

        placeholder?.classList.add('hidden');
    });
};

const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const formatValue = (value, money) => {
    if (money) {
        // The kyat has no minor unit, so amounts are whole numbers.
        return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(value) + ' Ks';
    }

    return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(value);
};

const setupCounters = () => {
    const nodes = document.querySelectorAll('[data-count-to]');

    if (!nodes.length) {
        return;
    }

    nodes.forEach((node) => {
        const target = parseFloat(node.dataset.countTo || '0');
        const money = node.hasAttribute('data-count-money');

        if (reducedMotion()) {
            node.textContent = formatValue(target, money);
            return;
        }

        const duration = 900;
        const start = performance.now();

        const step = (now) => {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);

            node.textContent = formatValue(target * eased, money);

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    });
};

const setupBarStagger = () => {
    if (reducedMotion()) {
        return;
    }

    const bars = document.querySelectorAll('.bar-animate');

    bars.forEach((bar, index) => {
        bar.style.animationDelay = `${Math.min(index * 35, 700)}ms`;
    });
};

const setupLiveStamp = () => {
    const region = document.querySelector('[data-live-region]');
    const stamp = region?.querySelector('[data-live-stamp]');

    if (!region || !stamp) {
        return;
    }

    const interval = parseInt(region.dataset.liveInterval || '60', 10);
    const startedAt = Date.now();

    const tick = () => {
        const seconds = Math.floor((Date.now() - startedAt) / 1000);

        if (seconds < 10) {
            stamp.textContent = 'just now';
        } else if (seconds < 60) {
            stamp.textContent = `${seconds}s ago`;
        } else {
            stamp.textContent = `${Math.floor(seconds / 60)}m ago`;
        }
    };

    setInterval(tick, 5000);
};

const setupAutoRefresh = () => {
    const button = document.querySelector('[data-autorefresh]');

    if (!button) {
        return;
    }

    const label = button.querySelector('[data-autorefresh-label]');
    const interval = parseInt(button.dataset.autorefresh || '60', 10) * 1000;
    let timer = null;

    button.addEventListener('click', () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
            button.classList.remove('btn-soft');
            button.classList.add('btn-secondary');
            if (label) label.textContent = 'Auto-refresh off';
            return;
        }

        timer = setInterval(() => window.location.reload(), interval);
        button.classList.remove('btn-secondary');
        button.classList.add('btn-soft');
        if (label) label.textContent = `Auto-refresh ${interval / 1000}s`;
    });
};

const setupQuantitySteppers = () => {
    document.querySelectorAll('[data-step]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            const input = document.getElementById(button.dataset.target);

            if (!input) {
                return;
            }

            const step = parseInt(button.dataset.step, 10);
            const min = parseInt(input.min || '1', 10);
            const max = parseInt(input.max || '99', 10);
            const next = (parseInt(input.value || '0', 10) || 0) + step;

            input.value = Math.max(min, Math.min(next, max));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
};

const setupShelfLife = () => {
    const produced = document.querySelector('[name="produced_at"]');
    const expires = document.querySelector('[name="expires_at"]');
    const output = document.querySelector('[data-shelf-life-text]');

    if (!produced || ! expires || ! output) {
        return;
    }

    const render = () => {
        if (!produced.value || !expires.value) {
            output.textContent = 'Set both dates to see the shelf life.';
            return;
        }

        const from = new Date(`${produced.value}T00:00:00`);
        const to = new Date(`${expires.value}T00:00:00`);
        const days = Math.round((to - from) / 86400000);

        if (days < 0) {
            output.textContent = 'The expiry date is before the production date.';
            output.classList.add('text-rose-600');
            return;
        }

        output.classList.remove('text-rose-600');
        output.textContent = `Shelf life ${days} ${days === 1 ? 'day' : 'days'}.`;
    };

    produced.addEventListener('change', render);
    expires.addEventListener('change', render);
    render();
};

const setupDeliveryQuote = () => {
    const calculator = document.querySelector('[data-delivery-calculator]');
    const township = document.querySelector('[name="township"]');
    const quote = document.querySelector('[data-delivery-quote]');

    if (!calculator || ! township) {
        return;
    }

    let map = {};
    try {
        map = JSON.parse(calculator.dataset.map || '{}');
    } catch {
        map = {};
    }

    const subtotal = parseInt(calculator.dataset.subtotal || '0', 10);
    const symbol = calculator.dataset.currency || 'Ks';

    const nf = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });
    const money = (value) => `${nf.format(value)} ${symbol}`;

    let labels = {};
    try {
        labels = JSON.parse(calculator.dataset.zones || '{}');
    } catch {
        labels = {};
    }

    const zoneName = document.querySelector('[data-delivery-zone]');
    const eta = document.querySelector('[data-delivery-eta]');
    const fee = document.querySelector('[data-delivery-fee]');
    const summary = document.querySelector('[data-delivery-summary]');
    const total = document.querySelector('[data-order-total]');

    const render = () => {
        const entry = map[township.value];

        if (!entry) {
            if (quote) quote.hidden = true;
            if (summary) summary.textContent = 'Pick a township';
            if (total) total.textContent = money(subtotal);
            return;
        }

        const [zoneKey, feeValue, etaText] = entry;
        const isFree = feeValue === 0;

        if (quote) quote.hidden = false;
        if (zoneName) zoneName.textContent = labels[zoneKey] || 'Delivery';
        if (eta) eta.textContent = etaText;
        if (fee) fee.textContent = isFree ? 'Free' : money(feeValue);
        if (summary) summary.textContent = isFree ? 'Free' : money(feeValue);
        if (total) total.textContent = money(subtotal + feeValue);
    };

    township.addEventListener('change', render);
    render();
};

/**
 * The homepage carousel.
 *
 * Every slide is stacked and faded, so nothing reflows. Autoplay stops for
 * anyone who asked for reduced motion, while the arrows, dots and swipe keep
 * working — the content is never locked behind the animation.
 */
const setupCarousels = () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-carousel]').forEach((root) => {
        if (root.dataset.ready === 'true') {
            return;
        }

        root.dataset.ready = 'true';

        const slides = [...root.querySelectorAll('[data-slide]')];

        if (slides.length === 0) {
            return;
        }

        const dots = [...root.querySelectorAll('[data-carousel-dot]')];
        const delay = Number(root.dataset.autoplay || 0);
        let current = 0;
        let timer = null;

        const playVideo = (index) => {
            slides.forEach((slide, i) => {
                const video = slide.querySelector('[data-promo-video]');

                if (!video) {
                    return;
                }

                if (i === index) {
                    video.play().catch(() => {});
                } else {
                    video.pause();
                }
            });
        };

        const show = (index) => {
            current = (index + slides.length) % slides.length;

            slides.forEach((slide, i) => {
                const active = i === current;

                slide.classList.toggle('opacity-100', active);
                slide.classList.toggle('opacity-0', ! active);
                slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            });

            dots.forEach((dot, i) => {
                const active = i === current;

                dot.classList.toggle('w-6', active);
                dot.classList.toggle('bg-white', active);
                dot.classList.toggle('w-1.5', !active);
                dot.classList.toggle('bg-white/40', !active);
            });

            playVideo(current);
        };

        const stop = () => {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        };

        const start = () => {
            // One slide is not a carousel, and reduced motion means no timer.
            if (slides.length < 2 || delay < 1000 || reducedMotion) {
                return;
            }

            stop();
            timer = setInterval(() => show(current + 1), delay);
        };

        root.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
            show(current + 1);
            start();
        });

        root.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
            show(current - 1);
            start();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                show(Number(dot.dataset.carouselDot));
                start();
            });
        });

        root.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight') {
                show(current + 1);
                start();
            }

            if (event.key === 'ArrowLeft') {
                show(current - 1);
                start();
            }
        });

        // Pause while the shopper is reading or tabbing around.
        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);
        root.addEventListener('focusin', stop);
        root.addEventListener('focusout', start);

        // A swipe is the natural gesture on a phone.
        let touchX = null;

        root.addEventListener('touchstart', (event) => {
            touchX = event.touches[0].clientX;
            stop();
        }, { passive: true });

        root.addEventListener('touchend', (event) => {
            if (touchX === null) {
                return;
            }

            const delta = event.changedTouches[0].clientX - touchX;

            if (Math.abs(delta) > 40) {
                show(current + (delta < 0 ? 1 : -1));
            }

            touchX = null;
            start();
        }, { passive: true });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stop();
            } else {
                start();
            }
        });

        show(0);
        start();
    });
};

const boot = () => {
    // A throw in one feature used to abort everything after it, so a single
    // mistake silently killed the delivery quote, the back links and the
    // carousel at the same time. Isolate them and say which one broke.
    const safely = (name, fn) => {
        try {
            fn();
        } catch (error) {
            console.error(`[${name}] could not start:`, error);
        }
    };

    safely('dropdowns', setupDropdowns);
    safely('tree-toggles', setupTreeToggles);
    safely('flash', setupFlash);
    safely('debounced-search', setupDebouncedSearch);
    safely('image-preview', setupPreview);
    safely('counters', setupCounters);
    safely('bar-stagger', setupBarStagger);
    safely('live-stamp', setupLiveStamp);
    safely('auto-refresh', setupAutoRefresh);
    safely('quantity-steppers', setupQuantitySteppers);
    safely('shelf-life', setupShelfLife);
    safely('delivery-quote', setupDeliveryQuote);
    safely('outside-click', closeTogglesOnOutsideClick);
    safely('back-links', setupBackLinks);
    safely('carousels', setupCarousels);

    document.body.dataset.ready = 'true';
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
