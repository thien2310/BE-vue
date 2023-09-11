<?php

namespace App\Models;

use App\Models\Admin\Coupon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User_coupons extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function coupons()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function updateVoucher($id)
    {
        $voucher = DB::table('user_coupons')->where('id', $id)->first();
        if ($voucher) {
            DB::table('user_coupons')->where('id', $id)->update([
                'redeemed' => 0,
                'date_redeemed' => Carbon::now()
            ]);

        } 
    }
}
