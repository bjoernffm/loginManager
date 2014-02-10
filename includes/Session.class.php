<?

	namespace LoginManager;
	use \Encryption;
	
	require_once 'Base.class.php';
	require_once 'SessionException.class.php';
	require_once 'Encryption.class.php';
	
	class Session extends Base {
		
		private $id = null;
		private $key = null;
		private $ip = null;
		private $vars = array();
		
		const SESS_NAME = 'sec_sess';
		const SESS_LIFETIME = 3600; // one hour
				
		public function start() {
			$this->removeExpiredSessions();
			
			if (isset($_COOKIE[self::SESS_NAME]) and !empty($_COOKIE[self::SESS_NAME])) {
				try {
					$this->resume($_COOKIE[self::SESS_NAME]);
				} catch(SessionException $e) {
					$this->create();
				}	
			} else {
				$this->create();	
			}
		}
		
		public function create() {
			$mysqli = self::getMysqlConnection();
			
			while (true) {
				$key = self::generateKey();
				
				$result = $mysqli->query('SELECT
												`session_id`
											FROM
												`sessions`
											WHERE
												`session_key` =
												BINARY "' . $key . '"');
				
				if ($result->num_rows == 0)
					break;
			}
			
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
			
			$mysqli->query('INSERT INTO
								`sessions`
								(
									`session_key`,
									`session_ip`,
									`session_vars`,
									`session_start`,
									`session_last_used`
								) VALUES (
									"' . $key . '",
									"' . $ip . '",
									"' . Encryption::encrypt('[]') . '",
									NOW(),
									NOW()
								)');
			$id = $mysqli->insert_id;
			
			$this->id = $id;
			$this->key = $key;
			$this->ip = $ip;
			
			setcookie (self::SESS_NAME, $this->key, time()+self::SESS_LIFETIME, self::COOKIE_PATH);
			
		}
		
		public function resume($key) {
			$mysqli = self::getMysqlConnection();
			
			$key = $mysqli->real_escape_string($key);
			
			if (empty(trim($key)))
				throw new SessionException('No valid session key given. Key variable is empty');
			
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
			
			$result = $mysqli->query('SELECT
											`session_id`,
											`session_key`,
											`session_ip`,
											`session_vars`
										FROM
											`sessions`
										WHERE
											`session_key` = BINARY "' . $key . '" AND
											`session_ip` = ' . $ip . '
										LIMIT 1');
										
			if ($result->num_rows != 1)
				throw new SessionException('Session could not be found.');
			
			$sessionRow = $result->fetch_assoc();
			$this->id = $sessionRow['session_id'];
			$this->key = $sessionRow['session_key'];
			$this->ip = $sessionRow['session_ip'];
			
			$this->vars = json_decode(Encryption::decrypt($sessionRow['session_vars']), true);
			
			$mysqli->query('UPDATE
								`sessions`
							SET
								`session_last_used` = NOW()
							WHERE
								`session_id` = ' . $this->id . '
							LIMIT 1');
		}
		
		public function getVar($varKey = null) {
			if ($varKey === null) {
				return $this->vars;
			} else {
				if (!isset($this->vars[$varKey]))
					return null;
				
				return $this->vars[$varKey];
			}
		}
		
		public function setVar($varKey, $varValue) {
			if(empty(trim($varKey)))
				throw new SessionException('Variable key missing.');
			
			$this->vars[$varKey] = $varValue;
			$this->syncVars();			
		}
		
		public function unsetVar($varKey = null) {
			if ($varKey === null) {
				$this->vars = array();
			} else {
				unset($this->vars[$varKey]);
			}
			
			$this->syncVars();			
		}
		
		private function syncVars() {
			if (empty($this->id))
				throw new SessionException('No session initialzed.');
			
			$mysqli = self::getMysqlConnection();
			
			$vars = json_encode($this->vars);
			$vars = Encryption::encrypt($vars);
			
			$mysqli->query('UPDATE
								`sessions`
							SET
								`session_vars` = "' . $vars . '"
							WHERE
								`session_id` = ' . $this->id . '
							LIMIT 1');
		}

		private function removeExpiredSessions() {
			$mysqli = self::getMysqlConnection();
			
			$mysqli->query('DELETE FROM
								`sessions`
							WHERE
								`session_last_used` < DATE_SUB(
									NOW(),
									INTERVAL ' . self::SESS_LIFETIME . ' SECOND
								)');	
		}
		
		static function generateKey() {
			$characters = range(0, 9);
			$characters = array_merge($characters, range('A', 'Z'));
			$characters = array_merge($characters, range('a', 'z'));
			$key = array();
			for ($i = 0; $i < 32; $i++) {
				$key[] = $characters[rand(0, count($characters)-1)];
			}
			
			$key = implode('', $key);
			
			return $key;
		}
		
	}
	
?>