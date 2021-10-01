<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class LoadedPlugin
 * Вспомогательный класс подключения моделей плагина
 *
 * @package DCFramework
 */
class LoadedPlugin{
    public $pluginName = false;
    function __get($modelName){
        return Instance::getModelInstance($modelName,$this->pluginName);
    }
}