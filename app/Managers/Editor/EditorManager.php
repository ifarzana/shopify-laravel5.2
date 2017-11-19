<?php

namespace App\Managers\Editor;

use App\Models\User;
use App\Models\Editor\EditorPlaceholder;

class EditorManager
{
    /**
     * Placeholders
     *
     * @var array
     */
    protected $placeholders = array();

    /**
     * Construct - set all arrays and objects
     *
     * @return void
     */
    public function __construct()
    {
        /*Placeholders*/
        $this->placeholders = EditorPlaceholder::all()->toArray();

        $placeholders = array();

        foreach ($this->placeholders as $placeholder) {
            $placeholders[$placeholder['key']] = $placeholder;
        }

        $this->placeholders = $placeholders;
    }

    /**
     * Returns the html template
     *
     * @param $content string
     * @param $User User
     *
     * @return string
     */
    public function render($content, $User)
    {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');

        $find = array();
        $replace_with = array();

        foreach ($this->placeholders as $key => $placeholder) {
            $find[] =  '{{'.$placeholder['display'].'}}';
            $replace_with[$key] = $this->findValue($User, $key);
        }

        $content = str_replace($find, $replace_with, $content);

        return $content;
    }


    /**
     * Returns the value of the placeholder
     *
     * @param $User User
     * @param $key string
     *
     * @return string
     */
    protected function findValue($User, $key)
    {
        $value = '';

        if(array_key_exists($key, $this->placeholders)) {

            $placeholder = $this->placeholders[$key];

            if($placeholder['function'] != null) {

                $function = $placeholder['function'];

                $value = $User->$function();

            }else{

                $field = $placeholder['field'];

                $value = $User->$field;
            }

        }

        return $value;

    }

}