<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelpers;
use App\Models\Admin\Category;
use App\Models\Admin\Color;
use App\Models\Admin\Manufacturer;
use App\Models\Admin\Origin;
use App\Models\Admin\Product;
use App\Models\Admin\Size;
use App\Models\Admin\Table_colors;
use App\Models\Admin\Table_sizes;
use App\Models\Common\File;
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
    private $manufacture;
    private $origin;
    private $color;
    private $size;
    public function __construct(Category $category, Product $product, Manufacturer $manufacture, Origin $origin, Color $color, Size $size)
    {
        $this->category = $category;
        $this->product = $product;
        $this->manufacture = $manufacture;
        $this->origin = $origin;
        $this->color = $color;
        $this->size = $size;
    }

    public function index()
    {
        $products = $this->product->getProduct();

        return response()->json([
            "products" => $products,
        ]);
    }

    public function create()
    {
        $category = $this->category->getForSelect();
        $status = $this->product::STATUSES;
        $origin = Origin::get();
        $manufacturer = Manufacturer::get();
        $state = $this->product::STATE;
        $colors = $this->color->latest()->get();
        $sizes = $this->size->latest()->get();
        // $sizes = Table_sizes::get();

        return response()->json([
            "category" => $category,
            "status" => $status,
            "origin"  => $origin,
            "manufacturer" => $manufacturer,
            "state" => $state,
            "colors" => $colors,
            "sizes" => $sizes
        ]);
    }


    public function store(Request $request)
    {



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


            if (isset($request->all()['color'])) {
                $this->product->addColor($request->all()['color']);
            }

            if (isset($request->all()['size'])) {
                $this->product->addSize($request->all()['size']);
            }


            if ($request->fileList) {
                FileHelpers::uploadFiles($request->fileList, "ListProducts", $this->product->id, Product::class, "ListProduct", 1);
            }

            if ($request->imageUrl) {
                FileHelpers::uploadFile($request->imageUrl, "Product", $this->product->id, Product::class, "avatarProduct", 1);
            }

            DB::commit();

            return response([
                'code' => 1,
                'message' => 'Thêm mới sản phẩm thành công',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }


    public function edit($id)
    {
        $product = $this->product->find($id);
        $manufacture = $this->manufacture->get();
        $orgin = $this->origin->get();
        $product->origin;
        $product->manufacture;
        $category = $this->category->getForSelect();
        $status = $this->product::STATUSES;
        $state = $this->product::STATE;
        $images = $product->images;
        $image = $product->image;
        $colors = $this->color->latest()->get();
        $sizes = $this->size->latest()->get();
        $product->colors->pluck('id')->toArray();
        $product->sizes->pluck('id')->toArray();
        // $color = $product->color;

        return response([
            'product' => $product,
            'category' => $category,
            'status' => $status,
            'state' => $state,
            'orgin' => $orgin,
            'manufacture' => $manufacture,
            'images' => $images,
            'image' => $image,
            "colors" => $colors,
            "sizes" => $sizes,
            // 'color' => $color

        ]);
    }


    public function update(Request $request, $id)
    {

        // return $request->fileList;


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

            $oject = $this->product->find($id);
            $oject->name = $request->name;
            $oject->status = $request->status;
            $oject->update_by = Auth()->id();
            $oject->price = $request->price;
            $oject->cate_id = $request->parent_id;
            $oject->base_price = $request->base_price;
            $oject->body = $request->body;
            $oject->intro = $request->intro;
            $oject->slug = Str::slug($request->name);
            $oject->manufacturer_id = $request->manufacturer;
            $oject->origin_id = $request->origin;
            $oject->state = $request->state;
            $oject->save();




            if (isset($request->all()['color'])) {
                $oject->updateColors($request->all()['color']);
            }

            if (isset($request->all()['size'])) {
                $oject->updateSizes($request->all()['size']);
            }



            if ($request->imageUrl) {
                if ($oject->image) {
                    FileHelpers::forceDeleteFiles($oject->image->id, $oject->id, Product::class, 'avatarProduct');
                }
                FileHelpers::uploadFile($request->imageUrl, 'Product', $oject->id, Product::class, 'avatarProduct', 1);
            }

            if ($request->fileList) {
                foreach ($request->fileList as $key) {
                    if (isset($key['url'])) {
                        $url[] =  $key['url'];
                    }
                    if (isset($key['id'])) {
                        $ids[] = $key['id'];
                    }
                    if (isset($key['thumbUrl'])) {
                        $thumurl[] = ['thumbUrl' => $key['thumbUrl'] ];
                    }
                }
                foreach ($oject->images as $val) {
                    $allids[] = $val['id'];
                }

                if (isset($url) && isset($ids) && !empty($url) && !empty($ids)) {
                    $idsDelete = array_diff($allids, $ids);
                    $file = File::query()->where([
                        'model_id' => $this->product->find($id)->id,
                        'model_type' => get_class($this->product),
                    ])->whereIn('id', $idsDelete);

                    foreach ($file->get() as $val) {
                        if (file_exists(public_path($val->path))) {
                            unlink(public_path($val->path));
                        }
                    }
                    $file->delete();
                }

                if (isset($thumurl) && !empty($thumurl)) {
                    FileHelpers::uploadFiles($thumurl, 'ListProducts', $oject->id, Product::class, 'ListProduct', 1);
                }
            }


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }


        return response([
            'code' => 1,
            'message' => 'Cập nhật sản phẩm thành công',
        ]);
    }
}
