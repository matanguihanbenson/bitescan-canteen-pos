<div class="category-info">
    <span class="badge bg-primary fw-light">{{ $row->category->name ?? 'Uncategorized' }}</span>
    <div class="subcategories mt-1">
        @if($row->product_subcategories && $row->product_subcategories->count() > 0)
            @foreach($row->product_subcategories->take(3) as $product_subcategory)
                <span class="badge bg-secondary fw-light">{{ $product_subcategory->subcategory->name }}</span>
            @endforeach
            @if($row->product_subcategories->count() > 3)
                <span class="badge bg-secondary fw-light">
                    +{{ $row->product_subcategories->count() - 3 }}
                </span>
            @endif
        @else
            <span class="badge bg-secondary fw-light">No Subcategories</span>
        @endif
    </div>
</div>
