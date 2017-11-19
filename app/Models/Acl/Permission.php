<?php

namespace App\Models\Acl;

use App\Models\User\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Permission extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_permission';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['resource_id', 'user_group_id', 'privilege_id'];

    public function resources()
    {
        return $this->hasMany(Resource::class, 'id', 'resource_id');
    }

    public function userGroups()
    {
        return $this->hasMany(Group::class, 'id', 'user_group_id');
    }

    public function privileges()
    {
        return $this->hasMany(Privilege::class, 'id', 'privilege_id');
    }
    
    public static function checkByUserGroupIdResourceIdAndPrivilegeId($user_group_id, $resource_id, $privilege_id)
    {
        $user_group_id = (int)$user_group_id;
        $resource_id = (int)$resource_id;
        $privilege_id = (int)$privilege_id;

        $query = Permission::where('resource_id', $resource_id)
            ->where('user_group_id', $user_group_id)
            ->where('privilege_id', $privilege_id)
            ->first();

        return $query;
    }

    public function scopeGetByUserGroupId(Builder $query, $user_group_id)
    {
        $user_group_id = (int)$user_group_id;

        $columns = array(
            $this::getTable().'.*',
            'ACL_privilege.privilege as name',
            'ACL_resource.default as default_resource',
        );

        $query->select($columns);

        $query->join('ACL_privilege',$this::getTable().'.privilege_id', '=', 'ACL_privilege.id' , 'left');
        $query->join('ACL_resource',$this::getTable().'.resource_id', '=', 'ACL_resource.id' , 'left');

        $query->where('user_group_id', '=', $user_group_id);

        return $query->get();
    }
    
}