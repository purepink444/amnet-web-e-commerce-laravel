/**
 * Notification Manager
 * จัดการการแสดงผลแจ้งเตือนต่างๆ ในระบบ
 */
class NotificationManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // สร้าง container สำหรับ notifications
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.className = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
    }

    /**
     * แสดง notification
     * @param {string} message - ข้อความแจ้งเตือน
     * @param {string} type - ประเภท (success, error, warning, info)
     * @param {number} duration - ระยะเวลาแสดง (ms)
     */
    show(message, type = 'info', duration = 5000) {
        const notification = this.createNotification(message, type);

        // เพิ่ม pointer-events ให้ notification สามารถ interact ได้
        notification.style.pointerEvents = 'auto';

        this.container.appendChild(notification);

        // แสดง animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // ซ่อนและลบ notification อัตโนมัติ
        if (duration > 0) {
            setTimeout(() => {
                this.hide(notification);
            }, duration);
        }

        return notification;
    }

    /**
     * สร้าง notification element
     */
    createNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const iconMap = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${iconMap[type] || 'info-circle'} notification-icon"></i>
                <span class="notification-message">${message}</span>
                <button class="notification-close" aria-label="ปิดการแจ้งเตือน">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // จัดการ event สำหรับปุ่มปิด
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => this.hide(notification));

        return notification;
    }

    /**
     * ซ่อน notification
     */
    hide(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    /**
     * แสดง success notification
     */
    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    /**
     * แสดง error notification
     */
    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    /**
     * แสดง warning notification
     */
    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    /**
     * แสดง info notification
     */
    info(message, duration) {
        return this.show(message, 'info', duration);
    }

    /**
     * ล้าง notification ทั้งหมด
     */
    clear() {
        const notifications = this.container.querySelectorAll('.notification');
        notifications.forEach(notification => this.hide(notification));
    }
}

// สร้าง instance กลาง
const notificationManager = new NotificationManager();

// Export สำหรับการใช้งาน
export default notificationManager;

// ทำให้สามารถเข้าถึงได้จาก global scope
if (typeof window !== 'undefined') {
    window.NotificationManager = notificationManager;
}