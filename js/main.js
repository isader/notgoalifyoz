(function () {
	$(document).ready(function () {
		function loginBackground () {
			var min = 1,
				max = 3;

			var index = Math.floor(Math.random() * (max - min + 1)) + min;
			$("#login").css({
				'background-image': 'url(backgrounds/' + index + '.jpg)'
			});
		}

		function attachEvents () {
			// Login page - switch to sign up form
			$(".j-switch-to-signup").on('click', function (event) {
				event.preventDefault();

				$("#signup-form").removeClass("hidden");
				$("#login-form").addClass("hidden");
			});

			// Login page - switch to login form
			$(".j-switch-to-login").on('click', function (event) {
				event.preventDefault();

				$("#login-form").removeClass("hidden");
				$("#signup-form").addClass("hidden");
			});

			// Sign out link
			$(".sign-out").on('click', function (event) {
				event.preventDefault();
				
				$.removeCookie("ud");
				location.reload();
			});
		}

		loginBackground();
		attachEvents();
		if (typeof user !== 'undefined') {
			$("#userName").html(user.first_name);
		}
	});
})();