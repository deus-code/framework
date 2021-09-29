<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Model
 * Вспомагательный класс для моделей
 *
 * @package DCFramework
 * @property Session $session
 * @property Popup $popup
 * @property Form $form
 * @property Query $query
 * @property Template $template
 * @property Files $files
 * @property Settings $settings
 */
class Model{
    public $session;
    public $popup;
    public $form;
    public $query;
    public $template;
    public $files;
    public $settings;
    public $pluginName = false;
    public $loadedPlugins = array();

    public function __construct(){
        preg_match('/'.Storage::$namespacePlugins.'(.*)'.Storage::$controllersFolder.'/', get_called_class(), $matches);
        if(isset($matches[1])) $this->pluginName = str_replace('\\','',$matches[1]);
        $this->session = Instance::getSessionInstance();
        $this->popup = Instance::getPopupInstance();
        $this->form = Instance::getFormInstance();
        $this->query = Instance::getQueryInstance();
        $this->template = Instance::getTemplateInstance();
        if($this->pluginName!=false) $this->template->pluginName = $this->pluginName;
        $this->files = Instance::getFilesInstance();
        $this->settings = Instance::getSettingsInstance();
    }

    public function table($table){
        $db = new Db();
        $db->connect(
            Storage::$dbName,
            Storage::$dbHost,
            Storage::$dbUser,
            Storage::$dbPassword,
            $table,
            Storage::$dbTablePrefix,
            Storage::$dbCharset,
            Storage::$dbDriver
        );
        return $db;
    }

    function plugin($pluginName){
        if($this->checkPlugin($pluginName)){
            if(array_key_exists($pluginName,$this->loadedPlugins)){
                return $this->loadedPlugins[$pluginName];
            }else{
                $this->loadedPlugins[$pluginName] = new Controller();
                $this->loadedPlugins[$pluginName]->pluginName = $pluginName;
                return $this->loadedPlugins[$pluginName];
            }
        }else{
            trigger_error('Plugin "'.$pluginName.'" not found',E_USER_ERROR);
            return $this; // для подсказок ide
        }
    }

    function __get($modelName){
        return Instance::getModelInstance($modelName,$this->pluginName);
    }

    function checkPlugin($pluginName){
        return (in_array($pluginName,Storage::$pluginsList)) ? true : false ;
    }

    public function getWebRoot(){
        return Storage::$webRoot;
    }
}