/**
 * Checkout Page Handler
 * Manages checkout process and form validation
 */
export class CheckoutHandler {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 3;
        this.init();
    }

    init() {
        this.bindStepNavigation();
        this.bindFormValidation();
        this.bindPaymentMethodSelection();
        this.updateStepIndicator();
        console.log('Checkout JavaScript loaded successfully');
    }

    bindStepNavigation() {
        // Next step buttons
        document.querySelectorAll('.next-step').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const nextStep = parseInt(button.dataset.step);
                this.goToStep(nextStep);
            });
        });

        // Previous step buttons
        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const prevStep = parseInt(button.dataset.step);
                this.goToStep(prevStep);
            });
        });
    }

    goToStep(step) {
        if (this.validateCurrentStep() && step > this.currentStep) {
            this.showStep(step);
        } else if (step < this.currentStep) {
            this.showStep(step);
        }
    }

    showStep(step) {
        // Hide all steps
        document.querySelectorAll('.checkout-step').forEach(stepElement => {
            stepElement.classList.remove('active');
        });

        // Show target step
        const targetStep = document.getElementById(`step-${step}`);
        if (targetStep) {
            targetStep.classList.add('active');
            this.currentStep = step;
            this.updateStepIndicator();
            this.scrollToTop();
        }
    }

    updateStepIndicator() {
        // Update step indicators
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const stepNumber = index + 1;
            indicator.classList.toggle('active', stepNumber === this.currentStep);
            indicator.classList.toggle('completed', stepNumber < this.currentStep);
        });
    }

    validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${this.currentStep}`);
        if (!currentStepElement) return true;

        let isValid = true;
        const requiredFields = currentStepElement.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Additional validation for step 1 (shipping info)
        if (this.currentStep === 1) {
            isValid = this.validateShippingInfo() && isValid;
        }

        // Additional validation for step 2 (payment method)
        if (this.currentStep === 2) {
            isValid = this.validatePaymentMethod() && isValid;
        }

        if (!isValid) {
            Toast.fire({
                icon: 'error',
                title: 'กรุณากรอกข้อมูลให้ครบถ้วน'
            });
        }

        return isValid;
    }

    validateShippingInfo() {
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');

        let isValid = true;

        // Email validation
        if (email && email.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                isValid = false;
            }
        }

        // Phone validation
        if (phone && phone.value) {
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phone.value.replace(/\D/g, ''))) {
                phone.classList.add('is-invalid');
                isValid = false;
            }
        }

        return isValid;
    }

    validatePaymentMethod() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            Toast.fire({
                icon: 'error',
                title: 'กรุณาเลือกวิธีการชำระเงิน'
            });
            return false;
        }
        return true;
    }

    bindFormValidation() {
        // Real-time validation
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.addEventListener('blur', () => {
                if (field.value.trim()) {
                    field.classList.remove('is-invalid');
                }
            });
        });
    }

    bindPaymentMethodSelection() {
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.onPaymentMethodChange(e.target.value);
            });
        });
    }

    onPaymentMethodChange(method) {
        // Hide all payment forms
        document.querySelectorAll('.payment-form').forEach(form => {
            form.style.display = 'none';
        });

        // Show selected payment form
        const selectedForm = document.getElementById(`${method}-form`);
        if (selectedForm) {
            selectedForm.style.display = 'block';
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    async submitOrder() {
        if (!this.validateCurrentStep()) return;

        const formData = new FormData(document.getElementById('checkout-form'));

        try {
            const response = await fetch('/checkout/process', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Toast.fire({
                    icon: 'success',
                    title: 'สั่งซื้อเรียบร้อย',
                    text: 'กำลังนำไปยังหน้าชำระเงิน...'
                });

                setTimeout(() => {
                    window.location.href = data.redirect_url || '/payment';
                }, 2000);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'เกิดข้อผิดพลาดในการสั่งซื้อ'
                });
            }
        } catch (error) {
            console.error('Checkout error:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
            });
        }
    }
}

// Global functions for backward compatibility
window.goToStep = function(step) {
    if (window.checkoutHandler) {
        window.checkoutHandler.goToStep(step);
    }
};

window.validateAndProceed = function(nextStep) {
    if (window.checkoutHandler) {
        window.checkoutHandler.goToStep(nextStep);
    }
};

// Initialize checkout handler
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.checkout-container') ||
        document.getElementById('checkout-form')) {
        window.checkoutHandler = new CheckoutHandler();
    }
});