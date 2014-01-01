<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>LoginManager +++ Sign in</title>

		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="pageLogin">
				<form class="form-signin" role="form">
					<h2 class="form-signin-heading">Please sign in</h2>
					<input type="text" class="form-control" placeholder="Email address" autofocus />
					<input type="password" class="form-control" placeholder="Password" />
					<button class="btn btn-lg btn-primary btn-block btn-login">Sign in</button>
				</form>
			</div>
			<div class="pageOverview" style="display: none;">
				<nav class="navbar navbar-default" role="navigation">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="#"><span class="text-primary">Login</span>Manager</a>
					</div>
					
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#" class="btn-logout">Logout</a></li>
						</ul>
					</div>
				</nav>
				<div class="row">
					<div class="col-xs-12">
						<div style="position: relative;">
							<span class="glyphicon glyphicon-remove search-remove"></span>
							<input type="text" class="form-control input-sm pull-right search-input" placeholder="Type here to search ..."/>
						</div>
					</div>
				</div>
				<h3>Own login data</h3>
				<p class="alert alert-warning alert-owned-logins"></p>
				<table class="table table-owned-logins">
					<thead>
						<tr>
							<th>Username</th>
							<th>Password</th>
							<th>Location/Host</th>
							<th>Tags</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<hr /><td></td>
				<h3>Shared login data</h3>
				<p class="alert alert-warning alert-shared-logins"></p>
				<table class="table table-shared-logins">
					<thead>
						<tr>
							<th>Username</th>
							<th>Password</th>
							<th>Location/Host</th>
							<th>Tags</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/ZeroClipboard.min.js"></script>
		<script src="js/jquery-zeroclipboard.js"></script>
		<script src="js/LoginManager.js"></script>
		<script>
			loginManager = new LoginManager();
			loginManager.run();
		</script>
	</body>
</html>