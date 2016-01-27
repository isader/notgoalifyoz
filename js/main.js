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

		function populateMyProfileForm () {
			var $myProfilePopup = $("#myprofile-popup");

			if ($myProfilePopup.length) {
				$myProfilePopup.find('.first-name').val(user.first_name);
				$myProfilePopup.find('.last-name').val(user.last_name);
				$myProfilePopup.find('.age').val(user.age);
				$myProfilePopup.find('.user-id').val(user.user_id);
			}
		}

		function showStatus (statusClass) {
			$myProfilePopup = $("#myprofile-popup");

			$(".status p", $myProfilePopup).hide();
			$(".status ." + statusClass, $myProfilePopup).show();
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

			$("#my-profile-form").on('submit', function (event) {
				event.preventDefault();

				var $form = $(this),
					url = $form.attr('action'),
					data = $form.serialize();

				$.ajax({
   					type: 'POST',
   					url: url,
   					data: data,
   					dataType: "json",
   					success: function (data) {
   						var userObject = $form.serializeObject();
   						delete userObject["myprofile_flag"];

   						if (data.status === "ok") {
   							$.cookie('ud', JSON.stringify(userObject));
   							user = userObject;

   							showStatus("success");
   						}
   						else {
   							console.log(data.message);
   							showStatus("error");
   						}
   					}
				});
			});

			// Sign out link
			$(".sign-out").on('click', function (event) {
				event.preventDefault();
				
				$.removeCookie("ud");
				location.reload();
			});
		}

		loginBackground();
		populateMyProfileForm();
		attachEvents();
		if (typeof user !== 'undefined') {
			$("#userName").html(user.first_name);
		}
	});
})();