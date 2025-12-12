/**
 * ProductCard Web Component - Modern Web Component
 * Handles product display with cart and wishlist functionality
 */
class ProductCard extends HTMLElement {
    static get observedAttributes() {
        return ['product', 'compact', 'show-wishlist'];
    }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.product = null;
        this.compact = false;
        this.showWishlist = true;
        this.isLoading = false;
    }

    connectedCallback() {
        this.render();
        this.attachEventListeners();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (oldValue !== newValue) {
            if (name === 'product') {
                this.product = JSON.parse(newValue);
            } else if (name === 'compact') {
                this.compact = newValue === 'true';
            } else if (name === 'show-wishlist') {
                this.showWishlist = newValue !== 'false';
            }
            this.render();
        }
    }

    /**
     * Render the product card
     */
    render() {
        if (!this.product) return;

        const discountPercentage = this.product.discount
            ? Math.round(((this.product.originalPrice - this.product.price) / this.product.originalPrice) * 100)
            : 0;

        this.shadowRoot.innerHTML = `
            <style>
                @import url('https://cdn.tailwindcss.com');

                :host {
                    display: block;
                }

                .product-card {
                    position: relative;
                    background: white;
                    border-radius: 0.75rem;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    overflow: hidden;
                    border: 1px solid #f3f4f6;
                    transition: all 0.3s ease;
                }

                .product-card:hover {
                    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                    transform: translateY(-2px);
                }

                .product-image-container {
                    position: relative;
                    overflow: hidden;
                    background: #f9fafb;
                }

                .product-image-container img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s ease;
                }

                .product-card:hover .product-image-container img {
                    transform: scale(1.05);
                }

                .product-badges {
                    position: absolute;
                    top: 0.75rem;
                    left: 0.75rem;
                    display: flex;
                    flex-direction: column;
                    gap: 0.25rem;
                }

                .badge {
                    padding: 0.25rem 0.5rem;
                    border-radius: 9999px;
                    font-size: 0.75rem;
                    font-weight: 500;
                }

                .badge-discount {
                    background: #dc2626;
                    color: white;
                }

                .badge-out-of-stock {
                    background: #6b7280;
                    color: white;
                }

                .wishlist-btn {
                    position: absolute;
                    top: 0.75rem;
                    right: 0.75rem;
                    padding: 0.5rem;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.8);
                    border: none;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }

                .wishlist-btn:hover {
                    background: white;
                    transform: scale(1.1);
                }

                .wishlist-btn.active {
                    background: #ef4444;
                    color: white;
                }

                .product-content {
                    padding: 1rem;
                }

                .product-brand {
                    font-size: 0.75rem;
                    color: #6b7280;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-bottom: 0.25rem;
                }

                .product-title {
                    font-weight: 600;
                    color: #111827;
                    margin-bottom: 0.5rem;
                    line-height: 1.3;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .product-rating {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-bottom: 0.75rem;
                }

                .rating-stars {
                    display: flex;
                    align-items: center;
                    gap: 0.125rem;
                }

                .star {
                    width: 1rem;
                    height: 1rem;
                    color: #d1d5db;
                }

                .star.active {
                    color: #fbbf24;
                }

                .rating-text {
                    font-size: 0.875rem;
                    color: #6b7280;
                }

                .product-price {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-bottom: 0.75rem;
                }

                .current-price {
                    font-weight: 700;
                    color: #ea580c;
                }

                .original-price {
                    font-size: 0.875rem;
                    color: #6b7280;
                    text-decoration: line-through;
                }

                .add-to-cart-btn {
                    width: 100%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.5rem 1rem;
                    border-radius: 0.375rem;
                    font-weight: 500;
                    border: none;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    background: #ea580c;
                    color: white;
                }

                .add-to-cart-btn:hover:not(:disabled) {
                    background: #dc2626;
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
                }

                .add-to-cart-btn:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }

                .add-to-cart-btn.loading {
                    background: #9ca3af;
                }

                .btn-content {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
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

                /* Compact mode adjustments */
                :host([compact]) .product-image-container {
                    aspect-ratio: 1;
                }

                :host(:not([compact])) .product-image-container {
                    aspect-ratio: 4/3;
                }

                :host([compact]) .product-title {
                    font-size: 0.875rem;
                }

                :host([compact]) .current-price {
                    font-size: 1.125rem;
                }

                :host([compact]) .add-to-cart-btn {
                    padding: 0.375rem 0.75rem;
                    font-size: 0.875rem;
                }
            </style>

            <article class="product-card" role="article" aria-labelledby="product-title-${this.product.id}">
                <div class="product-image-container">
                    <img
                        src="${this.product.image || '/images/placeholder.jpg'}"
                        alt="${this.product.name}"
                        loading="lazy"
                    />

                    <!-- Badges -->
                    <div class="product-badges">
                        ${discountPercentage > 0 ? `<span class="badge badge-discount">-${discountPercentage}%</span>` : ''}
                        ${!this.product.isInStock ? `<span class="badge badge-out-of-stock">Out of Stock</span>` : ''}
                    </div>

                    <!-- Wishlist Button -->
                    ${this.showWishlist ? `
                        <button class="wishlist-btn" aria-label="Add to wishlist">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    ` : ''}
                </div>

                <div class="product-content">
                    <!-- Brand -->
                    ${this.product.brand && !this.compact ? `<p class="product-brand">${this.product.brand}</p>` : ''}

                    <!-- Product Name -->
                    <h3 id="product-title-${this.product.id}" class="product-title">
                        ${this.product.name}
                    </h3>

                    <!-- Rating -->
                    ${!this.compact && this.product.rating ? this.renderRating() : ''}

                    <!-- Price -->
                    <div class="product-price">
                        <span class="current-price">
                            ฿${this.product.price.toLocaleString()}
                        </span>
                        ${this.product.originalPrice && this.product.originalPrice > this.product.price ?
                            `<span class="original-price">฿${this.product.originalPrice.toLocaleString()}</span>` : ''}
                    </div>

                    <!-- Add to Cart Button -->
                    <button
                        class="add-to-cart-btn ${this.isLoading ? 'loading' : ''}"
                        ${!this.product.isInStock || this.isLoading ? 'disabled' : ''}
                        aria-describedby="product-title-${this.product.id}"
                    >
                        ${this.isLoading ? `
                            <span class="btn-content">
                                <span class="spinner"></span>
                                Adding...
                            </span>
                        ` : `
                            <span class="btn-content">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"/>
                                    <circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                                ${this.product.isInStock ? 'Add to Cart' : 'Out of Stock'}
                            </span>
                        `}
                    </button>
                </div>
            </article>
        `;
    }

    /**
     * Render star rating
     */
    renderRating() {
        if (!this.product.rating) return '';

        const stars = [1, 2, 3, 4, 5].map(star => `
            <svg class="star ${star <= this.product.rating ? 'active' : ''}" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        `).join('');

        return `
            <div class="product-rating">
                <div class="rating-stars" aria-label="Rating: ${this.product.rating} out of 5 stars">
                    ${stars}
                </div>
                <span class="rating-text">
                    ${this.product.rating} (${this.product.reviewCount || 0})
                </span>
            </div>
        `;
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Add to cart button
        const addToCartBtn = this.shadowRoot.querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleAddToCart();
            });

            // Keyboard navigation for add to cart button
            addToCartBtn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.handleAddToCart();
                }
            });
        }

        // Wishlist button
        const wishlistBtn = this.shadowRoot.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleToggleWishlist();
            });

            // Keyboard navigation for wishlist button
            wishlistBtn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.handleToggleWishlist();
                }
            });
        }

        // Image lazy loading
        const img = this.shadowRoot.querySelector('.product-image-container img');
        if (img) {
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });

            // Add alt text for accessibility
            if (!img.alt && this.product?.name) {
                img.alt = this.product.name;
            }
        }

        // Focus management for the card itself
        this.addEventListener('focusin', () => {
            this.setAttribute('aria-expanded', 'true');
        });

        this.addEventListener('focusout', () => {
            this.setAttribute('aria-expanded', 'false');
        });
    }

    /**
     * Handle add to cart
     */
    async handleAddToCart() {
        if (!this.product?.isInStock || this.isLoading) return;

        this.isLoading = true;
        this.updateButtonState();

        try {
            const result = await CartService.addItem(this.product.id, 1);

            if (result.success) {
                // Dispatch custom event for global cart updates
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: {
                        action: 'add',
                        product: this.product,
                        quantity: 1
                    }
                }));

                // Show success message
                this.showNotification('Product added to cart!', 'success');
            } else {
                this.showNotification(result.error || 'Failed to add product to cart', 'error');
            }
        } catch (error) {
            console.error('Failed to add to cart:', error);
            this.showNotification('Network error. Please try again.', 'error');
        } finally {
            this.isLoading = false;
            this.updateButtonState();
        }
    }

    /**
     * Handle toggle wishlist
     */
    async handleToggleWishlist() {
        try {
            const result = await WishlistService.toggleItem(this.product.id);

            if (result.success) {
                const action = result.action; // 'added' or 'removed'
                this.updateWishlistButton(action === 'added');

                // Dispatch custom event
                window.dispatchEvent(new CustomEvent('wishlist-updated', {
                    detail: {
                        action,
                        productId: this.product.id
                    }
                }));

                this.showNotification(
                    action === 'added' ? 'Added to wishlist!' : 'Removed from wishlist!',
                    'success'
                );
            } else {
                this.showNotification(result.error || 'Failed to update wishlist', 'error');
            }
        } catch (error) {
            console.error('Failed to toggle wishlist:', error);
            this.showNotification('Network error. Please try again.', 'error');
        }
    }

    /**
     * Update button state
     */
    updateButtonState() {
        const btn = this.shadowRoot?.querySelector('.add-to-cart-btn');
        if (!btn) return;

        if (this.isLoading) {
            btn.classList.add('loading');
            btn.disabled = true;
        } else {
            btn.classList.remove('loading');
            btn.disabled = !this.product.isInStock;
        }
    }

    /**
     * Update wishlist button state
     */
    updateWishlistButton(isInWishlist) {
        const btn = this.shadowRoot?.querySelector('.wishlist-btn');
        if (!btn) return;

        btn.classList.toggle('active', isInWishlist);
        btn.setAttribute('aria-label', isInWishlist ? 'Remove from wishlist' : 'Add to wishlist');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Use SweetAlert2 if available, otherwise create a simple notification
        if (window.Swal) {
            window.Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 16px;
                border-radius: 8px;
                color: white;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#6b7280'};
                z-index: 1000;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease;
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }

    /**
     * Update product data
     */
    updateProduct(product) {
        this.product = { ...this.product, ...product };
        this.render();
        this.attachEventListeners();
    }

    /**
     * Destroy component
     */
    destroy() {
        this.remove();
    }
}

// Register the custom element
customElements.define('product-card', ProductCard);

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductCard;
}