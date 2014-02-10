<?
	
	require_once 'version.inc.php';

	$modules = array();
	$error = array();
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
		$error[] = 'PHP Version 5.3 is required';
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
		$error[] = 'MySQLi Extension has to be enabled';
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
		$error[] = 'Mcrypt Extension has to be enabled';
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
		$error[] = 'Password has/verfy functions not found - see <a href="http://de2.php.net/manual/de/ref.password.php" target="_blank">php.net</a>';
	}

	$installAvaialable = (count($error)==0);
	
	if (isset($_POST['type'])) {
		if ($_POST['type'] == 'Test settings') {
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
			
			/* close connection */
			@$mysqli->close();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>LoginManager +++ Sign in</title>

		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div class="container">
			<form role="form" action="install.php" method="post">
				<h1>Installation</h1>
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
						<?if(count($error)>0){?>
						<p class="alert alert-danger">
							<b>Error</b><br />
							- <?=implode('<br /> - ', $error)?>	
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