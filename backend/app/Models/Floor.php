<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function panaromas()
    {
        return $this->hasMany(Panaroma::class);
    }
}
