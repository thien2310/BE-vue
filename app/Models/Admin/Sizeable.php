<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Sizeable extends Model
{
    //
    protected $table = 'Sizeables';

    protected $fillable = ['id', 'sizeable_type', 'sizeable_id', 'size_id'];


}
