<?php

namespace App\Http\Controllers;

use App\Models\Admin\Coupon;
use App\Models\User;
use App\Models\User_coupons;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class couponController extends Controller
{
    //
    private $coupon;
    private $user;
    private $user_coupons;
    public function __construct(Coupon $coupon, User_coupons $user_coupons, User $user)
    {
        $this->coupon = $coupon;
        $this->user_coupons = $user_coupons;
        $this->user = $user;
    }

    public function index()
    {

        $coupons = $this->coupon::get();
        return response()->json([
            'coupons' => $coupons
        ]);
    }
    public function create(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required',
                'number' => 'required',
                'feature' => 'required',
                'price_coupon' => 'required',

            ],
            [
                'name.required' => 'Vui lòng nhập size',
                'number.required' => 'Vui lòng nhập số lượng mã',
                'feature.required' => 'Vui lòng chọn tính năng mã',
                'price_coupon.required' => 'Vui lòng nhập số tiền hoặc %',
            ]
        );

        DB::beginTransaction();
        try {

            $this->coupon->name = $request->name;
            $this->coupon->number = $request->number;
            $this->coupon->feature = $request->feature;
            $this->coupon->price_coupon = $request->price_coupon;
            $this->coupon->coupon_code = strtoupper(Str::random(6));
            $this->coupon->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm mã giảm giá thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }

    public function user_create(Request $request)
    {

        foreach ($request->Coupon as $key) {
            $idCoupon = $key['id'];
        }

        $existingUserCoupon = $this->user_coupons::where('user_id', auth()->id())
            ->where('coupon_id', $idCoupon)
            ->first();

        if ($existingUserCoupon) {
            return response()->json([
                'code' => 4,
                "message" => "Bạn đã có voucher này rồi, làm nhiệm vụ để có thêm voucher",
                "alert-type" => "warning"
            ]);
        }

        DB::beginTransaction();
        try {

            $this->user_coupons->user_id = Auth()->id();
            $this->user_coupons->coupon_id = $idCoupon;

            $this->user_coupons->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm mã giảm giá thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }

    public function user_index()
    {
        $userCoupons = $this->user_coupons::with('coupons')
            ->where('user_id', auth()->id())->where('redeemed', 1)
            ->get();

        return response([
            'coupons' => $userCoupons,
        ]);
    }

    public function find_code(Request $request)
    {


        $coupon_code = $request->coupon_code;
        $codes = DB::table('coupons')
            ->where('coupon_code', 'like', '%' . $coupon_code . '%')
            ->get();
        foreach ($codes as $key) {
            $id_coupons = $key->id;
        }


        if ($codes->count() > 0) {
            //xu lý số lượng mã tuôn ra
            $countVoucher = DB::table('user_coupons')->where('coupon_id', $id_coupons)->count();
            $numberVoucher = DB::table('coupons')->select('number')->where('id', $id_coupons)->first();
            $existingUserCoupon = $this->user_coupons::where('user_id', auth()->id())
                ->where('coupon_id', $id_coupons)->where('redeemed', 0)
                ->first();

            if ($existingUserCoupon) {
                return response()->json([
                    'code' => 4,
                    "message" => "Bạn đã có voucher này rồi hoặc voucher đã được sử dụng",
                    "alert-type" => "warning"
                ]);
            }

            if ($countVoucher <= $numberVoucher->number) {
                $this->user_coupons::create([
                    'user_id' => auth()->id(),
                    'coupon_id' => $id_coupons
                ]);
                return response()->json([
                    'code' => 3,
                    "message" => "Thêm mã giảm giá thành công",
                    "alert-type" => "success"
                ]);
            } else {
                return response()->json([
                    'code' => 4,
                    "message" => "Số lượng mã đã hết",
                    "alert-type" => "error"
                ]);
            }
        } else {
            return response()->json([
                'code' => 4,
                "message" => "Không tồn tại mã " . $coupon_code,
                "alert-type" => "error"
            ]);
        }
    }
}
