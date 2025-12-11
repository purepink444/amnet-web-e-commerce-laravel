document.addEventListener('DOMContentLoaded', function() {
    // Bulk actions functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkButtons = document.querySelector('.bulk-buttons');
    const bulkActionSelect = document.querySelector('.bulk-action-select');
    const newRoleSelect = document.getElementById('newRoleSelect');

    // Handle select all checkbox
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const totalBoxes = userCheckboxes.length;

        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes && totalBoxes > 0;
        headerCheckbox.checked = selectAllCheckbox.checked;

        // Show/hide bulk actions
        if (checkedBoxes.length > 0) {
            bulkButtons.style.display = 'flex';
        } else {
            bulkButtons.style.display = 'none';
            bulkActionSelect.value = '';
            if (newRoleSelect) newRoleSelect.style.display = 'none';
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            headerCheckbox.checked = this.checked;
            updateSelectAllState();
        });
    }

    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', function() {
            selectAllCheckbox.checked = this.checked;
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllState();
        });
    }

    // Individual checkbox changes
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });

    // Handle bulk action selection
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', function() {
            if (this.value === 'change_role' && newRoleSelect) {
                newRoleSelect.style.display = 'inline-block';
                newRoleSelect.name = 'new_role_id';
            } else if (newRoleSelect) {
                newRoleSelect.style.display = 'none';
                newRoleSelect.name = '';
            }
        });
    }

    // Bulk action form submission
    const bulkForm = document.getElementById('bulkForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            if (selectedUsers.length === 0) {
                e.preventDefault();
                showNotification('กรุณาเลือกผู้ใช้อย่างน้อยหนึ่งคน', 'warning');
                return;
            }

            if (!bulkActionSelect || !bulkActionSelect.value) {
                e.preventDefault();
                showNotification('กรุณาเลือกการดำเนินการ', 'warning');
                return;
            }

            let confirmMessage = '';
            switch (bulkActionSelect.value) {
                case 'activate':
                    confirmMessage = `ต้องการเปิดใช้งานผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                    break;
                case 'deactivate':
                    confirmMessage = `ต้องการปิดใช้งานผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                    break;
                case 'change_role':
                    const selectedRole = newRoleSelect ? newRoleSelect.options[newRoleSelect.selectedIndex].text : '';
                    confirmMessage = `ต้องการเปลี่ยนบทบาทเป็น "${selectedRole}" ของผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                    break;
                case 'delete':
                    confirmMessage = `ต้องการลบผู้ใช้ ${selectedUsers.length} คนหรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้`;
                    break;
            }

            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        });
    }

    // Clear filters link
    const clearFiltersLink = document.querySelector('.clear-filters');
    if (clearFiltersLink) {
        clearFiltersLink.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('role');
            url.searchParams.delete('status');
            url.searchParams.delete('date_from');
            url.searchParams.delete('date_to');
            window.location.href = url.toString();
        });
    }

    // Auto-submit search on enter
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

    // Utility function for notifications
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Initialize
    updateSelectAllState();
});