<?php

namespace App\Managers\Navigation;

use App\Helpers\UrlHelper;
use App\Models\Acl\Page;
use App\Models\Acl\Acl;
use App\Managers\Acl\AclManager;
use Auth;

class NavigationManager
{
    /**
     * ACL Manager
     *
     * @var object
     */
    protected $aclManager;

    /**
     * Pages
     *
     * @var array
     */
    protected $pages;

    /**
     * The logged in user
     *
     * @var object User
     */
    protected $user;

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*ACL Manager*/
        $this->aclManager = new AclManager();

        /*Pages*/
        $this->pages = Page::orderBy('order', 'ASC')->get();

        /*User*/
        $this->user = Auth::User();
    }

    /**
     * Return the navigation array + pages
     *
     * @param $route_name string
     * @param $excludeHiddenFromNavigation bool
     * @param $check_route bool 
     * 
     * @return array
     */
    public function getNavigation($route_name, $check_route = true, $excludeHiddenFromNavigation = false)
    {
        $navigation = array();

        $results = Acl::getByUserGroupId($this->user->group_id, false, $excludeHiddenFromNavigation);

        $allPermissions = $this->aclManager->getAllPermissions($this->user->group_id);

        foreach ($results as $result)
        {
            /*BEGIN CHECK FOR RESOURCES WIN HIDDEN NAVIGATION ACTIVE*/
            if($check_route == true) {
                if( ($result->route == $route_name) AND ($result->hidden_navigation == 1) ) {
                    return $navigation;
                }   
            }
            /*END CHECK FOR RESOURCES WIN HIDDEN NAVIGATION ACTIVE*/

            $pages = $this->getPages();

            $pages_array = array();

            if(isset($pages[$result->resource_id])) {

                foreach ($pages[$result->resource_id] as $page) {

                    $page['visible'] = false;
                    
                    $page['active'] = false;

                    if($allPermissions[$this->user->group_id][$page['resource_id']][$page['privilege_id']] == true) {
                        $page['visible'] = true;
                    }

                    $route_details = UrlHelper::getRouteDetails();

                    if( ($route_details['controller'] == $page['controller']) AND ($route_details['action'] == $page['action']) ) {
                        $page['active'] = true;
                    }

                    $pages_array[] = $page;
                }

            }

            $active = false;

            if($result->route == $route_name) {
                $active = true;
            }

            $configuration_menu = false;

            if($result->configuration_menu == 1) {
                $configuration_menu = true;
            }

            $navigation[] = array(
                'icon'  => $result->icon,
                'title' => $result->label,
                'route' => $result->route,
                'active' => $active,
                'configuration_menu' => $configuration_menu,
                'pages' => $pages_array
            );
        }

        return $navigation;
    }

    /**
     * Return an array of all pages
     *
     * @return array
     */
    protected function getPages()
    {
        $array = array();

        foreach ($this->pages as $page){
            $array[$page->resource_id][] = array(
                'icon' => $page->icon,
                'title' => $page->name,
                'controller' => $page->controller,
                'action' => $page->action,
                'resource_id' => $page->resource_id,
                'privilege_id' => $page->privilege_id
            );
        }

        return $array;
    }

}