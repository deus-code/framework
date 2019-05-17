<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Form
 *
 * @package DCFramework
 */
class Form{
    public function setSuccessMessage($message){
        Storage::$form = true;
        Storage::$formSuccessMessage = $message;
    }
    public function setErrorMessage($message){
        Storage::$form = true;
        Storage::$formErrorMessage = $message;
    }
    public function setSuccessCallback($callback_function){
        Storage::$form = true;
        Storage::$formSuccessCallback = $callback_function;
    }
}