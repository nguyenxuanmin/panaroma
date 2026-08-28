<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    public function panorama()
    {
        return $this->belongsTo(Panorama::class, 'panorama_id');
    }

    public function targetPanorama()
    {
        return $this->belongsTo(Panorama::class, 'target_panorama_id');
    }
}
