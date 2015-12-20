(function () {
	$(document).ready(function () {
		
		$(".expand").on('click', function (event) {
			event.preventDefault();

			$(this).parents('.task').toggleClass('expand-task');
		});

	});
})();