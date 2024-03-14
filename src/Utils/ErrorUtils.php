<?php

namespace Jgu\RowVisibilityMapper\Utils;

class ErrorUtils {

    public static $_ERRORS = [        
        'COMMONS' => [
            'CONFIG_ERROR' => ['description' => 'Incorrect data configuration. [[description]]. Please contact system admin.', 'type' => 'config_error', 'code' => 'jgu/rvm/q/001', 'var' => ['description']],            
        ]        
    ];

    private static function renderError($masterType, $type, $vars = []){
        $error = self::$_ERRORS[$masterType][$type];
        foreach ($vars as $key => $val) {
            if( array_key_exists('var', $error) && in_array($key,$error['var'])){
                $error['description'] = str_replace('[[' . $key . ']]', $val, $error['description']);
            }
        }
        $error['error'] = true;
        return (object) $error;
    }

    public static function renderCreateError($type, $vars = []){
        return self::renderError('CREATE_BOOKING', $type, $vars);
    }

    public static function renderCancelError($type, $vars = []){
        return self::renderError('CANCEL_BOOKING', $type, $vars);
    }

    public static function renderCommonError($type, $vars = []){
        return self::renderError('COMMONS', $type, $vars);
    }

}