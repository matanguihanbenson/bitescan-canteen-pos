<script>
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};

if (typeof toastr === 'undefined') {
    }
window.toastr = toastr;
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};
document.addEventListener('DOMContentLoaded', function() {
    
    const searchInput = document.getElementById('menuSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const items = document.querySelectorAll('#menuItemsList .list-group-item:not(.list-group-item-secondary)');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});

const bestsellersItems = [
    { name: 'Crispy Fried Chicken', price: '₱199.00', category: 'Main Dishes', image: 'chicken.jpg' },
    { name: 'Shanghai Lumpia', price: '₱150.00', category: 'Appetizers', image: 'lumpia.jpg' },
    { name: 'Pinoy Spaghetti', price: '₱180.00', category: 'Main Dishes', image: 'spaghetti.jpg' }
];


let menuGroups = [];

document.addEventListener('DOMContentLoaded', function() {
    const serverData = @json($menuGroups ?? []);
    
    window.menuGroupsData = serverData;
    menuGroups = serverData;

    renderMenuGroups(menuGroups);

});

const archivedMenuGroups = @json($archivedMenuGroups ?? []);

function addMenuGroup() {
    const modal = new bootstrap.Modal(document.getElementById('newMenuGroupModal'));
    modal.show();
}

function loadMenu() {
    alert('Load menu functionality to be implemented.');
}

function showMenuGroupDetails(groupName, items) {
    try {
        const itemsArray = Array.isArray(items) ? items : (typeof items === 'string' ? JSON.parse(items) : items);
        
        const modal = document.getElementById('menuGroupDetailsModal');
        const modalTitle = modal.querySelector('.modal-title');
        const itemsList = modal.querySelector('#menuGroupDetailsList');
        const searchInput = modal.querySelector('#menuItemSearchInput');
        
        modalTitle.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-collection fs-4 me-2"></i>
                <div>
                    <h5 class="mb-0">${groupName}</h5>
                    <div class="text-muted small">${itemsArray.length} items in this group</div>
                </div>
            </div>
        `;
        
        const renderItems = (items) => {
            const itemsHtml = items.map(item => `
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="${item.image}" class="rounded-3" alt="${item.name}" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 fw-bold">${item.name}</h6>
                                        <span class="badge ${item.isActive ? 'bg-success' : 'bg-danger'} rounded-pill">
                                            ${item.isActive ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold">${item.price}</span>
                                        <span class="badge bg-light text-dark border">
                                            ${item.category}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            itemsList.innerHTML = itemsHtml;
        };

        renderItems(itemsArray);

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredItems = itemsArray.filter(item => 
                item.name.toLowerCase().includes(searchTerm) ||
                item.category.toLowerCase().includes(searchTerm) ||
                item.price.toLowerCase().includes(searchTerm)
            );
            renderItems(filteredItems);
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            searchInput.value = '';
            renderItems(itemsArray);
        }, { once: true });

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } catch (error) {
        toastr.error('Error showing menu items');
    }
}

function editMenuGroup(groupName, items) {
    document.getElementById('editMenuGroupModalLabel').innerText = groupName;
    const editMenuGroupList = document.getElementById('editMenuGroupList');
    editMenuGroupList.innerHTML = '';
    items.forEach(item => {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.innerHTML = `
            ${item}
            <button class="btn btn-danger btn-sm" onclick="removeMenuItem(this)">Remove</button>
        `;
        editMenuGroupList.appendChild(listItem);
    });
    const editMenuGroupModal = new bootstrap.Modal(document.getElementById('editMenuGroupModal'));
    editMenuGroupModal.show();
}

function addMenuItem() {
    alert('Add menu item functionality to be implemented.');
}

function removeMenuItem(button) {
    button.closest('li').remove();
}

function deleteMenuGroup() {
    alert('Delete menu group functionality to be implemented.');
}

function renderMenuItems(items) {
    const grid = document.querySelector('.menu-items-grid');
    grid.innerHTML = items.map(item => `
        <div class="menu-item">
            <img src="/images/menu.png" alt="${item.name}" class="menu-item-image">
            <div class="menu-item-details">
                <h6 class="mb-1">${item.name}</h6>
                <p class="mb-0">${item.price}</p>
            </div>
        </div>
    `).join('');
}

function exportMenuData() {
    alert('Exporting menu data...');
}

function renderMenuItems(items) {
    const grid = document.querySelector('.menu-items-grid');
    grid.innerHTML = '';

    items.forEach(item => {
        const template = document.getElementById('menuItemTemplate');
        const clone = template.content.cloneNode(true);

        clone.querySelector('img').src = `{{asset('images/menu.png}')}}`;
        clone.querySelector('img').alt = item.name;
        clone.querySelector('h6').textContent = item.name;
        clone.querySelector('.price').textContent = item.price;
        clone.querySelector('.status').textContent =
            `${item.isActive ? 'Active' : 'Deactivated'}`;

        const stockPercentage = (item.currentStock / item.maxStock) * 100;
        clone.querySelector('.progress-bar').style.width = `${stockPercentage}%`;

        grid.appendChild(clone);
    });
}

function renderMenuGroups(groups) {
    const container = document.getElementById('menuGroupsContainer');
    if (groups.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="text-center py-5">
                    <img src="/images/no-menu.png" alt="No menu groups" class="mb-4" style="width: 100px; opacity: 0.5;">
                    <h4 class="text-muted">No Menu Groups Found</h4>
                    <p class="text-muted">Create your first menu group to get started</p>
                    <button class="btn btn-primary" onclick="addMenuGroup()">
                        <i class="bi bi-plus-circle me-2"></i>Create Menu Group
                    </button>
                </div>
            </div>
        `;
        return;
    }

    const html = groups.map(group => {
        const isDisplayed = group.is_displayed === true || group.is_displayed === 'true';
        
        return `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold">${group.name}</h6>
                            <small class="text-muted">${group.itemCount} items</small>
                            <span class="badge bg-info ms-2">${group.category}</span>
                        </div>
                           <button class="btn btn-primary btn-sm rounded" style="width: 32px; height: 32px;" 
                                        onclick='viewMenuGroup(${JSON.stringify(group).replace(/'/g, "\\'")})'
                                        type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                    </div>
                    <div class="card-body">
                        <div class="menu-items-grid">
                            ${renderMenuItemsHtml(group.items)}
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-warning text-white p-0" style="width:30px;height:30px;" onclick="toggleArchiveGroup(${group.id})">
                                <i class="bi bi-archive"></i>
                            </button>
                            <div class="d-flex gap-2"> <!-- Add gap-2 class for consistent spacing -->
                                ${isDisplayed ? `
                                    <button class="btn btn-outline-danger btn-sm d-inline-flex align-items-center" onclick="unloadMenuGroup(this, ${group.id})">
                                        <i class="bi bi-box-arrow-left me-1"></i>
                                        Unload
                                    </button>
                                ` : ''}
                                <button class="btn btn-${isDisplayed ? 'success' : 'primary'} btn-sm load-menu-btn d-inline-flex align-items-center" 
                                        onclick="loadMenuGroup(this, ${group.id})"
                                        ${isDisplayed ? 'disabled' : ''}>
                                    <i class="bi bi-${isDisplayed ? 'check2-circle' : 'arrow-right-circle'} me-1"></i>
                                    ${isDisplayed ? 'Loaded' : 'Load Menu'}
                                </button>
                             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = html;
}

function renderMenuItemsHtml(items) {
    const visibleItems = items.slice(0, 2);
    const remainingCount = items.length - 2;

    let html = visibleItems.map(item => `
        <div class="menu-item">
            <div class="menu-item-badge">
                <span class="badge ${item.isActive ? 'bg-success' : 'bg-danger'}">
                    ${item.isActive ? 'Active' : 'Deactivated'}
                </span>
            </div>
            <div class="position-relative">
                <img src="/images/menu.png" style="opacity:.5;" alt="${item.name}" class="menu-item-image">
                <div class="overlay position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
            </div>
            <div class="menu-item-details">
                <h6 class="mb-1">${item.name}</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">${item.price}</p>
                </div>
            </div>
        </div>
    `).join('');

    if (remainingCount > 0) {
        html += `
            <button class="btn btn-link btn-sm p-0 text-decoration-none mt-2" onclick='showMenuGroupDetails("Menu Items", ${JSON.stringify(items)})'>
                <i class="bi bi-grid-3x3-gap me-1"></i>
                View ${remainingCount} more item${remainingCount > 1 ? 's' : ''}
            </button>
        `;
    }

    return html;
}

document.addEventListener('DOMContentLoaded', function() {
    renderMenuGroups(menuGroups);

    const searchInput = document.getElementById('searchMenu');
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filterMenuGroups(searchTerm, getCurrentCategory());
    });

    const categoryLinks = document.querySelectorAll('#categoryFilter a');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            filterMenuGroups(searchInput.value.toLowerCase(), this.dataset.category);
        });
    });

    const sortSelect = document.getElementById('sortBy');
    sortSelect.addEventListener('change', function() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = getCurrentCategory();
        filterMenuGroups(searchTerm, category, this.value);
    });

    document.getElementById('archived-tab').addEventListener('shown.bs.tab', function (e) {
        renderArchivedGroups();
    });
});

function getCurrentCategory() {
    return document.querySelector('#categoryFilter a.active').dataset.category;
}

function filterMenuGroups(searchTerm, category, sortBy = '') {
    let filtered = window.menuGroupsData;

    if (category && category !== 'all') {
        if (category === 'loaded') {
            filtered = filtered.filter(group => 
                group.is_displayed === true || group.is_displayed === 'true'
            );
        } else {
            filtered = filtered.filter(group => 
                group.category.toLowerCase() === category.toLowerCase()
            );
        }
    }

    if (searchTerm) {
        filtered = filtered.filter(group =>
            group.name.toLowerCase().includes(searchTerm) ||
            group.items.some(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                item.price.includes(searchTerm)
            )
        );
    }

    if (sortBy) {
        filtered.sort((a, b) => {
            switch(sortBy) {
                case 'name':
                    return a.name.localeCompare(b.name);
                case 'category':
                    return a.category.localeCompare(b.category);
                case 'price':
                    return a.items.reduce((acc, item) => acc + parseFloat(item.price), 0) -
                           b.items.reduce((acc, item) => acc + parseFloat(item.price), 0);
                default:
                    return 0;
            }
        });
    }

    renderMenuGroups(filtered);
}

function toggleArchiveGroup(groupId) {
    const group = menuGroups.find(g => g.id === groupId);
    if (group && (group.is_displayed === true || group.is_displayed === 'true')) {
        toastr.warning('Cannot archive a loaded menu group. Please unload it first.');
        return;
    }

    document.getElementById('archiveGroupId').value = groupId;
    document.getElementById('archiveReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
    modal.show();
}

function confirmArchive() {
    const groupId = document.getElementById('archiveGroupId').value;
    const reason = document.getElementById('archiveReason').value.trim();
    
    if (!reason) {
        toastr.warning('Please provide a reason for archiving');
        return;
    }

    const archiveBtn = document.querySelector('#archiveModal .btn-danger');
    const originalContent = archiveBtn.innerHTML;
    archiveBtn.disabled = true;
    archiveBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Archiving...
    `;

    fetch(`/menu/groups/${groupId}/archive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Menu group archived successfully');
            bootstrap.Modal.getInstance(document.getElementById('archiveModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to archive menu group');
        }
    })
    .catch(error => {
        archiveBtn.disabled = false;
        archiveBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while archiving the menu group');
    });
}

function viewArchivedGroups() {
    alert('Viewing archived groups...');
}

function saveMenuGroup() {
    toastr.clear();

    const form = document.getElementById('newMenuGroupForm');
    
    const groupName = document.getElementById('groupName').value.trim();
    if (!groupName) {
        toastr.error('Please enter a group name');
        return;
    }

    const categoryId = document.getElementById('groupCategory').value;
    if (!categoryId) {
        toastr.error('Please select a category');
        return;
    }

    const selectedItems = Array.from(document.querySelectorAll('#selectedItemsList li'))
        .map(li => li.dataset.itemId)
        .filter(id => id);

    if (selectedItems.length === 0) {
        toastr.warning('Please select at least one menu item');
        return;
    }

    toastr.info(
        '<i class="bi bi-arrow-repeat spinner me-2"></i>Creating menu group...', 
        'Please wait'
    );

    const requestData = {
        name: groupName,
        category_id: categoryId,
        description: document.getElementById('groupDescription').value.trim() || '',
        menu_items: selectedItems
    };

    fetch('/menu/groups', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        toastr.clear();
        
        if (data.success) {
            toastr.success('Menu group created successfully!', 'Success');
            
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('newMenuGroupModal'));
                modal.hide();
                location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to create menu group');
        }
    })
    .catch(error => {
        toastr.clear();
        toastr.error(error.message || 'An error occurred while creating the menu group');
    });
}

function incrementOrder() {
    const input = document.getElementById('displayOrder');
    input.value = parseInt(input.value) + 1;
}

function decrementOrder() {
    const input = document.getElementById('displayOrder');
    if (parseInt(input.value) > 0) {
        input.value = parseInt(input.value) - 1;
    }
}

const availableMenuItems = [];
function searchMenuItems() {
    const searchTerm = document.getElementById('menuItemSearch').value.toLowerCase();
    const searchResults = document.getElementById('searchResults');
    const addButton = document.getElementById('addToGroupBtn');

    const filteredItems = availableMenuItems.filter(item =>
        item.name.toLowerCase().includes(searchTerm) ||
        item.category.toLowerCase().includes(searchTerm)
    );

    searchResults.innerHTML = filteredItems.map(item => `
        <option value="${item.id}">${item.name} - ${item.price}</option>
    `).join('');

    if (filteredItems.length > 0) {
        searchResults.style.display = 'block';
        addButton.style.display = 'block';
    } else {
        searchResults.style.display = 'none';
        addButton.style.display = 'none';
    }
}

function addSelectedItemsToGroup() {
    const selectedOptions = $('#menuItemSearch').find("option:selected");
    const selectedItemsList = document.getElementById('selectedItemsList');

    selectedOptions.each(function() {
        const option = $(this);
        const itemId = option.val();
        if (!selectedItemsList.querySelector(`[data-item-id="${itemId}"]`)) {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            listItem.dataset.itemId = itemId;
            
            const textContent = option.text().split(' - ');
            listItem.innerHTML = `
                <div>
                    <span class="fw-bold">${textContent[0]}</span>
                    <span class="text-muted">${textContent[1]}</span>
                    <small class="badge bg-secondary ms-2">${option.closest('optgroup').attr('label')}</small>
                </div>
                <button class="btn btn-danger btn-sm" onclick="removeSelectedItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            selectedItemsList.appendChild(listItem);
        }
    });

    $('.selectpicker').selectpicker('deselectAll');
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('newMenuGroupModal');
    modal.addEventListener('shown.bs.modal', function() {
        document.getElementById('menuItemSearch').selectedIndex = -1;
    });

    modal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('menuItemSearch').selectedIndex = -1;
        document.getElementById('selectedItemsList').innerHTML = '';
    });
});

function removeSelectedItem(button) {
    button.closest('li').remove();
}

document.addEventListener('DOMContentLoaded', function() {
    const newMenuGroupModal = document.getElementById('newMenuGroupModal');
    const confirmationModal = document.getElementById('confirmationModal');
    let isIntentionalClose = false;

    newMenuGroupModal.addEventListener('show.bs.modal', function() {
        document.getElementById('groupName').value = 'Untitled Group';
    });

    document.getElementById('confirmNo').addEventListener('click', function() {
        const confirmModal = bootstrap.Modal.getInstance(confirmationModal);
        confirmModal.hide();
    });

    document.getElementById('confirmYes').addEventListener('click', function() {
        const confirmModal = bootstrap.Modal.getInstance(confirmationModal);
        const menuModal = bootstrap.Modal.getInstance(newMenuGroupModal);
        isIntentionalClose = true;
        confirmModal.hide();
        menuModal.hide();
        document.getElementById('newMenuGroupForm').reset();
        document.getElementById('selectedItemsList').innerHTML = '';
        isIntentionalClose = false;
    });

});

function renderArchivedGroups() {
    const container = document.getElementById('archivedGroupsContainer');

    if (!archivedMenuGroups || archivedMenuGroups.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <img src="{{ asset('images/empty-archive.png') }}" alt="No archived items" class="mb-4" style="width: 200px; opacity: 0.5;">
                <h4 class="text-muted">No Archived Menu Groups</h4>
                <p class="text-muted">When you archive menu groups, they will appear here</p>
            </div>
        `;
        return;
    }

    const html = archivedMenuGroups.map(group => `
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="card-title mb-1">${group.name}</h5>
                        <span class="badge bg-secondary">${group.category}</span>
                        <span class="badge bg-danger">Archived</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Archived on: ${group.archivedDate}</small>
                        <small class="text-muted d-block">By: ${group.archivedBy}</small>
                        <small class="text-muted d-block">Reason: ${group.archiveReason}</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary btn-sm me-2" onclick="restoreMenuGroup(${group.id})">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteArchivedGroup(${group.id})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="collapse" id="archivedItems${group.id}">
                    <div class="mt-3">
                        <h6 class="mb-2">Archived Items (${group.itemCount})</h6>
                        <div class="row g-2">
                            ${group.items.map(item => `
                                <div class="col-md-6">
                                    <div class="p-2 border rounded">
                                        <div class="d-flex align-items-center">
                                            <img src="/images/menu.png" alt="${item.name}" class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">${item.name}</h6>
                                                <small class="text-muted">${item.price}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
                <button class="btn btn-link btn-sm text-muted mt-2 toggle-items-btn"
                        data-bs-toggle="collapse"
                        data-bs-target="#archivedItems${group.id}"
                        aria-expanded="false">
                    <i class="bi bi-chevron-down me-1"></i>
                    <span>Show Items</span>
                </button>
            </div>
        </div>
    `).join('');

    container.innerHTML = html;

    container.querySelectorAll('.toggle-items-btn').forEach(button => {
        const targetId = button.getAttribute('data-bs-target');
        const collapseElement = document.querySelector(targetId);

        collapseElement.addEventListener('show.bs.collapse', () => {
            button.innerHTML = '<i class="bi bi-chevron-up me-1"></i><span>Hide Items</span>';
        });

        collapseElement.addEventListener('hide.bs.collapse', () => {
            button.innerHTML = '<i class="bi bi-chevron-down me-1"></i><span>Show Items</span>';
        });
    });
}

function restoreMenuGroup(groupId) {
    document.getElementById('restoreGroupId').value = groupId;
    
    const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

function confirmRestore() {
    const groupId = document.getElementById('restoreGroupId').value;
    
    const restoreBtn = document.querySelector('#restoreModal .btn-primary');
    const originalContent = restoreBtn.innerHTML;
    restoreBtn.disabled = true;
    restoreBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Restoring...
    `;

    toastr.info('Restoring menu group...', 'Please wait');

    fetch(`/menu/groups/${groupId}/restore`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Menu group restored successfully');
            const index = archivedMenuGroups.findIndex(g => g.id === groupId);
            if (index !== -1) {
                archivedMenuGroups.splice(index, 1);
            }
            bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
            renderArchivedGroups();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to restore menu group');
        }
    })
    .catch(error => {
        restoreBtn.disabled = false;
        restoreBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while restoring the menu group');
    });
}

function deleteArchivedGroup(groupId) {
    document.getElementById('deleteGroupId').value = groupId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function confirmDelete() {
    const groupId = document.getElementById('deleteGroupId').value;
    
    const deleteBtn = document.querySelector('#deleteModal .btn-danger');
    const originalContent = deleteBtn.innerHTML;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Deleting...
    `;

    toastr.info('Deleting menu group...', 'Please wait');

    fetch(`/menu/groups/${groupId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const index = archivedMenuGroups.findIndex(g => g.id === parseInt(groupId));
            if (index !== -1) {
                archivedMenuGroups.splice(index, 1);
            }
            
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            setTimeout(() => {
                toastr.success('Menu group deleted successfully');
                renderArchivedGroups();
            }, 300);
        } else {
            throw new Error(data.message || 'Failed to delete menu group');
        }
    })
    .catch(error => {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while deleting the menu group');
    });
}

function openMenuItemsModal() {
    const newMenuGroupModal = bootstrap.Modal.getInstance(document.getElementById('newMenuGroupModal'));
    const menuItemsModal = new bootstrap.Modal(document.getElementById('menuItemsModal'));
    
    newMenuGroupModal.hide();
    
    menuItemsModal.show();

    document.getElementById('menuItemsModal').addEventListener('hidden.bs.modal', function () {
        newMenuGroupModal.show();
    }, { once: true });
}

function addSelectedMenuItems() {
    const selectedItems = document.querySelectorAll('#menuItemsList input[type="checkbox"]:checked');
    const selectedItemsList = document.getElementById('selectedItemsList');
    
    selectedItems.forEach(checkbox => {
        const listItem = checkbox.closest('.list-group-item');
        const itemName = checkbox.nextElementSibling.textContent.trim();
        const itemPrice = checkbox.nextElementSibling.nextElementSibling.textContent.trim();
        const category = listItem.previousElementSibling?.textContent.trim() || 'Uncategorized';
        
        if (!selectedItemsList.querySelector(`[data-item-id="${checkbox.value}"]`)) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.dataset.itemId = checkbox.value;
            
            li.innerHTML = `
                <div>
                    <span class="fw-bold">${itemName}</span>
                    <span class="text-muted">${itemPrice}</span>
                </div>
                <button class="btn btn-danger btn-sm" onclick="removeSelectedItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            selectedItemsList.appendChild(li);
        }
    });

    const menuItemsModal = bootstrap.Modal.getInstance(document.getElementById('menuItemsModal'));
    menuItemsModal.hide();
}

function addSelectedMenuItems() {
    const selectedItems = document.querySelectorAll('#menuItemsList input[type="checkbox"]:checked');
    
    if (selectedItems.length === 0) {
        toastr.warning('Please select at least one menu item', 'Warning');
        return;
    }

    if (selectedItems.length > 10) {
        toastr.warning('You can only select up to 10 items per group', 'Warning');
        return;
    }

    selectedItems.forEach(checkbox => {
        const listItem = checkbox.closest('.list-group-item');
        const itemName = checkbox.nextElementSibling.textContent.trim();
        const itemPrice = checkbox.nextElementSibling.nextElementSibling.textContent.trim();
        const category = listItem.previousElementSibling?.textContent.trim() || 'Uncategorized';
        
        if (!selectedItemsList.querySelector(`[data-item-id="${checkbox.value}"]`)) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.dataset.itemId = checkbox.value;
            
            li.innerHTML = `
                <div>
                    <span class="fw-bold">${itemName}</span>
                    <span class="text-muted">${itemPrice}</span>
                </div>
                <button class="btn btn-danger btn-sm" onclick="removeSelectedItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            selectedItemsList.appendChild(li);
        }
    });

    toastr.success(`Added ${selectedItems.length} item(s) to the group`, 'Success');
    const menuItemsModal = bootstrap.Modal.getInstance(document.getElementById('menuItemsModal'));
    menuItemsModal.hide();
}

function loadMenuGroup(button, groupId) {
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <span class="d-flex align-items-center">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Loading...
        </span>
    `;

    fetch(`/menu/groups/${groupId}/display`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const groupIndex = menuGroups.findIndex(g => g.id === groupId);
            if (groupIndex !== -1) {
                menuGroups[groupIndex].is_displayed = 'true';
                renderMenuGroups(menuGroups);
            }
            
            toastr.success('Menu group has been loaded successfully');
        } else {
            throw new Error(data.message || 'Failed to load menu group');
        }
    })
    .catch(error => {
        button.disabled = false;
        button.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while loading the menu group');
    });
}

function unloadMenuGroup(button, groupId) {
    const buttonContainer = button.closest('.d-flex');
    const buttons = buttonContainer.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);

    const originalWidth = button.offsetWidth;
    button.style.width = `${originalWidth}px`;

    const originalContent = button.innerHTML;
    button.innerHTML = `
        <div class="d-flex align-items-center justify-content-center">
            <div class="spinner-border spinner-border-sm" style="width: 1rem; height: 1rem;"></div>
            <span class="ms-2">Unloading</span>
        </div>
    `;

    fetch(`/menu/groups/${groupId}/undisplay`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const groupIndex = menuGroups.findIndex(g => g.id === groupId);
            if (groupIndex !== -1) {
                menuGroups[groupIndex].is_displayed = false;
                renderMenuGroups(menuGroups);
            }
            toastr.success('Menu group has been unloaded successfully');
        } else {
            throw new Error(data.message || 'Failed to unload menu group');
        }
    })
    .catch(error => {
        buttons.forEach(btn => btn.disabled = false);
        button.style.width = '';
        button.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while unloading the menu group');
    });
}

function restoreMenuGroup(groupId) {
    document.getElementById('restoreGroupId').value = groupId;
    
    const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

function confirmRestore() {
    const groupId = document.getElementById('restoreGroupId').value;
    
    const restoreBtn = document.querySelector('#restoreModal .btn-primary');
    const originalContent = restoreBtn.innerHTML;
    restoreBtn.disabled = true;
    restoreBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Restoring...
    `;

    toastr.info('Restoring menu group...', 'Please wait');

    fetch(`/menu/groups/${groupId}/restore`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Menu group restored successfully');
            const index = archivedMenuGroups.findIndex(g => g.id === groupId);
            if (index !== -1) {
                archivedMenuGroups.splice(index, 1);
            }
            bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
            renderArchivedGroups();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to restore menu group');
        }
    })
    .catch(error => {
        restoreBtn.disabled = false;
        restoreBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while restoring the menu group');
    });
}

function deleteArchivedGroup(groupId) {
    document.getElementById('deleteGroupId').value = groupId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function confirmDelete() {
    const groupId = document.getElementById('deleteGroupId').value;
    
    const deleteBtn = document.querySelector('#deleteModal .btn-danger');
    const originalContent = deleteBtn.innerHTML;
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Deleting...
    `;

    toastr.info('Deleting menu group...', 'Please wait');

    fetch(`/menu/groups/${groupId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const index = archivedMenuGroups.findIndex(g => g.id === parseInt(groupId));
            if (index !== -1) {
                archivedMenuGroups.splice(index, 1);
            }
            
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            setTimeout(() => {
                toastr.success('Menu group deleted successfully');
                renderArchivedGroups();
            }, 300);
        } else {
            throw new Error(data.message || 'Failed to delete menu group');
        }
    })
    .catch(error => {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while deleting the menu group');
    });
}

let viewingGroup = null;

function viewMenuGroup(group) {
    viewingGroup = group;
    
    document.getElementById('viewGroupName').textContent = group.name;
    document.getElementById('viewGroupCategory').textContent = group.category;
    document.getElementById('lastUpdated').textContent = `Last updated: ${group.lastUpdated}`;
    
    const activeItems = group.items.filter(item => item.isActive).length;
    document.getElementById('viewTotalItems').textContent = group.items.length;
    document.getElementById('viewActiveItems').textContent = activeItems;
    document.getElementById('viewInactiveItems').textContent = group.items.length - activeItems;
    
    renderViewItems(group.items);
    
    const modal = new bootstrap.Modal(document.getElementById('viewMenuGroupModal'));
    modal.show();
    
    const searchInput = document.getElementById('viewItemSearch');
    searchInput.value = '';
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredItems = group.items.filter(item => 
            item.name.toLowerCase().includes(searchTerm) ||
            item.category.toLowerCase().includes(searchTerm) ||
            item.price.toLowerCase().includes(searchTerm)
        );
        renderViewItems(filteredItems);
    });
}

function renderViewItems(items) {
    const grid = document.getElementById('viewItemsGrid');
    grid.innerHTML = items.map(item => `
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img src="/images/menu.png" class="rounded-3" 
                                 alt="${item.name}" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0">${item.name}</h6>
                                <span class="badge ${item.isActive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">
                                    ${item.isActive ? 'Active' : 'Inactive'}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary">${item.category}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-primary fw-bold fs-5">₱${parseFloat(item.price).toFixed(2)}</div>
                                <button class="btn btn-sm btn-light" title="More options">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

let isEditMode = false;
let deletedMenuItems = [];
let originalGroupName = '';

function toggleEditMode() {
    isEditMode = !isEditMode;
    const viewGroupName = document.getElementById('viewGroupName');
    const editGroupName = document.getElementById('editGroupName');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const saveChangesBtn = document.getElementById('saveChangesBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    if (isEditMode) {
        originalGroupName = viewGroupName.textContent;
        viewGroupName.classList.add('d-none');
        editGroupName.value = originalGroupName;
        editGroupName.classList.remove('d-none');
        toggleEditBtn.classList.add('d-none');
        saveChangesBtn.classList.remove('d-none');
        cancelEditBtn.classList.remove('d-none');
        document.getElementById('addItemsBtn').classList.remove('d-none');
        
        document.querySelectorAll('.delete-item-btn').forEach(btn => {
            btn.classList.remove('d-none');
        });
    } else {
        exitEditMode();
    }
}

function cancelEditMode() {
    isEditMode = false;
    exitEditMode(true);
}

function exitEditMode(isCancelled = false) {
    const viewGroupName = document.getElementById('viewGroupName');
    const editGroupName = document.getElementById('editGroupName');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const saveChangesBtn = document.getElementById('saveChangesBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    if (isCancelled) {
        viewGroupName.textContent = originalGroupName;
        deletedMenuItems = [];
        newlyAddedItems = [];
        renderViewItems(viewingGroup.items);
    }

    viewGroupName.classList.remove('d-none');
    editGroupName.classList.add('d-none');
    toggleEditBtn.classList.remove('d-none');
    saveChangesBtn.classList.add('d-none');
    cancelEditBtn.classList.add('d-none');
    document.getElementById('addItemsBtn').classList.add('d-none');

    document.querySelectorAll('.delete-item-btn').forEach(btn => {
        btn.classList.add('d-none');
    });
}

function deleteMenuItem(itemId, element) {
    deletedMenuItems.push(itemId);
    
    element.closest('.col-md-6').remove();
    
    const totalItems = document.getElementById('viewTotalItems');
    totalItems.textContent = parseInt(totalItems.textContent) - 1;
    
    const item = viewingGroup.items.find(i => i.id === itemId);
    if(item && item.isActive) {
        const activeItems = document.getElementById('viewActiveItems');
        activeItems.textContent = parseInt(activeItems.textContent) - 1;
    } else {
        const inactiveItems = document.getElementById('viewInactiveItems');
        inactiveItems.textContent = parseInt(inactiveItems.textContent) - 1;
    }
    
    toastr.success('Item removed from group');
}

function saveMenuChanges() {
    const newGroupName = document.getElementById('editGroupName').value.trim();
    if (!newGroupName) {
        toastr.error('Group name cannot be empty');
        return;
    }

    const saveBtn = document.getElementById('saveChangesBtn');
    const originalContent = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Saving...`;

    const updateData = {
        name: newGroupName,
        deletedItems: deletedMenuItems,
        addedItems: newlyAddedItems
    };

    fetch(`/menu/groups/${viewingGroup.id}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(updateData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            viewingGroup.name = newGroupName;
            viewingGroup.items = viewingGroup.items.filter(item => 
                !deletedMenuItems.includes(item.id)
            );
            
            document.getElementById('viewGroupName').textContent = newGroupName;
            
            deletedMenuItems = [];
            newlyAddedItems = [];
            isEditMode = false;
            exitEditMode();
            
            toastr.success('Menu group updated successfully');
            
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Failed to update menu group');
        }
    })
    .catch(error => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalContent;
        toastr.error(error.message || 'An error occurred while updating the menu group');
    });
}

function renderViewItems(items) {
    const grid = document.getElementById('viewItemsGrid');
    grid.innerHTML = items.map(item => `
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img src="/images/menu.png" class="rounded-3" 
                                 alt="${item.name}" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0">${item.name}</h6>
                                <button class="btn btn-danger btn-sm delete-item-btn d-none" 
                                        onclick="deleteMenuItem(${item.id}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="mb-2">
                                <span class="badge ${item.isActive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">
                                    ${item.isActive ? 'Active' : 'Inactive'}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary">${item.category}</span>
                            </div>
                            <div class="text-primary fw-bold fs-5">₱${parseFloat(item.price).toFixed(2)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

let newlyAddedItems = [];

function openAddItemsModal() {
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewMenuGroupModal'));
    viewModal.hide();
    
    fetch(`/menu/available-items/${viewingGroup.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateAddItemsList(data.items);
                const addItemsModal = new bootstrap.Modal(document.getElementById('addItemsModal'));
                addItemsModal.show();
                
                document.getElementById('addItemsModal').addEventListener('hidden.bs.modal', function () {
                    viewModal.show();
                }, { once: true });
            }
        })
        .catch(error => {
            toastr.error('Failed to load available items');
            viewModal.show();
        });
}

function populateAddItemsList(items) {
    const container = document.getElementById('addItemsList');
    container.innerHTML = items.map(item => `
        <label class="list-group-item">
            <input class="form-check-input me-2" type="checkbox" value="${item.id}">
            <span class="fw-medium">${item.name}</span>
            <span class="text-muted ms-2">₱${parseFloat(item.price).toFixed(2)}</span>
            <span class="badge bg-secondary ms-2">${item.category_name}</span>
        </label>
    `).join('');
}

function addSelectedItems() {
    const selectedItems = Array.from(document.querySelectorAll('#addItemsList input:checked'))
        .map(input => parseInt(input.value));
    
    if (selectedItems.length === 0) {
        toastr.warning('Please select items to add');
        return;
    }
    
    newlyAddedItems = newlyAddedItems.concat(selectedItems);
    
    bootstrap.Modal.getInstance(document.getElementById('addItemsModal')).hide();
    
    fetch(`/menu/items-details`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ items: selectedItems })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            viewingGroup.items = viewingGroup.items.concat(data.items);
            renderViewItems(viewingGroup.items);
            updateStats();
            toastr.success(`Added ${selectedItems.length} item(s) to the group`);
        }
    })
    .catch(error => toastr.error('Failed to add items'));
}

function updateStats() {
    const activeItems = viewingGroup.items.filter(item => item.isActive).length;
    document.getElementById('viewTotalItems').textContent = viewingGroup.items.length;
    document.getElementById('viewActiveItems').textContent = activeItems;
    document.getElementById('viewInactiveItems').textContent = viewingGroup.items.length - activeItems;
}
</script>

