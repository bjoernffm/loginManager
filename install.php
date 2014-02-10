<?
	
	use LoginManager\Session;
	
	require_once 'includes/Session.class.php';
	require_once 'version.inc.php';

	$modules = array();
	$errorsModules = array();
	$errorsMysqli = array();
	
	/**
	 * Check php version
	 */
	if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		$modules['php_version'] = array(
			'class' => 'text-success',
			'icon' => 'ok'
		);
	} else {
		$modules['php_version'] = array(
			'class' => 'text-danger',
			'icon' => 'remove'
		);
		$errorsModules[] = 'PHP Version 5.3 is required';
	}
	
	/**
	 * Check if mysqli extension is installed
	 */
	if (function_exists('mysqli_connect')) {
		$modules['mysqli_ext'] = array(
			'class' => 'text-success',
			'icon' => 'ok'
		);
	} else {
		$modules['mysqli_ext'] = array(
			'class' => 'text-danger',
			'icon' => 'remove'
		);
		$errorsModules[] = 'MySQLi Extension has to be enabled';
	}
	
	/**
	 * Check if mcrypt extension is installed
	 */
	if (function_exists('mcrypt_module_open')) {
		$modules['mcrypt_ext'] = array(
			'class' => 'text-success',
			'icon' => 'ok'
		);
	} else {
		$modules['mcrypt_ext'] = array(
			'class' => 'text-danger',
			'icon' => 'remove'
		);
		$errorsModules[] = 'Mcrypt Extension has to be enabled';
	}
	
	/**
	 * Check if password functions is installed
	 */
	if (function_exists('password_hash')) {
		$modules['password_ext'] = array(
			'class' => 'text-success',
			'icon' => 'ok'
		);
	} else {
		$modules['password_ext'] = array(
			'class' => 'text-danger',
			'icon' => 'remove'
		);
		$errorsModules[] = 'Password has/verfy functions not found - see <a href="http://de2.php.net/manual/de/ref.password.php" target="_blank">php.net</a>';
	}
	
	/**
	 * Check if config.inc.php is writable
	 */
	if (is_writable('config.inc.php')) {
		$modules['writable'] = array(
			'class' => 'text-success',
			'icon' => 'ok'
		);
	} else {
		$modules['writable'] = array(
			'class' => 'text-danger',
			'icon' => 'remove'
		);
		$errorsModules[] = 'config.inc.php is not writable';
	}

	$installAvaialable = (count($errorsModules)==0);
	
	if (isset($_POST['submitted']) and $_POST['submitted'] == 'true') {
		
		@$mysqli = new mysqli(
			$_POST['inputHost'],
			$_POST['inputUsername'],
			$_POST['inputPassword'],
			$_POST['inputDatabase']);
	
		/* check connection */
			if ($mysqli->connect_errno)
			$errorsMysqli[] = $mysqli->connect_error;
		
		/* check if server is alive */
		if (@$mysqli->ping() === false)
			$errorsMysqli[] = $mysqli->error;
			
		$query = '
	CREATE TABLE IF NOT EXISTS `devices` (
	  `device_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `device_token` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
	  `user_id` int(10) unsigned NOT NULL,
	  PRIMARY KEY (`device_id`),
	  UNIQUE KEY `device_token` (`device_token`),
	  KEY `user_id` (`user_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	
	CREATE TABLE IF NOT EXISTS `logins` (
	  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `login_user` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	  `login_password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
	  `login_location` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	  `login_description` text COLLATE utf8_unicode_ci NOT NULL,
	  `login_tags` text COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`login_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	
	CREATE TABLE IF NOT EXISTS `login_tags` (
	  `login_id` int(10) unsigned NOT NULL,
	  `tag_id` int(10) unsigned NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	
	CREATE TABLE IF NOT EXISTS `sessions` (
	  `session_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `session_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	  `session_ip` int(10) unsigned NOT NULL,
	  `session_vars` text COLLATE utf8_unicode_ci NOT NULL,
	  `session_start` datetime NOT NULL,
	  `session_last_used` datetime NOT NULL,
	  PRIMARY KEY (`session_id`),
	  UNIQUE KEY `session_key` (`session_key`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	
	CREATE TABLE IF NOT EXISTS `tags` (
	  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `tag_value` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`tag_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
	
	CREATE TABLE IF NOT EXISTS `users` (
	  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `user_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	  `user_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	  `user_login` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	  `user_password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	  `user_autologin_token` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
	  PRIMARY KEY (`user_id`),
	  UNIQUE KEY `user_autologin_token` (`user_autologin_token`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
	
	CREATE TABLE IF NOT EXISTS `user_logins` (
	  `user_id` int(10) unsigned NOT NULL,
	  `login_id` int(10) unsigned NOT NULL,
	  `type` set("OWNED","SHARED") COLLATE utf8_unicode_ci NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
		
		@$mysqli->multi_query($query);
		@$mysqli->close();
		
		$content = 
	"<?
		define('MYSQLI_USER'    , '" . $_POST['inputUsername'] . "');
		define('MYSQLI_PASSWORD', '" . $_POST['inputPassword'] . "');
		define('MYSQLI_HOST'    , '" . $_POST['inputHost'] . "');
		define('MYSQLI_DATABASE', '" . $_POST['inputDatabase'] . "');
		define('APP_SECRET'     , '" . Session::generateKey() . "');
	?>";
		file_put_contents('config.inc.php', $content, LOCK_EX);
		
		if (count($errorsMysqli) == 0 and count($errorsModules) == 0)
			header('Location: install.php?install=success');
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>LoginManager +++ Install</title>

		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div class="container">
			<form role="form" action="install.php" method="post">
				<input type="hidden" name="submitted" value="true" />
				<h1>Installation</h1>
				<?if(isset($_GET['install'])) {?>
					<p class="alert alert-success">
						<b>Congratulations!</b> You just installed LoginManager v<?=VERSION?>.<br />
						Now please delete the install.php and have fun using your password managing tool!
					</p>
				<?}?>
				<hr />
				<div class="row">
					<div class="col-sm-6">
						<h2>MySQL Credentials</h2>
						<?if(count($errorsMysqli)>0){?>
						<p class="alert alert-danger">
							<b>Error</b><br />
							- <?=implode('<br /> - ', $errorsMysqli)?>	
						</p>
						<?}?>
						<div class="form-group">
					    	<label for="inputUsername">Username</label>
			    			<input type="text" class="form-control" name="inputUsername" value="<?=((isset($_POST['inputUsername']))?$_POST['inputUsername']:'')?>" placeholder="Enter MySQL username" required />
						</div>
						<div class="form-group">
			    			<label for="inputPassword">Password</label>
			    			<input type="password" class="form-control" name="inputPassword" value="<?=((isset($_POST['inputPassword']))?$_POST['inputPassword']:'')?>" placeholder="Enter MySQL password" required />
						</div>
						<div class="form-group">
			    			<label for="inputHost">Host</label>
			    			<input type="text" class="form-control" name="inputHost" value="<?=((isset($_POST['inputHost']))?$_POST['inputHost']:'localhost')?>" placeholder="Enter MySQL host (normally localhost)" required />
						</div>
						<div class="form-group">
			    			<label for="inputDatabase">Database</label>
			    			<input type="text" class="form-control" name="inputDatabase" value="<?=((isset($_POST['inputDatabase']))?$_POST['inputDatabase']:'')?>" placeholder="Enter MySQL database" required />
						</div>
					</div>
					<div class="col-sm-6">
						<h2>PHP-Modules</h2>
						<?if(count($errorsModules)>0){?>
						<p class="alert alert-danger">
							<b>Error</b><br />
							- <?=implode('<br /> - ', $errorsModules)?>	
						</p>
						<?}?>
						<p class="<?=$modules['php_version']['class']?>">
							<span class="glyphicon glyphicon-<?=$modules['php_version']['icon']?>"></span> PHP 5.3
						</p>
						<p class="<?=$modules['mysqli_ext']['class']?>">
							<span class="glyphicon glyphicon-<?=$modules['mysqli_ext']['icon']?>"></span> PHP MySQLi
						</p>
						<p class="<?=$modules['mcrypt_ext']['class']?>">
							<span class="glyphicon glyphicon-<?=$modules['mcrypt_ext']['icon']?>"></span> PHP Mcrypt
						</p>
						<p class="<?=$modules['password_ext']['class']?>">
							<span class="glyphicon glyphicon-<?=$modules['password_ext']['icon']?>"></span> PHP Password hash/verify
						</p>
						<p class="<?=$modules['writable']['class']?>">
							<span class="glyphicon glyphicon-<?=$modules['writable']['icon']?>"></span> config.inc.php writable?
						</p>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-sm-12">
						<button id="btn-install" class="btn btn-block btn-success"<? if(!$installAvaialable) echo 'disabled';?>>Install LoginManager v<?=VERSION?></button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>