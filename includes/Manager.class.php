<?

	namespace LoginManager;
	use \stdClass;
	use \Exception;
	use \Encryption;
	
	require_once 'Base.class.php';
	require_once 'Login.class.php';
	require_once 'Encryption.class.php';

	class Manager extends Base {
		
		private $userId = 0;
		const COOKIE_AUTOLOGIN = 'sec_atlg';
		
		public function __construct($userId) {
			$this->userId = (int) $userId;
		}

		public function addAutologin() {
			
			if ($this->userId <= 0)
				throw new Exception('No user initialized', 400);
			
			$token = self::generateRandomString(32);
			
			$mysqli = self::getMysqlConnection();
			
			$result = $mysqli->query('UPDATE
											`users`
										SET 
											`user_autologin_token` = "' . $token . '"
										WHERE
											`user_id` = ' . $this->userId . '
										LIMIT 1');
			if ($result === false)
				throw new Exception('Could not add login token: '.$mysqli->error, 500);
			
			setcookie (self::COOKIE_AUTOLOGIN, $token, time() + (86400 * 365), self::COOKIE_PATH);
		}
		
		/**
		 * This function adds a new user to the system.
		 * Usage:
		 * <code>
		 *   Manager::addUser(array(
		 *     'name' => 'Max Mustermann',
		 *     'email' => 'max@mustermann.de',
		 *     'login' => 'max',
		 *     'password' => 'secret',
		 *     'mailPassword' => true
		 *   ));
		 * </code>
		 */
		public static function addUser($params) {
			
			/**
			 * Check if given data is valid.
			 */
			if (!isset($params['name']) or trim($params['name']) == '')
				throw new Exception('Parameter "name" missing.');
			
			if (!isset($params['email']) or trim($params['email']) == '')
				throw new Exception('Parameter "email" missing.');
				
			if (!isset($params['login']) or trim($params['login']) == '')
				throw new Exception('Parameter "login" missing.');
				
			if (!isset($params['password']) or trim($params['password']) == '')
				$params['password'] = self::generateRandomString(12);
				
			if (!isset($params['mailPassword']))
				$params['mailPassword'] = false;
			
			/**
			 * Prepare given data.
			 */	
			$params['name'] = trim($params['name']);
			$params['email'] = trim($params['email']);
			$params['login'] = trim($params['login']);
			$params['password'] = trim($params['password']);
			
			$options = array(
				'cost' => 12,
				'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
			);
			$params['password'] = password_hash($params['password'], PASSWORD_BCRYPT, $options);
			
			$params['mailPassword'] = (bool) $params['mailPassword'];
			
			$mysqli = self::getMysqlConnection();
			
			$params['name'] = $mysqli->real_escape_string($params['name']);
			$params['email'] = $mysqli->real_escape_string($params['email']);
			$params['login'] = $mysqli->real_escape_string($params['login']);
			
			$result = $mysqli->query('INSERT INTO
										`users` (
											`user_name`,
											`user_email`,
											`user_login`,
											`user_password`
										) VALUES (
											"' . $params['name'] . '",
											"' . $params['email'] . '",
											"' . $params['login'] . '",
											"' . $params['password'] . '",
										)');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
			
			$id = $mysqli->insert_id;
			$mysqli->close();
			
			return $id;
			
		}
		
		public static function removeUser($userId) {
			
			$userId = (int) $userId;
			
			$mysqli = self::getMysqlConnection();
			
			$result = $mysqli->query('DELETE FROM
											`users`
										WHERE
											`user_id` = ' . $userId . '
										LIMIT 1');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
			
			$mysqli->close();
			
		}
		
		public static function updateUser($dataArray) {}
		
		public function removeAutologin() {
			
			if ($this->userId <= 0)
				throw new Exception('No user initialized', 400);
			
			$mysqli = self::getMysqlConnection();
			
			$result = $mysqli->query('UPDATE
											`users`
										SET 
											`user_autologin_token` = NULL
										WHERE
											`user_id` = ' . $this->userId . '
										LIMIT 1');
			if ($result === false)
				throw new Exception('Could not remove login token: '.$mysqli->error, 500);
			
			unset($_COOKIE[self::COOKIE_AUTOLOGIN]);
			setcookie(self::COOKIE_AUTOLOGIN, '', time() - 3600);
		}
		
		public function checkForAutologin() {
			$mysqli = self::getMysqlConnection();
			
			if (!isset($_COOKIE[self::COOKIE_AUTOLOGIN]))
				throw new Exception('No autologin token set', 202);
			
			$token = $mysqli->real_escape_string($_COOKIE[self::COOKIE_AUTOLOGIN]);
			
			$result = $mysqli->query('SELECT
											*
										FROM
											`users`
										WHERE
											`user_autologin_token` =  "' . $token . '"
										LIMIT 1');
			if ($result->num_rows != 1)
				throw new Exception('User not found', 404);
			
			$row = $result->fetch_assoc();
			return $row;
			
		}
		
		/**
		 * Checks the credetials of a user and returns an array if it was
		 * successful.
		 * 
		 * @param string The username.
		 * @param string The password.
		 * @return array
		 * @throws Exception
		 */
		public static function checkUserCredentials($login, $password) {

			$mysqli = self::getMysqlConnection();
			
			$login = $mysqli->real_escape_string($login);
			$password = $mysqli->real_escape_string($password);
			
			$result = $mysqli->query('SELECT
											*
										FROM
											`users`
										WHERE
											`user_login` =  "' . $login . '" AND
											`user_password` =  SHA1("' . $password . '")
										LIMIT 1');
			if ($result->num_rows != 1)
				throw new Exception('User not found', 404);
			
			$row = $result->fetch_assoc();
			return $row;
			
		}
		
		public function getPasswordById($id) {
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			
			$result = $mysqli->query('SELECT
											`login_password`
										FROM
											`logins`
										WHERE
											`login_id` = '.$id);
											
			$password = false;
			
			if ($row = $result->fetch_assoc()) {
				$password = trim(Encryption::decrypt($row['login_password']));
			}
			
			return $password;
			
		}
		
		public function getLogin($id) {
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			
			$result = $mysqli->query('SELECT
											*
										FROM
											`logins`
										WHERE
											`login_id` = ' .$id);
											
			if ($result->num_rows != 1)
				throw new Exception('Record not found.', 404);
									
			$row = $result->fetch_assoc();
					
			$login = new stdClass();
			$login->id = $row['login_id'];
			$login->user = $row['login_user'];
			$login->password = trim(Encryption::decrypt($row['login_password']));
			$login->location = $row['login_location'];
			$login->description = $row['login_description'];
				
			if (empty($row['login_tags'])) {
				$login->tags = array();
			} else {
				$login->tags = explode(',', $row['login_tags']);
			}
		
			return $login;	
		}
		
		public function getLoginList() {
			$mysqli = self::getMysqlConnection();
			
			$result = $mysqli->query('SELECT
											`logins`.*,
											`user_logins`.`type`
										FROM
											`user_logins`
										LEFT JOIN
											`logins`
										ON
											`logins`.`login_id` = `user_logins`.`login_id`
										WHERE
											`user_id` = '.$this->userId);
											
			$logins = array();
			while ($row = $result->fetch_assoc()) {
				$object = new stdClass();
				$object->id = $row['login_id'];
				$object->user = $row['login_user'];
				$object->password = $row['login_password'];
				$object->location = $row['login_location'];
				$object->description = $row['login_description'];
				
				if (empty($row['login_tags'])) {
					$object->tags = array();
				} else {
					$object->tags = explode(',', $row['login_tags']);
				}
				
				$object->type = $row['type'];
				
				$logins[] = $object;
			}
			
			return $logins;
		}	
		
		public function getLoginListByKeywords($keywords) {
			
			if (empty(trim($keywords)))
				return array();
			
			$mysqli = self::getMysqlConnection();
			
			$keywords = explode(' ', $keywords);
			$prepared = array();
			foreach ($keywords as $keyword) {
				$keyword = $mysqli->real_escape_string($keyword);
				$prepared[] = '`login_user` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_location` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_description` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_tags` LIKE "%' . $keyword . '%"';
			}
			
			$result = $mysqli->query('SELECT
											`logins`.*,
											`user_logins`.`type`
										FROM
											`user_logins`
										LEFT JOIN
											`logins`
										ON
											`logins`.`login_id` = `user_logins`.`login_id`
										WHERE
											`user_id` = ' . $this->userId . ' AND
											(
												' . implode(' OR ', $prepared) . '	
											)');
											
			$logins = array();
			while ($row = $result->fetch_assoc()) {
				$object = new stdClass();
				$object->id = $row['login_id'];
				$object->user = $row['login_user'];
				$object->password = $row['login_password'];
				$object->location = $row['login_location'];
				$object->description = $row['login_description'];
				
				if (empty($row['login_tags'])) {
					$object->tags = array();
				} else {
					$object->tags = explode(',', $row['login_tags']);
				}
				
				$object->type = $row['type'];
				
				$logins[] = $object;
			}
			
			return $logins;
		}

		public function addLogin($array) {
			
			if (!isset($array['user']) or empty(trim($array['user'])))
				throw new Exception('Missing user element.', 400);
				
			if (!isset($array['password']) or empty(trim($array['password'])))
				throw new Exception('Missing password element.', 400);
			
			$user = $array['user'];
			$password = Encryption::encrypt($array['password']);
			$location = $array['location'];
			$description = $array['description'];
			$tags = $array['tags'];
			
			$mysqli = self::getMysqlConnection();
			
			$user = $mysqli->real_escape_string($user);
			$password = $mysqli->real_escape_string($password);
			$location = $mysqli->real_escape_string($location);
			$description = $mysqli->real_escape_string($description);
			$tags = $mysqli->real_escape_string($tags);
			
			$result = $mysqli->query('INSERT INTO
										`logins`
										(
											`login_user`,
											`login_password`,
											`login_location`,
											`login_description`,
											`login_tags`
										) VALUES (
											"' . $user . '",
											"' . $password . '",
											"' . $location . '",
											"' . $description . '",
											"' . $tags . '"
										)');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
								
			$id = $mysqli->insert_id;
			
			$result = $mysqli->query('INSERT INTO
										`user_logins`
										(
											`user_id`,
											`login_id`,
											`type`
										) VALUES (
											' . $this->userId . ',
											' . $id . ',
											"OWNED"
										)');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
		}	

		public function editLogin($array) {
			
			if (0 >= (int) $array['id'])
				throw new Exception('Missing id element.', 400);
			
			if (!isset($array['user']) or empty(trim($array['user'])))
				throw new Exception('Missing user element.', 400);
				
			if (!isset($array['password']) or empty(trim($array['password'])))
				throw new Exception('Missing password element.', 400);
				
			$id = $array['id'];
			$user = $array['user'];
			$password = Encryption::encrypt($array['password']);
			$location = $array['location'];
			$description = $array['description'];
			$tags = $array['tags'];
			
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			$user = $mysqli->real_escape_string($user);
			$password = $mysqli->real_escape_string($password);
			$location = $mysqli->real_escape_string($location);
			$description = $mysqli->real_escape_string($description);
			$tags = $mysqli->real_escape_string($tags);
			
			$result = $mysqli->query('UPDATE
											`logins`
										SET
											`login_user` = "' . $user . '",
											`login_password` = "' . $password . '",
											`login_location` = "' . $location . '",
											`login_description` = "' . $description . '",
											`login_tags` = "' . $tags . '"
										WHERE
											`login_id` = ' . $id . '
										LIMIT 1');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
		}

		public function removeLogin($id) {
			
			$id = (int) $id;
			
			if ($id <= 0)
				throw new Exception('Invalid id given. " ' .$id .  '"', 400);
			
			$mysqli = self::getMysqlConnection();

			$result = $mysqli->query('DELETE FROM
											`logins`
										WHERE
											`login_id` = ' . $id . '
										LIMIT 1');
			if ($result === false)
				throw new Exception($mysqli->error, 500);

			$result = $mysqli->query('DELETE FROM
											`user_logins`
										WHERE
											`login_id` = ' . $id);
			if ($result === false)
				throw new Exception($mysqli->error, 500);
		}	
	}

?>