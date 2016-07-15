$(document).ready(function(){
	$(".update-status").click(function(e){
		e.preventDefault();
		var status = $(this).data("status");
		var id = $(this).data("id");
		$("#status-"+id).val(status);
		$("#status-form-"+id).submit();
	});

	$(".view-message").click(function(e) {
		e.preventDefault();
		$('#message-body p').html('');
		var id = $(this).data('id');
		var message = $('#message-'+id).val();

		$('#message-body p').html(message);
		$("#message-modal").modal();
	})
});