/**
 * Cart Component
 * Handles cart quantity updates and cart-related functionality
 */
export class CartManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindQuantityButtons();
    }

    bindQuantityButtons() {
        // Bind quantity update buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const action = button.dataset.action;
                const productId = button.dataset.productId;
                const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);

                if (input) {
                    let currentValue = parseInt(input.value) || 1;

                    if (action === 'increase') {
                        currentValue++;
                    } else if (action === 'decrease' && currentValue > 1) {
                        currentValue--;
                    }

                    input.value = currentValue;
                    this.updateQuantity(productId, currentValue);
                }
            });
        });

        // Bind quantity input changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const productId = e.target.dataset.productId;
                const quantity = parseInt(e.target.value) || 1;
                this.updateQuantity(productId, Math.max(1, quantity));
            });
        });
    }

    async updateQuantity(productId, quantity) {
        try {
            const response = await fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity })
            });

            const data = await response.json();

            if (data.success) {
                // Update UI elements
                this.updateCartUI(data.cart);
                Toast.fire({
                    icon: 'success',
                    title: 'อัพเดทจำนวนสินค้าเรียบร้อย'
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการอัพเดท'
                });
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
            });
        }
    }

    updateCartUI(cartData) {
        // Update cart count in header
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = cartData.total_items || 0;
        }

        // Update cart total
        const cartTotal = document.querySelector('.cart-total');
        if (cartTotal) {
            cartTotal.textContent = `฿${cartData.total_price || 0}`;
        }

        // Update item total in cart row
        const itemRow = document.querySelector(`[data-product-id="${cartData.product_id}"]`);
        if (itemRow) {
            const itemTotal = itemRow.querySelector('.item-total');
            if (itemTotal) {
                itemTotal.textContent = `฿${cartData.item_total || 0}`;
            }
        }
    }

    async removeItem(productId) {
        try {
            const response = await fetch(`/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Remove item from DOM
                const itemRow = document.querySelector(`[data-product-id="${productId}"]`);
                if (itemRow) {
                    itemRow.remove();
                }

                this.updateCartUI(data.cart);
                Toast.fire({
                    icon: 'success',
                    title: 'ลบสินค้าออกจากตะกร้าเรียบร้อย'
                });
            }
        } catch (error) {
            console.error('Error removing item:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการลบสินค้า'
            });
        }
    }
}

// Global cart functions for backward compatibility
window.updateQuantity = function(productId, quantity) {
    if (window.cartManager) {
        window.cartManager.updateQuantity(productId, quantity);
    }
};

window.removeCartItem = function(productId) {
    if (window.cartManager) {
        window.cartManager.removeItem(productId);
    }
};

// Initialize cart manager
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.cart-container') || document.querySelector('.quantity-btn')) {
        window.cartManager = new CartManager();
    }
});