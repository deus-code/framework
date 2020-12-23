<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Instance
 * Хранилище экземпляров классов
 *
 * @package DCFramework
 */
class Instance{
    public static $_model_instance = array();
    public static function getModelInstance($class,$plugin=false) {
        if($plugin){
            if (!array_key_exists($class.$plugin,self::$_model_instance)) {
                $class_name = '\\'.Storage::$namespacePlugins.'\\'.$plugin.'\\'.Storage::$modelsFolder.'\\'.$class.'Model';
                self::$_model_instance[$class.$plugin] = new $class_name();
            }
            return self::$_model_instance[$class.$plugin];
        }else{
            if (!array_key_exists($class,self::$_model_instance)) {
                $class_name = '\\'.Storage::$namespaceApp.'\\'.Storage::$modelsFolder.'\\'.$class.'Model';
                self::$_model_instance[$class] = new $class_name();
            }
            return self::$_model_instance[$class];
        }
    }

    public static $_session_instance = null;
    public static function getSessionInstance() {
        if (self::$_session_instance==null) {
            self::$_session_instance = new Session();
        }
        return self::$_session_instance;
    }

    public static $_popup_instance = null;
    public static function getPopupInstance() {
        if (self::$_popup_instance==null) {
            self::$_popup_instance = new Popup();
        }
        return self::$_popup_instance;
    }

    public static $_form_instance = null;
    public static function getFormInstance() {
        if (self::$_form_instance==null) {
            self::$_form_instance = new Form();
        }
        return self::$_form_instance;
    }

    public static $_query_instance = null;
    public static function getQueryInstance() {
        if (self::$_query_instance==null) {
            self::$_query_instance = new Query();
        }
        return self::$_query_instance;
    }

    public static $_template_instance = null;
    public static function getTemplateInstance() {
        if (self::$_template_instance==null) {
            self::$_template_instance = new Template();
        }
        return self::$_template_instance;
    }

    public static $_files_instance = null;
    public static function getFilesInstance() {
        if (self::$_files_instance==null) {
            self::$_files_instance = new Files();
        }
        return self::$_files_instance;
    }
}