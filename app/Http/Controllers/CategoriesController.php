<?php

namespace App\Http\Controllers;

use App\Models\Admin\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CategoriesController extends Controller
{
    //
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $category = $this->category->orderBy('sort_order', 'asc')->get();

        return response()->json([
            "category" => $category,


        ]);
    }

    public function deletel($id)
    {
        $category = $this->category->findOrFail($id);

        if (!$category->canDelete()) {

            return response()->json([
                'code' => 0,
                "message" => "Không thể xóa!!"
            ]);
        } else {

            $category->delete();
        }
    }

    public function create()
    {
        $status = Category::STATUSES;

        $parent = $this->category->getForSelect();

        return response()->json([
            "parent" => $parent,
            "status" => $status
        ]);
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            if ($request->parent_id) {
                $parent = category::where('id', $request->parent_id)->first();
                if ($parent->level + 1 > 3) {
                    return response()->json([
                        "code" => 2,
                        "message" => "Không quá 3 cấp",
                        "alert-type" => "warning"
                    ]);
                }
                $stt = category::where('parent_id', $request->parent_id)->count();
                if ($stt > 0) {
                    $stt += $stt;
                } else {
                    $stt = $parent->sort_order + 1;
                }

                $this->category->parent_id = $request->parent_id;
                $this->category->level = $parent->level + 1;
                $this->category->sort_order = $stt;
            } else {
                $this->category->parent_id = 0;
                $this->category->level = 0;
                $this->category->sort_order = 0;
            }
            // Cập nhật lại stt các danh mục có stt lớn hơn
            if ($request->parent_id) {
                foreach (category::where('sort_order', '>=', $stt)->where('id', '<>', $this->category->id)->orderBy('sort_order', 'asc')->get() as $item) {
                    $item->sort_order = $item->sort_order + 1;
                    $item->save();
                }
            }
            $this->category->slug = Str::slug($request->name);

            $this->category->name = $request->name;
            $this->category->status = $request->status;
            $this->category->save();

            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thao tác thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function edit($id)
    {
        $category = $this->category->getAllForEdit($id);
        $statuses = $this->category::STATUSES;
        $categories = $this->category->find($id);
        return response()->json([
            "category" => $category,
            "statuses" => $statuses,
            'categories' => $categories
        ]);
    }

    public function update($id, Request $request)
    {

        $validated = $request->validate(
            [
                'cate' => 'nullable',
                'name' => 'required|max:255',

            ],
            [
                'name.required' => 'Vùi lòng nhập tên danh mục',
                'name.max' => 'Nhập tối đa mà 255:max',
                'cate.nullable' => 'Không được để trống'
            ]
        );

        $categories = $this->category->find($id);

        if ($request->cate) {
            $parent = $categories::where('id', $request->cate)->first();
            // dd($parent);
            if ($parent->level + 1 > 3) {
                return response()->json([
                    "code" => 2,
                    "message" => "Không quá 3 cấp",
                    "alert-type" => "warning"
                ]);
            }
            $stt = $categories::where('parent_id', $request->cate)->count();
            if ($stt > 0) {
                $stt += $stt;
            } else {
                $stt = $parent->sort_order + 1;
            }
            $data = [
                $categories->name = $request->name,
                $categories->sort_order = $stt,
                $categories->parent_id = $request->cate,
                $categories->level = $parent->level + 1,
                $categories->status = $request->status_id,

                date('Y-m-d H:i:s'),

            ];
        } else {
            $data = [
                $request->name,
                $categories->sort_order = 0,
                $categories->parent_id = 0,
                $categories->level = 0,
                $request->status_id,
                date('Y-m-d H:i:s'),
            ];
        }

        $this->category->updataCategory($id, $data);

        return response([
            'message' => 'cập nhập thành công',
            'code' => '17'
        ]);
    }

    



    public function showHome()
    {
        $show = $this->category->showHome();

        return response([
            'show' => $show
        ]);
    }
}
