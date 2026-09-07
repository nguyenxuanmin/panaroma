<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    public function panaroma()
    {
        return $this->belongsTo(Panaroma::class, 'panaroma_id');
    }

    public function targetPanaroma()
    {
        return $this->belongsTo(Panaroma::class, 'target_panaroma_id');
    }
}
