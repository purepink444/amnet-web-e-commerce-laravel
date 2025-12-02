/**
 * Product Component
 * Handles product-related functionality like add to cart, reviews, etc.
 */
export class ProductManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindAddToCartButtons();
        this.bindQuantityControls();
        this.bindReviewSystem();
    }

    bindAddToCartButtons() {
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.dataset.productId;
                const quantity = button.dataset.quantity || 1;
                this.addToCart(productId, quantity);
            });
        });
    }

    async addToCart(productId, quantity = 1) {
        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count || 0;
                }

                Toast.fire({
                    icon: 'success',
                    title: 'เพิ่มสินค้าในตะกร้าเรียบร้อย'
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'เกิดข้อผิดพลาดในการเพิ่มสินค้า'
                });
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
            });
        }
    }

    bindQuantityControls() {
        // Product detail page quantity controls
        const decreaseBtn = document.getElementById('decreaseQty');
        const increaseBtn = document.getElementById('increaseQty');
        const quantityInput = document.getElementById('quantity');

        if (decreaseBtn && increaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value) || 1;
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            increaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value) || 1;
                quantityInput.value = currentValue + 1;
            });

            quantityInput.addEventListener('change', () => {
                const value = parseInt(quantityInput.value) || 1;
                quantityInput.value = Math.max(1, value);
            });
        }
    }

    bindReviewSystem() {
        this.bindStarRating();
        this.bindImageModal();
    }

    bindStarRating() {
        const stars = document.querySelectorAll('.star-rating input[type="radio"]');
        const ratingValue = document.getElementById('rating-value');

        stars.forEach(star => {
            star.addEventListener('change', () => {
                const rating = star.value;
                if (ratingValue) {
                    ratingValue.textContent = `${rating} ดาว`;
                }
                this.updateStarDisplay(rating);
            });
        });
    }

    updateStarDisplay(rating) {
        const stars = document.querySelectorAll('.star-rating label');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    bindImageModal() {
        document.querySelectorAll('.review-image').forEach(image => {
            image.addEventListener('click', () => {
                const imageSrc = image.src;
                this.showImageModal(imageSrc);
            });
        });
    }

    showImageModal(imageSrc) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('imageModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'imageModal';
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body text-center p-0">
                            <img src="" class="img-fluid" id="modalImage">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        document.getElementById('modalImage').src = imageSrc;
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

// Global functions for backward compatibility
window.addToCart = function(productId) {
    if (window.productManager) {
        window.productManager.addToCart(productId);
    }
};

window.increaseQty = function() {
    const input = document.getElementById('quantity');
    if (input) {
        const currentValue = parseInt(input.value) || 1;
        input.value = currentValue + 1;
    }
};

window.decreaseQty = function() {
    const input = document.getElementById('quantity');
    if (input) {
        const currentValue = parseInt(input.value) || 1;
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }
};

window.showImageModal = function(imageSrc) {
    if (window.productManager) {
        window.productManager.showImageModal(imageSrc);
    }
};

// Initialize product manager
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.product-detail') ||
        document.querySelector('.add-to-cart-btn') ||
        document.querySelector('.star-rating') ||
        document.querySelector('.review-image')) {
        window.productManager = new ProductManager();
    }
});