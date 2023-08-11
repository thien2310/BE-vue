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

    public function index(){
        $category = $this->category->orderBy('sort_order', 'asc')->get();

        return response()->json([
            "category" => $category,


        ]);
    }

    public function deletel($id) {
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

    public function showHome(){
        $show = $this->category->showHome();
        
        return response([
            'show' => $show
        ]);
    }


}
