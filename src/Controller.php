<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Controller
 * Вспомогательный класс для контроллеров приложения
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
class Controller{
    private $status=200;
    private $type='html';
    private $jsonData;
    public $is_redirect = false;
    public $is_reload = false;
    public $mainTemplate;
    public $outputTemplate;
    public $session;
    public $popup;
    public $form;
    public $query;
    public $template;
    public $files;
    public $settings;

    public function __construct(){
        $this->session = Instance::getSessionInstance();
        $this->popup = Instance::getPopupInstance();
        $this->form = Instance::getFormInstance();
        $this->query = Instance::getQueryInstance();
        $this->template = Instance::getTemplateInstance();
        $this->files = Instance::getFilesInstance();
        $this->settings = Instance::getSettingsInstance();
    }
    
    function loadModel($modelName,$plugin=false){
        if(!isset($this->$modelName)) {
            $this->$modelName = Instance::getModelInstance($modelName,$plugin);
        }
    }

    function checkPlugin($pluginName){
        return (in_array($pluginName,Storage::$pluginsList)) ? true : false ;
    }

    public function getType(){
        return $this->type;
    }
    public function getStatus(){
        return $this->status;
    }
    public function setStatus($status){
        $this->status = $status;
    }
    public function error404(){
        $this->status=404;
    }
    public function error403(){
        $this->status=403;
    }
    public function isHtmlType(){
        $this->type='html';
    }
    public function isJsonType(){
        $this->type='json';
    }
    public function setJsonData($data){
        $this->jsonData = $data;
    }
    public function getJsonData(){
        return $this->jsonData;
    }
    public function redirect($link,$webroot=true){
        $this->setStatus(302);
        if($webroot){
            if($link!=Storage::$webRoot) $link = Storage::$webRoot.$link;
        }
        $this->is_redirect = $link;
    }
    public function redirectToHome(){
        $this->setStatus(302);
        $this->is_redirect = Storage::$webRoot;
    }
    public function reload(){
        $this->setStatus(302);
        $this->is_reload = true;
    }
    public function getWebRoot(){
        return Storage::$webRoot;
    }
}