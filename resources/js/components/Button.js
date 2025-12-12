/**
 * Button Web Component - Reusable button component
 */
class Button extends HTMLElement {
    static get observedAttributes() {
        return ['variant', 'size', 'disabled', 'loading'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this._loading = false;
    }

    connectedCallback() {
        this.render();
        this.attachEventListeners();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            if (name === 'loading') {
                this._loading = newValue === 'true';
            }
            this.render();
        }
    }

    get variant() {
        return this.getAttribute('variant') || 'primary';
    }

    get size() {
        return this.getAttribute('size') || 'md';
    }

    get disabled() {
        return this.hasAttribute('disabled') || this._loading;
    }

    get loading() {
        return this._loading;
    }

    set loading(value) {
        this._loading = value;
        this.setAttribute('loading', value);
    }

    render() {
        const variants = {
            primary: 'bg-orange-500 hover:bg-orange-600 text-white border-orange-500',
            secondary: 'bg-gray-500 hover:bg-gray-600 text-white border-gray-500',
            outline: 'border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white',
            ghost: 'text-orange-500 hover:bg-orange-50 border-transparent',
            danger: 'bg-red-500 hover:bg-red-600 text-white border-red-500'
        };

        const sizes = {
            sm: 'px-3 py-1.5 text-sm',
            md: 'px-4 py-2 text-base',
            lg: 'px-6 py-3 text-lg',
            xl: 'px-8 py-4 text-xl'
        };

        this.shadowRoot.innerHTML = `
            <style>
                @import url('https://cdn.tailwindcss.com');

                .btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 0.375rem;
                    font-weight: 500;
                    transition: all 0.2s ease;
                    cursor: pointer;
                    border: 1px solid transparent;
                    outline: none;
                    gap: 0.5rem;
                    position: relative;
                    overflow: hidden;
                }

                .btn:focus {
                    box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.5);
                }

                .btn:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                    pointer-events: none;
                }

                .btn.loading {
                    pointer-events: none;
                }

                .spinner {
                    width: 1rem;
                    height: 1rem;
                    border: 2px solid transparent;
                    border-top: 2px solid currentColor;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    to { transform: rotate(360deg); }
                }

                .btn-content {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                /* Loading overlay */
                .loading-overlay {
                    position: absolute;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: inherit;
                    color: inherit;
                }
            </style>

            <button
                class="btn ${variants[this.variant]} ${sizes[this.size]} ${this.loading ? 'loading' : ''}"
                ?disabled="${this.disabled}"
                part="button"
            >
                ${this.loading ? `
                    <span class="loading-overlay">
                        <span class="spinner"></span>
                        <span>Loading...</span>
                    </span>
                ` : ''}
                <span class="btn-content ${this.loading ? 'opacity-0' : ''}">
                    <slot name="prefix"></slot>
                    <slot></slot>
                    <slot name="suffix"></slot>
                </span>
            </button>
        `;
    }

    attachEventListeners() {
        const button = this.shadowRoot.querySelector('button');
        if (button) {
            // Click event
            button.addEventListener('click', (e) => {
                if (this.disabled) {
                    e.preventDefault();
                    return;
                }
                this.dispatchEvent(new CustomEvent('button-click', {
                    bubbles: true,
                    composed: true,
                    detail: { originalEvent: e }
                }));
            });

            // Keyboard navigation
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (!this.disabled) {
                        button.click();
                    }
                }
            });

            // Focus management
            button.addEventListener('focus', () => {
                this.dispatchEvent(new CustomEvent('button-focus', {
                    bubbles: true,
                    composed: true
                }));
            });

            button.addEventListener('blur', () => {
                this.dispatchEvent(new CustomEvent('button-blur', {
                    bubbles: true,
                    composed: true
                }));
            });
        }
    }

    /**
     * Programmatically click the button
     */
    click() {
        if (!this.disabled) {
            this.shadowRoot.querySelector('button')?.click();
        }
    }

    /**
     * Focus the button
     */
    focus() {
        this.shadowRoot.querySelector('button')?.focus();
    }

    /**
     * Blur the button
     */
    blur() {
        this.shadowRoot.querySelector('button')?.blur();
    }
}

// Register the custom element
customElements.define('ui-button', Button);

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Button;
}