<div class="modal fade" id="unitSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-sm" style="width: 300px;height: 300px;">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title">Select Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="product-info mb-3">
                    <p class="mb-1"><strong>Product:</strong> <span id="unitModalProductName"></span></p>
                    <p class="mb-0"><small class="text-muted">Code: <span id="unitModalProductCode"></span></small></p>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" id="groupUnitButton">
                        <i class="bi bi-box-seam"></i>
                        Buy as <span id="groupUnitName"></span><br>
                        ₱<span id="groupUnitPrice"></span>
                    </button>
                    <button class="btn btn-outline-primary" id="pieceUnitButton">
                        <i class="bi bi-grid"></i>
                        Buy per Piece<br>
                        ₱<span id="pieceUnitPrice"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
