<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogFramework extends Model
{
    protected $fillable = [
        'name',
    ];

    public function fields()
    {
        return $this->hasMany(CatalogFrameworkField::class, 'framework_id');
    }
}

