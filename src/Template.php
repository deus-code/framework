<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Template
 * Управление шаблонами
 *
 * @package DCFramework
 */
class Template{
    public $pluginName = false;
    public function assign($key,$value){
        Storage::$templateVars[$key] = $value;
    }
    public function setMain($template,$plugin=false){
        if(!$plugin and $this->pluginName!=false) $plugin = $this->pluginName;
        Storage::$mainTemplate = $template;
        Storage::$mainTemplatePlugin = $plugin;
    }
    public function setOutput($template,$plugin=false){
        if(!$plugin and $this->pluginName!=false) $plugin = $this->pluginName;
        Storage::$outputTemplate = $template;
        Storage::$outputTemplatePlugin = $plugin;
    }
    public function setTitle($title){
        Storage::$metaTitle = $title;
    }
    public function setDescription($description){
        Storage::$metaDescription = $description;
    }
    public function setKeywords($keywords=array()){
        foreach ($keywords as $keyword){
            Storage::$metaKeywords[] = $keyword;
        }
    }

    public function exist($template,$plugin=false){
        if(!$plugin and $this->pluginName!=false) $plugin = $this->pluginName;
        $file = Storage::$frameworkTemplatesDir . $template . '.phtml';
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template . '.phtml';
        $result = false;
        if(file_exists($appMainFile)) $result = true;
        if($plugin!=false){
            $pluginMainFile = Storage::$pluginsDir . $plugin . DIRECTORY_SEPARATOR . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template .'.phtml';
            if(file_exists($pluginMainFile)) $file = $pluginMainFile;
        }
        if(file_exists($file)) $result = true;
        return $result;
    }

    public function getRender($template,$plugin=false){
        if(!$plugin and $this->pluginName!=false) $plugin = $this->pluginName;
        $temp = Storage::$mainTemplate;
        $temp_plugin = Storage::$mainTemplatePlugin;
        Storage::$mainTemplate = $template;
        Storage::$mainTemplatePlugin = $plugin;
        $templateClass = new TemplateEngine();
        $templateClass->render();
        $html = $templateClass->getRenderHtml();
        Storage::$mainTemplate = $temp;
        Storage::$mainTemplatePlugin = $temp_plugin;
        return $html;
    }

    function checkPlugin($pluginName){
        return (in_array($pluginName,Storage::$pluginsList)) ? true : false ;
    }
}