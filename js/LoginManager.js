LoginManager = function() {
	
	self = this;
	
	self.init = function() {
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
				$('.search-remove').show();
			} else {
				$('.search-remove').hide();
			}
		});
		$('.search-remove').click(function() {
			$('.search-input').val('').change();	
		});
	};
	
	self.loadOverviewTable = function() {
		$.getJSON( "ajax/getLoginList.ajax.php", function( data ) {
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
				
				row = '<tr>' +
						'<td>' + val.user + '</td>' +
						'<td><button class="btn btn-xs btn-default">hidden</button></td>'+
						'<td>' + val.location + '</td>'+
						'<td>' + val.tags + '</td>'+
					'</tr>';
				
				if (val.type == 'OWNER') {
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
		});
	};
	
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
};
