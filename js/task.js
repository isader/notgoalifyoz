(function () {
	$(document).ready(function () {

		function taskStage (state) {
			$('#user-modules').hide();
		}

		function populateCurrentModule (module) {
			var template = _.template($('#list_modules').html()),
				html = template({modules: [module]});

			$("#selected-module").html(html);
		}

		function populateTasks (module_id) {

			function ajaxSuccess (data) {
				var template = _.template($('#list_tasks').html()),
					html = template({tasks: data.tasks});

				$("#selected-module-tasks").html(html);
			}


			var url = "services/task.php",
				data = {
				user_id: user.user_id,
				module_id: module_id
			};

			$.ajax({
				dataType: "json",
				url: url,
				type: "GET",
				data: data,
				success: ajaxSuccess
			});
		}


		$(document).on('module/click', function (event, module) {
			taskStage("show");
			populateCurrentModule(module);
			populateTasks(module.module_id);
		});
	});
})();