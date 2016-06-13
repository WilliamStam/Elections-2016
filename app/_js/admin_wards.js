$(document).ready(function () {
	
	
	
	getData();
	
	
	
	$(document).on("change", "#wardID", function (e) {
		var key = $(this).val();
		var $this = $(this);
		/*
		$.getData("/lookup/ward/"+key,{"check":"true"},function(data){
			
			var $field = $("#wardID");
			//console.info(k)
			var $block = $field.closest(".form-group");
		
			if (data.code==404){
				$block.addClass("has-error");
				$field.after('<div class="fa fa-times form-control-feedback form-validation" aria-hidden="true"></div>');
				$field.after('<span class="help-block s form-validation">Ward doesn\'t exist</span>');
				
			} else {
				$block.addClass("has-success");
				$field.after('<div class="fa fa-check form-control-feedback form-validation" aria-hidden="true"></div>');
			}
			
		});*/
	});
	
	
	$(document).on("submit", "#filter-form", function (e) {
		e.preventDefault();
		
		getData();
		return false;
	});
	$(document).on("reset", "#filter-form", function (e) {
		e.preventDefault();
		$("#search").val("");
		
		getData();
		return false;
	});
	
	
	$(document).on("click", ".delete-btn", function (e) {
		e.preventDefault();
		var $this = $(this).closest("form");
		if(confirm("Are you sure you want to delete this record?")){
			$.post("/save/admin_wards/delete?ID="+$.bbq.getState("ID"),{},function(result){
				result = result.data;
				if (!result.error){
					toastr["info"]("Record Removed")
					if (typeof getData == 'function') {
						getData();
					}
				} else {
					toastr["error"](result.error,"Error")
					$this.prepend("<div class='alert alert-danger'>"+result.error+"</div>");
				}
				
			})
			
		}
		
		
	});
	
	
	
});

$(document).on("click", "#record-list .record", function () {
	$.bbq.pushState({"ID":$(this).attr("data-id")});
	getData();
});
$(document).on("click", "#details-form .cancel-btn", function () {
	$.bbq.removeState("ID");
	getData();
});



$(document).on("submit","#details-form",function(e){
	e.preventDefault();
	var $this = $(this);
	var data = $this.serialize();
	var saveForm = true;
	
	if (saveForm){
		$.post("/save/admin_wards/form?ID="+$.bbq.getState("ID"),data,function(result){
			result = result.data;
			validationErrors(result, $this);
			if (!result.errors){
				if (typeof getData == 'function') {
					getData();
				}
			} 
			
		})
		
	}
	
	
});









function getData(){
	var page = $.bbq.getState("page");
	page = (page) ? page : "";
	$("#btn-search-clear").hide();
	var search = $("#search").val();
	
	
	var ID = $.bbq.getState("ID");
	
	
	$.getData("/data/admin_wards/data", {"page": page, "search": search, "ID": ID}, function (data) {
		

		$("#record-list tbody").jqotesub($("#template-records"), data);
		$("#left-area").jqotesub($("#template-details"), data);
		
		
		var $pagenation = $("#pagination-area");
		if (data['pagination']['pages'].length > 1) {
			$pagenation.jqotesub($("#template-records-pagination"), data['pagination']).stop(true, true).fadeIn(400);
		} else {
			$pagenation.stop(true, true).fadeOut(400)
		}
		
		if (data['search']){
			$("#btn-search-clear").show();
			$("#search").val(data['search']);
		}
		
		
	})
	
}




