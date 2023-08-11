<?php

namespace App\Models\Common;


use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'path',
        'custom_field',
        'name',
        'model_id',
        'model_type'
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
