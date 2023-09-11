<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Colorable extends Model
{
    //
    protected $table = 'colorables';

    protected $fillable = ['id', 'colorable_type', 'colorable_id', 'color_id'];


}
