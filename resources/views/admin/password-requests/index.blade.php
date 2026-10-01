@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="mb-0">Password Reset Requests</h4>
            <p class="text-muted mb-0">Manage user password reset requests</p>
        </div>
        <div class="col-md-4">
            <div class="search-wrapper">
                <div class="position-relative">
                    <input type="text" 
                        id="searchInput" 
                        class="form-control form-control-lg shadow-sm" 
                        placeholder="Search by name or email..."
                        autocomplete="off">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted search-icon"></i>
                    <small class="position-absolute end-0 top-100 mt-1 text-muted d-none" id="searchCount"></small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3">User</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Requested On</th>
                        <th class="py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="requestsTableBody">
                    @forelse($requests as $request)
                    <tr class="request-row" 
                        data-search="{{ strtolower($request->user->firstname . ' ' . $request->user->lastname . ' ' . $request->user->email) }}"
                        data-name="{{ $request->user->firstname . ' ' . $request->user->lastname }}"
                        data-email="{{ $request->user->email }}"
                        data-date="{{ $request->created_at->format('Y-m-d H:i:s') }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2">
                                    {{ strtoupper(substr($request->user->firstname, 0, 1)) }}{{ strtoupper(substr($request->user->lastname, 0, 1)) }}
                                </div>
                                <div class="fw-bold">{{ $request->user->firstname . ' ' . $request->user->lastname }}</div>
                            </div>
                        </td>
                        <td>{{ $request->user->email }}</td>
                        <td>
                            <div>{{ $request->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-success me-1" 
                                data-bs-toggle="tooltip"
                                title="Reset Password"
                                onclick="showResetModal({{ $request->id }}, '{{ $request->user->email }}')">
                                <i class="bi bi-key"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                onclick="if(confirm('Are you sure you want to reject this request?')) document.getElementById('rejectForm{{ $request->id }}').submit();">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <form id="rejectForm{{ $request->id }}" 
                                action="{{ route('admin.password.reject', $request->id) }}" 
                                method="POST" class="d-none">
                                @csrf
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="noRecordsRow">
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox display-6 mb-3 d-block"></i>
                                <h6 class="fw-normal">No pending password reset requests</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Reset Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-person-lock text-primary display-6"></i>
                        <p class="mb-1 small">Reset password for:</p>
                        <strong id="userEmail" class="text-primary"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">New Password</label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="new_password" id="newPassword" 
                                class="form-control" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small">Minimum 8 characters</div>
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <span class="spinner-border spinner-border-sm d-none me-1"></span>
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.search-wrapper {
    position: relative;
}
.search-icon {
    left: 15px;
    z-index: 3;
}
#searchInput {
    padding-left: 40px;
    padding-right: 100px;
    border-radius: 20px;
    border: 1px solid #dee2e6;
    font-size: 0.95rem;
}
#searchInput:focus {
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.15);
    border-color: #86b7fe;
}
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
}
.table > :not(caption) > * > * {
    padding: 1rem;
}
/* Add compact modal styles */
.modal-sm {
    max-width: 300px;
}
.modal-header {
    border-bottom: 1px solid rgba(0,0,0,.1);
}
.modal-footer {
    border-top: 1px solid rgba(0,0,0,.1);
}
</style>

<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltip => {
    new bootstrap.Tooltip(tooltip);
});

const searchInput = document.getElementById('searchInput');
const searchCount = document.getElementById('searchCount');
const rows = document.querySelectorAll('.request-row');
const noRecordsRow = document.getElementById('noRecordsRow');

function filterTable(searchTerm) {
    let visibleCount = 0;
    searchTerm = searchTerm.toLowerCase().trim();

    rows.forEach(row => {
        const name = row.querySelector('.fw-bold').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm)) {
            row.classList.remove('d-none');
            visibleCount++;
        } else {
            row.classList.add('d-none');
        }
    });

    if (searchTerm) {
        searchCount.textContent = `${visibleCount} result${visibleCount !== 1 ? 's' : ''}`;
        searchCount.classList.remove('d-none');
    } else {
        searchCount.classList.add('d-none');
    }

    if (noRecordsRow) {
        if (visibleCount === 0) {
            noRecordsRow.style.display = '';
            noRecordsRow.querySelector('td').innerHTML = `
                <div class="text-muted">
                    <i class="bi bi-search display-6 mb-3 d-block"></i>
                    <h6 class="fw-normal">No matching records found</h6>
                </div>
            `;
        } else {
            noRecordsRow.style.display = 'none';
        }
    }
}

const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

searchInput.addEventListener('input', debounce(e => filterTable(e.target.value), 300));

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('newPassword');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});

function showResetModal(requestId, email) {
    document.getElementById('userEmail').textContent = email;
    document.getElementById('resetPasswordForm').action = `/password-reset/reset/${requestId}`;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            throw new Error('Failed to reset password');
        }
    })
    .catch(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
        modal.hide();
        alert('Failed to reset password. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>
@endsection
