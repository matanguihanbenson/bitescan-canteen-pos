.menu-items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    /* Add these properties to improve rendering performance */
    contain: content;
    will-change: contents;
}

/* Add containment for better performance */
#menuGroupsContainer {
    contain: content;
}

.menu-item {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(0,0,0,.125);
    /* Remove transition to prevent delay */
}

.menu-item-image {
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.menu-item-details {
    padding: 0.5rem;
}

.card {
    /* Remove transition to prevent delay */
    position: relative;
    z-index: 1;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.btn-link {
    color: #000;
    text-decoration: none;
}

.btn-link:hover {
    color: #000;
    text-decoration: none;
}

/* Add new styles */
.menu-item {
    position: relative;
    border: 1px solid rgba(0,0,0,.125);
    transition: all 0.3s ease;
}


.menu-item-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1;
}

/* Reset relative positioning */
.card {
    position: relative;
    z-index: 1;
}

/* Ensure dropdown container has proper positioning */
.dropdown {
    position: relative !important;
    z-index: 1050 !important;
}

/* Style dropdown menu */
.dropdown-menu {
    position: absolute !important;
    z-index: 1060 !important; /* Increased z-index to ensure it appears above neighboring cards */
    transform: none !important;
}

/* Ensure parent containers don't clip */
.card-header, 
.container-fluid,
.col-md-6, 
.col-lg-4 {
    overflow: visible !important;
}

/* Remove any transform that might create new stacking context */
.card-header .dropdown,
.card-header .dropdown-menu {
    transform: none !important;
    -webkit-transform: none !important;
}

/* Ensure proper stacking for menu items */
.dropdown-menu > li {
    z-index: 1061;
    position: relative;
}   
/* Add this to ensure the container doesn't clip the dropdown */
.container-fluid {
    overflow: visible;
}

.row {
    overflow: visible;
}

.col-md-6, .col-lg-4 {
    overflow: visible;
}

/* Replace all existing dropdown-related styles with these new ones */
.card {
    isolation: isolate;
}

.dropdown {
    position: static;
}

.card-header .dropdown {
    position: relative;
}

.dropdown-menu {
    margin: 0;
    position: absolute;
    inset: auto 0 auto auto;
    transform: none !important;
    max-width: 200px;
}

.dropdown-menu-end {
    --bs-position: end;
}

/* Remove any existing overflow rules and add these */
.container-fluid,
.row,
.col-md-6,
.col-lg-4,
.card,
.card-header {
    overflow: visible !important;
}

/* Add these new styles */
.card-header {
    position: relative;
    z-index: 3;
}

.card-body {
    position: relative;
    z-index: 2;
}

.card-footer {
    position: relative;
    z-index: 1;
}

.modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

#displayOrder {
    width: 60px;
    text-align: center;
}

/* Add these new styles */
#searchResults {
    max-height: 200px;
}

#selectedItemsList {
    max-height: 300px;
    overflow-y: auto;
}

/* Remove the previous modal-specific styles for display order */
#confirmationModal .modal-dialog {
    max-width: 400px;
}
#confirmationModal .bi-exclamation-circle {
    color: #ffc107;
}

#confirmationModal .modal-content {
    border-radius: 1rem;
}

#confirmationModal .modal-body {
    padding: 2rem;
}

#confirmationModal .btn {
    min-width: 100px;
}

/* Update existing styles */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s ease-in-out;
}

.menu-item {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease;
}
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
}

.stats-card {
    border-radius: 0.5rem;
    border: none;
    transition: transform 0.2s ease;
}
.menu-card {
    transition: transform 0.2s ease-in-out;
    border-radius: 8px;
    overflow: hidden;
}

.menu-card:hover {
    transform: translateY(-2px);
}

.menu-card .restore-item {
    opacity: 0.9;
    font-size: 0.875rem;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    backdrop-filter: blur(4px);
    background-color: rgba(255, 255, 255, 0.9);
}

.menu-card .restore-item:hover {
    opacity: 1;
    background-color: #198754;
    color: white;
    border-color: #198754;
}

.menu-card .card-img-top {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.menu-card .badge {
    font-weight: normal;
    font-size: 0.75rem;
}

.menu-card .card-title {
    max-width: 70%;
}