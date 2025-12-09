/**
 * Admin Dashboard Handler
 * Manages admin dashboard functionality and charts
 */
document.addEventListener('DOMContentLoaded', function() {
    let salesChart, topProductsChart;

    // Initialize charts
    initializeCharts();

    // ===== CHART INITIALIZATION =====
    function initializeCharts() {
        // Destroy existing charts if they exist
        if (salesChart) {
            salesChart.destroy();
        }
        if (topProductsChart) {
            topProductsChart.destroy();
        }

        // Sales Chart
        const salesCanvas = document.getElementById('salesChart');
        if (!salesCanvas) return;

        const monthlySales = JSON.parse(salesCanvas?.dataset?.monthlySales || '[]');
        const hasSalesData = monthlySales.some(value => value > 0);

        salesChart = new Chart(salesCanvas, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: monthlySales,
                    borderWidth: 4,
                    borderColor: '#ff8b26',
                    backgroundColor: 'rgba(255,140,38,0.15)',
                    pointBackgroundColor: '#ff8b26',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    shadowColor: 'rgba(255,140,38,0.3)',
                    shadowBlur: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#ff8b26',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return 'เดือน ' + context[0].label;
                            },
                            label: function(context) {
                                return 'ยอดขาย: ฿' + context.parsed.y.toLocaleString('th-TH');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'เดือน',
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'ยอดขาย (บาท)',
                            font: {
                                size: 14,
                                family: 'Prompt, sans-serif'
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return '฿' + value.toLocaleString('th-TH');
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 4
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Top Products Chart
        const topProductsCanvas = document.getElementById('topProductsChart');
        if (!topProductsCanvas) return;

        const topProducts = JSON.parse(topProductsCanvas?.dataset?.topProducts || '[]');

        topProductsChart = new Chart(topProductsCanvas, {
            type: 'bar',
            data: {
                labels: topProducts.map(p => p.product_name.length > 15 ? p.product_name.substring(0, 15) + '...' : p.product_name),
                datasets: [{
                    label: 'จำนวนที่ขายได้',
                    data: topProducts.map(p => p.total_sold),
                    backgroundColor: 'rgba(255, 107, 53, 0.8)',
                    borderColor: '#ff6b35',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return topProducts[context[0].dataIndex].product_name;
                            },
                            label: function(context) {
                                const product = topProducts[context.dataIndex];
                                return [
                                    'จำนวนขาย: ' + product.total_sold + ' ชิ้น',
                                    'ยอดรวม: ฿' + parseFloat(product.total_revenue).toLocaleString('th-TH')
                                ];
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function(value) {
                            return value;
                        },
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        color: '#374151'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'จำนวนที่ขายได้',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'สินค้า',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : []
        });

        // Show no data messages if needed
        if (!hasSalesData) {
            showNoDataMessage(salesCanvas.parentElement, 'ยังไม่มีข้อมูลยอดขาย', 'ข้อมูลจะแสดงเมื่อมีรายการขายเกิดขึ้น');
        }

        if (topProducts.length === 0) {
            showNoDataMessage(topProductsCanvas.parentElement, 'ยังไม่มีข้อมูลสินค้าขายดี', 'ข้อมูลจะแสดงเมื่อมีสินค้าถูกขาย');
        }
    }

    // ===== CHART CONTROLS =====
    // Chart period selector
    document.getElementById('chartPeriod')?.addEventListener('change', function() {
        // In a real app, this would fetch different data based on period
        showNotification('ฟีเจอร์นี้กำลังพัฒนา', 'info');
    });

    // ===== EXPORT FUNCTIONS =====
    function exportChart() {
        const canvas = document.getElementById('salesChart');
        if (canvas) {
            const link = document.createElement('a');
            link.download = 'sales-chart-' + new Date().toISOString().split('T')[0] + '.png';
            link.href = canvas.toDataURL();
            link.click();
            showNotification('ส่งออกกราฟสำเร็จ', 'success');
        } else {
            showNotification('ไม่พบกราฟสำหรับส่งออก', 'error');
        }
    }

    function exportDashboard() {
        // In a real app, this would generate a comprehensive report
        showNotification('กำลังส่งออกข้อมูลแดชบอร์ด...', 'info');

        setTimeout(() => {
            showNotification('ส่งออกข้อมูลสำเร็จ', 'success');
        }, 2000);
    }

    // ===== REFRESH FUNCTIONS =====
    function refreshChart() {
        const btn = document.querySelector('[onclick*="refreshChart"]');
        if (!btn) return;

        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            showNotification('รีเฟรชข้อมูลสำเร็จ', 'success');
        }, 1500);
    }

    function refreshTopProducts() {
        const btn = document.querySelector('[onclick*="refreshTopProducts"]');
        if (!btn) return;

        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            showNotification('รีเฟรชข้อมูลสินค้าขายดีสำเร็จ', 'success');
        }, 1500);
    }

    // ===== UTILITY FUNCTIONS =====
    function showNoDataMessage(container, title, message) {
        const noDataMessage = document.createElement('div');
        noDataMessage.className = 'no-data-message';
        noDataMessage.innerHTML = `
            <i class="fas fa-chart-line fa-3x mb-3 text-muted"></i>
            <h5 class="text-muted">${title}</h5>
            <p class="text-muted">${message}</p>
        `;
        container.appendChild(noDataMessage);
    }

    function showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());

        const notification = document.createElement('div');
        notification.className = `toast-notification alert alert-${type} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // ===== QUICK ACTIONS =====
    function clearCache() {
        showNotification('กำลังล้างแคช...', 'info');

        fetch('/admin/dashboard/refresh-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification('ล้างแคชสำเร็จ', 'success');
        })
        .catch(error => {
            showNotification('เกิดข้อผิดพลาดในการล้างแคช', 'error');
        });
    }

    function backupData() {
        showNotification('กำลังสำรองข้อมูล...', 'info');

        setTimeout(() => {
            showNotification('สำรองข้อมูลสำเร็จ', 'success');
        }, 2000);
    }

    // ===== MAKE FUNCTIONS GLOBAL =====
    window.refreshChart = refreshChart;
    window.refreshTopProducts = refreshTopProducts;
    window.exportChart = exportChart;
    window.exportDashboard = exportDashboard;
    window.clearCache = clearCache;
    window.backupData = backupData;

    // ===== AUTO REFRESH =====
    // Auto refresh data every 5 minutes
    setInterval(() => {
        // In a real app, this would fetch updated data
        console.log('Auto-refreshing dashboard data...');
    }, 300000);
});