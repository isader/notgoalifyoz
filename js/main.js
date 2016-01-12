(function () {
	$(document).ready(function () {
		
		$(".expand").on('click', function (event) {
			event.preventDefault();

			$(this).parents('.task').toggleClass('expand-task');
		});

		var min = 1,
			max = 3;

		var index = Math.floor(Math.random() * (max - min + 1)) + min;
		$("#login").css({
			'background-image': 'url(backgrounds/' + index + '.jpg)'
		});

		$('.open-popup-link').magnificPopup({
  			type:'inline',
  			midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
		});
	});
})();