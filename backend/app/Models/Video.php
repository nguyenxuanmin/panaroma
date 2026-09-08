<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['project_id', 'title', 'link'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
