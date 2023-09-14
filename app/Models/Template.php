<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Template extends  Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lang',
        'content',
        'parameters',
        'event',
        'workspace_id',
    ];

    public function workspace()
    {
         return $this->belongsTo(Workspace::class);
    }

}
