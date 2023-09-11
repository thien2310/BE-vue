<?php

namespace App\Http\Controllers;

use App\Models\Admin\Color;
use App\Models\Admin\Product;
use App\Models\Admin\Size;
use App\Models\Common\Devvn_quanhuyen;
use App\Models\Common\Devvn_tinhthanhpho;
use App\Models\Common\Devvn_xaphuongthitran;
use App\Models\orders;
use App\Models\Staticsical;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class orderController extends Controller
{
    //
    private $orders;
    private $city;
    private $district;
    private $ward;
    private $product;
    private $size;
    private $color;
    private $staticsical;

    public function __construct(orders $orders, Devvn_tinhthanhpho $city, Devvn_quanhuyen $district, Devvn_xaphuongthitran $ward, Product $product, Size $size, Color $color, Staticsical $staticsical)
    {
        $this->orders = $orders;
        $this->city = $city;
        $this->district = $district;
        $this->ward = $ward;
        $this->product = $product;
        $this->size = $size;
        $this->color = $color;
        $this->staticsical = $staticsical;
    }

    public function index()
    {
        $orders = $this->orders->where('status_order', '=', '1')->orWhere('status_order', '2')->get();
        $status = $this->orders::STATUSES;

        return response([
            'orders' => $orders,
            'status' => $status

        ]);
    }

    public function preview($id)
    {
        $order = $this->orders->find($id);
        $order->orderProducts;
        $city = $this->city::get();
        $district = $this->district::get();
        $ward = $this->ward::get();
        $product = $this->product::get();
        $size = $this->size::get();
        $color = $this->color::get();

        return response([
            'order' => $order,
            'city' => $city,
            'district' => $district,
            'ward' => $ward,
            'product' => $product,
            'size' => $size,
            'color' => $color
        ]);
    }

    public function updateStatus($id)
    {
        $order = $this->orders->find($id);

        $order->update([
            'status_order' => 2
        ]);

        return response([
            'message' => 'Cập nhật trạng thái đơn hàng thành công',
            'code' => 4
        ]);
    }

    public function comleOrder($id)
    {
        $order = $this->orders->find($id);

        $order->update([
            'status_order' => 3
        ]);

        return response([
            'message' => 'Cập nhật trạng thái đơn hàng thành công',
            'code' => 4
        ]);
    }

    public function indexCompleOrder()
    {
        $staticsical = DB::table('orders')->join('order_product', 'orders.id', '=', 'order_product.order_id')->join('products', 'order_product.product_id', '=', 'products.id')->select('orders.id as orders_id', 'products.id as product_id', 'order_product.id as id')->where('status_order', '3')->get();

        $orders = $this->orders->where('status_order', '3')->get();
        return response([
            'orders' => $orders,
        ]);
    }

    public function revenue(Request $request)
    {


        DB::beginTransaction();
        try {
            $orderIds = $request->id_order;
            $totalProfit = DB::table('orders')
                ->whereIn('id', $orderIds)
                ->sum('total');
            $base_price = DB::table('orders')->join('order_product', 'orders.id', '=', 'order_product.order_id')->join('products', 'order_product.product_id', '=', 'products.id')->whereIn('orders.id', $orderIds)->sum('products.base_price');

            $quantity = DB::table('orders')->join('order_product', 'orders.id', '=', 'order_product.order_id')->whereIn('orders.id', $orderIds)->sum('order_product.quantity');

            $total_order = DB::table('orders')
                ->whereIn('id', $orderIds)
                ->count();


            $existingStaticsical = Staticsical::where('order_date', $request->date)->first();


            if ($existingStaticsical) {
                $existingStaticsical->sales += $totalProfit;
                $existingStaticsical->profit += ($totalProfit - $base_price);
                $existingStaticsical->quantity += $quantity;
                $existingStaticsical->total_order += $total_order;
                $existingStaticsical->save();
            } else {
                $this->staticsical->order_date  = $request->date;
                $this->staticsical->sales = $totalProfit;
                $this->staticsical->profit = $totalProfit - $base_price;
                $this->staticsical->quantity = $quantity;
                $this->staticsical->total_order = $total_order;
                $this->staticsical->save();
            }




            orders::query()->whereIn('id', $orderIds)->update([
                'status_order' => 4
            ]);
            DB::commit();


            return response()->json([
                'code' => 3,
                "message" => "Chốt doanh thu $request->date thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }
}
