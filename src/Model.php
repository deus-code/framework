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
 */
class Model{
    public $session;
    public $popup;
    public $form;
    public $query;
    public $template;
    public $files;

    public function __construct(){
        $this->session = Instance::getSessionInstance();
        $this->popup = Instance::getPopupInstance();
        $this->form = Instance::getFormInstance();
        $this->query = Instance::getQueryInstance();
        $this->template = Instance::getTemplateInstance();
        $this->files = Instance::getFilesInstance();
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
	
    function loadModel($modelName,$plugin=false){
        if(!isset($this->$modelName)) {
            $this->$modelName = Instance::getModelInstance($modelName,$plugin);
        }
    }

    function checkPlugin($pluginName){
        return (in_array($pluginName,Storage::$pluginsList)) ? true : false ;
    }

    public function getWebRoot(){
        return Storage::$webRoot;
    }
}