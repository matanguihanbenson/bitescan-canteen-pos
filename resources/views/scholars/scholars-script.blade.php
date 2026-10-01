@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Load summary statistics
    loadSummaryStats();
    
    // Setup form handlers for Add Scholar
    $('#addScholarForm').on('submit', function(e) {
        e.preventDefault();
        var formData = {
            student_id: $('#student_id').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            contact_number: $('#contact_number').val(),
            program: $('#program').val(),
            year_level: $('#year_level').val(),
            daily_allowance: $('#daily_allowance').val() || 70.00,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '/scholars',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Scholar added successfully!');
                    } else {
                        alert('Scholar added successfully!');
                    }
                    
                    // Hide modal
                    $('#addScholarModal').modal('hide');
                    
                    // Reset form
                    $('#addScholarForm')[0].reset();
                    
                    // Reload page to show new data
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                var message = 'Error adding scholar';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    message = Object.keys(errors).map(function(key) {
                        return errors[key][0];
                    }).join('\n');
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }
        });
    });
    
    // Setup form handlers for Edit Scholar
    $('#editScholarForm').on('submit', function(e) {
        e.preventDefault();
        var scholarId = $('#edit_scholar_id').val();
        var formData = {
            student_id: $('#edit_student_id').val(),
            first_name: $('#edit_first_name').val(),
            last_name: $('#edit_last_name').val(),
            email: $('#edit_email').val(),
            contact_number: $('#edit_contact_number').val(),
            program: $('#edit_program').val(),
            year_level: $('#edit_year_level').val(),
            daily_allowance: $('#edit_daily_allowance').val() || 70.00,
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        $.ajax({
            url: '/scholars/' + scholarId,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Scholar updated successfully!');
                    } else {
                        alert('Scholar updated successfully!');
                    }
                    
                    $('#editScholarModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                var message = 'Error updating scholar';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(message);
                } else {
                    alert(message);
                }
            }
        });
    });
    
    // Edit Scholar Button Handler
    $(document).on('click', '.edit-scholar', function() {
        var scholarId = $(this).data('id');
        $.ajax({
            url: '/scholars/' + scholarId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var scholar = response.scholar;
                    $('#edit_scholar_id').val(scholar.id);
                    $('#edit_student_id').val(scholar.student_id);
                    $('#edit_first_name').val(scholar.first_name);
                    $('#edit_last_name').val(scholar.last_name);
                    $('#edit_email').val(scholar.email || '');
                    $('#edit_contact_number').val(scholar.contact_number || '');
                    $('#edit_program').val(scholar.program || '');
                    $('#edit_year_level').val(scholar.year_level || '');
                    $('#edit_daily_allowance').val(scholar.daily_allowance || 70.00);
                    
                    $('#editScholarModal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Error loading scholar data');
            }
        });
    });
    
    // View History Button Handler
    $(document).on('click', '.view-history', function() {
        var scholarId = $(this).data('id');
        $.ajax({
            url: '/scholars/' + scholarId + '/history',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var scholar = response.scholar;
                    $('#historyScholarName').text(scholar.first_name + ' ' + scholar.last_name);
                    $('#historyScholarId').text('Student ID: ' + scholar.student_id);
                    $('#historyCurrentBalance').text('₱' + Number(scholar.current_balance).toFixed(2));
                    
                    var tbody = $('#historyTableBody');
                    tbody.empty();
                    
                    if (response.history && response.history.length > 0) {
                        response.history.forEach(function(record) {
                            var typeClass = record.type === 'Credit' ? 'bg-success' : 'bg-danger';
                            var row = '<tr>' +
                                '<td>' + record.date + '</td>' +
                                '<td><span class="badge ' + typeClass + '">' + record.type + '</span></td>' +
                                '<td>₱' + Number(record.amount).toFixed(2) + '</td>' +
                                '<td>' + record.description + '</td>' +
                                '<td>₱' + Number(record.balance_after).toFixed(2) + '</td>' +
                                '<td>' + record.processed_by + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    } else {
                        tbody.append('<tr><td colspan="6" class="text-center">No history records found</td></tr>');
                    }
                    
                    $('#scholarHistoryModal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Error loading scholar history');
            }
        });
    });
    
    // Initialize DataTable
    function initializeDataTable() {
        $.ajax({
            url: '/scholars/data',
            type: 'GET',
            data: {
                draw: 1,
                start: 0,
                length: 100
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    var tbody = $('#scholarsTable tbody');
                    tbody.empty();
                    
                    response.data.forEach(function(scholar) {
                        var row = '<tr>' +
                            '<td>' + scholar.student_id + '</td>' +
                            '<td>' + scholar.full_name + '</td>' +
                            '<td>' + (scholar.program || '-') + '</td>' +
                            '<td>' + (scholar.year_level || '-') + '</td>' +
                            '<td>' + scholar.balance + '</td>' +
                            '<td>' + scholar.last_claim + '</td>' +
                            '<td>' + scholar.status + '</td>' +
                            '<td>' + scholar.actions + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                    
                    // Initialize DataTable on the populated table
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#scholarsTable')) {
                        $('#scholarsTable').DataTable().destroy();
                    }
                    
                    if ($.fn.DataTable) {
                        $('#scholarsTable').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            pageLength: 10
                        });
                    }
                } else {
                    $('#scholarsTable tbody').html('<tr><td colspan="8" class="text-center">No scholars found</td></tr>');
                }
            },
            error: function(xhr) {
                $('#scholarsTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }
    
    // Load Summary Statistics
    function loadSummaryStats() {
        $.ajax({
            url: '/scholars/summary/stats',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var stats = response.stats;
                    $('#totalScholars').text(stats.total_scholars || 0);
                    $('#claimedToday').text(stats.claimed_today || 0);
                    $('#percentageClaimed').text((stats.percentage_claimed || 0) + '%');
                    $('#allowanceToday').text('₱' + Number(stats.total_allowance_today || 0).toFixed(2));
                    $('#totalBalance').text('₱' + Number(stats.total_balance_remaining || 0).toFixed(2));
                }
            },
            error: function(xhr) {
                }
        });
    }
});
</script>
@endsection
