<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom py-2">
                <h5 class="modal-title" id="addMenuModalLabel">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMenuForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-3" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <div id="imagePreview" class="text-center mb-2">
                                <div class="no-image">No image selected</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label small fw-bold mb-1" for="menu_image">Upload Image</label>
                                <input type="file" class="form-control form-control-sm" id="menu_image" name="image" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group mb-2">
                                <label class="form-label small fw-bold mb-1" for="menu_code">Menu Code</label>
                                <input type="text" class="form-control form-control-sm" id="menu_code" name="menu_code" readonly>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group mb-2">
                                <label class="form-label small fw-bold mb-1" for="menu_name">Name</label>
                                <input type="text" class="form-control form-control-sm" id="menu_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-group mb-2">
                                <label class="form-label small fw-bold mb-1" for="menu_price">Price</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="menu_price" name="price" step="0.01" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-6">
                            <label class="form-label small fw-bold mb-1" for="menu_category">Category</label>
                            <select class="form-select form-select-sm" id="menu_category" name="menucategory_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label small fw-bold mb-1" for="menu_subcategory">Subcategory</label>
                            <select class="form-select form-select-sm" id="menu_subcategory" name="menusubcategory_id" disabled>
                                <option value="">Select Subcategory</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1" for="menu_description">Description</label>
                        <textarea class="form-control form-control-sm" id="menu_description" name="description" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 position-sticky bottom-0 bg-white"></div>
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Save Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal-dialog {
    display: flex;
    flex-direction: column;
    height: auto;
}

.modal-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.modal-body {
    flex: 1;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,.2) transparent;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: transparent;
}

.modal-body::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,.2);
    border-radius: 3px;
}

.modal-dialog-scrollable {
    max-height: 90vh;
}

#imagePreview {
    border: 1px dashed #ddd;
    border-radius: 4px;
    padding: 4px;
    background: #f8f9fa;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#imagePreview img {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
}

#imagePreview .no-image {
    color: #999;
    font-size: 0.875rem;
}

.invalid-feedback {
    font-size: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('menu_image');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            imagePreview.innerHTML = '<div class="no-image">No image selected</div>';
            return;
        }

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="img-fluid">
                `;
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = '<div class="no-image text-danger">Please select an image file</div>';
            imageInput.value = '';
        }
    });

    const addMenuModal = document.getElementById('addMenuModal');
    addMenuModal.addEventListener('hidden.bs.modal', function() {
        imagePreview.innerHTML = '<div class="no-image">No image selected</div>';
        imageInput.value = '';
    });

    const categorySelect = document.getElementById('menu_category');
    const subcategorySelect = document.getElementById('menu_subcategory');

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        subcategorySelect.disabled = !categoryId;
        
        if (categoryId) {
            fetch(`/menu/categories/${categoryId}/subcategories`)
                .then(response => response.json())
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                    if (data.subcategories && data.subcategories.length > 0) {
                        data.subcategories.forEach(subcategory => {
                            subcategorySelect.innerHTML += `
                                <option value="${subcategory.id}">${subcategory.name}</option>
                            `;
                        });
                        subcategorySelect.disabled = false;
                    } else {
                        subcategorySelect.innerHTML = '<option value="">No subcategories available</option>';
                        subcategorySelect.disabled = true;
                    }
                })
                .catch(error => {
                    subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                    subcategorySelect.disabled = true;
                });
        } else {
            subcategorySelect.innerHTML = '<option value="">Select Category First</option>';
            subcategorySelect.disabled = true;
        }
    });

    function generateRandomNumber(length) {
        return Math.random().toString().slice(2, 2 + length);
    }

    function generateRandomLetter() {
        return String.fromCharCode(65 + Math.floor(Math.random() * 26));
    }

    function generateMenuCode(namePrefix = 'XXX') {
        return `F-${namePrefix}-${generateRandomNumber(5)}`;
    }

    function getFirst3Letters(name) {
        const letters = name.toUpperCase().replace(/[^A-Z]/g, '');
        let prefix = letters.slice(0, 3);
        while (prefix.length < 3) {
            prefix += generateRandomLetter();
        }
        return prefix;
    }

    const menuCodeInput = document.getElementById('menu_code');
    const menuNameInput = document.getElementById('menu_name');

    addMenuModal.addEventListener('show.bs.modal', function() {
        menuCodeInput.value = generateMenuCode();
    });

    menuNameInput.addEventListener('keyup', function() {
        const name = this.value.trim();
        const prefix = name ? getFirst3Letters(name) : 'XXX';
        menuCodeInput.value = generateMenuCode(prefix);
    });

    addMenuModal.addEventListener('hidden.bs.modal', function() {
        menuNameInput.value = '';
        menuCodeInput.value = generateMenuCode();
    });

    const addMenuForm = document.getElementById('addMenuForm');
    addMenuForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        fetch('/menu/items', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Menu item added successfully');
                
                addMenuForm.reset();
                imagePreview.innerHTML = '<div class="no-image">No image selected</div>';
                
                const modal = bootstrap.Modal.getInstance(addMenuModal);
                modal.hide();
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(data.message || 'Error adding menu item');
            }
        })
        .catch(error => {
            toastr.error('An error occurred while adding the menu item');
        })
        .finally(() => {
            submitButton.disabled = false;
        });
    });
});
</script>
