<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;

    public const STATUSES = [
        [
            'value' => 0,
            'label' => 'Chưa kích hoạt',
        ],

        [
            'value' => 1,
            'label' => 'Kích hoạt',

        ],

    ];

    protected $table = 'categories';
    protected $fillable = [];

    public static function getForSelect()
    {
        $all = self::select(['id', 'name', 'sort_order', 'level'])->orderBy('sort_order', 'asc')->get()->toArray();

        // $result = [];
        $result = array_map(function ($value) {
            if ($value['level'] == 1) {
                $value['name'] = ' |-- ' . $value['name'];
            }
            if ($value['level'] == 2) {
                $value['name'] = ' |-- |--' . $value['name'];
            }
            if ($value['level'] == 3) {
                $value['name'] = ' |-- |-- |--' . $value['name'];
            }
            if ($value['level'] == 4) {
                $value['name'] = ' |-- |-- |-- |--' . $value['name'];
            }
            return $value;
        }, $all);

        // dd($result);

        return $result;
    }

    public function canDelete()
    {
        return true;
    }
    public function showHome() {
        return DB::table('categories')->select('name','id','slug')->where('status','=','1')->get();
    }
}
