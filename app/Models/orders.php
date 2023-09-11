<?php

namespace App\Models;

use App\Models\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class orders extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'orders';

    public const STATUSES = [
        [
            'value' => 1,
            'label' => 'Đơn hàng mới',

        ],
        [
            'value' => 2,
            'label' => 'Đã xử lý',

        ],
        [
            'value' => 3,
            'label' => 'Hoàn tất đơn hàng',

        ],


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(Order_product::class, 'order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function NameAdress(){
        return DB::table('orders')->join('devvn_tinhthanhpho','orders.city','=','devvn_tinhthanhpho.matp')->join('devvn_quanhuyen','orders.district','=','devvn_quanhuyen.maqh')->join('devvn_xaphuongthitran','orders.ward','=','devvn_xaphuongthitran.xaid')->select('orders.*','devvn_tinhthanhpho.name as nameCity','devvn_quanhuyen.name as nameDistrict', 'devvn_xaphuongthitran.name as nameward' )->get();
    }
}
