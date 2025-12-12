// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeAnimations();
});

// Initialize Charts
function initializeCharts() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        const monthlySales = JSON.parse(salesCtx.dataset.monthlySales || '[]');
        const ordersData = JSON.parse(salesCtx.dataset.monthlyOrders || '[]');

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: monthlySales,
                    borderColor: '#ff6b35',
                    backgroundColor: 'rgba(255, 107, 53, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'จำนวนคำสั่งซื้อ',
                    data: ordersData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'ยอดขาย (บาท)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'จำนวนคำสั่งซื้อ'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    }

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart');
    if (topProductsCtx) {
        const topProducts = JSON.parse(topProductsCtx.dataset.topProducts || '[]');

        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topProducts.map(p => p.product_name.substring(0, 20) + (p.product_name.length > 20 ? '...' : '')),
                datasets: [{
                    label: 'จำนวนที่ขายได้',
                    data: topProducts.map(p => p.total_sold),
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'จำนวนที่ขายได้'
                        }
                    }
                }
            }
        });
    }
}

// Initialize Animations
function initializeAnimations() {
    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    // Observe animated elements
    document.querySelectorAll('.admin-animate-fade-in, .admin-animate-slide-in').forEach(el => {
        observer.observe(el);
    });
}

// Export Dashboard Data
function exportDashboard() {
    // Create export data
    const exportData = {
        stats: {
            total_products: document.querySelector('.admin-stat-value')?.textContent || '0',
            total_orders: document.querySelectorAll('.admin-stat-value')[1]?.textContent || '0',
            total_users: document.querySelectorAll('.admin-stat-value')[2]?.textContent || '0',
            total_sales: document.querySelectorAll('.admin-stat-value')[3]?.textContent || '0'
        },
        exported_at: new Date().toISOString(),
        exported_by: document.querySelector('.admin-mb-2')?.textContent || 'Unknown'
    };

    // Create and download JSON file
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `dashboard-export-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Show success message
    showNotification('ข้อมูลถูกส่งออกเรียบร้อยแล้ว', 'success');
}

// Refresh Chart
function refreshChart() {
    // Show loading
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    // Simulate refresh (in real app, make AJAX call)
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
        showNotification('กราฟถูกอัปเดตเรียบร้อยแล้ว', 'success');
    }, 1000);
}

// Export Chart
function exportChart() {
    const canvas = document.getElementById('salesChart');
    if (canvas) {
        const link = document.createElement('a');
        link.download = `sales-chart-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
}

// Refresh Top Products
function refreshTopProducts() {
    // Show loading
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    // Simulate refresh
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
        showNotification('ข้อมูลสินค้าขายดีถูกอัปเดตเรียบร้อยแล้ว', 'success');
    }, 1000);
}

// Show Notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `admin-alert admin-alert-${type} admin-animate-fade-in`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
    notification.innerHTML = `
        <i class="admin-alert-icon fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <div>${message}</div>
        <button onclick="this.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: inherit; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Handle chart period change
document.getElementById('chartPeriod')?.addEventListener('change', function() {
    // In real app, update chart data based on selected period
    showNotification('ฟีเจอร์นี้ยังไม่พร้อมใช้งาน', 'info');
});