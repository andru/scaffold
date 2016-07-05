(function($){
	$(function(){
		//show tooltips
		$('[data-toggle=tooltip]').tooltip();

		$('.nav-tabs a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		})
	});
})(jQuery)
