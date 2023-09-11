<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Product;

class Size extends Model
{
    use HasFactory;
    protected $table = 'Sizes';
    protected $fillable = [];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'sizeable');
    }

}
