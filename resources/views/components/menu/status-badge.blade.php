<span class="badge bg-{{ $row->status === 'active' ? 'success' : 'secondary' }}">
    {{ ucfirst($row->status) }}
</span>
