<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class TemplateEngine
 * Шаблонизатор
 *
 * @package DCFramework
 * @property \DCFramework\Minify $minify
 * @property Session $session
 * @property Query $query
 * @property Settings $settings
 */
class TemplateEngine{
	private $minify;
    private $vars = array();
	private $html = '';
    public $session;
    public $query;
    public $settings;

	function __construct(){
        $this->session = Instance::getSessionInstance();
        $this->vars = Storage::$templateVars;
        $this->minify = new Minify();
        $this->query = Instance::getQueryInstance();
        $this->settings = Instance::getSettingsInstance();
    }

    public function render(){
        $mainTemplate = Storage::$mainTemplate;
        if(count(Storage::$fatalError)>0){
            $mainTemplate = 'dc-fatal-error';
        }
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $mainTemplate .'.phtml';
        $mainFile = Storage::$frameworkTemplatesDir . $mainTemplate .'.phtml';
        if(file_exists($appMainFile)) $mainFile = $appMainFile;
        if(Storage::$mainTemplatePlugin!=false){
            $pluginMainFile = Storage::$pluginsDir . Storage::$mainTemplatePlugin . DIRECTORY_SEPARATOR . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $mainTemplate .'.phtml';
            if(file_exists($pluginMainFile)) $mainFile = $pluginMainFile;
        }
        if(file_exists($mainFile)){
            ob_start();
            require $mainFile;
            $this->html = ob_get_clean();
        }
	}

	public function getRenderHtml(){
	    return $this->html;
    }

	public function output(){
		ob_start();
        $appHtmlFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . Storage::$outputTemplate .'.phtml';
        $dcHtmlFile = Storage::$frameworkTemplatesDir . Storage::$outputTemplate .'.phtml';
        $pluginHtmlFile = false;
        if(Storage::$outputTemplatePlugin!=false){
            $pluginHtmlFile = Storage::$pluginsDir . Storage::$outputTemplatePlugin . DIRECTORY_SEPARATOR . Storage::$templatesFolder . DIRECTORY_SEPARATOR . Storage::$outputTemplate .'.phtml';
        }
        if(file_exists($appHtmlFile)){
            require $appHtmlFile;
        }elseif($pluginHtmlFile!=false and file_exists($pluginHtmlFile)){
            require $pluginHtmlFile;
        }elseif(file_exists($dcHtmlFile)){
            require $dcHtmlFile;
        }
		$html = ob_get_clean();
		if(Storage::$minifyHtml) $html = $this->minify->html($html);
		echo $html;
	}

	public function loadTemplate($template,$vars=array(),$plugin=false){
	    if(is_array($vars)){
	        foreach ($vars as $key=>$var){
                $this->vars[$key]=$var;
            }
        }
		$file = Storage::$frameworkTemplatesDir . $template . '.phtml';
        $appMainFile = Storage::$appDir . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template . '.phtml';
        if(file_exists($appMainFile)) $file = $appMainFile;
        if($plugin!=false){
            $pluginMainFile = Storage::$pluginsDir . $plugin . DIRECTORY_SEPARATOR . Storage::$templatesFolder . DIRECTORY_SEPARATOR . $template .'.phtml';
            if(file_exists($pluginMainFile)) $file = $pluginMainFile;
        }
		if(file_exists($file)) require $file;
	}

	public function existTemplate($template,$plugin=false){
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

	public function get($key){
		if(array_key_exists($key,$this->vars)) {
			return $this->vars[$key];
		}else{
			return false;
		}
	}

	public function frameworkBody(){
        echo $this->html;
	}

	public function frameworkHeader(){
	    $meta = "\t".'<meta charset="utf-8" />'."\n";
	    $meta .= "\t".'<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
	    $meta .= "\t".'<title>'.Storage::$metaTitle.'</title>'."\n";
	    if(strlen(Storage::$metaDescription)>0){
            $meta .= "\t".'<meta name="description" content="'.Storage::$metaDescription.'" />'."\n";
        }
	    if(count(Storage::$metaKeywords)>0){
            $meta .= "\t".'<meta name="keywords" content="'.implode(', ',Storage::$metaKeywords).'" />'."\n";
        }
	    echo $meta;
	    $this->compileStyles();
	}

	public function frameworkFooter(){
		$dir = Storage::$frameworkTemplatesDir;
		if(Storage::$debug){
            $this->loadTemplate('dc-debug');
        }
        $this->compileScripts();
	}

    public function getWebRoot(){
        return Storage::$webRoot;
    }

    public function setStyle($file,$plugin='main'){
	    $template = false;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if(isset($backtrace[0]['file'])) $template = $backtrace[0]['file'];
        if($template) Storage::$assetsTemplatesList[$template] = $template;
	    $dir = (isset(Storage::$assetsDirs[$plugin])) ? Storage::$assetsDirs[$plugin] : '';
        $file = $dir.$file;
        if(file_exists($file)){
            Storage::$styles[md5($file)] = array(
                'file'=>$file,
                'version'=>filemtime($file),
            );
        }
    }

    public function setScript($file,$plugin='main'){
	    $template = false;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if(isset($backtrace[0]['file'])) $template = $backtrace[0]['file'];
        if($template) Storage::$assetsTemplatesList[$template] = $template;
	    $dir = (isset(Storage::$assetsDirs[$plugin])) ? Storage::$assetsDirs[$plugin] : '';
        $file = $dir.$file;
        if(file_exists($file)){
            Storage::$scripts[md5($file)] = array(
                'file'=>$file,
                'version'=>filemtime($file),
                'type'=>'file',
            );
        }
    }

    public function setScriptCDN($url){
	    $template = false;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
        if(isset($backtrace[0]['file'])) $template = $backtrace[0]['file'];
        if($template) Storage::$assetsTemplatesList[$template] = $template;
        Storage::$scripts[md5('cdn:'.$url)] = array(
            'file'=>$url,
            'version'=>'1.0',
            'type'=>'cdn',
        );
    }

    private function compileStyles(){
	    $result = '';
        if(is_array(Storage::$styles)){
            $f = Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'popup.less';
            array_unshift(Storage::$styles, array( 'file'=>$f, 'version'=>filemtime($f)));
            if(Storage::$debug){
                $f = Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'debug.less';
                array_unshift(Storage::$styles, array( 'file'=>$f, 'version'=>filemtime($f)));
            }
            $f = Storage::$frameworkAssetsDir.'less'.DIRECTORY_SEPARATOR.'framework.less';
            array_unshift(Storage::$styles, array( 'file'=>$f, 'version'=>filemtime($f)));

            $hash_pack = md5(serialize(Storage::$assetsTemplatesList));
            $hash = md5(serialize(Storage::$styles));
            $name = $hash_pack.'_'.$hash . '.css';
            $cache = new Cache($name);
            if (!$cache->exist()) {
                $style_min = '';
                $less_content = "@WEB_ROOT: '".Storage::$webRoot."';\n";
                foreach (Storage::$styles as $item){
                    if(isset($item['file']) and isset($item['version'])){
                        $file = $item['file'];
                        $version = $item['version'];
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $style_content = file_get_contents($file);
                        switch (strtolower($ext)){
                            case 'less':
                                $less_content .= $style_content . "\n";
                                break;
                            default:
                                $style_min .= $style_content . "\n";
                                break;
                        }
                    }
                }
                if(strlen($less_content)>0){
                    $less = new \lessc();
                    try {
                        $less_content = $less->compile($less_content);
                    } catch (\Exception $e) {
                        trigger_error('Error Less gen: '. $e->getMessage());
                    }
                    $style_min .= $less_content . "\n";
                }
                array_map("unlink", glob( Storage::$publicDir . 'cache' . DIRECTORY_SEPARATOR . $hash_pack . '_*.css'));
                if(Storage::$minifyStyles and strlen($style_min)>0) $style_min = $this->minify->css($style_min);
                $cache->save($style_min);
            }
            $result .= "\t".'<link rel="stylesheet" href="' . Storage::$webRoot . 'cache/' . $name . '" />'."\n";
        }
        echo $result;
    }

	private function compileScripts(){
	    $result = '';
        if(is_array(Storage::$scripts) and count(Storage::$scripts)>0){
            $f = Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'framework.js';
            array_unshift(Storage::$scripts, array( 'file'=>$f, 'version'=>filemtime($f), 'type'=>'file'));
            $f = Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery.maskedinput.min.js';
            array_unshift(Storage::$scripts, array( 'file'=>$f, 'version'=>filemtime($f), 'type'=>'file'));
            $f = Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'validate_ru.js';
            array_unshift(Storage::$scripts, array( 'file'=>$f, 'version'=>filemtime($f), 'type'=>'file'));
            $f = Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery.validate.min.js';
            array_unshift(Storage::$scripts, array( 'file'=>$f, 'version'=>filemtime($f), 'type'=>'file'));
            $f = Storage::$frameworkAssetsDir.'js'.DIRECTORY_SEPARATOR.'jquery-3.3.1.min.js';
            array_unshift(Storage::$scripts, array( 'file'=>$f, 'version'=>filemtime($f), 'type'=>'file'));

            foreach (Storage::$scripts as $key=>$item){
                if(isset($item['type']) and $item['type']=='cdn'){
                    $result .= "\t".'<script src="' . $item['file'] . '"></script>'."\n";
                    unset(Storage::$scripts[$key]);
                }
            }
            $hash_pack = md5(serialize(Storage::$assetsTemplatesList));
            $hash = md5(serialize(Storage::$scripts));
            $name = $hash_pack . '_' . $hash . '.js';
            $cache = new Cache($name);
            if (!$cache->exist()) {
                $script_min = "var WEB_ROOT = '".Storage::$webRoot."';\n";
                foreach (Storage::$scripts as $item){
                    $file = $item['file'];
                    $version = $item['version'];
                    $script_content = file_get_contents($file);
                    $script_min .= $script_content . "\n";
                }
                array_map("unlink", glob( Storage::$publicDir . 'cache' . DIRECTORY_SEPARATOR . $hash_pack . '_*.js'));
                if(Storage::$minifyScripts and strlen($script_min)>0) $script_min = \JsMin\Minify::minify($script_min);
                $cache->save($script_min);
            }
            $result .= "\t".'<script src="' . Storage::$webRoot . 'cache/' . $name . '"></script>'."\n";
        }
        echo $result;
    }
}