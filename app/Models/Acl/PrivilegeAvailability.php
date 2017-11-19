<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class PrivilegeAvailability extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_privilege_availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['resource_id', 'privilege_id'];

    public function resources()
    {
        return $this->hasMany(Resource::class, 'id', 'resource_id');
    }

    public function privilege()
    {
        return $this->hasOne(Privilege::class, 'id', 'privilege_id');
    }
    
}