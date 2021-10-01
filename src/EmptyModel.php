<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class EmptyModel
 * Вспомогательный класс недостающих моделей
 *
 * @package DCFramework
 */
class EmptyModel{
    function __call($name, $arguments){
        return (count($arguments)>0) ? array_shift($arguments) : false;
    }
}