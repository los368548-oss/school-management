/**
 * Custom JavaScript for School Management System
 */

// Global variables
let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeComponents();
    setupEventListeners();
    initializeTooltips();
    initializePopovers();
});

/**
 * Initialize all components
 */
function initializeComponents() {
    // Initialize Bootstrap tooltips
    initializeTooltips();

    // Initialize Bootstrap popovers
    initializePopovers();

    // Initialize form validations
    initializeFormValidations();

    // Initialize data tables if DataTables is available
    if (typeof $.fn.DataTable !== 'undefined') {
        initializeDataTables();
    }

    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }

    // Auto-hide alerts after 5 seconds
    autoHideAlerts();
}

/**
 * Setup global event listeners
 */
function setupEventListeners() {
    // Handle form submissions with loading states
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('loading-on-submit')) {
            showLoadingState(form);
        }
    });

    // Handle AJAX form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('ajax-submit')) {
            e.preventDefault();
            submitAjaxForm(form);
        }
    });

    // Handle delete confirmations
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-confirm') || e.target.closest('.delete-confirm')) {
            e.preventDefault();
            const element = e.target.classList.contains('delete-confirm') ? e.target : e.target.closest('.delete-confirm');
            confirmDelete(element);
        }
    });

    // Handle bulk actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('bulk-action') || e.target.closest('.bulk-action')) {
            e.preventDefault();
            const element = e.target.classList.contains('bulk-action') ? e.target : e.target.closest('.bulk-action');
            handleBulkAction(element);
        }
    });
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize Bootstrap popovers
 */
function initializePopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Initialize form validations
 */
function initializeFormValidations() {
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Initialize DataTables
 */
function initializeDataTables() {
    $('.data-table').each(function() {
        const table = $(this);
        const options = {
            responsive: true,
            pageLength: 25,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        };

        // Add export buttons if available
        if (typeof $.fn.DataTable.Buttons !== 'undefined') {
            options.dom = 'Bfrtip';
            options.buttons = [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ];
        }

        table.DataTable(options);
    });
}

/**
 * Initialize Charts
 */
function initializeCharts() {
    $('.chart-canvas').each(function() {
        const canvas = this;
        const config = JSON.parse(canvas.dataset.chartConfig || '{}');

        if (config.type && config.data) {
            new Chart(canvas, config);
        }
    });
}

/**
 * Auto-hide alerts
 */
function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Show loading state for forms
 */
function showLoadingState(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
    }
}

/**
 * Submit form via AJAX
 */
function submitAjaxForm(form) {
    const formData = new FormData(form);
    const action = form.action || window.location.href;
    const method = form.method || 'POST';

    // Add CSRF token if available
    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    showLoadingState(form);

    fetch(action, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success', data.message || 'Operation completed successfully', 'success');
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                location.reload();
            }
        } else {
            showToast('Error', data.message || 'An error occurred', 'danger');
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        showToast('Error', 'Network error occurred', 'danger');
    })
    .finally(() => {
        // Reset form state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtn.dataset.originalText || 'Submit';
        }
    });
}

/**
 * Confirm delete action
 */
function confirmDelete(element) {
    const url = element.dataset.url || element.href;
    const message = element.dataset.message || 'Are you sure you want to delete this item?';
    const title = element.dataset.title || 'Confirm Delete';

    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Add CSRF token
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        document.body.appendChild(form);
        form.submit();
    }
}

/**
 * Handle bulk actions
 */
function handleBulkAction(element) {
    const action = element.dataset.action;
    const selectedItems = getSelectedItems();

    if (selectedItems.length === 0) {
        showToast('Warning', 'Please select items to perform this action', 'warning');
        return;
    }

    switch (action) {
        case 'delete':
            if (confirm(`Are you sure you want to delete ${selectedItems.length} items?`)) {
                submitBulkAction(action, selectedItems);
            }
            break;
        case 'activate':
        case 'deactivate':
            submitBulkAction(action, selectedItems);
            break;
        default:
            console.warn('Unknown bulk action:', action);
    }
}

/**
 * Get selected items from checkboxes
 */
function getSelectedItems() {
    const checkboxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

/**
 * Submit bulk action
 */
function submitBulkAction(action, items) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('items', JSON.stringify(items));

    if (csrfToken) {
        formData.append('csrf_token', csrfToken);
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success', data.message || 'Bulk action completed', 'success');
            location.reload();
        } else {
            showToast('Error', data.message || 'Bulk action failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Bulk action error:', error);
        showToast('Error', 'Network error occurred', 'danger');
    });
}

/**
 * Show toast notification
 */
function showToast(title, message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toastEl);

    // Initialize and show toast
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();

    // Remove toast element after hiding
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

/**
 * Show loading overlay
 */
function showLoadingOverlay(message = 'Loading...') {
    let overlay = document.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">${message}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

/**
 * Hide loading overlay
 */
function hideLoadingOverlay() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'INR') {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };

    return new Date(date).toLocaleDateString('en-IN', { ...defaultOptions, ...options });
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Success', 'Copied to clipboard', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Success', 'Copied to clipboard', 'success');
    }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Generate random string
 */
function generateRandomString(length = 8) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

// Export functions to global scope for use in HTML
window.showToast = showToast;
window.showLoadingOverlay = showLoadingOverlay;
window.hideLoadingOverlay = hideLoadingOverlay;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.copyToClipboard = copyToClipboard;
window.isValidEmail = isValidEmail;
window.isValidPhone = isValidPhone;