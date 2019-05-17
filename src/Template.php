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
    public function assign($key,$value){
        Storage::$templateVars[$key] = $value;
    }
    public function setMain($template){
        Storage::$mainTemplate = $template;
    }
    public function setOutput($template){
        Storage::$outputTemplate = $template;
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
    public function loadPackage($package){
        Storage::$loadPackages[] = $package;
    }

    public function exist($template){
        $file = Storage::$frameworkTemplatesDir . $template . '.phtml';
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template . '.phtml';
        $result = false;
        if(file_exists($appMainFile)) $result = true;
        if(file_exists($file)) $result = true;
        return $result;
    }

    public function getRender($template){
        $temp = Storage::$mainTemplate;
        Storage::$mainTemplate = $template;
        $templateClass = new TemplateEngine();
        $templateClass->render();
        $html = $templateClass->getRenderHtml();
        Storage::$mainTemplate = $temp;
        return $html;
    }

}