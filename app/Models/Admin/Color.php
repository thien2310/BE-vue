<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Product;

class Color extends Model
{
    use HasFactory;
    protected $table = 'Colors';
    protected $fillable = [];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'colorable');
    }

}
