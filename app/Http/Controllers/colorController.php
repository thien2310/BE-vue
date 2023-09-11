<?php

namespace App\Http\Controllers;

use App\Models\Admin\Color;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class colorController extends Controller
{
    //
    private $color;
    public function __construct(Color $color)
    {
        $this->color= $color;
    }

    public function create(Request $request){
        $validated = $request->validate(
            [
                'name' => 'required',
                'code' => 'required',
            ],
            [
                'name.required' => 'Vui lòng nhập màu',
                'code.required' => 'Vui lòng nhập code',
            ]
        );

        DB::beginTransaction();
        try {

            $this->color->name = $request->name;
            $this->color->code = $request->code;
            $this->color->create_by = auth()->id();

            $this->color->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm mã màu mới thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };

    }


}
