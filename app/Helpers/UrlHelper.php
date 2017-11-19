<?php

namespace App\Helpers;

use Config;

class UrlHelper
{
    /**
     * Get the keys from the config to include in the route details
     *
     * @return array
     */
    static function getKeysToInclude()
    {
        $keys = Config::get('url');

        return $keys['include'];
    }

    /**
     * Get the keys from the config to exclude from the route details
     *
     * @return array
     */
    static function getKeysToExclude()
    {
        $keys = Config::get('url');

        return $keys['exclude'];
    }
    
    /**
     * Set the correct array for the pagination
     *
     * @param $array array
     * @return array
     */
    static function setArray($array = array())
    {
        $request = request();
        
        foreach ($array as $key => $value) {

            if( $request->get($key) != null ) {
                $array[$key] = htmlspecialchars($request->get($key));
            }else {
                if(!empty($value)) {
                    $array[$key] = htmlspecialchars($value);
                }else {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    /**
     * Return a url based on the params - used for pagination
     *
     * @param $controller string
     * @param $action string
     * @param $paginationData array
     * @param $params array
     * @param $revert_order bool
     * @return string
     */
    static function getUrl($controller, $action, $paginationData = array(), $params = array(), $revert_order = false)
    {
        /*Pagination*/
        foreach ($paginationData as $pDataKey => $pData) {
            if( (empty($pData)) OR ($pData == null) ) {
                unset($paginationData[$pDataKey]);
            }
        }

        /*Revert order*/
        if($revert_order == true) {
            $paginationData['order'] = $paginationData['order'] == 'ASC' ? 'DESC' : 'ASC';
        }

        /*Params*/
        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $paginationData[$key] = $value;
            }
        }

        /*Exclude params*/
        foreach (self::getKeysToExclude() as $keyToExclude) {
            unset($paginationData[$keyToExclude]);
        }

        /*Url*/
        $url = action($controller.'@'.$action, $paginationData);

        return $url;
    }

    /**
     * Return an array with the route details (controller , action etc.)
     *
     * @return array
     */
    static function getRouteDetails()
    {
        $request = request();

        $routeAction = $request->route()->getAction();

        $namespace = $routeAction['namespace'];
        $fullController = $routeAction['controller'];
        
        $fullController = substr(str_replace($namespace, '', $fullController), 1);
        $fullController = explode('@', $fullController);
        
        $controller = $fullController[0];
        $action = $fullController[1];
        
        $array = array(
            'controller' => $controller,
            'action'     => $action
        );

        /*Keys to include*/
        foreach (self::getKeysToInclude() as $key) {
            if( $request->get($key) != null ) {
                $array[$key] = $request->get($key);
            }
        }
        

        return $array;
    }
        
    
}