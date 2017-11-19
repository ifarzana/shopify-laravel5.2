<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_privilege';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['privilege', 'icon'];
    
}