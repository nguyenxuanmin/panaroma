<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
