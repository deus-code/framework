<?php
/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace DCFramework;

use PDO;

/**
 * Class PDOext
 * PDO дополнение для записи логов и измерения скорости запросов
 *
 * @package DCFramework
 */
class PDOext extends PDO{
	public $table = null;
	public $driver = null;
	public $executeResult;
	public $executeData;

	public function query($sql,array $data = array()){

		$sql = parent::prepare($sql);

		$start = microtime(true);
		$this->executeResult = $sql->execute($data);
		$this->executeData = $data;
		$time = round(microtime(true)-$start,4);

		if($this->executeResult!=true){
			$errorInfo = $sql->errorInfo();
			trigger_error($this->driver." error: '".$errorInfo[2]."'");
		}

		return $sql;
	}
}