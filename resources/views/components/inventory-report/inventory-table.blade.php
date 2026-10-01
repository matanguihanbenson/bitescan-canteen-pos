<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Inventory Status</h6>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" id="generateInventoryReport">
                <i class="bi bi-file-earmark-bar-graph me-1"></i>Generate Report
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                    id="exportDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><h6 class="dropdown-header">Quick Export</h6></li>
                    <li><a class="dropdown-item" href="#" onclick="exportInventory('excel')">📊 Excel Spreadsheet</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportInventory('pdf')">📄 PDF Document</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportInventory('csv')">📋 CSV File</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="openInventoryReportModal()">
                        <i class="bi bi-gear-fill me-1"></i>Advanced Options
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        </div>
    </div>
</div>
