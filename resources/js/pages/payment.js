/**
 * Payment Page Handler
 * Manages payment method selection and QR code generation
 */
export class PaymentHandler {
    constructor() {
        this.qrCode = null;
        this.init();
    }

    init() {
        this.bindPaymentMethodSelector();
        this.initializeQRCode();
    }

    bindPaymentMethodSelector() {
        const paymentMethodSelect = document.getElementById('payment_method');
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', (e) => {
                this.onPaymentMethodChange(e.target.value);
            });
        }
    }

    onPaymentMethodChange(method) {
        // Hide all payment sections
        document.querySelectorAll('.payment-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show selected payment section
        const selectedSection = document.getElementById(`${method}_section`);
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }

        // Generate QR code for promptpay
        if (method === 'promptpay') {
            this.generatePromptPayQR();
        }
    }

    initializeQRCode() {
        // Load QRCode library if not already loaded
        if (typeof QRCode === 'undefined') {
            this.loadQRCodeLibrary().then(() => {
                this.setupQRCode();
            });
        } else {
            this.setupQRCode();
        }
    }

    async loadQRCodeLibrary() {
        return new Promise((resolve, reject) => {
            if (document.querySelector('script[src*="qrcode"]')) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    setupQRCode() {
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer && typeof QRCode !== 'undefined') {
            // Clear existing QR code
            qrContainer.innerHTML = '';

            // Create new QR code
            this.qrCode = new QRCode(qrContainer, {
                text: this.generatePromptPayPayload(),
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    generatePromptPayPayload() {
        // This is a simplified PromptPay QR generation
        // In real implementation, you would use a proper PromptPay library
        const phoneNumber = "0812345678"; // Should come from config
        const amount = this.getOrderAmount();

        // Basic PromptPay format (simplified)
        return `00020101021129370016A000000677010111011300660000000005802TH5303764540${amount}5802TH6304`;
    }

    getOrderAmount() {
        // Get amount from page data or form
        const amountInput = document.getElementById('amount');
        return amountInput ? amountInput.value : '100.00';
    }

    generatePromptPayQR() {
        if (this.qrCode) {
            // Update existing QR code
            const qrContainer = document.getElementById('qrcode');
            if (qrContainer) {
                qrContainer.innerHTML = '';
                this.qrCode = new QRCode(qrContainer, {
                    text: this.generatePromptPayPayload(),
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        } else {
            this.setupQRCode();
        }
    }

    async submitPayment(formData) {
        try {
            const response = await fetch('/payment/process', {
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
                    title: 'การชำระเงินสำเร็จ'
                });

                // Redirect to success page
                setTimeout(() => {
                    window.location.href = data.redirect_url || '/order/success';
                }, 2000);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'การชำระเงินล้มเหลว'
                });
            }
        } catch (error) {
            console.error('Payment error:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
            });
        }
    }
}

// Global function for backward compatibility
window.generateQRCode = function() {
    if (window.paymentHandler) {
        window.paymentHandler.generatePromptPayQR();
    }
};

// Initialize payment handler
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('payment_method') || document.getElementById('qrcode')) {
        window.paymentHandler = new PaymentHandler();
    }
});