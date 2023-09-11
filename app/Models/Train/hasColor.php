<?php

namespace App\Models\Train;


use App\Models\Admin\Color;
use App\Models\Admin\Colorable;

trait hasColor
{

    public function colors()
    {
        return $this->morphToMany(Color::class, 'colorable');
    }



    public function addColor($color_ids)
    {
        foreach ($color_ids as $color_id) {

            Colorable::query()->create([
                'colorable_type' => get_class($this),
                'colorable_id' => $this->id,
                'color_id' => $color_id
            ]);
        }
    }


    public function updateColors($color_ids) {

        $color_ids_current = $this->colors->pluck('id')->toArray();
        $color_ids_delete = array_diff($color_ids_current, $color_ids);



        Colorable::query()->where([
            'colorable_id' => $this->id,
            'colorable_type' => get_class($this),
        ])->whereIn('color_id', $color_ids_delete)->delete();

        

        foreach ($color_ids as $color_id) {
            $exists = Colorable::query()->where([
                'colorable_id' => $this->id,
                'colorable_type' => get_class($this),
                'color_id' => $color_id,
            ])->exists();
            if(! $exists) {
                Colorable::query()->create([
                    'colorable_id' => $this->id,
                    'colorable_type' => get_class($this),
                    'color_id' => $color_id
                ]);
            }

        }
    }
}
