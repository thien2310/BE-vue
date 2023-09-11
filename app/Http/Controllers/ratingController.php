<?php

namespace App\Http\Controllers;

use App\Models\Admin\Product;
use App\Models\Admin\Rate;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ratingController extends Controller
{
    //
    private $user;
    private $product;
    private $rate;

    public function __construct(User $user , Product $product, Rate $rate)
    {
        $this->user = $user;
        $this->product = $product;
        $this->rate = $rate;
    }
    public function create(Request $request, $id){


        DB::beginTransaction();
        try {

            $this->rate->rating = $request->rate;
            $this->rate->product_id = $id;
            $this->rate->create_by = Auth()->id();
            $this->rate->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Đánh giá sản phẩm thành công",
                "alert-type" => "success"
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }

    public function update(Request $request,$id){
        // return $id;
        DB::beginTransaction();
        try {

            $rate = $this->rate->find($id);

            $rate->rating = $request->rate;
            $rate->update_by = Auth()->id();
            $rate->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Cảm ơn bạn đã đánh giá lại",
                "alert-type" => "success"
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };



    }
}
