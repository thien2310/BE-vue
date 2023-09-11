<?php

namespace App\Http\Controllers;

use App\Models\Admin\Manufacturer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturersController extends Controller
{
    //

    private $manufacturers;
    public function __construct(Manufacturer $manufacturers)
    {
        $this->manufacturers= $manufacturers;
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

            $this->manufacturers->name = $request->name;
            $this->manufacturers->code = $request->code;
            $this->manufacturers->create_by = auth()->id();

            $this->manufacturers->save();
            DB::commit();

            return response()->json([
                'code' => 3,
                "message" => "Thêm thương hiệu thành công",
                "alert-type" => "success"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        };
    }
}
