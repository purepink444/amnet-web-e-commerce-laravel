/**
 * Admin Dashboard Handler
 * Manages admin dashboard functionality and charts
 */
export class AdminDashboard {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        this.initializeCharts();
        this.bindEventListeners();
    }

    initializeCharts() {
        // Initialize any charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            this.createSalesChart();
            this.createOrdersChart();
            this.createProductsChart();
        }
    }

    createSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            this.charts.sales = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.getLast7Days(),
                    datasets: [{
                        label: 'ยอดขาย (บาท)',
                        data: this.getSalesData(),
                        borderColor: '#ff6b35',
                        backgroundColor: 'rgba(255, 107, 53, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'ยอดขาย 7 วันล่าสุด'
                        }
                    }
                }
            });
        }
    }

    createOrdersChart() {
        const ctx = document.getElementById('ordersChart');
        if (ctx) {
            this.charts.orders = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.getLast7Days(),
                    datasets: [{
                        label: 'จำนวนคำสั่งซื้อ',
                        data: this.getOrdersData(),
                        backgroundColor: '#ff6b35',
                        borderColor: '#e85d2a',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'คำสั่งซื้อ 7 วันล่าสุด'
                        }
                    }
                }
            });
        }
    }

    createProductsChart() {
        const ctx = document.getElementById('productsChart');
        if (ctx) {
            this.charts.products = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['สินค้าขายดี', 'สินค้าทั่วไป', 'สินค้าใหม่'],
                    datasets: [{
                        data: this.getProductsData(),
                        backgroundColor: [
                            '#ff6b35',
                            '#ff8c5f',
                            '#ffb088'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'สัดส่วนสินค้า'
                        }
                    }
                }
            });
        }
    }

    getLast7Days() {
        const days = [];
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            days.push(date.toLocaleDateString('th-TH', { month: 'short', day: 'numeric' }));
        }
        return days;
    }

    getSalesData() {
        // This should come from server data
        return [1200, 1900, 3000, 5000, 2000, 3000, 4500];
    }

    getOrdersData() {
        // This should come from server data
        return [12, 19, 30, 50, 20, 30, 45];
    }

    getProductsData() {
        // This should come from server data
        return [45, 30, 25];
    }

    bindEventListeners() {
        // Bind any dashboard-specific events
        this.bindQuickActions();
        this.bindExportButtons();
    }

    bindQuickActions() {
        // Quick action buttons
        document.querySelectorAll('.quick-action-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.target.dataset.action;
                this.handleQuickAction(action);
            });
        });
    }

    handleQuickAction(action) {
        switch (action) {
            case 'add-product':
                window.location.href = '/admin/products/create';
                break;
            case 'view-orders':
                window.location.href = '/admin/orders';
                break;
            case 'manage-users':
                window.location.href = '/admin/users';
                break;
            case 'reports':
                window.location.href = '/admin/reports';
                break;
        }
    }

    bindExportButtons() {
        document.querySelectorAll('.export-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const format = e.target.dataset.format || 'pdf';
                this.exportData(format);
            });
        });
    }

    async exportData(format) {
        try {
            const response = await fetch(`/admin/export/${format}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `report.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                Toast.fire({
                    icon: 'success',
                    title: 'ส่งออกข้อมูลเรียบร้อย'
                });
            } else {
                throw new Error('Export failed');
            }
        } catch (error) {
            console.error('Export error:', error);
            Toast.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการส่งออก'
            });
        }
    }

    refreshCharts() {
        // Refresh chart data
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                chart.update();
            }
        });
    }
}

// Initialize admin dashboard
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.admin-dashboard') ||
        document.getElementById('salesChart') ||
        document.getElementById('ordersChart')) {
        window.adminDashboard = new AdminDashboard();
    }
});