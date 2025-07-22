/**
 * POS System - Main JavaScript Application
 * 
 * This file bootstraps the JavaScript application for the POS system
 * and includes all necessary functionality.
 */

// Import Bootstrap and jQuery
import 'bootstrap';
import $ from 'jquery';

// Import custom POS modules
import './pos/pos-system';
import './pos/inventory';
import './pos/sales';
import './pos/reports';
import './pos/customers';
import './pos/barcode';
import './pos/dashboard';

// Make jQuery available globally
window.$ = window.jQuery = $;

// Initialize the POS system when document is ready
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Initialize modals
    $('.modal').modal();
    
    // Common AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Global error handler
    $(document).ajaxError(function(event, xhr, settings, error) {
        if (xhr.status === 401) {
            window.location.href = '/login';
        } else if (xhr.status === 403) {
            alert('You do not have permission to perform this action.');
        } else if (xhr.status === 500) {
            alert('An error occurred. Please try again.');
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Initialize number formatting
    initializeNumberFormatting();
    
    // Initialize date/time formatting
    initializeDateTimeFormatting();
    
    // Initialize barcode scanner
    initializeBarcodeScanner();
    
    // Initialize keyboard shortcuts
    initializeKeyboardShortcuts();
});

/**
 * Initialize number formatting for currency and quantities
 */
function initializeNumberFormatting() {
    $('.currency-input').on('input', function() {
        let value = $(this).val().replace(/[^0-9.]/g, '');
        $(this).val(value);
    });
    
    $('.quantity-input').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(value);
    });
}

/**
 * Initialize date/time formatting
 */
function initializeDateTimeFormatting() {
    $('.date-picker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
}

/**
 * Initialize barcode scanner functionality
 */
function initializeBarcodeScanner() {
    let barcodeBuffer = '';
    let barcodeTimeout;
    
    $(document).on('keypress', function(e) {
        // Only process if we're not in an input field
        if ($(e.target).is('input, textarea, select')) return;
        
        // Clear timeout
        clearTimeout(barcodeTimeout);
        
        // Add character to buffer
        barcodeBuffer += String.fromCharCode(e.which);
        
        // Set timeout to process barcode
        barcodeTimeout = setTimeout(function() {
            if (barcodeBuffer.length > 5) {
                // Process barcode
                processBarcode(barcodeBuffer);
            }
            barcodeBuffer = '';
        }, 100);
    });
}

/**
 * Process scanned barcode
 */
function processBarcode(barcode) {
    // Emit custom event
    $(document).trigger('barcode:scanned', [barcode]);
    
    // Try to find product
    if (typeof window.POS !== 'undefined' && window.POS.findProductByBarcode) {
        window.POS.findProductByBarcode(barcode);
    }
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    $(document).on('keydown', function(e) {
        // Don't process if we're in an input field
        if ($(e.target).is('input, textarea, select')) return;
        
        // Ctrl+N - New Sale
        if (e.ctrlKey && e.which === 78) {
            e.preventDefault();
            if (typeof window.POS !== 'undefined' && window.POS.newSale) {
                window.POS.newSale();
            }
        }
        
        // Ctrl+S - Save Sale
        if (e.ctrlKey && e.which === 83) {
            e.preventDefault();
            if (typeof window.POS !== 'undefined' && window.POS.saveSale) {
                window.POS.saveSale();
            }
        }
        
        // F1 - Help
        if (e.which === 112) {
            e.preventDefault();
            showHelp();
        }
        
        // F2 - Search Products
        if (e.which === 113) {
            e.preventDefault();
            $('#product-search').focus();
        }
        
        // F3 - Search Customers
        if (e.which === 114) {
            e.preventDefault();
            $('#customer-search').focus();
        }
        
        // F4 - Payment
        if (e.which === 115) {
            e.preventDefault();
            if (typeof window.POS !== 'undefined' && window.POS.processPayment) {
                window.POS.processPayment();
            }
        }
    });
}

/**
 * Show help modal
 */
function showHelp() {
    $('#help-modal').modal('show');
}

/**
 * Format currency
 */
window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

/**
 * Format number
 */
window.formatNumber = function(number, decimals = 2) {
    return parseFloat(number).toFixed(decimals);
};

/**
 * Show loading indicator
 */
window.showLoading = function() {
    $('#loading-overlay').show();
};

/**
 * Hide loading indicator
 */
window.hideLoading = function() {
    $('#loading-overlay').hide();
};

/**
 * Show success message
 */
window.showSuccess = function(message) {
    showAlert(message, 'success');
};

/**
 * Show error message
 */
window.showError = function(message) {
    showAlert(message, 'danger');
};

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alerts-container').append(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('#alerts-container .alert').last().fadeOut('slow');
    }, 5000);
}

/**
 * Confirm action
 */
window.confirmAction = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};

/**
 * Print receipt
 */
window.printReceipt = function(receiptHtml) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(receiptHtml);
    printWindow.document.close();
    printWindow.print();
};

// Export for module usage
export {
    formatCurrency,
    formatNumber,
    showLoading,
    hideLoading,
    showSuccess,
    showError,
    confirmAction,
    printReceipt
}; 