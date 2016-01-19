(function () {
	$(document).ready(function () {

		// Populate all modules container
		function populateAllModules () {
			var url = "services/module.php";

			function postSuccess (data) {
				if (data.status == "ok") {
					$module = $('.module-id-' + data.module_id);
					$module.find('.add-icon').addClass('hidden');
					$module.find('.already-added').removeClass('hidden');
					populateUserModules();
				}
				else {
					console.log(data.message);
				}
			}


			function attachEvents () {
				$(".add-icon").on('click', function (event) {
					event.preventDefault();

					var module_id = $(this).parents(".module").attr("data-module-id"),
						data = {
						new_module: 1,
						user_id: user.user_id,
						module_id: module_id,
						progress: "0",
						is_complete: "0",
						date_completed: ""
					};

					$.ajax({
					  	type: "POST",
					  	dataType: "json",
					  	url: url,
					  	data: data,
					  	success: postSuccess
					});
				});
			}

			function ajaxSuccess (data) {
				if (data) {
					if (data.status === "ok") {
						var template = _.template($('#add_module').html()),
    						html = template({modules: data.modules});

						$("#module-list").html(html);

						attachEvents();
					}
					else {
						console.log(data.message);
					}
				}
			}

			var data = {
				show_all_for_user: 1,
				user_id: user.user_id
			};

			$.ajax({
			  	dataType: "json",
			  	url: url,
			  	type: "GET",
			  	data: data,
				success: ajaxSuccess
			});
		}

		// Populate user modules
		function populateUserModules () {
			function attachEvents () {
				// Add module link
				$('.open-popup-link').magnificPopup({
		  			type:'inline',
		  			midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
				});

				$('.module').on('click', function (event) {
					event.preventDefault();

					var module = $(this).attr('data-module');
					module = JSON.parse(module);
					$(document).trigger('module/click', [module]);
				});
			}

			function ajaxSuccess (data) {
				if (data.status === "ok") {
					var template = _.template($('#list_modules').html()),
						html = template({modules: data.modules});

					$("#user-modules").html(html);

					attachEvents();
				}
				else {
					console.log(data.message);
				}
			}

			var url = "services/module.php",
				data = {
				user_id: user.user_id
			};

			$.ajax({
				dataType: "json",
				url: url,
				type: "GET",
				data: data,
				success: ajaxSuccess
			});
		}

		$(document).on('task/updated', function (event, totalTasks, moduleProgress, moduleIsComplete) {
			var percentage = (moduleProgress * 100) / totalTasks;

			$("#selected-module").find(".completed").css("width", percentage+"%");
			$("#selected-module").find(".number").text(moduleProgress);
		});


		populateAllModules();
		populateUserModules();

	});
})();