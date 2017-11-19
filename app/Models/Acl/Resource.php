<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Config;


class Resource extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_resource';

    /**
     * The group by column
     *
     * @var string
     */
    protected $group_by = null;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'route', 'icon', 'order', 'default', 'hidden_navigation', 'hidden_from_navigation', 'hidden_from_dashboard'];
    
}