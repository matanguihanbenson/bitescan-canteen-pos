<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuGroupItem extends Model
{
    protected $table = 'menu_group_items';

    protected $fillable = [
        'menugroup_id',
        'menu_id'
    ];


    public function menuGroup()
    {
        return $this->belongsTo(MenuGroup::class, 'menugroup_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
