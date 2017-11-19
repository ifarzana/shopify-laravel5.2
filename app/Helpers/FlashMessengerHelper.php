<?php

namespace App\Helpers;
use Session;

class FlashMessengerHelper
{
    /**
     * The alerts classes
     */
    const ALERT_SUCCESS_CLASS = 'alert-success';
    const ALERT_ERROR_CLASS = 'alert-danger';
    const ALERT_WARNING_CLASS = 'alert-warning';
    const ALERT_INFO_CLASS = 'alert-info';

    /**
     * The alerts titles
     */
    const ALERT_SUCCESS_TITLE = 'Success';
    const ALERT_ERROR_TITLE = 'Error';
    const ALERT_WARNING_TITLE = 'Warning';
    const ALERT_INFO_TITLE = 'Info';

    /**
     * Create a success alert
     * 
     * @param $message string
     * @return bool
     */
    static function addSuccessMessage($message)
    {
        if(empty($message)) {
            return false;
        }
        
        Session::flash('message', $message);
        Session::flash('alert-title', self::ALERT_SUCCESS_TITLE);
        Session::flash('alert-class', self::ALERT_SUCCESS_CLASS);

        return true;
    }

    /**
     * Create an error alert
     *
     * @param $message string
     * @return bool
     */
    static function addErrorMessage($message)
    {
        if(empty($message)) {
            return false;
        }
        
        Session::flash('message', $message);
        Session::flash('alert-title', self::ALERT_ERROR_TITLE);
        Session::flash('alert-class', self::ALERT_ERROR_CLASS);

        return true;
    }

    /**
     * Create a warning alert
     *
     * @param $message string
     * @return bool
     */
    static function addWarningMessage($message)
    {
        if(empty($message)) {
            return false;
        }

        Session::flash('message', $message);
        Session::flash('alert-title', self::ALERT_WARNING_TITLE);
        Session::flash('alert-class', self::ALERT_WARNING_CLASS);

        return true;
    }

    /**
     * Create an info alert
     *
     * @param $message string
     * @return bool
     */
    static function addInfoMessage($message)
    {
        if(empty($message)) {
            return false;
        }
        
        Session::flash('message', $message);
        Session::flash('alert-title', self::ALERT_INFO_TITLE);
        Session::flash('alert-class', self::ALERT_INFO_CLASS);

        return true;
    }
    
}