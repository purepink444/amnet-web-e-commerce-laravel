/**
 * ProductCard Component - Vanilla JavaScript
 * Handles product display with cart and wishlist functionality
 */
class ProductCard {
    constructor(product, options = {}) {
        this.product = product;
        this.options = {
            compact: false,
            showWishlist: true,
            ...options
        };
        this.isLoading = false;
        this.element = null;
    }

    /**
     * Render the product card
     */
    render() {
        const card = document.createElement('article');
        card.className = 'product-card';
        card.setAttribute('role', 'article');
        card.setAttribute('aria-labelledby', `product-title-${this.product.id}`);

        const discountPercentage = this.product.discount
            ? Math.round(((this.product.originalPrice - this.product.price) / this.product.originalPrice) * 100)
            : 0;

        card.innerHTML = `
            <div class="product-image-container">
                <img
                    src="${this.product.image || '/images/placeholder.jpg'}"
                    alt="${this.product.name}"
                    loading="lazy"
                    class="product-image"
                />

                <!-- Badges -->
                <div class="product-badges">
                    ${discountPercentage > 0 ? `<span class="badge badge-discount">-${discountPercentage}%</span>` : ''}
                    ${!this.product.isInStock ? `<span class="badge badge-out-of-stock">Out of Stock</span>` : ''}
                </div>

                <!-- Wishlist Button -->
                ${this.options.showWishlist ? `
                    <button
                        class="wishlist-btn"
                        aria-label="Add to wishlist"
                        data-product-id="${this.product.id}"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                ` : ''}
            </div>

            <div class="product-content">
                <!-- Brand -->
                ${this.product.brand && !this.options.compact ? `<p class="product-brand">${this.product.brand}</p>` : ''}

                <!-- Product Name -->
                <h3 id="product-title-${this.product.id}" class="product-title ${this.options.compact ? 'text-sm' : 'text-base'}">
                    ${this.product.name}
                </h3>

                <!-- Rating -->
                ${!this.options.compact && this.product.rating ? this.renderRating() : ''}

                <!-- Price -->
                <div class="product-price">
                    <span class="current-price ${this.options.compact ? 'text-lg' : 'text-xl'}">
                        ฿${this.product.price.toLocaleString()}
                    </span>
                    ${this.product.originalPrice && this.product.originalPrice > this.product.price ?
                        `<span class="original-price">฿${this.product.originalPrice.toLocaleString()}</span>` : ''}
                </div>

                <!-- Add to Cart Button -->
                <button
                    class="add-to-cart-btn ${this.options.compact ? 'btn-sm' : 'btn-md'} ${this.isLoading ? 'loading' : ''}"
                    ${!this.product.isInStock || this.isLoading ? 'disabled' : ''}
                    data-product-id="${this.product.id}"
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
        `;

        this.element = card;
        this.attachEventListeners();
        return card;
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
        if (!this.element) return;

        // Add to cart button
        const addToCartBtn = this.element.querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleAddToCart();
            });
        }

        // Wishlist button
        const wishlistBtn = this.element.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleToggleWishlist();
            });
        }

        // Image lazy loading
        const img = this.element.querySelector('.product-image');
        if (img) {
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
        }
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
        const btn = this.element?.querySelector('.add-to-cart-btn');
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
        const btn = this.element?.querySelector('.wishlist-btn');
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
        if (this.element) {
            const newCard = this.render();
            this.element.parentNode?.replaceChild(newCard, this.element);
        }
    }

    /**
     * Destroy component
     */
    destroy() {
        if (this.element) {
            this.element.remove();
            this.element = null;
        }
    }
}

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProductCard;
}