<?php

namespace App\Models\Train;


use App\Models\Admin\Size;
use App\Models\Admin\Sizeable;

trait hasSize
{

    public function sizes()
    {
        return $this->morphToMany(Size::class, 'sizeable');
    }



    public function addSize($size_ids)
    {
        foreach ($size_ids as $size_id) {

            Sizeable::query()->create([
                'sizeable_type' => get_class($this),
                'sizeable_id' => $this->id,
                'size_id' => $size_id
            ]);
        }
    }


    public function updateSizes($size_ids) {

        $size_ids_current = $this->sizes->pluck('id')->toArray();

        $size_ids_delete = array_diff($size_ids_current, $size_ids);


        Sizeable::query()->where([
            'sizeable_id' => $this->id,
            'sizeable_type' => get_class($this),
        ])->whereIn('size_id', $size_ids_delete)->delete();

        foreach ($size_ids as $size_id) {
            $exists = Sizeable::query()->where([
                'sizeable_id' => $this->id,
                'sizeable_type' => get_class($this),
                'size_id' => $size_id,
            ])->exists();
            if(! $exists) {
                Sizeable::query()->create([
                    'sizeable_id' => $this->id,
                    'sizeable_type' => get_class($this),
                    'size_id' => $size_id
                ]);
            }

        }
    }
}
