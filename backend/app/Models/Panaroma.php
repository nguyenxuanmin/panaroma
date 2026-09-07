<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panaroma extends Model
{
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class, 'panaroma_id');
    }

    public function incomingHotspots()
    {
        return $this->hasMany(Hotspot::class, 'target_panaroma_id');
    }
}
