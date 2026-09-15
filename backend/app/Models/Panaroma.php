<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panaroma extends Model
{
    public function building(){
        return $this->belongsTo(Building::class);
    }
    
    public function floor()
    {
        return $this->belongsTo(Floor::class)->withDefault();;
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class, 'panaroma_id');
    }

    public function incomingHotspots()
    {
        return $this->hasMany(Hotspot::class, 'target_panaroma_id');
    }

    public function panaromaImages()
    {
        return $this->hasMany(PanaromaImage::class, 'panaroma_id');
    }
}
