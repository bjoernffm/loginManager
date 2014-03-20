LoginManager = function() {
	
	self = this;
	
	self.init = function() {
		$.zeroclipboard({
			moviePath: 'js/ZeroClipboard.swf',
            activeClass: 'active',
            hoverClass: 'hover'
		});
		
		$('#editTagsInput').tagsinput({
			tagClass: function(item) {
				return'label label-primary';
			},
			confirmKeys: [13, 188]
		});
		
		$('#addTagsInput').tagsinput({
			tagClass: function(item) {
				return'label label-primary';
			},
			confirmKeys: [13, 188]
		});
		
		$('#modalAccount').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		
		$('#modalAccount').on('hidden.bs.modal', function (e) {
			$('#accountIdInput').val('');
			$('#accountUsernameInput').val('');
			$('#accountEmailInput').val('');
			$('#accountPasswordInput').val('');
			$('#accountPasswordInput').attr('type', 'password');
			$('#accountShowPassword').removeAttr('checked');
		});
		
		$('#accountShowPassword').change(function() {
			if ($('#accountShowPassword').prop('checked')) {
				$('#accountPasswordInput').attr('type', 'text');	
			} else {
				$('#accountPasswordInput').attr('type', 'password');
			}
		});
		
		$('.btn-account').click(function() {
			$.getJSON('ajax/getUser.ajax.php?id=me', function( data ) {
				if (data.status == 200) {
					$('#accountIdInput').val(data.user.id);
					$('#accountUsernameInput').val(data.user.name);
					$('#accountEmailInput').val(data.user.email);
					$('#modalAccount').modal('show');
				}
			});
		});
		
		$('.btn-account-submit').click(function() {
			$.getJSON(
				'ajax/editUser.ajax.php',
				{
					'id': $('#accountIdInput').val(),
					'name': $('#accountUsernameInput').val(),
					'email': $('#accountEmailInput').val(),
					'password': $('#accountPasswordInput').val(),
				},
				function( data ) {
					if (data.status == 200) {
						$('#modalAccount').modal('hide');
					}
				}
			);
		});
		
		$('#modalEdit').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
				
		$('#modalEdit').on('hidden.bs.modal', function (e) {
			$('#editIdInput').val('');
			$('#editUserInput').val('');
			$('#editPasswordInput').val('');
			$('#editLocationInput').val('');
			$('#editDescriptionInput').val('');
			$('#editTagsInput').tagsinput('removeAll');
		});
		
		$('.btn-edit-submit').click(function() {
			self.editFormSubmit();
		});
		
		$('#modalAdd').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		
		$('#modalAdd').on('hidden.bs.modal', function (e) {
			$('#addUserInput').val('');
			$('#addPasswordInput').val('');
			$('#addLocationInput').val('');
			$('#addDescriptionInput').val('');
			$('#addTagsInput').tagsinput('removeAll');
		});
		
		$('.btn-add').click(function() {
			self.addFormShow();
		});
		
		$('.btn-add-submit').click(function() {
			self.addFormSubmit();
		});

		$('#modalRemove').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		
		$('.btn-remove-submit').click(function() {
			self.removeFormSubmit();
		});
        
		$('.btn-login').click(function(e) {
			e.preventDefault();
			
			error = false;
			
			/**
			 * Checking email input.
			 */
			username = $('.input-login-username').val();
			if (username.trim() == "") {
				$('.input-login-username').parent().addClass('has-error');
				error = true;
			} else {
				$('.input-login-username').parent().removeClass('has-error');
			}
			
			/**
			 * Checking password input.
			 */
			password = $('.input-login-password').val();
			if (password.trim() == "") {
				$('.input-login-password').parent().addClass('has-error');
				error = true;
			} else {
				$('.input-login-password').parent().removeClass('has-error');
			}
			
			if (error == true) {
				$('#loginMessage').text('Please fill the input fields below.').show();
			} else {
				$('#loginMessage').hide();

				autologin = $('#input-autologin').prop('checked');

				self.login(username, password, autologin, function(data) {
					console.log(data);

					if (data.status == 200) {
						$('#loginMessage').hide();
						self.loadOverviewTable();
						self.showOverview();
					} else {
						$('#loginMessage').text('Email or passwort incorrect.').show();
					}
				});
			}
		});
		$('.btn-logout').click(function(e) {
			e.preventDefault();
			self.logout(function(data) {
				self.showLogin();
			});
		});
		$('.search-input').on('keyup keydown change', function() {
			value = $(this).val(); 	
			if (value.trim().length > 0) {
				self.loadOverviewTable(value);
				$('.search-remove').show();
			} else {
				self.loadOverviewTable();
				$('.search-remove').hide();
			}
		});
		$('.search-remove').click(function() {
			$('.search-input').val('').change();	
		});
	};
	
	self.loadOverviewTable = function(searchTerm) {
		$.getJSON('ajax/getLoginList.ajax.php', {search: searchTerm}, function( data ) {
			var ownedLogins = [];
			var sharedLogins = [];

			$.each( data, function( key, val ) {
				if (val.tags.length > 0) {
					val.tags = '<span class="label label-primary">'+
								val.tags.join('</span>&nbsp;<span class="label label-primary">')+
								'</span>';	
				} else {
					val.tags = '';
				}
				
				if (self.isUrl(val.location)) {
					val.location = '<a href="' + val.location + '" target="_blank">' + val.location.truncate(35) + '</a>';
				} else {
					val.location = val.location.truncate(35);
				}
				
				row = '<tr>' +
						'<td width="20%">' + val.user + '</td>' +
						'<td width="20%"><button class="btn btn-xs btn-default btn-show-password" data-id="' + val.id + '">'+
						'show</button></td>'+
						'<td width="20%">' + val.location + '</td>'+
						'<td width="20%">' + val.tags + '</td>'+
						'<td width="20%"><button class="btn btn-xs btn-remove btn-danger pull-right" data-id="' + val.id + '">'+
						'<span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;remove</button>'+
						'<button class="btn btn-xs btn-success btn-edit pull-right" data-id="' + val.id + '" style="margin-right: 5px;">'+
						'<span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;edit</button></td>'+
					'</tr>';
				
				ownedLogins.push(row);

			});
			
			if (ownedLogins.length > 0) {
				$('.alert-owned-logins').hide();
				$('.table-owned-logins').show().find('tbody').html(ownedLogins.join(''));
			} else {
				$('.alert-owned-logins').text('You currently have no login data.').show();
				$('.table-owned-logins').hide();
			}
			
			/**
			 * The event listeners are defined here:
			 */
			
			$('.btn-edit').click(function() {
				self.editFormShow($(this).attr('data-id'));
			})
			
			$('.btn-remove').click(function() {
				self.removeFormShow($(this).attr('data-id'));
			});

			$('.btn-show-password').click( function() {
				btn = $(this);
				$.getJSON('ajax/getPassword.ajax.php', {id: btn.attr('data-id')}, function( data ) {
					if(data !== false) {
						test = $('<input type="text" />');
						test.val(data);
						test.attr('readonly');
						test.css('height', 22);
						test.addClass('form-control input-sm');
						btn.parent().html(test);
					}
				});
			});
		});
	};
	
	self.editFormSubmit = function() {
		isError = false;
		
		if ($('#editUserInput').val() == '') {
			$('#editUserInput').parent().parent().addClass('has-error');
			isError = true;
		}
		if ($('#editPasswordInput').val() == '') {
			$('#editPasswordInput').parent().parent().addClass('has-error');
			isError = true;
		}
		if (isError === false) {
			$.getJSON(
				'ajax/editLogin.ajax.php',
				{
					id: $('#editIdInput').val(),
					user: $('#editUserInput').val(),
					password: $('#editPasswordInput').val(),
					location: $('#editLocationInput').val(),
					description: $('#editDescriptionInput').val(),
					tags: $('#editTagsInput').val()
				},
				function( data ) {
					$('.search-input').change();
					$('#modalEdit').modal('hide');
				}
			)
		}
	}
	
	self.editFormShow = function (id) {
		$.getJSON('ajax/getLogin.ajax.php', {'id': id}, function( data ) {
			if (data.status == 200) {
				$('#editIdInput').val(data.login.id);
				$('#editUserInput').val(data.login.user);
				$('#editPasswordInput').val(data.login.password);
				$('#editLocationInput').val(data.login.location);
				$('#editDescriptionInput').val(data.login.description);
				$('#editTagsInput').tagsinput('removeAll');
				
				for (i = 0; i < data.login.tags.length; i++) {
					$('#editTagsInput').tagsinput('add', data.login.tags[i]);
				}
				
				$('#modalEdit').modal('show');
			}
		});
	};
	
	self.addFormShow = function () {
		$('#modalAdd').modal('show');
	};
	
	self.addFormSubmit = function() {
		isError = false;
		
		if ($('#addUserInput').val() == '') {
			$('#addUserInput').parent().parent().addClass('has-error');
			isError = true;
		}
		if ($('#addPasswordInput').val() == '') {
			$('#addPasswordInput').parent().parent().addClass('has-error');
			isError = true;
		}
		if (isError === false) {
			$.getJSON(
				'ajax/addLogin.ajax.php',
				{
					user: $('#addUserInput').val(),
					password: $('#addPasswordInput').val(),
					location: $('#addLocationInput').val(),
					description: $('#addDescriptionInput').val(),
					tags: $('#addTagsInput').val()
				},
				function( data ) {
					console.log(data);
					
					$('.search-input').change();
					$('#modalAdd').modal('hide');
				}
			)
		}
	}
	
	self.removeFormShow = function (id) {
		$('#removeIdInput').val(id);
		$('#modalRemove').modal('show');
	};
	
	self.removeFormSubmit = function() {
		isError = false;
		
		if ($('#removeIdInput').val() == '') {
			isError = true;
		}
		if (isError === false) {
			$.getJSON(
				'ajax/removeLogin.ajax.php',
				{
					id: $('#removeIdInput').val()
				},
				function( data ) {
					console.log(data);
					
					$('.search-input').change();
					$('#modalRemove').modal('hide');
				}
			);
		}
	}
	
	self.login = function(username, password, autologin, callback) {
		$.getJSON(
			'ajax/loginSession.ajax.php',
			{
				'username': username,
				'password': password,
				'autologin': autologin
			},
			function( data ) {
				callback.call(self, data);
			}
		);	
	}
	
	self.logout = function(callback) {
		$.getJSON(
			'ajax/logoutSession.ajax.php',
			function( data ) {
				callback.call(self, data);
			}
		);	
	}
	
	self.isLoggedIn = function(callback) {
		$.getJSON(
			'ajax/checkSession.ajax.php',
			function( data ) {
				if (data.loggedIn == true) {
					callback.call(self, true);
				} else {
					callback.call(self, false);
				}
			}
		);	
	}
	
	self.isAutologin = function(callback) {
		$.getJSON(
			'ajax/checkAutologin.ajax.php',
			function( data ) {
				if (data.status == 200) {
					callback.call(self, true);
				} else {
					callback.call(self, false);
				}
			}
		);	
	}
	
	self.showLogin = function() {
		$('.pageOverview').fadeOut(function() {
			self.changeTitle('Sign in');
			$('.pageLogin').fadeIn();
		});
	};
	self.showOverview = function() {
		$('.pageLogin').fadeOut(function() {
			self.changeTitle('Overview');
			$('.input-login-username').val('');
			$('.input-login-password').val('');
			$('.pageOverview').fadeIn();
		});
	};
	self.run = function() {
		self.init();

		self.isLoggedIn(function(logged) {
			if (logged == true) {
				self.loadOverviewTable();
				self.showOverview();
				console.log('RELOGIN');
			} else {
				self.isAutologin(function(autologin) {
					if (autologin == true) {
						self.loadOverviewTable();
						self.showOverview();
						console.log('AUTOLOGIN');
					} else {
						self.showLogin();
					}
				})
			}
		});
	};
	
	/**
	 * Helper functions.
	 */
	
	self.isUrl = function(s) {
		var regexp = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/;
		return regexp.test(s);
	};
	
	self.changeTitle = function(title) {
		$('title').text('LoginManager +++ ' + title);
	};
};
