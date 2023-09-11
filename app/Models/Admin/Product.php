<?php

namespace App\Models\Admin;

use App\Models\Common\File;
use App\Models\orders;
use App\Models\Admin\Manufacturer;
use App\Models\Admin\Origin;
use App\Models\Train\hasColor;
use App\Models\Train\hasSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    use hasColor;
    use hasSize;
    protected $table = 'products';
    protected $fillable = ['name'];



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
        return $this->morphOne(File::class, 'model')->where('custom_field', 'avatarProduct');
    }

    public function getProduct()
    {
        return DB::table('products')->join('manufacturers', 'products.manufacturer_id', '=', 'manufacturers.id')
            ->join('origins', 'products.origin_id', '=', 'origins.id')
            ->select('products.*', 'manufacturers.name as manufacturers', 'origins.name as origins')->get();
    }



    public function getProductGiayNu()
    {
        return Product::with('image')->where('cate_id', '=', '2')->where('status', '=', '1')->orderBy('views', 'desc')->take(4)->get();
    }

    public function getProductGiayNam()
    {
        return Product::with('image')->where('cate_id', '=', '1')->where('status', '=', '1')->orderBy('views', 'desc')->take(4)->get();
    }
    public function getProductPhukien()
    {
        return Product::with('image')->where('cate_id', '=', '3')->where('status', '=', '1')->orderBy('views', 'desc')->take(4)->get();
    }
    public function getProductBaloTui()
    {
        return Product::with('image')->where('cate_id', '=', '4')->where('status', '=', '1')->orderBy('views', 'desc')->take(4)->get();
    }

    public function getviewProduct()
    {
        return Product::with('image', 'manufacture')->where('status', '=', '1')->orderBy('views', 'desc')->take(4)->get();
    }



    public function images()
    {
        return $this->morphMany(File::class, 'model')->where('custom_field', 'ListProduct');
    }



    public function color()
    {
        return $this->morphMany(Colorable::class, 'able');
    }



    public function orders()
    {
        return $this->belongsToMany(orders::class);
    }

    public function manufacture()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function rate()
    {
        return $this->hasMany(Rate::class, 'product_id');
    }

    public function origin()
    {
        return $this->belongsTo(Origin::class, 'origin_id');
    }
}
