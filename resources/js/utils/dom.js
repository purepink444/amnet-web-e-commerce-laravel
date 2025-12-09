/**
 * DOM Utilities - Vanilla JavaScript
 * Helper functions for DOM manipulation
 */

/**
 * Create element with attributes and content
 */
function createElement(tag, attributes = {}, content = '') {
    const element = document.createElement(tag);

    // Set attributes
    Object.entries(attributes).forEach(([key, value]) => {
        if (key === 'className') {
            element.className = value;
        } else if (key === 'textContent') {
            element.textContent = value;
        } else if (key === 'innerHTML') {
            element.innerHTML = value;
        } else if (key.startsWith('data-')) {
            element.setAttribute(key, value);
        } else {
            element.setAttribute(key, value);
        }
    });

    // Set content
    if (content && !attributes.textContent && !attributes.innerHTML) {
        element.textContent = content;
    }

    return element;
}

/**
 * Add event listener with automatic cleanup
 */
function addEventListener(element, event, handler, options = {}) {
    element.addEventListener(event, handler, options);

    // Return cleanup function
    return () => {
        element.removeEventListener(event, handler, options);
    };
}

/**
 * Add multiple event listeners
 */
function addEventListeners(element, events) {
    const cleanups = [];

    Object.entries(events).forEach(([event, handler]) => {
        cleanups.push(addEventListener(element, event, handler));
    });

    // Return cleanup function for all listeners
    return () => {
        cleanups.forEach(cleanup => cleanup());
    };
}

/**
 * Toggle class with animation
 */
function toggleClass(element, className, force) {
    return new Promise(resolve => {
        const willBeVisible = force !== undefined ? force : !element.classList.contains(className);

        if (willBeVisible) {
            element.classList.add(className);
        } else {
            element.classList.add(`${className}-exit`);
            element.addEventListener('animationend', () => {
                element.classList.remove(className, `${className}-exit`);
                resolve();
            }, { once: true });
            return;
        }

        resolve();
    });
}

/**
 * Smooth scroll to element
 */
function scrollToElement(element, offset = 0) {
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - offset;

    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
}

/**
 * Check if element is in viewport
 */
function isInViewport(element, threshold = 0) {
    const rect = element.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;

    return (
        rect.top >= -threshold &&
        rect.left >= -threshold &&
        rect.bottom <= (windowHeight || document.documentElement.clientHeight) + threshold &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) + threshold
    );
}

/**
 * Lazy load images
 */
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

/**
 * Debounce function
 */
function debounce(func, wait, immediate = false) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Get element by selector with error handling
 */
function $(selector, context = document) {
    const element = context.querySelector(selector);
    if (!element) {
        console.warn(`Element not found: ${selector}`);
    }
    return element;
}

/**
 * Get elements by selector
 */
function $$(selector, context = document) {
    return Array.from(context.querySelectorAll(selector));
}

/**
 * Show loading spinner
 */
function showLoading(element, text = 'Loading...') {
    if (!element) return;

    element.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <span class="loading-text">${text}</span>
        </div>
    `;
    element.classList.add('loading');
}

/**
 * Hide loading spinner
 */
function hideLoading(element) {
    if (!element) return;

    element.classList.remove('loading');
    // Keep content, just remove loading class
}

/**
 * Create modal overlay
 */
function createModal(content, options = {}) {
    const {
        closable = true,
        size = 'md',
        title = '',
        onClose = null
    } = options;

    const overlay = createElement('div', {
        className: 'modal-overlay',
        'aria-modal': 'true',
        role: 'dialog'
    });

    const modal = createElement('div', {
        className: `modal modal-${size}`
    });

    if (title) {
        const header = createElement('div', { className: 'modal-header' });
        const titleElement = createElement('h3', { className: 'modal-title' }, title);

        header.appendChild(titleElement);

        if (closable) {
            const closeBtn = createElement('button', {
                className: 'modal-close',
                'aria-label': 'Close modal',
                type: 'button'
            }, '×');
            header.appendChild(closeBtn);
        }

        modal.appendChild(header);
    }

    const body = createElement('div', { className: 'modal-body' });
    if (typeof content === 'string') {
        body.innerHTML = content;
    } else {
        body.appendChild(content);
    }
    modal.appendChild(body);

    overlay.appendChild(modal);

    // Event listeners
    const closeModal = () => {
        overlay.remove();
        if (onClose) onClose();
    };

    if (closable) {
        // Close on overlay click
        addEventListener(overlay, 'click', (e) => {
            if (e.target === overlay) {
                closeModal();
            }
        });

        // Close on escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);

        // Close button
        const closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) {
            addEventListener(closeBtn, 'click', closeModal);
        }
    }

    return overlay;
}

// Export functions
const DOMUtils = {
    createElement,
    addEventListener,
    addEventListeners,
    toggleClass,
    scrollToElement,
    isInViewport,
    lazyLoadImages,
    debounce,
    throttle,
    $,
    $$,
    showLoading,
    hideLoading,
    createModal
};

// Make available globally
window.DOMUtils = DOMUtils;

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DOMUtils;
}