<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

/**
 * Class Cache
 * @package DCFramework
 * @property Files $Files
 */
class Cache{
	private $cache = null;
	private $Files;

	function __construct($file=null,$date_mode=false){
        $this->Files = new Files();
        $cache_dir = Storage::$publicDir.'cache'.DIRECTORY_SEPARATOR;
        $this->Files->makeDir($cache_dir);
        $this->cache = $cache_dir.$file;
        if($date_mode){
            $file_time = filemtime($this->cache);
            $next_time = strtotime(Storage::$cacheLiveTime,$file_time);
            if($file_time>time()){
                unlink($this->cache);
            }
        }
	}

	function exist(){
		if(file_exists($this->cache)){
			return true;
		}else{
			return false;
		}
	}

	function save($data){
        if(!file_exists($this->cache)){
            file_put_contents($this->cache, $data);
        }
	}

	function clear(){
        $this->Files->deleteDir(Storage::$publicDir.'cache'.DIRECTORY_SEPARATOR, true);
	}
}
