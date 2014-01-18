<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>LoginManager +++ Sign in</title>

		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/bootstrap-tagsinput.css" rel="stylesheet" />
		<link href="css/style.css" rel="stylesheet" />
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
					<div class="col-xs-6">
						<button class="btn btn-primary btn-sm btn-add">
							<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;add new record
						</button>
					</div>
					<div class="col-xs-6">
						<div style="position: relative;">
							<span class="glyphicon glyphicon-remove search-remove"></span>
							<input type="text" class="form-control input-sm pull-right search-input" placeholder="Type here to search ..."/>
						</div>
					</div>
				</div>
				<hr />
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
				
				<!-- modals are defined here -->
				
				<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="modalEditLabel">Edit a login record:</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form">
									<div class="form-group">
										<label for="editUserInput" class="col-sm-3 control-label">User</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="editUserInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="editPasswordInput" class="col-sm-3 control-label">Password</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="editPasswordInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="editLocationInput" class="col-sm-3 control-label">Location/Host</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="editLocationInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="editDescriptionInput" class="col-sm-3 control-label">Description</label>
										<div class="col-sm-9">
											<textarea class="form-control" rows="4" id="editDescriptionInput"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="editTagsInput" class="col-sm-3 control-label">Tags</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="editTagsInput" />
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<input type="hidden" id="editIdInput" />
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-success btn-edit-submit">
									<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Save changes
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAddLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="modalAddLabel">Add a new login record:</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal" role="form">
									<div class="form-group">
										<label for="addUserInput" class="col-sm-3 control-label">
											User <span class="text-danger">*</span>
										</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="addUserInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="addPasswordInput" class="col-sm-3 control-label">
											Password <span class="text-danger">*</span>
										</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="addPasswordInput" />
											<p class="help-block">Warning: Password is displayed on screen.</p>
										</div>
									</div>
									<div class="form-group">
										<label for="addLocationInput" class="col-sm-3 control-label">Location/Host</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="addLocationInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="addDescriptionInput" class="col-sm-3 control-label">Description</label>
										<div class="col-sm-9">
											<textarea class="form-control" rows="4" id="addDescriptionInput"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="addTagsInput" class="col-sm-3 control-label">Tags</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="addTagsInput" />
										</div>
									</div>
									<div class="form-group">
										<label for="addTagsInput" class="col-sm-3 control-label"></label>
										<div class="col-sm-9">
											<span class="text-danger">* required</span>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-primary btn-add-submit">
									<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add login
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="modal fade" id="modalRemove" tabindex="-1" role="dialog" aria-labelledby="modalRemoveLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="modalRemoveLabel">Do you really want to remove the record?</h4>
							</div>
							<div class="modal-footer" style="margin-top: 0; border-top-width: 0;">
								<input type="hidden" id="removeIdInput" />
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-danger btn-remove-submit">
									<span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Delete record
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<!-- end of modal definition -->
			</div>
		</div>
		
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/ZeroClipboard.min.js"></script>
		<script src="js/jquery-zeroclipboard.js"></script>
		<script src="js/bootstrap-tagsinput.min.js"></script>
		<script src="js/sugar.min.js"></script>
		<script src="js/LoginManager.js"></script>
		<script>
			loginManager = new LoginManager();
			loginManager.run();
		</script>
	</body>
</html>