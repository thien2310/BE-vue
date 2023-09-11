<?php

namespace App\Http\Controllers;

use App\Models\Admin\Size;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class sizeController extends Controller
{
    //
    private $size;
    public function __construct(Size $color)
    {
        $this->size= $color;
    }

    public function create(Request $request){

        $validated = $request->validate(
            [
                'name' => 'required',
                'code' => 'required',
            ],
            [
                'name.required' => 'Vui lòng nhập size',
                'code.required' => 'Vui lòng nhập size',
            ]
        );

        DB::beginTransaction();
        try {

            $this->size->name = $request->name;
            $this->size->code = $request->code;
            $this->size->create_by = auth()->id();

            $this->size->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm size thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };

    }

}
