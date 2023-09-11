<?php

namespace App\Http\Controllers;

use App\Models\Admin\FeeShipping;
use App\Models\Common\Devvn_quanhuyen;
use App\Models\Common\Devvn_tinhthanhpho;
use App\Models\Common\Devvn_xaphuongthitran;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipsController extends Controller
{
    //
    private $city;
    private $district;
    private $ward;
    private $fee_shipping;
    public function __construct(Devvn_tinhthanhpho $city, Devvn_quanhuyen $district, Devvn_xaphuongthitran $ward, FeeShipping $fee_shipping)
    {
        $this->city = $city;
        $this->district = $district;
        $this->ward = $ward;
        $this->fee_shipping = $fee_shipping;

    }

    public function index(){
        //lấy ra tên theo ...
        $fee_shipping = $this->fee_shipping->city();

        //lấy ra phí ship theo ward
        $ship = $this->fee_shipping->get();

        return response([
            'fee_shipping' => $fee_shipping,
            'ship' => $ship
        ]);
    }

    public function create(){
        $city = $this->city::get();
        $district = $this->district::get();
        $ward = $this->ward::get();

        return response([
            'city' => $city,
            'district' => $district,
            'ward' => $ward,
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate(
            [
                'city' => 'required',
                'district' => 'required',
                'ward' => 'required|:unique',
                'shippingFee' => 'required',
            ],
            [
                'city.required' => 'Vui chọn tỉnh | thành phố',
                'district.required' => 'Vui chọn quận huyện',
                'ward.required' => 'Vui chọn xã phường',
                'ward.unique' => 'Đã tồn tại',
                'shippingFee.required' => 'Vui lòng nhập phí ship',

            ]
        );

        DB::beginTransaction();
        try {

            $this->fee_shipping->city = $request->city;
            $this->fee_shipping->district = $request->district;
            $this->fee_shipping->ward = $request->ward;
            $this->fee_shipping->fee_ship = $request->shippingFee;


            $this->fee_shipping->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm phí vận chuyển thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };


    }
}
