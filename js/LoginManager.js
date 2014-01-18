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
		
		$('#modalEdit').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		
		$('.btn-edit-submit').click(function() {
			self.editFormSubmit();
		});
		
		$('#modalAdd').modal({
			backdrop: true,
			keyboard: true,
			show: false
		});
		
		$('.btn-add').click(function() {
			self.addFormShow();
		});
		
		$('.btn-add-submit').click(function() {
			self.addFormSubmit();
		});
        
		$('.btn-login').click(function(e) {
			e.preventDefault();
			self.loadOverviewTable();
			self.showOverview();
		});
		$('.btn-logout').click(function(e) {
			e.preventDefault();
			self.showLogin();
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
				
				console.log(val.location);
				console.log(self.isUrl(val.location));
				if (self.isUrl(val.location)) {
					val.location = '<a href="' + val.location + '" target="_blank">' + val.location.truncate(35) + '</a>';
				} else {
					val.location = val.location.truncate(35);
				}
				
				row = '<tr>' +
						'<td>' + val.user + '</td>' +
						'<td><button class="btn btn-xs btn-default btn-copy-password" data-id="' + val.id + '">'+
						'<span class="glyphicon glyphicon-share"></span>&nbsp;&nbsp;copy to clipboard</button></td>'+
						'<td>' + val.location + '</td>'+
						'<td>' + val.tags + '</td>'+
						'<td><button class="btn btn-xs btn-danger pull-right">'+
						'<span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;remove</button>'+
						'<button class="btn btn-xs btn-success btn-edit pull-right" data-id="' + val.id + '" style="margin-right: 5px;">'+
						'<span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;edit</button></td>'+
					'</tr>';
				
				if (val.type == 'OWNED') {
					ownedLogins.push(row);
				} else {
					sharedLogins.push(row);
				}

			});
			
			if (ownedLogins.length > 0) {
				$('.alert-owned-logins').hide();
				$('.table-owned-logins').show().find('tbody').html(ownedLogins.join(''));
			} else {
				$('.alert-owned-logins').text('You currently have no login data.').show();
				$('.table-owned-logins').hide();
			}
			
			if (sharedLogins.length > 0) {
				$('.alert-shared-logins').hide();
				$('.table-shared-logins').show().find('tbody').html(sharedLogins.join(''));
			} else {
				$('.alert-shared-logins').text('You currently have no shared login data.').show();
				$('.table-shared-logins').hide();
			}
			
			/**
			 * The event listeners are defined here:
			 */
			
			$('.btn-edit').click(function() {
				self.editFormShow($(this).attr('data-id'));
			})

			$('.btn-copy-password').zeroclipboard({
				dataRequested: function (event, setText) {
					button = $(this);
					$.getJSON('ajax/getPassword.ajax.php', {id: button.attr('data-id')}, function( data ) {
						if(data !== false) {
							//console.log(data);
							setText(data);
						}
					});
				},
				complete: function() {
					button.html('<span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;copied!');
					window.setTimeout(function() {
						button.html('<span class="glyphicon glyphicon-share"></span>&nbsp;&nbsp;copy to clipboard');
					}, 500);
				}
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
					console.log(data);
					
					$('.search-input').change();
					$('#modalEdit').modal('hide');
				}
			)
		}
	}
	
	self.editFormShow = function (id) {
		console.log(id);
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
	
		$('#addUserInput').val('');
		$('#addPasswordInput').val('');
		$('#addLocationInput').val('');
		$('#addDescriptionInput').val('');
		$('#addTagsInput').tagsinput('removeAll');
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
	
	self.showLogin = function() {
		$('.pageOverview').fadeOut(function() {
			$('.pageLogin').fadeIn();
		});
	};
	self.showOverview = function() {
		$('.pageLogin').fadeOut(function() {
			$('.pageOverview').fadeIn();
		});
	};
	self.run = function() {
		self.init();
		console.log('Program running');
	};
	
	/**
	 * Helper functions.
	 */
	
	self.isUrl = function(s) {
		var regexp = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/;
		return regexp.test(s);
	}
};
