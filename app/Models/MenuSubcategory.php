<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'menucategory_id',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menucategory_id');
    }
}
