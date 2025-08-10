<?php
class MySQLiConnectionFactory {
	static $SERVERS = array(
		array(
			'type' => 'readonly',
			'host' => 'localhost',
			'username' => '',
			'password' => '',
			'database' => '',
			'port' => '3306',
			'charset' => 'utf8'		//utf8, latin1, latin2, etc
		),
		array(
			'type' => 'write',
			'host' => 'localhost',
			'username' => '',
			'password' => '',
			'database' => '',
			'port' => '3306',
			'charset' => 'utf8'
		)
	);

	/**
	 * Symbiota assumes the following, which differ from the default in modern MySQL versions:
	 * - NO_ZERO_IN_DATE disabled, Symbiota relies on zero month/day semantics: https://github.com/Symbiota/Symbiota/issues/130
	 * - ONLY_FULL_GROUP_BY disabled, Symbiota has many queries which don't conform to this requirement.
	 */
	static $SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

	public static function getCon($type) {
		// Disable MYSQLI_REPORT_STRICT, which is the default in PHP 8.1+.
		// Symbiota checks boolean result status instead of catching exceptions, so it's not compatible with the new default
		mysqli_report(MYSQLI_REPORT_ERROR);

		// Figure out which connections are open, automatically opening any connections
		// which are failed or not yet opened but can be (re)established.
		for ($i = 0, $n = count(MySQLiConnectionFactory::$SERVERS); $i < $n; $i++) {
			$server = MySQLiConnectionFactory::$SERVERS[$i];
			if($server['type'] == $type){
				try{
					$connection = new mysqli($server['host'], $server['username'], $server['password'], $server['database'], $server['port']);
					if(isset($server['charset']) && $server['charset']) {
						if(!$connection->set_charset($server['charset'])){
							throw new Exception('Error loading character set '.$server['charset'].': '.$connection->error);
						}
					}

					$connection->query("SET SESSION sql_mode = '" . MySQLiConnectionFactory::$SQL_MODE . "'");
					return $connection;
				}
				catch(Exception $e){
					echo $e->getMessage();
					return null;
				}
			}
		}
	}
}
?>