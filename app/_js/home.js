$(document).ready(function () {
	
	
$(".status-lookup-form").on("submit",function(e){
	e.preventDefault();
	$(".form-validation",this).remove();
	var $IDNumber = $("#IDNumber",this);
	var IDNumber = $IDNumber.val();
	
	if (IDNumber){
		
		$.get("/iec/voter/"+IDNumber,function(data){
			$("#modal-window").jqotesub($("#template-voter-status"), data).modal("show").on("hidden.bs.modal", function () {
				
			})
			
		})
		
		
		
		
	} else {
		$field = $IDNumber.parent();
		$field.addClass("has-error");
		$field.after('<span class="help-block s form-validation">ID Number required</span>');
	}
	
	
})	
	
	
});
