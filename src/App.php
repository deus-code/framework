<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class App
 * Инициализация и запуск приложения
 *
 * @package DCFramework
 * @property AppConfig $configApp
 * @property ErrorHandler $errorHandler
 * @property Files $Files
 */
class App{
    private $configApp;
    private $errorHandler;
    private $status = 200;
    private $Files;

    function __construct($namespaceApp,$namespacePlugins='plugins'){
        if(!session_id()) session_start();
        $this->Files = new Files();
        Storage::$namespaceApp = $namespaceApp;
        Storage::$namespacePlugins = $namespacePlugins;
        Storage::$frameworkTemplatesDir = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
        Storage::$frameworkAssetsDir = __DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if(isset($backtrace[0]['file'])){
            Storage::$publicDir = dirname($backtrace[0]['file']) . DIRECTORY_SEPARATOR;
        }else{
            trigger_error('Unable to determine the path to public directory');
        }

        // инициализация обработчика ошибок
        $this->errorHandler = new ErrorHandler();
        $this->errorHandler->init();

        $this->initRequest();

        // подключение файла настроек
        $configAppClass = '\\'.Storage::$namespaceApp.'\\Config';
        $this->configApp = new $configAppClass();
        if(method_exists($this->configApp,Storage::$release)){
            $release = Storage::$release;
            $this->configApp->$release();
        }
        $reflection = new \ReflectionClass($configAppClass);
        Storage::$appDir = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR;

        // подключение настроек плагинов
        if(Storage::$pluginsDir==false) Storage::$pluginsDir = Storage::$appDir . DIRECTORY_SEPARATOR . Storage::$pluginsFolder . DIRECTORY_SEPARATOR;
        $plugins_folders = array_diff(scandir(Storage::$pluginsDir),array('..', '.'));
        foreach ($plugins_folders as $plugin) {
            $configPluginClass = '\\'.Storage::$namespacePlugins.'\\'.$plugin.'\\Config';
            new $configPluginClass();
            Storage::$pluginsList[] = $plugin;
        }

        if(count(Storage::$assetsLinks)>0){
            foreach (Storage::$assetsLinks as $item){
                Storage::$rules[$item['name'].'/{file:all}'] = array('symlink'=>$item['dir']);
            }
        }

        // заруск роутера
        $router = new Router();
        $result = $router->start();

        $render = true;
        if($result['status']==200){
            switch($result['type']){
                case 'redirect':
                    $this->redirect($result['redirect']);
                    $render = false;
                    break;
                case 'symlink':
                    $this->loadSymlink($result);
                    break;
                case 'controller':
                    $render = $this->loadController($result);
                    break;
            }
        }else{
            $this->setStatus(404);
        }
        if($render==true){
            $template = new TemplateEngine();
            switch ($this->status){
                case 404:
                    Storage::$metaTitle = '404 - Not found';
                    Storage::$mainTemplate = $this->status;
                    break;
                case 403:
                    Storage::$metaTitle = '403 - Forbidden';
                    Storage::$mainTemplate = $this->status;
                    break;
            }
            $template->render();
            $template->output();
        }
    }

    /**
     * Определение запроса пользователя
     */
    private function initRequest(){
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $path = null;
        if(isset($uri['path'])) $path = urldecode($uri['path']);
        Storage::$method = $_SERVER['REQUEST_METHOD'];
        Storage::$requestUri = $_SERVER['REQUEST_URI'];
        Storage::$serverIp = $_SERVER['SERVER_ADDR'];
        Storage::$userIp = $_SERVER['REMOTE_ADDR'];
        Storage::$host = $_SERVER['SERVER_NAME'];
        Storage::$url = $path;
        if(count($_REQUEST)>0){
            foreach ($_REQUEST as $key=>$request){
                Storage::$query[$key] = $request;
            }
        }
    }

    /**
     * Загрузка контроллера
     * @param $result
     * @return bool
     */
    private function loadController($result){
        $render = true;
        $result['controller'] = ucfirst($result['controller']);
        if($result['plugin']!=false){
            $controllerName = '\\'.Storage::$namespacePlugins.'\\'.$result['plugin'].'\\'.Storage::$controllersFolder.'\\'.$result['controller'].'Controller';
        }else{
            $controllerName = '\\'.Storage::$namespaceApp.'\\'.Storage::$controllersFolder.'\\'.$result['controller'].'Controller';
        }
        if(class_exists($controllerName)){
            $controller = new $controllerName();
            if(is_array($result['options']) and count($result['options'])>0){
                foreach ($result['options'] as $key=>$option){
                    Storage::$query[$key] = $option;
                }
            }
            $action = $result['action'];
            if(method_exists($controller,$action)){
                $controller->$action();
                $error = false;
                if(!empty($result['accessType'])){
                    if($controller->getType() != $result['accessType']){
                        $error = true;
                    }
                }
                if($error==false) {
                    $this->setStatus($controller->getStatus());
                    if($controller->is_redirect){
                        $this->redirect($controller->is_redirect);
                    }elseif($controller->is_reload){
                        $this->redirect(Storage::$requestUri);
                    }else{
                        switch($controller->getType()){
                            case 'json':
                                $this->setContentType('application/json');
                                $render = false;
                                $jsonData = array();
                                if(count(Storage::$errors)>0) $jsonData['dc_errors'] = Storage::$errors;
                                $controllerJsonData = $controller->getJsonData();
                                if(is_array($controllerJsonData)){
                                    foreach ($controllerJsonData as $key=>$item){
                                        $jsonData[$key] = $item;
                                    }
                                }
                                echo json_encode($jsonData);
                                break;
                            case 'html':
                                $this->setContentType('text/html');
                                break;
                        }
                    }
                }else{
                    $this->setStatus(404);
                }
            }else{
                $this->setStatus(404);
            }
        }else{
            $this->setStatus(404);
        }
        return $render;
    }

    private function loadSymlink($result){
        if(isset($result['options']['file'])){
            $result['options']['file'] = str_replace("/", DIRECTORY_SEPARATOR, $result['options']['file']);
            $file = $result['symlink'].$result['options']['file'];
            if(file_exists($file)){
                header('Content-type: '.$this->Files->getFileType($file).'; charset=utf-8');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                die;
            }else{
                $this->setStatus(404);
            }
        }else{
            $this->setStatus(404);
        }
    }

    private function setStatus($status){
        http_response_code($status);
        $this->status = $status;
    }

    private function setContentType($type){
        header('Content-type: '.$type.'; charset=utf-8');
    }

    private function redirect($link){
        if(!empty($link)){
            header('Location: '.$link);
            die();
        }
    }
}