<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Admin\Color;
use App\Models\Admin\Coupon;
use App\Models\Admin\Product;
use App\Models\Admin\Size;
use App\Models\User_coupons;
use Illuminate\Http\Request;

class homeController extends Controller
{
    //
    private $product;
    private $color;
    private $size;
    private $coupon;
    private $user_coupons;

    public function __construct(Product $product,Color $color , Size $size, Coupon $coupon, User_coupons $user_coupons)
    {
        $this->product = $product;
        $this->color = $color;
        $this->size = $size;
        $this->coupon = $coupon;
        $this->user_coupons = $user_coupons;

    }

    public function home()
    {

        $giaynu = $this->product->getProductGiayNu();
        $giaynam = $this->product->getProductGiayNam();
        $phukien = $this->product->getProductPhukien();
        $balotui = $this->product->getProductBaloTui();
        $viewProduct = $this->product->getviewProduct();

        return response([
            'giaynu' => $giaynu,
            'giaynam' => $giaynam,
            'phukien' => $phukien,
            'balotui' => $balotui,
            'viewProduct' => $viewProduct
        ]);
    }

    public function getProducts($id)
    {
        $product = $this->product::find($id);
        $imgPath[] = $product->images;
        $product->manufacture;
        $product->image;
        $product->sizes->pluck('id')->toArray();
        $product->colors->pluck('id')->toArray();
        $product->rate;
        // $product->rate->pluck('')->toArray();
        $coupon = $this->coupon::get();

        return response([
            'product' => $product,
            'coupon' => $coupon


        ]);
    }

    public function viewProduct($id)
    {

        $product = $this->product::find($id);
        $product->views += 1;
        $product->save();

        return response([
            'message' => 'Tăng một lượt truy cập',

        ]);
    }
}
