<?php

namespace App\Models\Acl;

use App\Models\User\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Acl extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ACL_acl';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_group_id', 'resource_id'];

    public static $rules = array(
        'user_group_id' => 'required',
        'resource_id' => 'required',
    );

    public function userGroup()
    {
        return $this->hasOne(Group::class, 'id', 'user_group_id');
    }

    public function resource()
    {
        return $this->hasOne(Resource::class, 'id', 'resource_id');
    }

    public function scopeGetByUserGroupId(Builder $query, $user_group_id, $excludeHiddenFromDashboard = false, $excludeHiddenFromNavigation = false)
    {
        $user_group_id = (int)$user_group_id;

        $columns = array(
            $this::getTable().'.*',
            'ACL_resource.name as label',
            'ACL_resource.route as route',
            'ACL_resource.icon as icon',
            'ACL_resource.order as order',
            'ACL_resource.hidden_navigation',
            'ACL_resource.hidden_from_navigation',
            'ACL_resource.hidden_from_dashboard',
            'ACL_resource.configuration_menu',
        );

        $query->select($columns);

        $query->join('ACL_resource',$this::getTable().'.resource_id', '=', 'ACL_resource.id' , 'left');

        $query->where('user_group_id', '=', $user_group_id);

        if($excludeHiddenFromDashboard)
        {
            $query->where('ACL_resource.hidden_from_dashboard', '=', 0);
        }

        if($excludeHiddenFromNavigation)
        {
            $query->where('ACL_resource.hidden_from_navigation', '=', 0);
        }

        $query->orderBy('ACL_resource.order', 'ASC');

        return $query->get();
    }

    public function scopeGetAllowedByUserGroupId(Builder $query, $user_group_id)
    {
        $user_group_id = (int)$user_group_id;

        $columns = array(
            $this::getTable().'.resource_id',
        );

        $query->select($columns);

        $query->where('user_group_id', '=', $user_group_id);

        return $query->get();
    }

    public static function checkByUserGroupIdAndResourceId($user_group_id, $resource_id)
    {
        $user_group_id = (int)$user_group_id;
        $resource_id = (int)$resource_id;

        $query = Acl::where('resource_id', $resource_id)
            ->where('user_group_id', $user_group_id)
            ->first();

        return $query;
    }

}