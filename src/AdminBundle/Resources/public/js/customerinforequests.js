$(document).ready(function(){
	$(".update-status").click(function(e){
		e.preventDefault();
		var status = $(this).data('status');
		var id = $(this).data('id');
		$('#status-'+id).val(status);
		$('#status-form-'+id).submit();
	});
});