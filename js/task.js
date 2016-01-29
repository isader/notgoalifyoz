(function () {
	$(document).ready(function () {

		function taskStage (state) {
			if (state === "show") {
				$('#user-modules').hide();
				$('#selected-module-details').show();
			}
			else {
				$('#user-modules').show();
				$('#selected-module-details').hide();
			}
		}

		function populateCurrentModule (module) {
			var template = _.template($('#list_modules').html()),
				html = template({modules: [module]});

			$("#selected-module").html(html);
		}

		function populateTasks (module_id) {

			function postSuccess (data) {
				
			}

			function saveStepProgress (state, step, task, module) {
				var module_id = $(this).parents(".module").attr("data-module-id"),
					url = "services/task.php",
					data;

				data = {
					step_action: state,
					user_id: user.user_id
				};

				data = _.extend(data, step, task, module);

				$.ajax({
				  	type: "GET",
				  	dataType: "json",
				  	url: url,
				  	data: data,
				  	success: postSuccess
				});
			}

			function updateTaskProgressUI($completed, taskIsComplete) {
				if (taskIsComplete === "1") {
					$completed.addClass('tick');
				}
				else {
					$completed.removeClass('tick');
				}
			}

			function attachEvents () {
				$(".go-back-to-all-modules").on('click', function (event) {
					event.preventDefault();

					$.magnificPopup.close();
					taskStage("hide");
				});
				$(".expand").on('click', function (event) {
					event.preventDefault();

					$(this).parents('.task').toggleClass('expand-task');
				});

				$(".j-expand-step").on('click', function (event) {
					event.preventDefault();

					var $stepDesc = $(this).siblings('.step-desc'),
						txt = $stepDesc.is(':visible') ? 'details' : 'close';

				     $(this).text(txt);
				     $stepDesc.slideToggle();
				});

				$('.steps input[type=checkbox]').on('click', function (event) {
					var step, task, module,
						step_id, isChecked,
						$task, task_id, totalSteps, taskProgress, taskIsComplete, taskDateCompleted,
						$module, $completed, module_id,	totalTasks, moduleProgress, moduleIsComplete, moduleDateCompleted;

					step_id = $(this).attr('data-step-id');
					isChecked = $(this).prop('checked');

					$task = $(this).parents(".task");
					$completed = $task.find('.completed');
					task_id = $task.attr("data-task-id");
					totalSteps = $task.find("input[type=checkbox]").length;
					taskProgress = $task.find("input[type=checkbox]:checked").length;
					taskIsComplete = (totalSteps == taskProgress) ? "1" : "0";
					taskDateCompleted = (taskIsComplete === "1") ? new Date() : "";

					updateTaskProgressUI($completed, taskIsComplete);

					$module = $("#selected-module-details");
					module_id = $module.attr("data-module-id");
					totalTasks = $module.find(".task").length;
					moduleProgress = $module.find(".tick").length;
					moduleIsComplete = (totalTasks === moduleProgress) ? "1" : "0";
					moduleDateCompleted = (moduleIsComplete === "1") ? new Date() : "";

					$(document).trigger('task/updated', [totalTasks, moduleProgress, parseInt(moduleIsComplete)]);

					step = {
						step_id: step_id,
						step_is_complete: "1",
						step_date_completed: new Date()
					};

					task = {
						task_id: task_id,
						task_progress: taskProgress,
						task_is_complete: taskIsComplete,
						task_date_completed: taskDateCompleted
					};

					module = {
						module_id: module_id,
						module_progress: moduleProgress,
						module_is_complete: moduleIsComplete,
						module_date_completed: moduleDateCompleted
					};

					if (isChecked) {
						saveStepProgress("record", step, task, module);
					}
					else {
						saveStepProgress("remove", step, task, module);
					}
				});
			}

			function ajaxSuccess (data) {
				var template = _.template($('#list_tasks').html()),
					html = template({tasks: data.tasks});

				$("#selected-module-tasks").html(html);
				$("#selected-module-details").attr('data-module-id', data.module_id);
				$("#selected-module-details a").not(".expand-step").attr("target", "_blank");

				attachEvents();
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