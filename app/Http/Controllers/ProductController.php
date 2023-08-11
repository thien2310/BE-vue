<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelpers;
use App\Models\Admin\Category;
use App\Models\Admin\Manufacturer;
use App\Models\Admin\Origin;
use App\Models\Admin\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //

    private $category;
    private $product;
    public function __construct(Category $category, Product $product)
    {
        $this->category = $category;
        $this->product = $product;
    }

    public function create()
    {
        $category = $this->category->getForSelect();
        $status = $this->product::STATUSES;
        $origin = Origin::get();
        $manufacturer = Manufacturer::get();
        $state = $this->product::STATE;
        return response()->json([
            "category" => $category,
            "status" => $status,
            "origin"  => $origin,
            "manufacturer" => $manufacturer,
            "state" => $state
        ]);
    }


    public function store(Request $request)
    {
        // foreach($request->fileList as $file){
        //     $urlbase64[] = $file['thumbUrl'];
        // }
        // return response()->json([
        //     "urlbase64" => $urlbase64,

        // ]);

        $validated = $request->validate(
            [
                'base_price' => 'required',
                'body' => 'required',
                'name' => 'required',
                'intro' => 'required',
                'manufacturer' => 'required',
                'origin' => 'required',
                'parent_id' => 'required',
                'price' => 'required',
                'status' => 'required'

            ],
            [

                'base_price.required' => 'Vui lòng nhập giá',
                'body.required' => 'Vui lòng nhập mô tả',
                'name.required' => 'Vui lòng nhập tên sản phẩm',
                'intro.required' => 'Vui lòng nhập mô tả chi tiết',
                'manufacturer.required' => 'Vui lòng chọn thương hiệu',
                'origin.required' => 'Vui lòng chọn nguồn gốc',
                'parent_id.required' => 'Chọn danh mục',
                'price.required' => 'Nhập giá gốc',
                'status.required' => 'Chọn trạng thái',

            ]
        );

        DB::beginTransaction();
        try {
            $this->product->name = $request->name;
            $this->product->status = $request->status;
            $this->product->create_by = Auth()->id();
            $this->product->price = $request->price;
            $this->product->cate_id = $request->parent_id;
            $this->product->base_price = $request->base_price;
            $this->product->body = $request->body;
            $this->product->intro = $request->intro;
            $this->product->slug = Str::slug($request->name);
            $this->product->manufacturer_id = $request->manufacturer;
            $this->product->origin_id = $request->origin;
            $this->product->state = $request->state;
            $this->product->save();

            if ($request->fileList) {
                FileHelpers::uploadFiles($request->fileList, "ListProduct", $this->product->id, Product::class, "ListProduct", 1);
            }

            if ($request->imageUrl) {
                FileHelpers::uploadFile($request->imageUrl, "Product", $this->product->id, Product::class, "avatarProduct", 1);
            }



            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

}
