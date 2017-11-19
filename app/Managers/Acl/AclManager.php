<?php

namespace App\Managers\Acl;

use App\Models\Acl\Resource;
use App\Models\Acl\Acl as DbAcl;
use App\Models\User\User;
use App\Models\Acl\Permission;
use App\Models\Acl\Privilege;
use App\Models\User\Group;
use Auth;
use DB;

class AclManager
{
    /**
     * Resources
     *
     * @var array
     */
    protected $resources;

    /**
     * Permissions
     *
     * @var array
     */
    protected $permissions;
    
    /**
     * Privileges
     *
     * @var array
     */
    protected $privileges;
    
    /**
     * Database acl
     *
     * @var array
     */
    protected $db_acl;

    /**
     * The logged in user
     *
     * @var object User
     */
    protected $user;

    /**
     * Groups
     *
     * @var object Group
     */
    protected $groups;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        $this->resources = Resource::all()->toArray();
        $this->permissions = Permission::all()->toArray();
        $this->privileges = Privilege::all()->toArray();
        $this->db_acl = DbAcl::all()->toArray();
        
        $this->user = Auth::User();

        $this->groups = Group::all();
    }

    /**
     * Return the logged in user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Check if a route exists
     *
     * @param  string $route_name
     * @return bool
     */
    public function checkRoute($route_name)
    {
        $response = false;

        foreach ($this->resources as $resource) {

            if(isset($resource['route']) && $resource['route'] == $route_name) {
                $response = true;
            }

        }

        return $response;
    }

    /**
     * Get resource id
     *
     * @param  string $route_name
     * @return int|false
     */
    protected function getResourceId($route_name)
    {
        $id = false;

        foreach ($this->resources as $resource) {

            if(isset($resource['route']) && $resource['route'] == $route_name) {
                $id = $resource['id'];
            }

        }

        return $id;
    }

    /**
     * Get resource name
     *
     * @param  string $route_name
     * @return int|false
     */
    public function getResourceName($route_name)
    {
        $id = false;

        foreach ($this->resources as $resource) {

            if(isset($resource['route']) && $resource['route'] == $route_name) {
                $id = $resource['name'];
            }

        }

        return $id;
    }

    /**
     * Get privilege id
     *
     * @param  string $privilege_name
     * @return int|false
     */
    protected function getPrivilegeId($privilege_name)
    {
        $id = false;

        foreach ($this->privileges as $privilege) {

            if(isset($privilege['privilege']) && $privilege['privilege'] == $privilege_name) {
                $id = $privilege['id'];
            }

        }

        return $id;
    }

    /**
     * Check acl
     *
     * @param  string $route_name
     * @return bool
     */
    public function checkAcl($route_name)
    {

        $response = false;

        /*Get the group id*/
        $group_id = $this->user->group_id;

        /*Get the resource id*/
        $resource_id = $this->getResourceId($route_name);

        foreach ($this->db_acl as $acl) {

            if( ($acl['user_group_id'] == $group_id) AND ($acl['resource_id'] == $resource_id) ) {

                return true;
            }

        }

        return $response;
    }

    /**
     * Check permission
     *
     * @param  string $route_name
     * @param  string $privilege_name
     * @return bool
     */
    public function checkPermission($route_name, $privilege_name)
    {
        $response = false;

        if($this->user != null) {

            $resource_id = $this->getResourceId($route_name);
            $privilege_id = $this->getPrivilegeId($privilege_name);

            $user_group_id = $this->user->group_id;

            $allPermissions = $this->getAllPermissions($user_group_id);

            if( (isset($allPermissions[$user_group_id][$resource_id][$privilege_id])) AND ($allPermissions[$user_group_id][$resource_id][$privilege_id] == true) ) {
                return true;
            }

        }


        return $response;
    }

    /**
     * Return all permissions
     *
     * @param $user_group_id int
     *
     * @return array
     */
    public function getAllPermissions($user_group_id)
    {
        $array = array();

        $group = Group::find($user_group_id);

        if($group == null) {
            return $array;
        }

        /*Db Acl*/
        $db_acl = DbAcl::getAllowedByUserGroupId($user_group_id)->pluck('resource_id', 'resource_id')->toArray();

        /*Permissions*/
        $permissions = array();

        if($group->locked == 0) {
            $db_permissions = Permission::where('user_group_id', $group->id)->get();

            if(count($db_permissions) > 0) {
                foreach ($db_permissions as $db_permission) {
                    $permissions[$db_permission->resource_id][$db_permission->privilege_id] =  true;
                }
            }
        }

        foreach ($this->resources as $resource) {

            if(isset($db_acl[$resource['id']])) {

                foreach ($this->privileges as $privilege) {

                    if($group->locked == 1) {
                        $array[$group->id][$resource['id']][$privilege['id']] = true;
                    }else{

                        if(isset( $permissions[$resource['id']][$privilege['id']])) {
                            $array[$group->id][$resource['id']][$privilege['id']] = true;
                        }else{
                            $array[$group->id][$resource['id']][$privilege['id']] = false;
                        }

                    }
                }

            }

        }

        return $array;
    }

}