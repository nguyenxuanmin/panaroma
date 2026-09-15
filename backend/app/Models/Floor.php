<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function panaromas()
    {
        return $this->hasMany(Panaroma::class);
    }
}
