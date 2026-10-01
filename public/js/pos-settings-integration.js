/**
 * POS Settings Integration
 * Loads dynamic settings from backend for POS system
 */

(function() {
    'use strict';

    // Global settings object
    window.POSSettings = {
        taxRate: 0,
        discountTypes: [],
        departments: [],
        creditUserTypes: [],
        loaded: false
    };

    /**
     * Load all POS settings from backend
     */
    function loadPOSSettings() {
        // Load tax rate
        $.get('/api/settings/tax-rate')
            .done(function(response) {
                window.POSSettings.taxRate = parseFloat(response.tax_rate) || 0;
                })
            .fail(function() {
                });

        // Load discount types
        $.get('/api/pos-settings/discount-types')
            .done(function(response) {
                window.POSSettings.discountTypes = response.discount_types || [];
                populateDiscountDropdown();
            })
            .fail(function() {
                });

        // Load departments
        $.get('/api/pos-settings/departments')
            .done(function(response) {
                window.POSSettings.departments = response.departments || [];
                populateDepartmentsDropdown();
            })
            .fail(function() {
                });

        // Load credit user types
        $.get('/api/pos-settings/credit-user-types')
            .done(function(response) {
                window.POSSettings.creditUserTypes = response.credit_user_types || [];
                populateCreditUserTypes();
            })
            .fail(function() {
                });

        window.POSSettings.loaded = true;
    }

    /**
     * Populate discount dropdown with dynamic types
     * APPENDS database discount types to the hardcoded ones (Scholar, Fixed Amount, Percentage)
     */
    function populateDiscountDropdown() {
        const $select = $('#discountType');
        
        if ($select.length > 0 && window.POSSettings.discountTypes.length > 0) {
            // Remove previously added dynamic options
            $select.find('option[data-dynamic="true"]').remove();
            
            // Append dynamic discount types from database
            window.POSSettings.discountTypes.forEach(function(discount) {
                $select.append(
                    $('<option></option>')
                        .attr('value', 'preset_' + discount.name)
                        .attr('data-percentage', discount.value)
                        .attr('data-dynamic', 'true')
                        .text(discount.name + ' (' + discount.value + '%)')
                );
            });
            
            }
    }

    /**
     * Populate credit user types in the charge type dropdown
     */
    function populateCreditUserTypes() {
        const $select = $('#chargeType');
        
        if ($select.length > 0 && window.POSSettings.creditUserTypes.length > 0) {
            const currentValue = $select.val();
            
            // Clear all options
            $select.empty();
            
            // Add default option
            $select.append('<option value="">Select type...</option>');
            
            // Append dynamic credit user types from database
            window.POSSettings.creditUserTypes.forEach(function(type) {
                $select.append(
                    $('<option></option>')
                        .attr('value', type.toLowerCase())
                        .text(type)
                );
            });
            
            // Restore previous value if it exists
            if (currentValue) {
                $select.val(currentValue);
            }
            
            }
    }

    /**
     * Populate departments dropdown
     */
    function populateDepartmentsDropdown() {
        const selectors = ['#departmentSelect', '#department', '[name="department"]'];
        
        selectors.forEach(function(selector) {
            const $select = $(selector);
            if ($select.length > 0) {
                const currentValue = $select.val();
                $select.empty();
                $select.append('<option value="">Select Department</option>');
                
                window.POSSettings.departments.forEach(function(dept) {
                    $select.append(
                        $('<option></option>')
                            .attr('value', dept)
                            .text(dept)
                    );
                });
                
                // Append Custom/Other option at the end
                $select.append(
                    $('<option></option>')
                        .attr('value', 'custom')
                        .text('Custom/Other')
                );
                
                if (currentValue) {
                    $select.val(currentValue);
                }
                
                }
        });
    }

    /**
     * Calculate total with tax
     */
    window.calculateTotalWithTax = function(subtotal) {
        const tax = subtotal * (window.POSSettings.taxRate / 100);
        const total = subtotal + tax;
        
        return {
            subtotal: parseFloat(subtotal).toFixed(2),
            tax: parseFloat(tax).toFixed(2),
            taxRate: window.POSSettings.taxRate,
            total: parseFloat(total).toFixed(2)
        };
    };

    /**
     * Get discount percentage by name
     */
    window.getDiscountPercentage = function(discountName) {
        const discount = window.POSSettings.discountTypes.find(d => d.name === discountName);
        return discount ? discount.value : 0;
    };

    // Initialize on document ready
    $(document).ready(function() {
        loadPOSSettings();
        
        // Re-populate dropdowns when modals are shown
        $(document).on('show.bs.modal', function(e) {
            setTimeout(function() {
                if (window.POSSettings.loaded) {
                    populateDiscountDropdown();
                    populateDepartmentsDropdown();
                    populateCreditUserTypes();
                }
            }, 100);
        });
    });

    // Export functions globally
    window.POSSettingsIntegration = {
        loadSettings: loadPOSSettings,
        populateDiscounts: populateDiscountDropdown,
        populateDepartments: populateDepartmentsDropdown,
        populateCreditUserTypes: populateCreditUserTypes,
        calculateTotalWithTax: window.calculateTotalWithTax,
        getDiscountPercentage: window.getDiscountPercentage
    };

})();