<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function panaromas(){
        return $this->hasMany(Panaroma::class);
    }

    public function floors(){
        return $this->hasMany(Floor::class);
    }
}
