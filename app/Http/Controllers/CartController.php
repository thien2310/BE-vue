<?php

namespace App\Http\Controllers;

use App\Models\Admin\Product;
use App\Models\Order_product;
use App\Models\orders;
use App\Models\User_coupons;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    //
    private $orders;
    private $order_product;
    private $user_coupons;
    private $product;
    public function __construct(orders $orders, Order_product $order_product, User_coupons $user_coupons, Product $product)
    {
        $this->orders = $orders;
        $this->order_product = $order_product;
        $this->user_coupons = $user_coupons;
    }

    public function checkout(Request $request)
    {
        // return $request;

        $validated = $request->validate(
            [
                'name' => 'required',
                'phoneNumber' => 'required',
                'email' => 'required',
                'address' => 'required',
                'addressDetail' => 'required',
                'transaction' => 'required',
                'city' => 'required',
                'district' => 'required',
                'ward' => 'required',

            ],
            [
                'name.required' => 'Vui lòng tên',
                'phoneNumber.required' => 'Vui lòng số điện thoại',
                'email.required' => 'Vui lòng địa chỉ email',
                'address.required' => 'Vui lòng nơi nhận',
                'addressDetail.required' => 'Vui lòng chi tiết nơi nhận',
                'transaction.required' => 'Vui lòng chọn phương thức thanh toán',
                'city.required' => 'Vui lòng chọn tỉnh, thành phố',
                'district.required' => 'Vui lòng chọn quận huyện',
                'ward.required' => 'Vui lòng chọn xã , phường',
            ]
        );

        DB::beginTransaction();
        try {


            $this->orders->user_id = Auth()->id();
            $this->orders->transaction_id = $request->transaction;
            $this->orders->total = $request->total;
            $this->orders->voucher = $request->voucher;
            $this->orders->ship = $request->ship;
            $this->orders->name = $request->name;
            $this->orders->phoneNumber = $request->phoneNumber;
            $this->orders->address = $request->address;
            $this->orders->city = $request->city;
            $this->orders->district = $request->district;
            $this->orders->ward = $request->ward;
            $this->orders->addressDetail = $request->addressDetail;
            $this->orders->email = $request->email;

            $uniqueCode = false;
            while (!$uniqueCode) {
                $randomCode = Str::random(8);
                $existingOrder = $this->orders::where('code_zip', $randomCode)->first();

                if (!$existingOrder) {
                    $this->orders->code_zip = $randomCode;
                    $uniqueCode = true;
                }
            }

            $this->orders->save();
            $this->user_coupons->updateVoucher($request->idVoucher);

            foreach ($request->cartStore['products'] as $value) {

                Order_product::query()->create([
                    'order_id' => $this->orders->id,
                    'product_id' => $value['id'],
                    'quantity' => $value['quantity'],
                    'price' => $value['price'],
                    'color' => $value['color'],
                    'size' => $value['size']
                ]);
            }

            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Bạn đã đặt hàng thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }


}
