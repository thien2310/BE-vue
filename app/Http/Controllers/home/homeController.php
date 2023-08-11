<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class homeController extends Controller
{
    //
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function home(){
        // $giaynu = $this->product->showProductGiayNu();
        $giaynu = $this->product->getProductGiayNu();

        return response([
            'giaynu' => $giaynu,
        ]);

    }

    public function getProducts($id){
        $product = $this->product::find($id);
        $imgPath[] = $product->images;

        return response([
            'product' => $product,
            // 'imgPath' => $imgPath
        ]);

    }




}
