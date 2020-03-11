<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'summary', 'code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];


    /**
     * The users that belong to the Project.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
