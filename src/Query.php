<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Query
 * Класс для управления запросами
 *
 * @package DCFramework
 */
class Query {
    public function get($key){
        if(isset(Storage::$query[$key])){
            return Storage::$query[$key];
        }else{
            return null;
        }
    }

    public function exist($key){
        if(isset(Storage::$query[$key])){
            return true;
        }else{
            return false;
        }
    }
}