/**
 * SweetAlert Utility Library
 * Centralized alert management for the entire application
 */

class SweetAlertUtils {
    constructor() {
        this.defaultConfig = {
            showConfirmButton: true,
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            allowOutsideClick: true,
            allowEscapeKey: true,
            showCloseButton: false,
            timer: null,
            timerProgressBar: false
        };
    }

    success(title, text = null, options = {}) {
        const config = {
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Great!',
            timer: 3000,
            timerProgressBar: true,
            showCloseButton: true,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    error(title, text = null, options = {}) {
        const config = {
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Try Again',
            showCloseButton: true,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    warning(title, text = null, options = {}) {
        const config = {
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Understood',
            showCloseButton: true,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    info(title, text = null, options = {}) {
        const config = {
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#17a2b8',
            confirmButtonText: 'Got it!',
            timer: 4000,
            timerProgressBar: true,
            showCloseButton: true,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    confirm(title, text = null, options = {}) {
        const config = {
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true,
            focusCancel: true,
            showCloseButton: true,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    confirmDelete(title = 'Are you sure?', text = 'This action cannot be undone!', options = {}) {
        const config = {
            icon: 'warning',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            showCloseButton: false,
            allowOutsideClick: false,
            ...this.defaultConfig,
            ...options
        };

        return Swal.fire(config);
    }

    loading(title = 'Loading...', text = 'Please wait') {
        return Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    toast(type, message, options = {}) {
        const config = {
            toast: true,
            position: options.position || 'top-end',
            showConfirmButton: false,
            timer: options.timer || 3000,
            timerProgressBar: true,
            icon: type,
            title: message,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            ...options
        };

        return Swal.fire(config);
    }

    successToast(message, options = {}) {
        return this.toast('success', message, {
            iconColor: '#28a745',
            ...options
        });
    }

    errorToast(message, options = {}) {
        return this.toast('error', message, {
            iconColor: '#dc3545',
            timer: 4000,
            ...options
        });
    }

    warningToast(message, options = {}) {
        return this.toast('warning', message, {
            iconColor: '#ffc107',
            ...options
        });
    }

    infoToast(message, options = {}) {
        return this.toast('info', message, {
            iconColor: '#17a2b8',
            ...options
        });
    }

    custom(config = {}) {
        return Swal.fire({
            ...this.defaultConfig,
            ...config
        });
    }

    async formValidationError(errors, title = 'Validation Failed') {
        let errorHtml = '<ul class="text-start mb-0">';
        
        if (typeof errors === 'object') {
            Object.values(errors).forEach(errorArray => {
                if (Array.isArray(errorArray)) {
                    errorArray.forEach(error => {
                        errorHtml += `<li>${error}</li>`;
                    });
                } else {
                    errorHtml += `<li>${errorArray}</li>`;
                }
            });
        } else {
            errorHtml += `<li>${errors}</li>`;
        }
        
        errorHtml += '</ul>';

        return this.error(title, null, {
            html: errorHtml,
            width: '500px'
        });
    }

    networkError(error = null) {
        let title = 'Network Error';
        let text = 'Unable to connect to the server. Please check your internet connection and try again.';
        
        if (error && error.status) {
            switch (error.status) {
                case 404:
                    title = 'Not Found';
                    text = 'The requested resource was not found.';
                    break;
                case 500:
                    title = 'Server Error';
                    text = 'An internal server error occurred. Please try again later.';
                    break;
                case 403:
                    title = 'Access Denied';
                    text = 'You do not have permission to perform this action.';
                    break;
                case 401:
                    title = 'Unauthorized';
                    text = 'Please log in to continue.';
                    break;
                case 422:
                    title = 'Validation Error';
                    text = 'Please check your input and try again.';
                    break;
                default:
                    text = `Error ${error.status}: ${error.statusText || 'Unknown error occurred'}`;
            }
        }

        return this.error(title, text, {
            confirmButtonText: 'Retry',
            showCloseButton: true
        });
    }

    sessionExpired() {
        return this.warning(
            'Session Expired',
            'Your session has expired. Please log in again to continue.',
            {
                confirmButtonText: 'Login',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false
            }
        ).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/login';
            }
        });
    }

    permissionDenied(message = 'You do not have permission to perform this action.') {
        return this.error('Access Denied', message, {
            confirmButtonText: 'Go Back',
            confirmButtonColor: '#6c757d'
        });
    }

    maintenanceMode() {
        return this.info(
            'System Maintenance',
            'The system is currently under maintenance. Please try again later.',
            {
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false
            }
        );
    }

    progressDialog(title = 'Processing...') {
        return Swal.fire({
            title: title,
            html: `
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" id="swal-progress-bar">
                        <span id="swal-progress-text">0%</span>
                    </div>
                </div>
                <div id="swal-progress-message">Initializing...</div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCloseButton: false,
            didOpen: () => {
                this.progressBar = document.getElementById('swal-progress-bar');
                this.progressText = document.getElementById('swal-progress-text');
                this.progressMessage = document.getElementById('swal-progress-message');
            }
        });
    }

    updateProgress(percentage, message = null) {
        if (this.progressBar && this.progressText) {
            this.progressBar.style.width = `${percentage}%`;
            this.progressText.textContent = `${Math.round(percentage)}%`;
            
            if (message && this.progressMessage) {
                this.progressMessage.textContent = message;
            }

            if (percentage >= 100) {
                setTimeout(() => {
                    Swal.close();
                    this.successToast('Operation completed successfully!');
                }, 500);
            }
        }
    }

    mixin(options = {}) {
        return Swal.mixin(options);
    }

    close() {
        return Swal.close();
    }

    isVisible() {
        return Swal.isVisible();
    }

    getContainer() {
        return Swal.getContainer();
    }

    getPopup() {
        return Swal.getPopup();
    }
}

// Create global instance
const SweetAlert = new SweetAlertUtils();

// Make it globally available
if (typeof window !== 'undefined') {
    window.SweetAlert = SweetAlert;
    window.SA = SweetAlert; // Short alias
}

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SweetAlertUtils;
}
