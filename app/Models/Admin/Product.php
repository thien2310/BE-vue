<?php

namespace App\Models\Admin;

use App\Models\Common\File;
use App\Models\orders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [];



    public const STATUSES = [
        [
            'value' => 1,
            'label' => 'Kích hoạt',
            'type' => 'success'
        ],
        [
            'value' => 0,
            'label' => 'Chưa kích hoạt',
            'type' => 'danger'
        ],
    ];

    const STATE = [
        [
            'value' => 1,
            'label' => 'Còn hàng',
            'type' => 'success'
        ],
        [
            'value' => 0,
            'label' => 'Hết hàng',
            'type' => 'danger'
        ],
    ];

    public function image()
    {
        return $this->morphOne(File::class,'model')->where('custom_field','image');
    }

    public function getProductGiayNu()
    {
        return Product::with('image')->where('cate_id', '=', '2')->where('status', '=', '1')->take(4)->get();
    }

    public function images()
    {
        return $this->morphMany(File::class,'model')->where('custom_field','image');
    }

    public function orders() {
        return $this->belongsToMany(orders::class);
    }

}
