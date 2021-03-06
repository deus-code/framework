<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class AppConfig
 * Настройка приложения
 *
 * @package DCFramework
 * @property Settings $settings
 */
class AppConfig{
    public $settings;

    public function __construct(){
        $this->settings = Instance::getSettingsInstance();
    }

    public function setReleaseInit($releases=array()){
        foreach ($releases as $release=>$host){
            if($_SERVER['SERVER_NAME']==$host or $_SERVER['SERVER_ADDR']==$host or $_SERVER['REMOTE_ADDR']==$releases){
                Storage::$release=$release;
            }
        }
    }

    public function setCahceLiveTime($cacheLiveTime){
        Storage::$cacheLiveTime = $cacheLiveTime;
    }

    public function enableMinifyStyles(){
        Storage::$minifyStyles = true;
    }

    public function enableMinifyScripts(){
        Storage::$minifyScripts = true;
    }

    public function enableMinifyHtml(){
        Storage::$minifyHtml = true;
    }

    public function disableMinifyStyles(){
        Storage::$minifyStyles = false;
    }

    public function disableMinifyScripts(){
        Storage::$minifyScripts = false;
    }

    public function disableMinifyHtml(){
        Storage::$minifyHtml = false;
    }

    public function setRule($rule,$options=array()){
        Storage::$rules[$rule] = $options;
    }

    public function setMainTemplate($mainTemplate='index'){
        Storage::$mainTemplate = $mainTemplate;
    }

    public function setWebRoot($webRoot='/'){
        Storage::$webRoot = $webRoot;
    }

    public function setAssetsLink($name,$dir){
        Storage::$assetsLinks[md5($name)] = array(
            'name'=>$name,
            'dir'=>$dir,
        );
    }

    public function setAssetsDir($dir,$plugin='main'){
        Storage::$assetsDirs[$plugin] = $dir;
        $assets_url = ($plugin!='main') ? $plugin.'/assets' : 'assets';
        Storage::$assetsLinks[md5($assets_url)] = array(
            'name'=>$assets_url,
            'dir'=>$dir,
        );
    }
    
    public function debug(){
        Storage::$debug = true;
    }

    public function setDbDriver($driver){
        Storage::$dbDriver = $driver;
    }

    public function setDbName($name){
        Storage::$dbName = $name;
    }

    public function setDbHost($host){
        Storage::$dbHost = $host;
    }

    public function setDbUser($user){
        Storage::$dbUser = $user;
    }

    public function setDbPassword($password){
        Storage::$dbPassword = $password;
    }

    public function setDbTablePrefix($prefix){
        Storage::$dbTablePrefix = $prefix;
    }

    public function setDbCharset($charset){
        Storage::$dbCharset = $charset;
    }
    public function getWebRoot(){
        return Storage::$webRoot;
    }
}