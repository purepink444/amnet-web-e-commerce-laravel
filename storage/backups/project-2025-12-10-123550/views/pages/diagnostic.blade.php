@extends('layouts.default')

@section('title', 'ระบบตรวจสอบ')

@section('content')
<!-- Hero Section -->
<div class="diagnostic-hero">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="bi bi-tools me-3"></i>ระบบตรวจสอบ
                </h1>
                <p class="lead text-white-50">
                    ตรวจสอบสถานะระบบ เครือข่าย และสินค้าออนไลน์
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-orange btn-lg" onclick="runAllChecks()">
                    <i class="bi bi-play-circle me-2"></i>ตรวจสอบทั้งหมด
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- System Health Check -->
    <div class="diagnostic-section mb-5">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-cpu me-2"></i>ตรวจสอบสถานะระบบ
            </h3>
            <button class="btn btn-outline-primary btn-sm" onclick="runSystemCheck()">
                <i class="bi bi-arrow-clockwise me-1"></i>ตรวจสอบ
            </button>
        </div>
        <div class="diagnostic-results" id="system-results">
            <div class="text-center text-muted">
                <i class="bi bi-info-circle display-4 mb-3"></i>
                <p>คลิกปุ่มตรวจสอบเพื่อดูสถานะระบบ</p>
            </div>
        </div>
    </div>

    <!-- Network Diagnostics -->
    <div class="diagnostic-section mb-5">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-router me-2"></i>ตรวจสอบเครือข่าย
            </h3>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" id="network-target" placeholder="google.com" style="width: 200px;">
                <button class="btn btn-outline-primary btn-sm" onclick="runNetworkCheck()">
                    <i class="bi bi-arrow-clockwise me-1"></i>ตรวจสอบ
                </button>
            </div>
        </div>
        <div class="diagnostic-results" id="network-results">
            <div class="text-center text-muted">
                <i class="bi bi-info-circle display-4 mb-3"></i>
                <p>คลิกปุ่มตรวจสอบเพื่อดูสถานะเครือข่าย</p>
            </div>
        </div>
    </div>

    <!-- Product Diagnostics -->
    <div class="diagnostic-section mb-5">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-box-seam me-2"></i>ตรวจสอบสินค้า
            </h3>
            <button class="btn btn-outline-primary btn-sm" onclick="runProductCheck()">
                <i class="bi bi-arrow-clockwise me-1"></i>ตรวจสอบ
            </button>
        </div>
        <div class="diagnostic-results" id="product-results">
            <div class="text-center text-muted">
                <i class="bi bi-info-circle display-4 mb-3"></i>
                <p>คลิกปุ่มตรวจสอบเพื่อดูสถานะสินค้า</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function runAllChecks() {
    runSystemCheck();
    runNetworkCheck();
    runProductCheck();
}

function runSystemCheck() {
    const resultsDiv = document.getElementById('system-results');
    resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังตรวจสอบ...</p></div>';

    fetch('/diagnostic/system')
        .then(response => response.json())
        .then(data => {
            displaySystemResults(data);
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>เกิดข้อผิดพลาดในการตรวจสอบ</div>';
        });
}

function runNetworkCheck() {
    const target = document.getElementById('network-target').value || 'google.com';
    const resultsDiv = document.getElementById('network-results');
    resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังตรวจสอบ...</p></div>';

    fetch(`/diagnostic/network?target=${encodeURIComponent(target)}`)
        .then(response => response.json())
        .then(data => {
            displayNetworkResults(data);
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>เกิดข้อผิดพลาดในการตรวจสอบ</div>';
        });
}

function runProductCheck() {
    const resultsDiv = document.getElementById('product-results');
    resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังตรวจสอบ...</p></div>';

    fetch('/diagnostic/product')
        .then(response => response.json())
        .then(data => {
            displayProductResults(data);
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>เกิดข้อผิดพลาดในการตรวจสอบ</div>';
        });
}

function displaySystemResults(data) {
    const resultsDiv = document.getElementById('system-results');
    let html = '<div class="row">';

    // CPU
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.cpu?.status)}">
                <div class="result-icon">
                    <i class="bi bi-cpu"></i>
                </div>
                <div class="result-content">
                    <h5>CPU Load</h5>
                    <p class="mb-1">${data.cpu?.load_average || 'N/A'}</p>
                    <small class="status-text">${getStatusText(data.cpu?.status)}</small>
                </div>
            </div>
        </div>
    `;

    // Memory
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass('good')}">
                <div class="result-icon">
                    <i class="bi bi-memory"></i>
                </div>
                <div class="result-content">
                    <h5>Memory</h5>
                    <p class="mb-1">${data.memory?.used || 'N/A'}</p>
                    <small class="status-text">Limit: ${data.memory?.limit || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    // Disk
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.disk?.status)}">
                <div class="result-icon">
                    <i class="bi bi-hdd"></i>
                </div>
                <div class="result-content">
                    <h5>Disk Space</h5>
                    <p class="mb-1">Free: ${data.disk?.free || 'N/A'}</p>
                    <small class="status-text">Used: ${data.disk?.used_percent || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    // Database
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.database?.status === 'connected' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-database"></i>
                </div>
                <div class="result-content">
                    <h5>Database</h5>
                    <p class="mb-1">${data.database?.status === 'connected' ? 'Connected' : 'Failed'}</p>
                    <small class="status-text">${data.database?.error || ''}</small>
                </div>
            </div>
        </div>
    `;

    // Cache
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.cache?.status === 'working' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-lightning"></i>
                </div>
                <div class="result-content">
                    <h5>Cache</h5>
                    <p class="mb-1">${data.cache?.status || 'N/A'}</p>
                    <small class="status-text">${data.cache?.error || ''}</small>
                </div>
            </div>
        </div>
    `;

    html += '</div>';
    resultsDiv.innerHTML = html;
}

function displayNetworkResults(data) {
    const resultsDiv = document.getElementById('network-results');
    let html = '<div class="row">';

    // Ping
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.ping?.status === 'reachable' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-broadcast"></i>
                </div>
                <div class="result-content">
                    <h5>Ping Test</h5>
                    <p class="mb-1">${data.ping?.host || 'N/A'}</p>
                    <small class="status-text">${data.ping?.status || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    // DNS
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.dns?.status === 'resolved' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-globe"></i>
                </div>
                <div class="result-content">
                    <h5>DNS Lookup</h5>
                    <p class="mb-1">${data.dns?.ip || 'N/A'}</p>
                    <small class="status-text">${data.dns?.status || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    // HTTP
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.http?.status === 'open' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-lock"></i>
                </div>
                <div class="result-content">
                    <h5>HTTP Port</h5>
                    <p class="mb-1">Port ${data.http?.port || '80'}</p>
                    <small class="status-text">${data.http?.status || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    // HTTPS
    html += `
        <div class="col-md-4 mb-3">
            <div class="result-card ${getStatusClass(data.https?.status === 'open' ? 'good' : 'critical')}">
                <div class="result-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="result-content">
                    <h5>HTTPS Port</h5>
                    <p class="mb-1">Port ${data.https?.port || '443'}</p>
                    <small class="status-text">${data.https?.status || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;

    html += '</div>';
    resultsDiv.innerHTML = html;
}

function displayProductResults(data) {
    const resultsDiv = document.getElementById('product-results');
    let html = '<div class="row">';

    // Total Products
    html += `
        <div class="col-md-3 mb-3">
            <div class="result-card good">
                <div class="result-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="result-content">
                    <h5>Total Products</h5>
                    <p class="mb-1">${data.total_products || 0}</p>
                    <small class="status-text">ทั้งหมด</small>
                </div>
            </div>
        </div>
    `;

    // Active Products
    html += `
        <div class="col-md-3 mb-3">
            <div class="result-card good">
                <div class="result-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="result-content">
                    <h5>Active Products</h5>
                    <p class="mb-1">${data.active_products || 0}</p>
                    <small class="status-text">พร้อมขาย</small>
                </div>
            </div>
        </div>
    `;

    // Out of Stock
    html += `
        <div class="col-md-3 mb-3">
            <div class="result-card ${data.out_of_stock > 0 ? 'warning' : 'good'}">
                <div class="result-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="result-content">
                    <h5>Out of Stock</h5>
                    <p class="mb-1">${data.out_of_stock || 0}</p>
                    <small class="status-text">สินค้าหมด</small>
                </div>
            </div>
        </div>
    `;

    // Query Time
    html += `
        <div class="col-md-3 mb-3">
            <div class="result-card good">
                <div class="result-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="result-content">
                    <h5>Query Time</h5>
                    <p class="mb-1">${data.query_time || 'N/A'}</p>
                    <small class="status-text">ประสิทธิภาพ</small>
                </div>
            </div>
        </div>
    `;

    // Images Check
    html += `
        <div class="col-md-6 mb-3">
            <div class="result-card ${data.images_check?.status === 'good' ? 'good' : 'warning'}">
                <div class="result-icon">
                    <i class="bi bi-images"></i>
                </div>
                <div class="result-content">
                    <h5>Product Images</h5>
                    <p class="mb-1">Checked: ${data.images_check?.checked || 0}</p>
                    <small class="status-text">Accessible: ${data.images_check?.accessible || 0}</small>
                </div>
            </div>
        </div>
    `;

    html += '</div>';
    resultsDiv.innerHTML = html;
}

function getStatusClass(status) {
    switch(status) {
        case 'good': return 'good';
        case 'warning': return 'warning';
        case 'critical': return 'critical';
        default: return 'unknown';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'good': return 'ปกติ';
        case 'warning': return 'เตือน';
        case 'critical': return 'วิกฤติ';
        default: return 'ไม่ทราบ';
    }
}
</script>
@endsection

@section('styles')
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-dark: #e85d2a;
    --black-primary: #1a1a1a;
    --black-secondary: #2d2d2d;
}

/* Hero Section */
.diagnostic-hero {
    background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--orange-dark) 100%);
    position: relative;
    overflow: hidden;
}

.diagnostic-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff6b35' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.btn-orange {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.8rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-orange:hover {
    background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange-primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    color: white;
}

/* Diagnostic Section */
.diagnostic-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--black-primary);
    margin: 0;
}

.diagnostic-results {
    min-height: 200px;
}

/* Result Cards */
.result-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #6c757d;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.result-card.good {
    border-left-color: #28a745;
}

.result-card.warning {
    border-left-color: #ffc107;
}

.result-card.critical {
    border-left-color: #dc3545;
}

.result-card.unknown {
    border-left-color: #6c757d;
}

.result-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

.result-icon {
    font-size: 2rem;
    color: var(--orange-primary);
    min-width: 50px;
    text-align: center;
}

.result-content h5 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--black-primary);
    margin-bottom: 0.5rem;
}

.result-content p {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--orange-primary);
    margin-bottom: 0.25rem;
}

.status-text {
    color: #6c757d;
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 768px) {
    .diagnostic-hero h1 {
        font-size: 2rem;
    }

    .section-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .result-card {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }

    .result-icon {
        font-size: 1.5rem;
    }
}
</style>
@endsection