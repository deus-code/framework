<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Settings
 * Хранилище данных для использования в приложении
 *
 * @package DCFramework
 */
class Settings{
    function set($key,$value){
        Storage::$settingsData[$key] = $value;
    }

    function get($key,$value=null){
        if(!empty($value)){
            if(isset(Storage::$settingsData[$key][$value])){
                return Storage::$settingsData[$key][$value];
            }else{
                return null;
            }
        }else{
            if(isset(Storage::$settingsData[$key])){
                return Storage::$settingsData[$key];
            }else{
                return null;
            }
        }
    }

    function exist($key,$value=null){
        if(!empty($value)){
            if(isset(Storage::$settingsData[$key][$value])){
                return true;
            }else{
                return false;
            }
        }else{
            if(isset(Storage::$settingsData[$key])){
                return true;
            }else{
                return false;
            }
        }
    }
}