<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Storage
 * Хранилище параметров
 *
 * @package DCFramework
 */
class Storage{
    public static $appDir;
    public static $publicDir;
    public static $pluginsDir=false;
    public static $namespaceApp;
    public static $namespacePlugins;
    public static $frameworkTemplatesDir;
    public static $frameworkAssetsDir;
    public static $cacheLiveTime = '+2 day';
    public static $webRoot = '/';
    public static $release = 'publicRelease';
    public static $controllersFolder = 'controllers';
    public static $modelsFolder = 'models';
    public static $pluginsFolder = 'plugins';
    public static $templatesFolder = 'templates';
    public static $assetsDirs = array();
    public static $errors = array();
    public static $fatalError = array();
    public static $rules = array();
    public static $styles = array();
    public static $scripts = array();
    public static $assetsLinks = array();
    public static $minifyStyles = true;
    public static $minifyScripts = true;
    public static $minifyHtml = true;
    public static $debug = false;
    public static $pluginsList = array();
    /**
     * Запросы
     */
    public static $method;
    public static $requestUri;
    public static $serverIp;
    public static $userIp;
    public static $host;
    public static $url;
    public static $query = array();
    /**
     * Настройки базы данных
     */
    public static $dbDriver = 'mysql';
    public static $dbName = 'dc';
    public static $dbHost = 'localhost';
    public static $dbUser = 'root';
    public static $dbPassword = 'password';
    public static $dbTablePrefix = '';
    public static $dbCharset = 'utf8';
    /**
     * Настройки шаблона
     */
    public static $mainTemplate = 'index';
    public static $mainTemplatePlugin = false;
    public static $outputTemplate = 'html';
    public static $outputTemplatePlugin = false;
    public static $templateVars = array();
    public static $metaTitle = '';
    public static $metaDescription = '';
    public static $metaKeywords = array();
    public static $assetsTemplatesList = array();
    /**
     * Хранилище данных для использования в приложении
     */
    public static $settingsData = array();
}