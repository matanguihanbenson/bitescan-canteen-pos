<div>
    <div class="fw-bold">{{ optional($row->category)->name ?? 'Uncategorized' }}</div>
    @if($row->category && $row->subcategory)
        <small class="text-muted">
            <i class="bi bi-chevron-right"></i> {{ $row->subcategory->name }}
        </small>
    @endif
</div>
