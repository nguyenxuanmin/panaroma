<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanaromaImage extends Model
{
    public function panaroma()
    {
        return $this->belongsTo(Panaroma::class, 'panaroma_id');
    }
}
