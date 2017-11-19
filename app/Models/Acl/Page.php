<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_page';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'controller', 'action', 'icon', 'resource_id', 'privilege_id', 'order'];
    
    public function scopeGetByResourceId(Builder $query, $resource_id)
    {
        $resource_id = (int)$resource_id;

        $query->where('resource_id', '=', $resource_id);

        $query->orderBy(array(
            'resource_id' => 'ASC',
            'order' => 'ASC',
        ));
        
        return $query->get();
    }
    
}