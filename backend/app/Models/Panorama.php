<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panorama extends Model
{
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class, 'panorama_id');
    }

    public function incomingHotspots()
    {
        return $this->hasMany(Hotspot::class, 'target_panorama_id');
    }
}
