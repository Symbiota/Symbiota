<?php
class QueryUtil {
	/*
	 * This function is a wrapper for mysqli_execute_query. Not all Symbiota portals can update version so this an effort to fix backward compat issues 
	 *
	 * @param mysqli $conn
	 * @param string $sql
	 * @param array $params
	 * @throws mysqli_sql_exception
	 */
	static function executeQuery(mysqli $conn, string $sql, array $params = []) {
		//This is supported from 4 to 8
		$version = phpversion();
		[$major, $minor, $patch] = explode('.', $version);

		if($major >= 8 && $minor >= 2) {
			$rs = mysqli_execute_query($conn, $sql, $params);
			if($conn->error) {
				throw new mysqli_sql_exception($conn->error);
			} else {
				return $rs;
			}
		} else {
			if(count($params)) {
				$bind_params_str = '';
				foreach($params as $param) {
					//Could just bind string instead?
					if(gettype($param) === 'string') {
						$bind_params_str .= 's';
					} else {
						$bind_params_str .= 'i';
					}
				}
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param($bind_params_str, ...$params);
					$stmt->execute();
					return $stmt->get_result();
				} else if($conn->error) {
					throw new mysqli_sql_exception($conn->error);
				} else {
					return false;
				}
			} else {
				$rs = mysqli_query($conn, $sql);
				if($conn->error) {
					throw new mysqli_sql_exception($conn->error);
				} else {
					return $rs;
				}
			}
		}
	}

	/*
	 * This function is a wrapper for executeQuery that automatically
	 * catches mysqli_execute_query and returns bool. Errors can
	 * Be accessed via the mysqli object.
	 *
	 * @param mysqli $conn
	 * @param string $sql
	 * @param array $params
	 */
	static function tryExecuteQuery(mysqli $conn, string $sql, array $params = []) {
		try {
			return self::executeQuery($conn, $sql, $params);
		} catch(mysqli_sql_exception $e) {
			return false;
		}
	}
}
?>
