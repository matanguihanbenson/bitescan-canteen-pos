<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    #table-filters select {
        width: 100%;
    }
    .dt-buttons {
        margin-top: .5rem;
    }
    .dataTables_filter {
        margin-bottom: 1rem;
    }
    @media (max-width: 767.98px) {
        .dt-buttons {
            margin-top: -1rem;
        }
    }
    .select2-container--bootstrap-5 {
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    
    /* Modal Styles */
    .modal-header {
        padding: 1rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.3rem;
    }
    
    .modal-body h6 {
        color: #444;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6;
    }

    .nav-tabs .nav-link {
        padding: 0.75rem 1rem;
        color: #666;
    }
    .nav-tabs .nav-link.active {
        font-weight: 500;
        color: #0d6efd;
    }
    .tab-content {
        min-height: 300px;
    }

    /* Updated Toastr custom styling */
    .toast-success {
        background-color: var(--bs-primary) !important;
    }
    
    .toast-error {
        background-color: var(--bs-danger) !important;
    }
    
    .toast-info {
        background-color: var(--bs-info) !important;
    }
    
    .toast-warning {
        background-color: var(--bs-warning) !important;
        color: var(--bs-dark) !important;
    }
    
    #toast-container > div {
        padding: 15px 15px 15px 50px;
        width: 350px;
        opacity: 1;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .toast-close-button {
        color: #fff;
        opacity: 0.8;
    }
    
    .toast-close-button:hover {
        color: #fff;
        opacity: 1;
    }

    #toast-container > div:hover {
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.25);
        opacity: 1;
    }
    
    #toast-container .toast-message {
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Add these rules to ensure proper scrollbar behavior */
    .dataTables_wrapper {
        overflow-x: auto;
        width: 100%;
    }
    
    .table {
        width: 100% !important;
    }
    /* Add these new styles */
    .product-image-container {
        position: relative;
        padding: 10px;
        border-radius: 4px;
        background: #f8f9fa;
    }
    
    .product-image {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #changeImageBtn {
        width: 100%;
        margin-top: 10px;
    }

    /* Status Confirmation Modal Styles */
    #statusConfirmModal .modal-dialog {
        max-width: 360px;
    }
    
    #statusConfirmModal .bi-toggle-on {
        color: var(--bs-primary);
        transform: rotate(0);
        transition: transform 0.3s ease;
    }
    
    #statusConfirmModal.deactivate .bi-toggle-on {
        color: var(--bs-danger);
        transform: rotate(180deg);
    }
    
    #statusAction {
        color: var(--bs-primary);
    }
    
    #statusConfirmModal.deactivate #statusAction {
        color: var(--bs-danger);
    }


    .text-gray-300 {
        color: #dddfeb !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }

    .card {
        transition: transform .2s;
    }
</style>