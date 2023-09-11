<?php

namespace App\Models\Admin;

use App\Models\Common\Devvn_tinhthanhpho;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FeeShipping extends Model
{
    use HasFactory;
    protected $table = 'fee_shipping';
    protected $fillable = [];

    public function city(){
        return DB::table('fee_shipping')->join('devvn_tinhthanhpho','fee_shipping.city','=','devvn_tinhthanhpho.matp')->join('devvn_quanhuyen','fee_shipping.district','=','devvn_quanhuyen.maqh')->join('devvn_xaphuongthitran','fee_shipping.ward','=','devvn_xaphuongthitran.xaid')->select('fee_shipping.*','devvn_tinhthanhpho.name as nameCity','devvn_quanhuyen.name as nameDistrict', 'devvn_xaphuongthitran.name as nameward' )->get();
    }


    
}
