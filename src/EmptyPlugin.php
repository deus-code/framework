<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class EmptyPlugin
 * Вспомогательный класс недостающих плагинов
 *
 * @package DCFramework
 */
class EmptyPlugin{
    function __get($modelName){
        return new EmptyModel();
    }
}