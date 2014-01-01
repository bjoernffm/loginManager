LoginManager = function() {
	
	self = this;
	
	self.init = function() {
		$('.btn-login').click(function(e) {
			e.preventDefault();
			self.showOverview();
		});
		$('.btn-logout').click(function(e) {
			e.preventDefault();
			self.showLogin();
		});
	};
	
	self.loadOverviewTable = function() {
		$.getJSON( "ajax/test.json", function( data ) {
			var items = [];
			
			$.each( data, function( key, val ) {
				items.push( "<li id='" + key + "'>" + val + "</li>" );
			});
			
			$( "<ul/>", {
				"class": "my-new-list",
				html: items.join( "" )
			}).appendTo( "body" );
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
